<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="Username already taken, please choose another.")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     */

    private $plainPassword;
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var Planet
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Planet", mappedBy="user")
     */
    private $planet;


    /**
     * @var Collection|Battle[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Battle", mappedBy="aggressor")
     */
    private $battlesAsAggressor;

    /**
     * @var Collection|Battle[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Battle", mappedBy="victim")
     */
    private $battlesAsVictim;

    /**
     * @var Collection|Battle[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Battle", mappedBy="winner")
     */
    private $battlesAsWinner;

    /**
     * @var Collection|BattleWarrior[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BattleWarrior", mappedBy="owner")
     */
    private $warriorsAtBattle;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->setPlanet(new Planet());
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
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
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

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Battle[]|Collection
     */
    public function getBattlesAsAggressor()
    {
        return $this->battlesAsAggressor;
    }

    /**
     * @param Battle[]|Collection $battlesAsAggressor
     */
    public function setBattlesAsAggressor($battlesAsAggressor): void
    {
        $this->battlesAsAggressor = $battlesAsAggressor;
    }

    /**
     * @return Battle[]|Collection
     */
    public function getBattlesAsVictim()
    {
        return $this->battlesAsVictim;
    }

    /**
     * @param Battle[]|Collection $battlesAsVictim
     */
    public function setBattlesAsVictim($battlesAsVictim): void
    {
        $this->battlesAsVictim = $battlesAsVictim;
    }

    /**
     * @return Battle[]|Collection
     */
    public function getBattlesAsWinner()
    {
        return $this->battlesAsWinner;
    }

    /**
     * @param Battle[]|Collection $battlesAsWinner
     */
    public function setBattlesAsWinner($battlesAsWinner): void
    {
        $this->battlesAsWinner = $battlesAsWinner;
    }

    /**
     * @return BattleWarrior[]|Collection
     */
    public function getWarriorsAtBattle()
    {
        return $this->warriorsAtBattle;
    }

    /**
     * @param BattleWarrior[]|Collection $warriorsAtBattle
     */
    public function setWarriorsAtBattle($warriorsAtBattle): void
    {
        $this->warriorsAtBattle = $warriorsAtBattle;
    }


    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->getId(),
            $this->getUsername(),
            $this->getPassword()
        ]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password
            ) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

}

