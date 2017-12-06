<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingCosts
 *
 * @ORM\Table(name="building_costs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuildingCostsRepository")
 */
class BuildingCosts
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
     * @var BuildingType;
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BuildingType")
     * @ORM\JoinColumn(name="building_type_id", referencedColumnName="id")
     */
    private $buildingType;


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
     * @return Stock
     */
    public function getStock(): Stock
    {
        return $this->stockType;
    }

    /**
     * @param Stock $stock
     */
    public function setStock(Stock $stock): void
    {
        $this->stockType = $stock;
    }

    /**
     * @return BuildingType
     */
    public function getBuildingType(): BuildingType
    {
        return $this->buildingType;
    }

    /**
     * @param BuildingType $buildingType
     */
    public function setBuildingType(BuildingType $buildingType): void
    {
        $this->buildingType = $buildingType;
    }


}

