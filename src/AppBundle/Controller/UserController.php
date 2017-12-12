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
use AppBundle\Form\UserType;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $this->generateNewPlanet($user);
            $user->getPlanet()->setName($form["planetName"]->getData());
            $user->getPlanet()->setUser($user);

            $this->fillDefaultWarriors($user);
            $this->fillDefaultBuildings($user);
            $this->fillDefaultStocks($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($user->getPlanet());

            foreach ($user->getPlanet()->getStocks() as $stock) {
                $em->persist($stock);
            }
            foreach ($user->getPlanet()->getWarriors() as $warrior) {
                $em->persist($warrior);
            }
            foreach ($user->getPlanet()->getBuildings() as $building) {
                $em->persist($building);
            }
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('game/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $utils)
    {
        return $this->render('security/login.html.twig', [
            'error' => $utils->getLastAuthenticationError(),
            'last_username' => $utils->getLastUsername()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

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
            $warrior = new Warrior($type, $user->getPlanet());
            $warriors = $user->getPlanet()->getWarriors();
            $warriors->add($warrior);
            $user->getPlanet()->setWarriors($warriors);
        }
    }

    private function fillDefaultBuildings(User &$user)
    {
        $buildingTypes = $this->getDoctrine()->getRepository(BuildingType::class)->findAll();
        foreach ($buildingTypes as $type) {
            $building = new Building($type, $user->getPlanet());
            $buildings = $user->getPlanet()->getBuildings();
            $buildings->add($building);
            $user->getPlanet()->setBuildings($buildings);
        }
    }

    private function fillDefaultStocks(User &$user)
    {
        $stockTypes = $this->getDoctrine()->getRepository(StockType::class)->findAll();
        foreach ($stockTypes as $type) {
            $stock = new Stock($type, $user->getPlanet());
            $stocks = $user->getPlanet()->getStocks();
            $stocks->add($stock);
            $user->getPlanet()->setStocks($stocks);
        }
    }
}
