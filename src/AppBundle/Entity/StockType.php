<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StockType
 *
 * @ORM\Table(name="stock_types")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StockTypeRepository")
 */
class StockType
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
     * @ORM\Column(name="start_with", type="integer")
     */
    private $startWith;

    /**
     * @var float
     * @ORM\Column(name="cost_per_block_travel", type="float")
     */
    private $costPerBlockTravel;

    /**
     * @var float
     * @ORM\Column(name="cost_per_year_travel", type="float")
     */
    private $costPerYearTravel;


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

    /**
     * @return float
     */
    public function getCostPerBlockTravel(): float
    {
        return $this->costPerBlockTravel;
    }

    /**
     * @param float $costPerBlockTravel
     */
    public function setCostPerBlockTravel(float $costPerBlockTravel): void
    {
        $this->costPerBlockTravel = $costPerBlockTravel;
    }

    /**
     * @return float
     */
    public function getCostPerYearTravel(): float
    {
        return $this->costPerYearTravel;
    }

    /**
     * @param float $costPerYearTravel
     */
    public function setCostPerYearTravel(float $costPerYearTravel): void
    {
        $this->costPerYearTravel = $costPerYearTravel;
    }


}

