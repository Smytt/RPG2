<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends Controller
{
    /**
     * @Route("/players", name="all_users")
     */
    public function showAllUsersAction()
    {
        $allUsers = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('game/viewAll.html.twig', ['users' => $allUsers]);
    }


    /**
     * @Route("/player/{username}", name="user_profile")
     * @param string $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showOneUserAction(string $username)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
        return $this->render('game/viewOne.html.twig', ['user' => $user]);
    }
}
