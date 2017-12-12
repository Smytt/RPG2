<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use AppBundle\Entity\BuildingCosts;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class PlayerController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard()
    {
        if (!$this->getUser()) {
            return $this->render('home.html.twig');
        }

        /**
         * @var User
         */
        $test = $this->getUser();

        $user = $this->getDoctrine()->getRepository(User::class)->find(4);

        return $this->render('player/dashboard.html.twig', ['user' => $test]);
    }
}
