<?php
/**
 * Created by PhpStorm.
 * User: parus
 * Date: 19/12/2017
 * Time: 2:46 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Battle;
use AppBundle\Entity\BattleWarrior;
use AppBundle\Entity\User;
use AppBundle\Entity\WarriorType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Range;

class BattleService
{
    /**
     * @var EntityManager
     */
    private $em;

    private $db;
    private $pass;
    private $user;


    const TIME_PER_BLOCK = 0.01;
    const TIME_PER_YEAR = 0.05;

    /**
     * BattleService constructor.
     * @param $db
     * @param $pass
     * @param $user
     * @param EntityManager $entityManager
     */
    public function __construct($db, $pass, $user, EntityManager $entityManager)
    {
        $this->db = $db;
        $this->pass = $pass;
        $this->user = $user;
        $this->em = $entityManager;
    }

    public function makeBattle(User $aggressor, User $victim, FormInterface $form)
    {
        $distance = $this->findDistance($aggressor, $victim);
        $timeDistance = $this->findTimeDistance($aggressor, $victim);
        $timeInEachDirection = $this->findTimeInEachDirection($aggressor, $victim);

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
                    $this->em->persist($battleWarriorBatch);
                }
            }
        }

        $this->em->persist($battle);
        $this->em->persist($aggressor);

        $this->em->flush();

        $this->battleCron($battle);
        return $battle;
    }

    public function canMakeTrip(User $aggressor, User $victim)
    {
        $canMakeTrip = true;
        $distance = $this->findDistance($aggressor, $victim);
        $timeDistance = $this->findTimeDistance($aggressor, $victim);
        foreach ($aggressor->getPlanet()->getStocks() as $stock) {
            if ($stock->getQuantity() < ceil($stock->getType()->getCostPerBlockTravel() * 2 * $distance)
                || $stock->getQuantity() < ceil($stock->getType()->getCostPerYearTravel() * 2 * $timeDistance)) {
                $canMakeTrip = false;
            }
        }

        return $canMakeTrip;
    }

    public function findDistance(User $aggressor, User $victim)
    {
        $x1 = $victim->getPlanet()->getCoordinateX();
        $x2 = $aggressor->getPlanet()->getCoordinateX();
        $y1 = $victim->getPlanet()->getCoordinateY();
        $y2 = $aggressor->getPlanet()->getCoordinateY();
        return floor(sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2)));
    }

    public function findTimeDistance(User $aggressor, User $victim)
    {
        return abs($victim->getPlanet()->getCoordinateTime()
            - $aggressor->getPlanet()->getCoordinateTime());
    }

    public function findTimeInEachDirection($aggressor, $victim)
    {
        $distance = $this->findDistance($aggressor, $victim);
        $timeDistance = $this->findTimeDistance($aggressor, $victim);

        return ceil($distance * self::TIME_PER_BLOCK + $timeDistance * self::TIME_PER_YEAR);
    }

    private function battleCron(Battle $battle)
    {
        $timeInEachDirection = $this->findTimeInEachDirection($battle->getAggressor(), $battle->getVictim());

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

        $query = "mysql --user={$this->user} --password={$this->pass} {$this->db} < c:/Temp/{$fileSQL}";
        exec($query);
//        unlink("c:/Temp/{$fileSQL}");
    }

    public function createFormChooseWarriors(User $aggressor, $warriorTypes, FormBuilderInterface $form)
    {
        /**
         * @var $type WarriorType
         */
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
}