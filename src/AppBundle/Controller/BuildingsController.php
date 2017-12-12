<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Building;
use AppBundle\Entity\BuildingCosts;
use AppBundle\Entity\User;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function upgradeBuildingReview(int $id, string $confirm = null)
    {
        $em = $this->getDoctrine()->getManager();

        $buildings = $this->getDoctrine()->getManager()
            ->getRepository(Building::class)->findBy(['id' => $id]);
        $building = $buildings[0];

        if ($building->isUpdating()) {
            return $this->render('player/upgradeConfirm.html.twig',
                ['building' => $building, 'timeLeft' => $this->timeLeft($building)]);
        }

        $upgradeTime = $building->getType()->getUpgradeTimePerLevel() * ($building->getLevel() + 1);
        $costs = $this->getDoctrine()->getRepository(BuildingCosts::class)
            ->findBy(['buildingType' => $building->getType()->getId()]);
        $myStocks = [];
        $hasSufficientFunds = true;
        foreach ($costs as $cost) {
            $currentCost = $cost->getRequiredAmount() * ($building->getLevel() + 1);
            foreach ($building->getPlanet()->getStocks() as $stock) {
                if ($stock->getType()->getType() == $cost->getStockType()->getType()) {
                    $myStocks[] = $stock;
                    if ($stock->getQuantity() < $currentCost) {
                        $hasSufficientFunds = false;
                    }
                }
            }
        }

        if ($confirm == 'confirm' && !$building->isUpdating()) {
            if ($hasSufficientFunds = true) {
                $now = new \DateTime();
                $building->setIsUpdating(true);
                $building->setUpdateDue($now->add(new \DateInterval("P0DT{$upgradeTime}S")));
                foreach ($costs as $cost) {
                    foreach ($building->getPlanet()->getStocks() as $stock) {
                        if ($stock->getType()->getType() == $cost->getStockType()->getType()) {
                            $newQuantity = $stock->getQuantity() - $cost->getRequiredAmount();
                            $stock->setQuantity($newQuantity);
                        }
                    }
                }
                $em->persist($building);
                $em->flush();
                $this->buildingUpgradeCron($building);
            }
            return $this->redirectToRoute('building_upgrade', ['id' => $building->getId()]);
        }

        return $this->render('player/upgrade.html.twig', [
            'building' => $building,
            'myStocks' => $myStocks,
            'upgradeTime' => $upgradeTime,
            'hasSufficientFunds' => $hasSufficientFunds]);
    }

    private function timeLeft(Building $building)
    {
        $now = new \DateTime();
        return $now->diff($building->getUpdateDue());
    }

    private function buildingUpgradeCron(Building $building)
    {
        $user = $this->container->getParameter('database_user');
        $pass = $this->container->getParameter('database_password');
        $db = $this->container->getParameter('database_name');

        $templateSQLContent = file_get_contents('c:/Temp/template/buildingUpgrade.sql');
        $fileSQL = 'building' . $building->getId() . '.sql';
        $generatedSQLFile = strtr($templateSQLContent, [
            '{id}' => $building->getId(),
            '{eventName}' => 'building' . $building->getId(),
            '{upgradeTime}' => $building->getType()->getUpgradeTimePerLevel() * ($building->getLevel() + 1)
        ]);
        $tempFile = fopen("c:/Temp/" . $fileSQL, 'w');
        fwrite($tempFile, $generatedSQLFile);
        fclose($tempFile);

        $query = "mysql --user={$user} --password={$pass} {$db} < c:/Temp/{$fileSQL}";
        exec($query);
        unlink("c:/Temp/{$fileSQL}");
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

    /**
     * @Route("/123/{id}", name="123")
     */
    public function dashboard($id)
    {
        $buildings = $this->getDoctrine()->getManager()
            ->getRepository(Building::class)->findBy(['id' => $id]);
        $building = $buildings[0];

        $this->buildingUpgradeCron($building);
        return $this->render('player/dashboard.html.twig', ['building' => $building]);
    }
}
