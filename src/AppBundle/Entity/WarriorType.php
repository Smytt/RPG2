<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * WarriorType
 *
 * @ORM\Table(name="warrior_types")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WarriorTypeRepository")
 */
class WarriorType
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
     * @ORM\Column(name="attack", type="integer")
     */
    private $attack;

    /**
     * @var int
     * @ORM\Column(name="defencece", type="integer")
     */
    private $defence;

    /**
     * @var int
     * @ORM\Column(name="health", type="integer")
     */
    private $health;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="birthsPerMinute", type="integer")
     */
    private $birthsPerMinute;

    /**
     * @var Collection|WarriorRequirements[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\WarriorRequirements", mappedBy="warriorType")
     */
    private $requirements;

    /**
     * @var Collection|WarriorCosts[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\WarriorCosts", mappedBy="warriorType")
     */
    private $cost;


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
    public function getBirthsPerMinute(): int
    {
        return $this->birthsPerMinute;
    }

    /**
     * @param int $birthsPerMinute
     */
    public function setBirthsPerMinute(int $birthsPerMinute): void
    {
        $this->birthsPerMinute = $birthsPerMinute;
    }

    /**
     * @return Collection|WarriorRequirements[]
     */
    public function getRequirements(): Collection
    {
        return $this->requirements;
    }

    /**
     * @param Collection|WarriorRequirements[] $requirements
     */
    public function setRequirements(Collection $requirements): void
    {
        $this->requirements = $requirements;
    }

    /**
     * @return Collection|WarriorCosts[]
     */
    public function getCost(): Collection
    {
        return $this->cost;
    }

    /**
     * @param Collection|WarriorCosts[] $cost
     */
    public function setCost(Collection $cost): void
    {
        $this->cost = $cost;
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
     * @return int
     */
    public function getAttack(): int
    {
        return $this->attack;
    }

    /**
     * @param int $attack
     */
    public function setAttack(int $attack): void
    {
        $this->attack = $attack;
    }

    /**
     * @return int
     */
    public function getDefence(): int
    {
        return $this->defence;
    }

    /**
     * @param int $defence
     */
    public function setDefence(int $defence): void
    {
        $this->defence = $defence;
    }

    /**
     * @return int
     */
    public function getHealth(): int
    {
        return $this->health;
    }

    /**
     * @param int $health
     */
    public function setHealth(int $health): void
    {
        $this->health = $health;
    }



}

