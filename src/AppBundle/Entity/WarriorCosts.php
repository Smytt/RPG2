<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarriorCosts
 *
 * @ORM\Table(name="warrior_costs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WarriorCostsRepository")
 */
class WarriorCosts
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="requiredAmount", type="integer")
     */
    private $requiredAmount;

    /**
     * @var WarriorType;
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WarriorType")
     * @ORM\JoinColumn(name="warrior_type_id", referencedColumnName="id")
     */
    private $warriorType;

    /**
     * @var Stock;
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\StockType")
     * @ORM\JoinColumn(name="stock_type_id", referencedColumnName="id")
     */
    private $stockType;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getRequiredAmount(): int
    {
        return $this->requiredAmount;
    }

    /**
     * @param int $requiredAmount
     */
    public function setRequiredAmount(int $requiredAmount): void
    {
        $this->requiredAmount = $requiredAmount;
    }

    /**
     * @return WarriorType
     */
    public function getWarriorType(): WarriorType
    {
        return $this->warriorType;
    }

    /**
     * @param WarriorType $warriorType
     */
    public function setWarriorType(WarriorType $warriorType): void
    {
        $this->warriorType = $warriorType;
    }

    /**
     * @return Stock
     */
    public function getStockType(): Stock
    {
        return $this->stockType;
    }

    /**
     * @param Stock $stockType
     */
    public function setStockType(Stock $stockType): void
    {
        $this->stockType = $stockType;
    }
}

