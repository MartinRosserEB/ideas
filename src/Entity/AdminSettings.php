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
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $mailText;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $mailSubject;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $votingActive;

    public function getId(): int
    {
        return $this->id;
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
    }

    public function getVotingActive()
    {
        return $this->votingActive;
    }
}
