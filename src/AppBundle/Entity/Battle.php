<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Battle
 *
 * @ORM\Table(name="battle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BattleRepository")
 */
class Battle
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
     * @var \DateTime
     *
     * @ORM\Column(name="battleDeclare", type="datetime")
     */
    private $battleDeclare;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="battleHappen", type="datetime")
     */
    private $battleHappen;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="returnDue", type="datetime")
     */
    private $returnDue;

    /**
     * @var BattleWarrior[]|Collection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BattleWarrior", mappedBy="battle")
     */
    private $warriors;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="battlesAsAggressor")
     * @ORM\JoinColumn(name="aggressor_id", referencedColumnName="id")
     */
    private $aggressor;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="battlesAsVictimt")
     * @ORM\JoinColumn(name="victim_id", referencedColumnName="id")
     */
    private $victim;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="battlesAsWinner")
     * @ORM\JoinColumn(name="winner_id", referencedColumnName="id", nullable=true)
     */
    private $winner;

    /**
     * Battle constructor.
     * @param User $aggressor
     * @param User $victim
     */
    public function __construct(User $aggressor, User $victim)
    {
        $this->setAggressor($aggressor);
        $this->setVictim($victim);
        $this->setBattleDeclare(new \DateTime());
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
     * Set battleDeclare
     *
     * @param \DateTime $battleDeclare
     *
     * @return Battle
     */
    public function setBattleDeclare($battleDeclare)
    {
        $this->battleDeclare = $battleDeclare;

        return $this;
    }

    /**
     * Get battleDeclare
     *
     * @return \DateTime
     */
    public function getBattleDeclare()
    {
        return $this->battleDeclare;
    }

    /**
     * Set battleHappen
     *
     * @param \DateTime $battleHappen
     *
     * @return Battle
     */
    public function setBattleHappen($battleHappen)
    {
        $this->battleHappen = $battleHappen;

        return $this;
    }

    /**
     * Get battleHappen
     *
     * @return \DateTime
     */
    public function getBattleHappen()
    {
        return $this->battleHappen;
    }

    /**
     * Set returnDue
     *
     * @param \DateTime $returnDue
     *
     * @return Battle
     */
    public function setReturnDue($returnDue)
    {
        $this->returnDue = $returnDue;

        return $this;
    }

    /**
     * Get returnDue
     *
     * @return \DateTime
     */
    public function getReturnDue()
    {
        return $this->returnDue;
    }

    /**
     * @return User
     */
    public function getAggressor(): User
    {
        return $this->aggressor;
    }

    /**
     * @param User $aggressor
     */
    public function setAggressor(User $aggressor): void
    {
        $this->aggressor = $aggressor;
    }

    /**
     * @return User
     */
    public function getVictim(): User
    {
        return $this->victim;
    }

    /**
     * @param User $victim
     */
    public function setVictim(User $victim): void
    {
        $this->victim = $victim;
    }

    /**
     * @return User
     */
    public function getWinner(): User
    {
        return $this->winner;
    }

    /**
     * @param User $winner
     */
    public function setWinner(User $winner): void
    {
        $this->winner = $winner;
    }

    /**
     * @return BattleWarrior[]|Collection
     */
    public function getWarriors()
    {
        return $this->warriors;
    }

    /**
     * @param BattleWarrior[]|Collection $warriors
     */
    public function setWarriors($warriors): void
    {
        $this->warriors = $warriors;
    }


}

