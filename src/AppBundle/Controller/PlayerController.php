<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
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
    public function dashboardAction()
    {
        if (!$this->getUser()) {
            return $this->render('home.html.twig');
        }

        return $this->render('player/dashboard.html.twig');
    }
}
