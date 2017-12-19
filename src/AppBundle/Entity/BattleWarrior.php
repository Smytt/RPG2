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
     * @var int
     *
     * @ORM\Column(name="aggressorStartedWith", type="integer")
     */
    private $aggressorStartedWith;


    /**
     * @var int
     *
     * @ORM\Column(name="aggressorLostInBattle", type="integer")
     */
    private $aggressorLostInBattle;

    /**
     * @var int
     *
     * @ORM\Column(name="aggressorEndedWith", type="integer")
     */
    private $aggressorEndedWith;

    /**
     * @var int
     *
     * @ORM\Column(name="victimStartedWith", type="integer")
     */
    private $victimSartedWith;


    /**
     * @var int
     *
     * @ORM\Column(name="victimLostInBattle", type="integer")
     */
    private $victimLostInBattle;

    /**
     * @var int
     *
     * @ORM\Column(name="victimEndedWith", type="integer")
     */
    private $victimEndedWith;

    /**
     * BattleWarrior constructor.
     * @param int $aggressorStartedWith
     * @param Battle $battle
     */

    public function __construct(int $aggressorStartedWith, Battle $battle)
    {
        $this->aggressorStartedWith = $aggressorStartedWith;
        $this->battle = $battle;
        $this->aggressorEndedWith = 0;
        $this->aggressorLostInBattle = 0;
        $this->victimSartedWith = 0;
        $this->victimEndedWith = 0;
        $this->victimLostInBattle = 0;
        $this->quantity = 0;
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

    /**
     * @return int
     */
    public function getAggressorStartedWith(): int
    {
        return $this->aggressorStartedWith;
    }

    /**
     * @param int $aggressorStartedWith
     */
    public function setAggressorStartedWith(int $aggressorStartedWith): void
    {
        $this->aggressorStartedWith = $aggressorStartedWith;
    }

    /**
     * @return int
     */
    public function getAggressorLostInBattle(): int
    {
        return $this->aggressorLostInBattle;
    }

    /**
     * @param int $aggressorLostInBattle
     */
    public function setAggressorLostInBattle(int $aggressorLostInBattle): void
    {
        $this->aggressorLostInBattle = $aggressorLostInBattle;
    }

    /**
     * @return int
     */
    public function getAggressorEndedWith(): int
    {
        return $this->aggressorEndedWith;
    }

    /**
     * @param int $aggressorEndedWith
     */
    public function setAggressorEndedWith(int $aggressorEndedWith): void
    {
        $this->aggressorEndedWith = $aggressorEndedWith;
    }

    /**
     * @return int
     */
    public function getVictimSartedWith(): int
    {
        return $this->victimSartedWith;
    }

    /**
     * @param int $victimSartedWith
     */
    public function setVictimSartedWith(int $victimSartedWith): void
    {
        $this->victimSartedWith = $victimSartedWith;
    }

    /**
     * @return int
     */
    public function getVictimLostInBattle(): int
    {
        return $this->victimLostInBattle;
    }

    /**
     * @param int $victimLostInBattle
     */
    public function setVictimLostInBattle(int $victimLostInBattle): void
    {
        $this->victimLostInBattle = $victimLostInBattle;
    }

    /**
     * @return int
     */
    public function getVictimEndedWith(): int
    {
        return $this->victimEndedWith;
    }

    /**
     * @param int $victimEndedWith
     */
    public function setVictimEndedWith(int $victimEndedWith): void
    {
        $this->victimEndedWith = $victimEndedWith;
    }


}
