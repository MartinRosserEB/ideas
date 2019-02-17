<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Collection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="UserCollection", mappedBy="collection")
     */
    private $collectionUsers;

    /**
     * @ORM\OneToOne(targetEntity="AdminSettings", mappedBy="collection")
     */
    private $adminSettings;

    /**
     * @ORM\OneToMany(targetEntity="Idea", mappedBy="collection")
     */
    private $ideas;

    public function __construct()
    {
        $this->collectionUsers = new ArrayCollection();
        $this->ideas = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getCollectionUsers()
    {
        return $this->collectionUsers;
    }

    public function getCollectionUserFor(User $user)
    {
        $userCollection = $this->getCollectionUsers()->filter(
            function ($collectionUser) use ($user) {
                return $collectionUser->getUser() === $user;
            }
        );
        if (count($userCollection) > 0) {
            return $userCollection->first();
        }
        return null;
    }

    public function addCollectionUser(UserCollection $userCollection)
    {
        $this->collectionUsers->add($userCollection);
    }

    public function getAdminSettings()
    {
        return $this->adminSettings;
    }

    public function setAdminSettings(AdminSettings $adminSettings)
    {
        $this->adminSettings = $adminSettings;
    }

    public function getIdeas()
    {
        return $this->ideas;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
