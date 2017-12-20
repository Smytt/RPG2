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
use AppBundle\Service\WarriorService;
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

    /**
     * @Route("/purchase/", name="purchase_warriors")
     * @param Request $request
     * @param WarriorService $warriorService
     * @return Response
     */
    public function purchaseWarriorsAction(Request $request, WarriorService $warriorService)
    {
        $chosenWarriors = [];
        $form = $this->createFormBuilder($chosenWarriors);
        $form = $warriorService->createFormPurchaseWarriors($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $warriorType = $this->getDoctrine()
                ->getRepository(WarriorType::class)
                ->find($form->getData()['warriorType']);
            $stocks = $this->getUser()->getPlanet()->getStocks();
            $warriorCosts = $this->getDoctrine()
                ->getRepository(WarriorCosts::class)
                ->findBy(['warriorType' => $warriorType]);
            $buildings = $this->getUser()->getPlanet()->getBuildings();
            $warriorRequirements = $this->getDoctrine()
                ->getRepository(WarriorRequirements::class)
                ->findBy(['warriorType' => $warriorType]);
            $warrior = $this->getDoctrine()->getRepository(Warrior::class)
                ->findOneBy([
                    'type' => $form->getData()['warriorType'],
                    'planet' => $this->getUser()->getPlanet()
                ]);

            $canPurchase = $warriorService->canPurchase($stocks, $warriorCosts, intval($form->getData()['quantity']));
            $requirementsMet = $warriorService->requirementsMet($buildings, $warriorRequirements);

            if (!$requirementsMet) return $this->render('player/purchase.html.twig', [
                'form' => $form->createView(),
                'data' => $form->getData(),
                'formSubmitted' => $form->isSubmitted() && $form->isValid(),
                'requirementsMet' => 0
            ]);
            if (!$canPurchase) return $this->render('player/purchase.html.twig', [
                'form' => $form->createView(),
                'data' => $form->getData(),
                'max' => $warriorService->getMax($stocks, $warriorCosts),
                'formSubmitted' => $form->isSubmitted() && $form->isValid(),
                'canPurchase' => 0
            ]);

            $warriorService->makePurchase($warrior, $stocks, intval($form->getData()['quantity']));

        }
        return $this->render('player/purchase.html.twig', [
            'form' => $form->createView(),
            'data' => $form->getData(),
            'formSubmitted' => $form->isSubmitted() && $form->isValid()
        ]);
    }
}
