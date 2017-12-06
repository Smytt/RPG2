<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WarriorRequirements
 *
 * @ORM\Table(name="warrior_requirements")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WarriorRequirementsRepository")
 */
class WarriorRequirements
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
     * @ORM\Column(name="requiredLevel", type="integer")
     */
    private $requiredLevel;


    /**
     * @var BuildingType;
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BuildingType")
     * @ORM\JoinColumn(name="building_type_id", referencedColumnName="id")
     */
    private $buildingType;

    /**
     * @var WarriorType;
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WarriorType")
     * @ORM\JoinColumn(name="warrior_type_id", referencedColumnName="id")
     */
    private $warriorType;

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
    public function getRequiredLevel(): int
    {
        return $this->requiredLevel;
    }

    /**
     * @param int $requiredLevel
     */
    public function setRequiredLevel(int $requiredLevel): void
    {
        $this->requiredLevel = $requiredLevel;
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

}

