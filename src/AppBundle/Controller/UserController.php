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
use AppBundle\Service\UserService;
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
     * @param Request $request
     * @param UserService $userService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserService $userService, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $warriorTypes = $this->getDoctrine()->getRepository(WarriorType::class)->findAll();
        $buildingTypes = $this->getDoctrine()->getRepository(BuildingType::class)->findAll();
        $stockTypes = $this->getDoctrine()->getRepository(StockType::class)->findAll();
        $planets = $this->getDoctrine()->getRepository(Planet::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->createUser(
                $user,
                $form,
                $passwordEncoder,
                $warriorTypes,
                $buildingTypes,
                $stockTypes,
                $planets
            );
            return $this->redirectToRoute('login', ['message' => 'Great! Now sign in.']);
        }

        return $this->render('system/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $utils
     * @param $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $utils, $message = null)
    {
        return $this->render('system/login.html.twig', [
            'error' => $utils->getLastAuthenticationError(),
            'last_username' => $utils->getLastUsername(),
            'message' => $message
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }
}
