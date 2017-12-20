<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Battle;
use AppBundle\Entity\User;
use AppBundle\Entity\WarriorType;
use AppBundle\Service\BattleService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BattleController extends Controller
{

    /**
     * @Route("/attack/{username}", name="attack_user")
     * @param Request $request
     * @param string $username
     * @param BattleService $battleService
     * @return Response
     */
    public function attackUserAction(Request $request, $username, BattleService $battleService)
    {
        /**
         * @var User
         */
        $aggressor = $this->getUser();
        $victim = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        $warriorTypes = $this->getDoctrine()
            ->getRepository(WarriorType::class)
            ->findAll();

        if (!$victim === null) {
            $message = "The user you are trying to attack doesn't exist";
            return $this->render('system/generalError.html.twig', ['message' => $message]);
        }

        if ($victim->getId() === $aggressor->getId()) {
            $message = "Don't attack yourself...";
            return $this->render('system/generalError.html.twig', ['message' => $message]);
        }

        $chosenWarriors = [];
        $form = $this->createFormBuilder($chosenWarriors);
        $form = $battleService->createFormChooseWarriors($aggressor, $warriorTypes, $form);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()
            && $battleService->canMakeTrip($aggressor, $victim)) {
            $battle = $battleService->makeBattle($aggressor, $victim, $form);
            return $this->redirectToRoute('battle_resume', ['id' => $battle->getId()]);
        }

        return $this->render('battle/attack.html.twig', [
            'canMakeTrip' => $battleService->canMakeTrip($aggressor, $victim),
            'victim' => $victim,
            'aggressor' => $aggressor,
            'distance' => $battleService->findDistance($aggressor, $victim),
            'timeDistance' => $battleService->findTimeDistance($aggressor, $victim),
            'time' => $battleService->findTimeInEachDirection($aggressor, $victim),
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/battle/{id}", name="battle_resume", requirements={"id": "\d+"})
     * @param int $id
     * @return Response
     */
    public function battleResumeAction(int $id)
    {
        $user = $this->getUser();

        $battles = $this->getDoctrine()->getRepository(Battle::class)->findBy(['id' => $id]);
        $battle = $battles[0];
        if ($battle->getAggressor() !== $user && $battle->getVictim() != $user) {
            $message = "This battle is not yours.";
            return $this->render('system/generalError.html.twig', ['message' => $message]);
        }
        $now = new \DateTime();
        $timeLeft = $now->diff($battle->getBattleHappen());
        $timeToReturn = $now->diff($battle->getReturnDue());
        return $this->render('battle/battle.html.twig', [
            'battle' => $battle,
            'timeLeft' => $timeLeft,
            'timeToReturn' => $timeToReturn
        ]);
    }


    /**
     * @Route("/battles", name="my_battles")
     */
    public function viewAllBattlesAction()
    {
        $battlesAsVictim = $this->getDoctrine()->getRepository(Battle::class)
            ->findBy(['victim' => $this->getUser()]);
        $battlesAsAggressor = $this->getDoctrine()->getRepository(Battle::class)
            ->findBy(['aggressor' => $this->getUser()]);

        return $this->render('battle/battles.html.twig',
            [
                'battlesAsVictim' => $battlesAsVictim,
                'battlesAsAggressor' => $battlesAsAggressor
            ]);
    }
}
