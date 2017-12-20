<?php
/**
 * Created by PhpStorm.
 * User: parus
 * Date: 19/12/2017
 * Time: 9:58 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Building;
use AppBundle\Entity\Stock;
use AppBundle\Entity\User;
use AppBundle\Entity\Warrior;
use AppBundle\Entity\WarriorCosts;
use AppBundle\Entity\WarriorRequirements;
use AppBundle\Entity\WarriorType;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class WarriorService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * WarriorService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function createFormPurchaseWarriors(FormBuilderInterface $form)
    {
        $form
            ->add('quantity', NumberType::class)
            ->add('warriorType', EntityType::class, [
                'class' => WarriorType::class,
                'choice_label' => 'type'
            ])
            ->add('save', SubmitType::class, ['label' => 'Purchase']);
        return $form->getForm();
    }

    public function canPurchase($stocks, $warriorCosts, $desiredQuantity)
    {
        /**
         * @var $cost WarriorCosts
         * @var $stock Stock
         */
        foreach ($warriorCosts as $cost) {
            foreach ($stocks as $stock) {
                if ($cost->getStockType()->getId() === $stock->getType()->getId()) {
                    if ($cost->getRequiredAmount() * $desiredQuantity > $stock->getQuantity()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function requirementsMet($buildings, $warriorRequirements)
    {
        /**
         * @var $requirement WarriorRequirements
         * @var $building Building
         */
        foreach ($warriorRequirements as $requirement) {
            foreach ($buildings as $building) {
                if ($requirement->getBuildingType()->getId() === $building->getType()->getId()) {
                    if ($requirement->getRequiredLevel() > $building->getLevel()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function makePurchase(Warrior $warrior, $stocks, $quantity)
    {
        /**
         * @var $stock Stock
         */
        foreach ($stocks as $stock) {
            foreach ($warrior->getType()->getCost() as $cost) {
                if ($stock->getType()->getId() === $cost->getStockType()->getId()) {
                    $stock->setQuantity($stock->getQuantity() - $cost->getrequiredAmount() * $quantity);
                    $this->em->persist($stock);
                }
            }
        }
        $warrior->setInQueue($warrior->getInQueue() + $quantity);
        $this->em->persist($warrior);
        $this->em->flush();
    }

    public function getMax($stocks, $warriorCosts)
    {
        /**
         * @var $cost WarriorCosts
         * @var $stock Stock
         */
        $max = PHP_INT_MAX;
        foreach ($warriorCosts as $cost) {
            foreach ($stocks as $stock) {
                if ($cost->getStockType()->getId() === $stock->getType()->getId()) {
                    $curMax = $stock->getQuantity() / $cost->getRequiredAmount();
                    if ($curMax < $max) $max = $curMax;
                }
            }
        }
        return floor($max);
    }
}