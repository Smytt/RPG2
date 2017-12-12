<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Warrior
 *
 * @ORM\Table(name="warriors")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WarriorRepository")
 */
class Warrior
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
     * @var int
     *
     * @ORM\Column(name="inQueue", type="integer")
     */
    private $inQueue;

    /**
     * @var WarriorType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WarriorType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var Planet
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Planet", inversedBy="warriors")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    private $planet;

    /**
     * Warrior constructor.
     * @param WarriorType $type
     */
    public function __construct(WarriorType $type, Planet $planet)
    {
        $this->setPlanet($planet);
        $this->setType($type);
        $this->setQuantity($type->getStartWith());
        $this->setInQueue(0);
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
     * @return int
     */
    public function getInQueue(): int
    {
        return $this->inQueue;
    }

    /**
     * @param int $inQueue
     */
    public function setInQueue(int $inQueue): void
    {
        $this->inQueue = $inQueue;
    }

    /**
     * @return WarriorType
     */
    public function getType(): WarriorType
    {
        return $this->type;
    }

    /**
     * @param WarriorType $type
     */
    public function setType(WarriorType $type): void
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

