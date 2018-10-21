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
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $voter;

    /**
     * @ORM\ManyToOne(targetEntity="Idea")
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

    public function setVoter(User $voter) : void
    {
        $this->voter = $voter;
    }

    public function getVoter()
    {
        return $this->voter;
    }

    public function setIdea(Idea $idea) : void
    {
        $this->idea = $idea;
    }

    public function getIdea()
    {
        return $this->idea;
    }

    public function setValue(int $value): void
    {
        if ($value > 0) {
            $this->value = 1;
        } elseif ($value < 0) {
            $this->value = -1;
        } else {
            $this->value = 0;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setDatetime(\DateTime $datetime)
    {
        $this->datetime = $datetime;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }
}
