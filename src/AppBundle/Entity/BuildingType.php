<?php

namespace AppBundle\Entity;

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

}

