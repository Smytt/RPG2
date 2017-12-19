<?php
/**
 * Created by PhpStorm.
 * User: parus
 * Date: 19/12/2017
 * Time: 2:46 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;

class BattleService
{

    public function canMakeTrip(User $aggressor, $distance, $timeDistance)
    {
        $canMakeTrip = true;
        foreach ($aggressor->getPlanet()->getStocks() as $stock) {
            if ($stock->getQuantity() < ceil($stock->getType()->getCostPerBlockTravel() * 2 * $distance)
                || $stock->getQuantity() < ceil($stock->getType()->getCostPerYearTravel() * 2 * $timeDistance)) {
                $canMakeTrip = false;
            }
        }

        return $canMakeTrip;
    }
}