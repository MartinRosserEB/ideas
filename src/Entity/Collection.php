<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Collection
{
    public static $defaultText = "---\n\n### Phase 1\n\nBenutzer werden erfasst, eine Mailvorlage vorbereitet und versendet. Anschliessend können alle Benutzer über ihren persönlichen Link einloggen und Ideen eintragen sowie kommentieren.\n\n### Phase 2\n\nDer Wahlmodus wird aktiviert. Z.B. nach neuerlichem Mailversand können alle teilnehmenden Benutzer über ihren persönlichen Link einloggen und für ihre favorisierten Ideen abstimmen.\n\n#### Formatierungshinweise\n\n[Das ist ein Link](http://www.handherz.ch/). Horizontale Trennstriche werden durch drei Minuszeichen erstellt (Beispiel am Anfang und am Ende).\n\n* Listen werden mit dem Stern kreiert.\n* Eine Liste kann mehrere Punkte beinhalten.\n\n---";

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
