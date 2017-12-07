<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use AppBundle\Entity\BuildingType;
use AppBundle\Entity\Planet;
use AppBundle\Entity\Stock;
use AppBundle\Entity\StockType;
use AppBundle\Entity\User;
use AppBundle\Entity\Warrior;
use AppBundle\Entity\WarriorType;
use AppBundle\Form\RegisterType;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/register", name="regiser")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordHash);
            $this->generateNewPlanet($user);
            $user->getPlanet()->setName($form["planetName"]->getData());

            $this->fillDefaultWarriors($user);
            $this->fillDefaultBuildings($user);
            $this->fillDefaultStocks($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('all_users');
        }

        return $this->render('game/register.html.twig', ['form' => $form->createView()]);
    }

    private function generateNewPlanet(User &$user)
    {
        do {
            $newX = mt_rand(0, 10000);
            $newY = mt_rand(0, 10000);
            $newTime = mt_rand(0, 10000);
            $isOccupied = $this
                ->getDoctrine()
                ->getRepository(Planet::class)
                ->findOneBy([
                    'coordinateX' => $newX,
                    'coordinateY' => $newY,
                    'coordinateTime' => $newTime,
                ]);
        } while ($isOccupied !== null);

        $user->getPlanet()->setCoordinateX($newX);
        $user->getPlanet()->setCoordinateY($newY);
        $user->getPlanet()->setCoordinateTime($newTime);
    }

    private function fillDefaultWarriors(User &$user)
    {
        $warriorTypes = $this->getDoctrine()->getRepository(WarriorType::class)->findAll();
        foreach ($warriorTypes as $type) {
            $warrior = new Warrior($type);
            $warriors = $user->getPlanet()->getWarriors();
            $warriors->add($warrior);
            $user->getPlanet()->setWarriors($warriors);
        }
    }

    private function fillDefaultBuildings(User &$user)
    {
        $buildingTypes = $this->getDoctrine()->getRepository(BuildingType::class)->findAll();
        foreach ($buildingTypes as $type) {
            $building = new Building($type);
            $buildings = $user->getPlanet()->getBuildings();
            $buildings->add($building);
            $user->getPlanet()->setBuildings($buildings);
        }
    }

    private function fillDefaultStocks(User &$user)
    {
        $stockTypes = $this->getDoctrine()->getRepository(StockType::class)->findAll();
        foreach ($stockTypes as $type) {
            $stock = new Stock($type);
            $stocks = $user->getPlanet()->getStocks();
            $stocks->add($stock);
            $user->getPlanet()->setStocks($stocks);
        }
    }
}
