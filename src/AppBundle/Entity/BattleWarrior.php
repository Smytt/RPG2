<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BattleWarrior
 *
 * @ORM\Table(name="battle_warrior")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattleWarriorRepository")
 */
class BattleWarrior
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
     * @var Battle
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Battle")
     * @ORM\JoinColumn(name="battle_id", referencedColumnName="id")
     */
    private $battle;

    /**
     * @var WarriorType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\WarriorType")
     * @ORM\JoinColumn(name="warrior_type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * BattleWarrior constructor.
     * @param int $quantity
     * @param Battle $battle
     * @param WarriorType $type
     * @param User $owner
     */

    public function __construct(int $quantity, Battle $battle, User $owner)
    {
        $this->quantity = $quantity;
        $this->battle = $battle;
        $this->owner = $owner;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return BattleWarrior
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return Battle
     */
    public function getBattle(): Battle
    {
        return $this->battle;
    }

    /**
     * @param Battle $battle
     */
    public function setBattle(Battle $battle): void
    {
        $this->battle = $battle;
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
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }


}
