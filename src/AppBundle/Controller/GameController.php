<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends Controller
{
    /**
     * @Route("/allusers", name="all_users")
     */
    public function showAllUsers()
    {
        $allUsers = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('game/viewAll.html.twig', ['users' => $allUsers]);
    }


    /**
     * @Route("/profile/{username}", name="user_profile")
     */
    public function showOneUser(string $username)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
        return $this->render('game/viewOne.html.twig', ['user' => $user]);
    }
}
