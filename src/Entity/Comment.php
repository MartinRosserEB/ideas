<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author mrosser
 * @ORM\Entity()
 * @ORM\Table(name="comment")
 */
class Comment {
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
     * @ORM\ManyToOne(targetEntity="Idea", inversedBy="comments")
     */
    private $idea;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    public function getId(): int
    {
        return $this->id;
    }

    public function setCreator(User $creator) : void
    {
        $this->creator = $creator;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function setIdea(Idea $idea) : void
    {
        $this->idea = $idea;
    }

    public function getIdea()
    {
        return $this->idea;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setDatetime(\DateTime $datetime)
    {
        $this->datetime = $datetime;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function __toString()
    {
        return $this->getComment().' - '.$this->getCreator().', '.$this->getDatetime()->format('d.m.Y');
    }
}
