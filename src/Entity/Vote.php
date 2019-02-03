<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @author mrosser
 * @ORM\Entity()
 * @ORM\Table(name="vote")
 * @UniqueEntity(fields={"voter", "idea"})
 */
class Vote {
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
    private $voter;

    /**
     * @ORM\ManyToOne(targetEntity="Idea", inversedBy="votes")
     */
    private $idea;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     */
    private $datetime;

    public function getId(): int
    {
        return $this->id;
    }

    public function setVoter(UserCollection $voter) : Vote
    {
        $this->voter = $voter;

        return $this;
    }

    public function getVoter()
    {
        return $this->voter;
    }

    public function setIdea(Idea $idea) : Vote
    {
        $this->idea = $idea;

        return $this;
    }

    public function getIdea()
    {
        return $this->idea;
    }

    public function setValue(int $value): Vote
    {
        if ($value > 0) {
            $this->value = 1;
        } elseif ($value < 0) {
            $this->value = -1;
        } else {
            $this->value = 0;
        }

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setDatetime(\DateTime $datetime) : Vote
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }
}
