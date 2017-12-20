<?php
/**
 * Created by PhpStorm.
 * User: parus
 * Date: 20/12/2017
 * Time: 10:13 AM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Building;
use AppBundle\Entity\Planet;
use AppBundle\Entity\Stock;
use AppBundle\Entity\User;
use AppBundle\Entity\Warrior;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * UserService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createUser(
        User $user,
        FormInterface $form,
        UserPasswordEncoderInterface $passwordEncoder,
        $warriorTypes,
        $buildingTypes,
        $stockTypes,
        $planets
    )
    {
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $this->generateNewPlanet($user, $planets);
        $user->getPlanet()->setName($form["planetName"]->getData());
        $user->getPlanet()->setUser($user);

        $this->fillDefaultWarriors($user, $warriorTypes);
        $this->fillDefaultBuildings($user, $buildingTypes);
        $this->fillDefaultStocks($user, $stockTypes);

        $this->em->persist($user);
        $this->em->persist($user->getPlanet());

        foreach ($user->getPlanet()->getStocks() as $stock) {
            $this->em->persist($stock);
        }
        foreach ($user->getPlanet()->getWarriors() as $warrior) {
            $this->em->persist($warrior);
        }
        foreach ($user->getPlanet()->getBuildings() as $building) {
            $this->em->persist($building);
        }
        $this->em->flush();
    }

    private function generateNewPlanet(User &$user, $planets)
    {
        do {
            $isOccupied = false;
            $newX = mt_rand(0, 10000);
            $newY = mt_rand(0, 10000);
            $newTime = mt_rand(0, 10000);
            foreach ($planets as $planet) {
                /**
                 * @var $planet Planet
                 */
                if (
                    $newX === $planet->getCoordinateX() &&
                    $newY === $planet->getCoordinateY() &&
                    $newTime === $planet->getCoordinateTime()
                ) $isOccupied = true;
            }
        } while ($isOccupied === true);

        $user->getPlanet()->setCoordinateX($newX);
        $user->getPlanet()->setCoordinateY($newY);
        $user->getPlanet()->setCoordinateTime($newTime);
    }

    private function fillDefaultWarriors(User &$user, $warriorTypes)
    {
        foreach ($warriorTypes as $type) {
            $warrior = new Warrior($type, $user->getPlanet());
            $warriors = $user->getPlanet()->getWarriors();
            $warriors->add($warrior);
            $user->getPlanet()->setWarriors($warriors);
        }
    }

    private function fillDefaultBuildings(User &$user, $buildingTypes)
    {
        foreach ($buildingTypes as $type) {
            $building = new Building($type, $user->getPlanet());
            $buildings = $user->getPlanet()->getBuildings();
            $buildings->add($building);
            $user->getPlanet()->setBuildings($buildings);
        }
    }

    private function fillDefaultStocks(User &$user, $stockTypes)
    {
        foreach ($stockTypes as $type) {
            $stock = new Stock($type, $user->getPlanet());
            $stocks = $user->getPlanet()->getStocks();
            $stocks->add($stock);
            $user->getPlanet()->setStocks($stocks);
        }
    }
}