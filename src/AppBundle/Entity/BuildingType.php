<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingType
 *
 * @ORM\Table(name="building_types")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuildingTypeRepository")
 */
class BuildingType
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="upgradeTimePerLevel", type="integer")
     */
    private $upgradeTimePerLevel;

    /**
     * @var BuildingCosts[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BuildingCosts", mappedBy="buildingType")
     */
    private $cost;

    /**
     * @var StockType;
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\StockType")
     * @ORM\JoinColumn(name="stock_type_id", referencedColumnName="id")
     */
    private $stockType;

    /**
     * @var int
     * @ORM\Column(name="income_per_minute", type="integer", nullable=true)
     */
    private $incomePerMinute;

    /**
     * @var int
     * @ORM\Column(name="start_with", type="integer")
     */
    private $startWith;

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getUpgradeTimePerLevel(): int
    {
        return $this->upgradeTimePerLevel;
    }

    /**
     * @param int $upgradeTimePerLevel
     */
    public function setUpgradeTimePerLevel(int $upgradeTimePerLevel): void
    {
        $this->upgradeTimePerLevel = $upgradeTimePerLevel;
    }

    /**
     * @return Collection|BuildingCosts[]
     */
    public function getCost(): Collection
    {
        return $this->cost;
    }

    /**
     * @param Collection|BuildingCosts[] $cost
     */
    public function setCost(Collection $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return StockType
     */
    public function getStockType(): StockType
    {
        return $this->stockType;
    }

    /**
     * @param StockType $stockType
     */
    public function setStockType(StockType $stockType): void
    {
        $this->stockType = $stockType;
    }

    /**
     * @return int
     */
    public function getIncomePerMinute(): int
    {
        return $this->incomePerMinute;
    }

    /**
     * @param int $incomePerMinute
     */
    public function setIncomePerMinute(int $incomePerMinute): void
    {
        $this->incomePerMinute = $incomePerMinute;
    }

    /**
     * @return int
     */
    public function getStartWith(): int
    {
        return $this->startWith;
    }

    /**
     * @param int $startWith
     */
    public function setStartWith(int $startWith): void
    {
        $this->startWith = $startWith;
    }


}

