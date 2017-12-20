<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use AppBundle\Entity\BuildingCosts;
use AppBundle\Entity\User;
use AppBundle\Service\BuildingService;
use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class BuildingsController extends Controller
{

    /**
     * @Route("/upgrade/{id}/{confirm}", name="building_upgrade", requirements={"id": "\d+", "confirm": "confirm"})
     * @param int $id
     * @param string $confirm
     * @param BuildingService $buildingService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function upgradeBuildingReview(int $id, string $confirm = null, BuildingService $buildingService)
    {
        /**
         * @var $building Building
         */

        $building = $this->getDoctrine()->getManager()
            ->getRepository(Building::class)->find($id);
        $costs = $this->getDoctrine()->getRepository(BuildingCosts::class)
            ->findBy(['buildingType' => $building->getType()->getId()]);

        if ($building->getPlanet()->getUser()->getId() !== $this->getUser()->getId()) {
            $message = 'This building is not yours.';
            return $this->render('system/generalError.html.twig', ['message' => $message]);
        }

        if ($building->isUpdating()) {
            return $this->render('building/upgradeConfirm.html.twig',
                ['building' => $building, 'timeLeft' => $buildingService->timeLeft($building)]);
        }

        if ($confirm == 'confirm' && !$building->isUpdating()) {
            if ($buildingService->hasSufficientFunds($building, $costs)) {
                $buildingService->createBuildingUpgrade($building, $costs);
            }
            return $this->redirectToRoute('building_upgrade', ['id' => $building->getId()]);
        }

        return $this->render('building/upgrade.html.twig', [
            'building' => $building,
            'upgradeTime' => $buildingService->findUpgradeTime($building),
            'hasSufficientFunds' => $buildingService->hasSufficientFunds($building, $costs)]);
    }

//
//    private function buildingUpgradeCron123(Building $building)
//    {
//        //open template sql, add id, save as tempfile
//        $templateSQLContent = file_get_contents('c:/Temp/template/buildingUpgrade.sql');
//        $fileSQL = 'building' . $building->getId() . '.sql';
//        $generatedSQLFile = strtr($templateSQLContent, [
//            '{$id}' => $building->getId()
//        ]);
//        $tempFile = fopen("c:/Temp/" . $fileSQL, 'w');
//        fwrite($tempFile, $generatedSQLFile);
//        fclose($tempFile);
//
//        //open template xml, replace nodes and save as tempfile
//        $user = $this->container->getParameter('database_user');
//        $pass = $this->container->getParameter('database_password');
//        $db = $this->container->getParameter('database_name');
//        $dueDate = $building->getUpdateDue()->format('Y-m-d');
//        $dueTime = $building->getUpdateDue()->format('H:i:s');
//        $delTime = $building->getUpdateDue()->add(new \DateInterval("P0DT1S"))->format('H:i:s');
//        $fullPath = "c:/Temp/{$fileSQL}";
//        $templateXMLContent = simplexml_load_file('c:/Temp/template/buildingUpgrade.xml');
//        $templateXMLContent->Actions->Exec->Arguments = "/C mysql --user={$user} --password={$pass} {$db} < {$fullPath}";
//        $templateXMLContent->Triggers->TimeTrigger->StartBoundary = "{$dueDate}T{$dueTime}";
//        $templateXMLContent->asXML("c:/Temp/building{$building->getId()}.xml");
//
//        //self delete task
//        $templateXMLContent = simplexml_load_file('c:/Temp/template/buildingUpgrade.xml');
//        $templateXMLContent->Actions->Exec->Arguments = "/C for /f %x in ('schtasks /query ^| findstr building{$building->getId()}task') do schtasks /Delete /TN %x /F";
//        $templateXMLContent->Triggers->TimeTrigger->StartBoundary = "{$dueDate}T{$delTime}";
//        $templateXMLContent->asXML("c:/Temp/building{$building->getId()}Delete.xml");
//
//        //execute command to create actual scheduled task
//        shell_exec("schtasks /create /xml c:/Temp/building{$building->getId()}.xml /tn building{$building->getId()}task /f");
//        shell_exec("schtasks /create /xml c:/Temp/building{$building->getId()}Delete.xml /tn building{$building->getId()}taskDelete /f");
//    }
}
