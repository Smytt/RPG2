<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Planet
 *
 * @ORM\Table(name="planets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlanetRepository")
 */
class Planet
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="coordinateX", type="integer")
     */
    private $coordinateX;

    /**
     * @var int
     *
     * @ORM\Column(name="coordinateY", type="integer")
     */
    private $coordinateY;

    /**
     * @var int
     *
     * @ORM\Column(name="coordinateTime", type="integer")
     */
    private $coordinateTime;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="planet")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Building[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Building", mappedBy="planet")
     */
    private $buildings;

    /**
     * @var Stock[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Stock", mappedBy="planet")
     */
    private $stocks;

    /**
     * @var Warrior[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Warrior", mappedBy="planet")
     */
    private $warriors;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCoordinateX(): int
    {
        return $this->coordinateX;
    }

    /**
     * @param int $coordinateX
     */
    public function setCoordinateX(int $coordinateX): void
    {
        $this->coordinateX = $coordinateX;
    }

    /**
     * @return int
     */
    public function getCoordinateY(): int
    {
        return $this->coordinateY;
    }

    /**
     * @param int $coordinateY
     */
    public function setCoordinateY(int $coordinateY): void
    {
        $this->coordinateY = $coordinateY;
    }

    /**
     * @return int
     */
    public function getCoordinateTime(): int
    {
        return $this->coordinateTime;
    }

    /**
     * @param int $coordinateTime
     */
    public function setCoordinateTime(int $coordinateTime): void
    {
        $this->coordinateTime = $coordinateTime;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Building[]
     */
    public function getBuildings(): array
    {
        return $this->buildings;
    }

    /**
     * @param Building[] $buildings
     */
    public function setBuildings(array $buildings): void
    {
        $this->buildings = $buildings;
    }

    /**
     * @return Stock[]
     */
    public function getStocks(): array
    {
        return $this->stocks;
    }

    /**
     * @param Stock[] $stocks
     */
    public function setStocks(array $stocks): void
    {
        $this->stocks = $stocks;
    }

    /**
     * @return Warrior[]
     */
    public function getWarriors(): array
    {
        return $this->warriors;
    }

    /**
     * @param Warrior[] $warriors
     */
    public function setWarriors(array $warriors): void
    {
        $this->warriors = $warriors;
    }


}

