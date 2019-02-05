<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class UserCollection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="userCollections")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $familyName;

    /**
     * @ORM\ManyToOne(targetEntity="Collection",inversedBy="collectionUsers")
     */
    private $collection;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $role;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getFamilyName()
    {
        return $this->familyName;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(String $role)
    {
        $this->role = $role;

        return $this;
    }

    public function __toString() {
        $name = '';
        if ($this->firstName) {
            $name .= $this->firstName;
        }
        if ($this->familyName) {
            if ($name !== '') {
                $name .= ' ';
            }
            $name .= $this->familyName;
        }
        if ($name === '') {
            $name = $this->getUser();
        }
        return (string) $name;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
