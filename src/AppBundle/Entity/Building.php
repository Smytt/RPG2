<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Building
 *
 * @ORM\Table(name="buildings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuildingRepository")
 */
class Building
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
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var bool
     *
     * @ORM\Column(name="isUpdating", type="boolean")
     */
    private $isUpdating;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updateDue", type="datetime", nullable=true)
     */
    private $updateDue;

    /**
     * @var BuildingType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BuildingType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var Planet
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Planet", inversedBy="buildings")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    private $planet;



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
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return bool
     */
    public function isUpdating(): bool
    {
        return $this->isUpdating;
    }

    /**
     * @param bool $isUpdating
     */
    public function setIsUpdating(bool $isUpdating): void
    {
        $this->isUpdating = $isUpdating;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDue(): \DateTime
    {
        return $this->updateDue;
    }

    /**
     * @param \DateTime $updateDue
     */
    public function setUpdateDue(\DateTime $updateDue): void
    {
        $this->updateDue = $updateDue;
    }

    /**
     * @return BuildingType
     */
    public function getType(): BuildingType
    {
        return $this->type;
    }

    /**
     * @param BuildingType $type
     */
    public function setType(BuildingType $type): void
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

