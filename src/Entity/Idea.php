<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author mrosser
 * @ORM\Entity(repositoryClass="App\Repository\IdeaRepository")
 * @ORM\Table(name="idea")
 */
class Idea {
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserCollection")
     */
    private $creator;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $ideaId;

    /**
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="ideas")
     */
    private $collection;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="idea")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="idea")
     */
    private $votes;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCreator(UserCollection $creator) : void
    {
        $this->creator = $creator;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setIdeaId(int $ideaId) : void
    {
        $this->ideaId = $ideaId;
    }

    public function getIdeaId()
    {
        return $this->ideaId;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDatetime(\DateTime $datetime)
    {
        $this->datetime = $datetime;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getVotes()
    {
        return $this->votes;
    }

    public function getVotesForUser(User $user)
    {
        $userCollection = $this->getCollection()->getCollectionUserFor($user);
        if ($userCollection !== null) {
            return $this->votes->filter(
                function ($vote) use ($userCollection) {
                    return $vote->getVoter() === $userCollection;
                }
            );
        }
        return null;
    }

    public function __toString()
    {
        return $this->ideaId.' - '.$this->title;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
