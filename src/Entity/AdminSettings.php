<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author mrosser
 * @ORM\Entity()
 * @ORM\Table(name="admin_settings")
 */
class AdminSettings {
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Collection", inversedBy="adminSettings")
     */
    private $collection;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailText;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $mailSubject;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $votingActive;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $anonymousVote;

    public function getId(): int
    {
        return $this->id;
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

    public function setMailText($mailText)
    {
        $this->mailText = $mailText;
    }

    public function getMailText()
    {
        return $this->mailText;
    }

    public function setMailSubject($mailSubject)
    {
        $this->mailSubject = $mailSubject;
    }

    public function getMailSubject()
    {
        return $this->mailSubject;
    }

    public function setVotingActive($votingActive)
    {
        $this->votingActive = $votingActive;

        return $this;
    }

    public function getVotingActive()
    {
        return $this->votingActive;
    }

    public function setAnonymousVote($anonymousVote)
    {
        $this->anonymousVote = $anonymousVote;

        return $this;
    }

    public function getAnonymousVote()
    {
        return $this->anonymousVote;
    }
}
