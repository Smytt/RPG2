<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
use AppBundle\Entity\BattleWarrior;
use AppBundle\Entity\User;
use AppBundle\Entity\WarriorType;
use AppBundle\Repository\BattleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Range;

class WarriorsController extends Controller
{
    const TIME_PER_BLOCK = 0.01;
    const TIME_PER_YEAR = 0.05;

    /**
     * @Route("/attack/{username}", name="attack_user")
     * @param Request $request
     * @param string $username
     * @return Response
     */
    public function attackUserAction(Request $request, $username)
    {
        /**
         * @var User
         */
        $aggressor = $this->getUser();
        $victim = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$victim === null) {
            $message = "The user you are trying to attack doesn't exist";
            return $this->render('generalError.html.twig', ['message' => $message]);
        }

        if ($victim->getId() === $aggressor->getId()) {
            $message = "Don't attack yourself...";
            return $this->render('generalError.html.twig', ['message' => $message]);
        }

        $battle = $this->getDoctrine()->getRepository(Battle::class)
            ->findOneBy(['aggressor' => $aggressor, 'victim' => $victim, 'isActive' => 1]);

        if ($battle) {
            return $this->redirectToRoute('battle_resume', ['id' => $battle->getId()]);
        }

        $distance = $this->findDistance($victim, $aggressor);
        $timeDistance = $this->findTimeDistance($victim, $aggressor);
        $timeInEachDirection = ceil($distance * self::TIME_PER_BLOCK + $timeDistance * self::TIME_PER_YEAR);

        $chosenWarriors = [];
        $form = $this->createFormChooseWarriors($chosenWarriors, $aggressor, $victim);
        $form->handleRequest($request);

        $canMakeTrip = $this->canMakeTrip($aggressor, $distance, $timeDistance);

        if ($form->isValid() && $form->isSubmitted() && $canMakeTrip) {
            return $this->makeBattle($aggressor, $victim, $distance, $timeDistance, $timeInEachDirection, $form);
        }

        return $this->render('battle/attack.html.twig', [
            'victim' => $victim,
            'aggressor' => $aggressor,
            'time' => $timeInEachDirection,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/battle/{id}", name="battle_resume", requirements={"id": "\d+"})
     * @param int $id
     * @return Response
     */
    public function battleResume(int $id)
    {
        $user = $this->getUser();

        $battles = $this->getDoctrine()->getRepository(Battle::class)->findBy(['id' => $id]);
        $battle = $battles[0];

        if ($battle->getAggressor() !== $user && $battle->getVictim() != $user) {
            $message = "This battle is not yours.";
            return $this->render('generalError.html.twig', ['message' => $message]);
        }

        return $this->render('battle/battle.html.twig', ['battle' => $battle]);
    }

    private function makeBattle(User $aggressor, User $victim, $distance, $timeDistance, $timeInEachDirection, FormInterface $form)
    {
        $em = $this->getDoctrine()->getManager();
        $battle = new Battle($aggressor, $victim, $timeInEachDirection);

        //subtract cost of travel
        foreach ($aggressor->getPlanet()->getStocks() as $stock) {
            $stock->setQuantity($stock->getQuantity() -
                $stock->getType()->getCostPerBlockTravel() * 2 * $distance -
                $stock->getType()->getCostPerYearTravel() * 2 * $timeDistance);
        }

        //subtract warriors of aggressor and insert into battlewarriors entity
        foreach ($form->getData() as $warriorType => $quantity) {
            $battleWarrior = new BattleWarrior($quantity, $battle, $aggressor);
            foreach ($aggressor->getPlanet()->getWarriors() as $currentWarrior) {
                if ($currentWarrior->getType()->getId() == $warriorType) {
                    $battleWarrior->setType($currentWarrior->getType());
                    $currentWarrior->setQuantity($currentWarrior->getQuantity() - $quantity);
                    $em->persist($battleWarrior);
                }
            }
        }

        $em->persist($battle);
        $em->persist($aggressor);

        $em->flush();
        $this->battleCron($battle, $timeInEachDirection);

        return $this->redirectToRoute('battle_resume', ['id' => $battle->getId()]);
    }

    private function battleCron(Battle $battle, $timeInEachDirection)
    {
        $user = $this->container->getParameter('database_user');
        $pass = $this->container->getParameter('database_password');
        $db = $this->container->getParameter('database_name');

        $templateSQLContent = file_get_contents('c:/Temp/template/battleProgress.sql');
        $fileSQL = 'battle' . $battle->getId() . '.sql';
        $generatedSQLFile = strtr($templateSQLContent, [
            '{battleEvent}' => 'battleEvent' . $battle->getId(),
            '{timeInEachDirection}' => $timeInEachDirection,
            '{victimId}' => $battle->getVictim()->getId(),
            '{aggressorId}' => $battle->getAggressor()->getId(),
            '{battleId}' => $battle->getId(),
            '{returnEvent}' => 'returnEvent' . $battle->getId(),
            '{totalTime}' => $timeInEachDirection * 2,
            '{planetId}' => $battle->getAggressor()->getPlanet()->getId(),

        ]);
        $tempFile = fopen("c:/Temp/" . $fileSQL, 'w');
        fwrite($tempFile, $generatedSQLFile);
        fclose($tempFile);

        $query = "mysql --user={$user} --password={$pass} {$db} < c:/Temp/{$fileSQL}";
        exec($query);
        unlink("c:/Temp/{$fileSQL}");
    }

    private function createFormChooseWarriors($chosenWarriors, User $aggressor, User $victim)
    {
        $warriorTypes = $this->getDoctrine()->getRepository(WarriorType::class)->findAll();
        $form = $this->createFormBuilder($chosenWarriors);
        foreach ($warriorTypes as $type) {
            $maxQuantity = 0;
            foreach ($aggressor->getPlanet()->getWarriors() as $warriorGroup) {
                if ($type->getType() == $warriorGroup->getType()->getType()) {
                    $maxQuantity = $warriorGroup->getQuantity();
                }
            }
            $form->add($type->getId(), NumberType::class, [
                'constraints' => new Range([
                    'min' => 0,
                    'max' => $maxQuantity,
                    'minMessage' => 'min error message',
                    'maxMessage' => "you only have $maxQuantity " . $type->getType()
                ]),
                'label' => $type->getType(),
                'attr' => [
                    'min' => 0,
                    'max' => $maxQuantity
                ],
            ]);
        }
        $form->add('save', SubmitType::class, ['label' => 'Send to battle!']);
        return $form->getForm();
    }

    private function findDistance(User $victim, User $aggressor)
    {
        $x1 = $victim->getPlanet()->getCoordinateX();
        $x2 = $aggressor->getPlanet()->getCoordinateX();
        $y1 = $victim->getPlanet()->getCoordinateY();
        $y2 = $aggressor->getPlanet()->getCoordinateY();
        return sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));
    }

    private function findTimeDistance(User $victim, User $aggressor)
    {
        return abs($victim->getPlanet()->getCoordinateTime()
            - $aggressor->getPlanet()->getCoordinateTime());
    }

    private function canMakeTrip(User $aggressor, $distance, $timeDistance)
    {
        $canMakeTrip = true;
        foreach ($aggressor->getPlanet()->getStocks() as $stock) {
            if ($stock->getQuantity() < $stock->getType()->getCostPerBlockTravel() * 2 * $distance
                || $stock->getQuantity() < $stock->getType()->getCostPerYearTravel() * 2 * $timeDistance) {
                $canMakeTrip = false;
            }
        }

        return $canMakeTrip;
    }
}
