<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stocks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StockRepository")
 */
class Stock
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
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var StockType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\StockType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;


    /**
     * @var Planet
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Planet", inversedBy="stocks")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    private $planet;

    /**
     * Stock constructor.
     * @param StockType $type
     */
    public function __construct(StockType $type, Planet $planet)
    {
        $this->setPlanet($planet);
        $this->setType($type);
        $this->setQuantity($type->getStartWith());
    }


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
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return StockType
     */
    public function getType(): StockType
    {
        return $this->type;
    }

    /**
     * @param StockType $type
     */
    public function setType(StockType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Planet
     */
    public function getPlanet(): Planet
    {
        return $this->planet;
    }

    /**
     * @param Planet $planet
     */
    public function setPlanet(Planet $planet): void
    {
        $this->planet = $planet;
    }


}

