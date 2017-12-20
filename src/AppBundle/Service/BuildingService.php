<?php
/**
 * Created by PhpStorm.
 * User: parus
 * Date: 20/12/2017
 * Time: 11:23 AM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Building;
use AppBundle\Entity\BuildingCosts;
use Doctrine\ORM\EntityManager;

class BuildingService
{
    /**
     * @var EntityManager
     */
    private $em;

    private $db;
    private $pass;
    private $user;

    /**
     * BattleService constructor.
     * @param $db
     * @param $pass
     * @param $user
     * @param EntityManager $entityManager
     */
    public function __construct($db, $pass, $user, EntityManager $entityManager)
    {
        $this->db = $db;
        $this->pass = $pass;
        $this->user = $user;
        $this->em = $entityManager;
    }


    public function timeLeft(Building $building)
    {
        $now = new \DateTime();
        return $now->diff($building->getUpdateDue());
    }

    public function hasSufficientFunds(Building $building, $costs)
    {
        $hasSufficientFunds = true;
        foreach ($costs as $cost) {
            /**
             * @var $cost BuildingCosts
             */
            $currentCost = $cost->getRequiredAmount() * ($building->getLevel() + 1);
            foreach ($building->getPlanet()->getStocks() as $stock) {
                if ($stock->getType()->getType() == $cost->getStockType()->getType()) {
                    if ($stock->getQuantity() < $currentCost) {
                        $hasSufficientFunds = false;
                    }
                }
            }
        }
        return $hasSufficientFunds;
    }

    public function createBuildingUpgrade(Building $building, $costs)
    {
        /**
         * @var $cost BuildingCosts
         */
        $upgradeTime = $this->findUpgradeTime($building);

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
        $this->em->persist($building);
        $this->em->flush();
        $this->buildingUpgradeCron($building);
    }

    private function buildingUpgradeCron(Building $building)
    {
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

        $query = "mysql --user={$this->user} --password={$this->pass} {$this->db} < c:/Temp/{$fileSQL}";
        exec($query);
        unlink("c:/Temp/{$fileSQL}");
    }

    public function findUpgradeTime(Building $building)
    {
       return $building->getType()->getUpgradeTimePerLevel() * ($building->getLevel() + 1);
    }
}