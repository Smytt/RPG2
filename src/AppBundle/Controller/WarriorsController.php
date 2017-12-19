<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
use AppBundle\Entity\BattleWarrior;
use AppBundle\Entity\Building;
use AppBundle\Entity\Planet;
use AppBundle\Entity\Stock;
use AppBundle\Entity\User;
use AppBundle\Entity\Warrior;
use AppBundle\Entity\WarriorCosts;
use AppBundle\Entity\WarriorRequirements;
use AppBundle\Entity\WarriorType;
use AppBundle\Repository\BattleRepository;
use AppBundle\Service\BattleService;
use Doctrine\Common\Collections\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
     * @param BattleService $battleService
     * @return Response
     */
    public function attackUserAction(Request $request, $username, BattleService $battleService)
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

        $distance = $this->findDistance($victim, $aggressor);
        $timeDistance = $this->findTimeDistance($victim, $aggressor);
        $timeInEachDirection = ceil($distance * self::TIME_PER_BLOCK + $timeDistance * self::TIME_PER_YEAR);

        $form = $this->createFormChooseWarriors($aggressor);
        $form->handleRequest($request);

        $canMakeTrip = $this->canMakeTrip($aggressor, $distance, $timeDistance);
        $canMakeTrip = $battleService->canMakeTrip($aggressor, $distance, $timeDistance);

        if ($form->isValid() && $form->isSubmitted() && $canMakeTrip) {
            return $this->makeBattle($aggressor, $victim, $distance, $timeDistance, $timeInEachDirection, $form);
        }

        return $this->render('battle/attack.html.twig', [
            'canMakeTrip' => $canMakeTrip,
            'victim' => $victim,
            'aggressor' => $aggressor,
            'distance' => $distance,
            'timeDistance' => $timeDistance,
            'time' => $timeInEachDirection,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/battle/{id}", name="battle_resume", requirements={"id": "\d+"})
     * @param int $id
     * @return Response
     */
    public function battleResumeAction(int $id)
    {
        $user = $this->getUser();

        $battles = $this->getDoctrine()->getRepository(Battle::class)->findBy(['id' => $id]);
        $battle = $battles[0];
        if ($battle->getAggressor() !== $user && $battle->getVictim() != $user) {
            $message = "This battle is not yours.";
            return $this->render('generalError.html.twig', ['message' => $message]);
        }
        $now = new \DateTime();
        $timeLeft = $now->diff($battle->getBattleHappen());
        $timeToReturn = $now->diff($battle->getReturnDue());
        return $this->render('battle/battle.html.twig', ['battle' => $battle, 'timeLeft' => $timeLeft, 'timeToReturn' => $timeToReturn]);
    }

    /**
     * @Route("/purchase/", name="purchase_warriors")
     * @param Request $request
     * @return Response
     */
    public function purchaseWarriorsAction(Request $request)
    {
        $chosenWarriors = [];
        $form = $this->createFormBuilder($chosenWarriors);
        $form
            ->add('quantity', NumberType::class)
            ->add('warriorType', EntityType::class, [
                'class' => WarriorType::class,
                'choice_label' => 'type'
            ])
            ->add('save', SubmitType::class, ['label' => 'Purchase']);
        $form = $form->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $canPurchase = $this->canPurchase($form);
            $requirementsMet = $this->requirementsMet($form);
            if (!$requirementsMet) return $this->render('player/purchase.html.twig', [
                'form' => $form->createView(),
                'data' => $form->getData(),
                'formSubmitted' => $form->isSubmitted() && $form->isValid(),
                'requirementsMet' => 0
            ]);
            if (!$canPurchase) return $this->render('player/purchase.html.twig', [
                'form' => $form->createView(),
                'data' => $form->getData(),
                'max' => $this->getMax($form),
                'formSubmitted' => $form->isSubmitted() && $form->isValid(),
                'canPurchase' => 0
            ]);

            $this->makePurchase($form);

        }
        return $this->render('player/purchase.html.twig', [
            'form' => $form->createView(),
            'data' => $form->getData(),
            'formSubmitted' => $form->isSubmitted() && $form->isValid()
        ]);

    }

    private function makeBattle(User $aggressor, User $victim, $distance, $timeDistance, $timeInEachDirection, FormInterface $form)
    {
        $em = $this->getDoctrine()->getManager();
        $battle = new Battle($aggressor, $victim, $timeInEachDirection);

        //subtract cost of travel
        foreach ($aggressor->getPlanet()->getStocks() as $stock) {
            $stock->setQuantity($stock->getQuantity() -
                ceil($stock->getType()->getCostPerBlockTravel() * 2 * $distance) -
                ceil($stock->getType()->getCostPerYearTravel() * 2 * $timeDistance));
        }

        //subtract warriors of aggressor and insert into battlewarriors entity
        foreach ($form->getData() as $warriorType => $quantity) {
            $battleWarriorBatch = new BattleWarrior($quantity, $battle);
            foreach ($aggressor->getPlanet()->getWarriors() as $currentWarrior) {
                if ($currentWarrior->getType()->getId() == $warriorType) {
                    $battleWarriorBatch->setType($currentWarrior->getType());
                    $currentWarrior->setQuantity($currentWarrior->getQuantity() - $quantity);
                    $em->persist($battleWarriorBatch);
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
//        unlink("c:/Temp/{$fileSQL}");
    }

    private function createFormChooseWarriors(User $aggressor)
    {
        $chosenWarriors = [];
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
                    'minMessage' => 'Please enter only positive quantities',
                    'maxMessage' => "You only have $maxQuantity " . $type->getType()
                ]),
                'label' => $type->getType() . ' - max ' . $maxQuantity,
                'attr' => [
                    'min' => 0,
                    'max' => $maxQuantity
                ],
            ]);
        }
        $form->add('save', SubmitType::class, ['label' => 'Send to battle']);
        return $form->getForm();
    }

    private function findDistance(User $victim, User $aggressor)
    {
        $x1 = $victim->getPlanet()->getCoordinateX();
        $x2 = $aggressor->getPlanet()->getCoordinateX();
        $y1 = $victim->getPlanet()->getCoordinateY();
        $y2 = $aggressor->getPlanet()->getCoordinateY();
        return floor(sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2)));
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
            if ($stock->getQuantity() < ceil($stock->getType()->getCostPerBlockTravel() * 2 * $distance)
                || $stock->getQuantity() < ceil($stock->getType()->getCostPerYearTravel() * 2 * $timeDistance)) {
                $canMakeTrip = false;
            }
        }

        return $canMakeTrip;
    }

    private function canPurchase(FormInterface $form)
    {
        /**
         * @var Stock[]
         */
        $stocks = $this->getUser()->getPlanet()->getStocks();
        $warriorType = $this->getDoctrine()
            ->getRepository(WarriorType::class)
            ->find($form->getData()['warriorType']);
        $warriorCosts = $this->getDoctrine()
            ->getRepository(WarriorCosts::class)
            ->findBy(['warriorType' => $warriorType]);

        $desiredQuantity = intval($form->getData()['quantity']);

        foreach ($warriorCosts as $cost) {
            foreach ($stocks as $stock) {
                if ($cost->getStockType()->getId() === $stock->getType()->getId()) {
                    if ($cost->getRequiredAmount() * $desiredQuantity > $stock->getQuantity()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function requirementsMet(FormInterface $form)
    {
        /**
         * @var Building[]
         */
        $buildings = $this->getUser()->getPlanet()->getBuildings();
        $warriorType = $this->getDoctrine()
            ->getRepository(WarriorType::class)
            ->find($form->getData()['warriorType']);
        $warriorRequirements = $this->getDoctrine()
            ->getRepository(WarriorRequirements::class)
            ->findBy(['warriorType' => $warriorType]);

        foreach ($warriorRequirements as $requirement) {
            foreach ($buildings as $building) {
                if ($requirement->getBuildingType()->getId() === $building->getType()->getId()) {
                    if ($requirement->getRequiredLevel() > $building->getLevel()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function makePurchase(FormInterface $form)
    {
        /**
         * @var User
         */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $quantity = intval($form->getData()['quantity']);

        $warrior = $this->getDoctrine()->getRepository(Warrior::class)
            ->findOneBy([
                'type' => $form->getData()['warriorType'],
                'planet' => $user->getPlanet()
            ]);

        /**
         * @var Stock[]
         */
        $stocks = $this->getDoctrine()->getRepository(Stock::class)
            ->findBy(['planet' => $user->getPlanet()]);

        foreach ($stocks as $stock) {
            foreach ($warrior->getType()->getCost() as $cost) {
                if ($stock->getType()->getId() === $cost->getStockType()->getId()) {
                    $stock->setQuantity($stock->getQuantity() - $cost->getrequiredAmount() * $quantity);
                    $em->persist($stock);
                }
            }
        }
        $warrior->setInQueue($warrior->getInQueue() + $quantity);
        $em->persist($warrior);
        $em->flush();
    }

    private function getMax(FormInterface $form)
    {
        $stocks = $this->getUser()->getPlanet()->getStocks();
        $warriorType = $this->getDoctrine()
            ->getRepository(WarriorType::class)
            ->find($form->getData()['warriorType']);
        $warriorCosts = $this->getDoctrine()
            ->getRepository(WarriorCosts::class)
            ->findBy(['warriorType' => $warriorType]);

        $max = PHP_INT_MAX;
        foreach ($warriorCosts as $cost) {
            foreach ($stocks as $stock) {
                if ($cost->getStockType()->getId() === $stock->getType()->getId()) {
                    $curMax = $stock->getQuantity() / $cost->getRequiredAmount();
                    if ($curMax < $max) $max = $curMax;
                }
            }
        }
        return floor($max);
    }
}
