<?php

namespace AppBundle\Entity;

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


}

