<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(name="name", type="string", length=255)
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
     * @var Collection|Building[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Building", mappedBy="planet")
     */
    private $buildings;

    /**
     * @var Collection|Stock[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Stock", mappedBy="planet")
     */
    private $stocks;

    /**
     * @var Collection|Warrior[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Warrior", mappedBy="planet")
     */
    private $warriors;

    /**
     * Planet constructor.
     */
    public function __construct()
    {
        $this->setBuildings(new ArrayCollection());
        $this->setStocks(new ArrayCollection());
        $this->setWarriors(new ArrayCollection());
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
     * @return Collection|Building[]
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    /**
     * @param Collection|Building[] $buildings
     */
    public function setBuildings($buildings): void
    {
        $this->buildings = $buildings;
    }

    /**
     * @return Collection|Stock[]
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * @param Collection|Stock[] $stocks
     */
    public function setStocks($stocks): void
    {
        $this->stocks = $stocks;
    }

    /**
     * @return Collection|Warrior[]
     */
    public function getWarriors(): Collection
    {
        return $this->warriors;
    }

    /**
     * @param Collection|Warrior[] $warriors
     */
    public function setWarriors($warriors): void
    {
        $this->warriors = $warriors;
    }


}

