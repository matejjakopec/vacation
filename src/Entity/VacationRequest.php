<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity()]
class VacationRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'requests')]
    private $user;

    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[ORM\Column(type: 'datetime')]
    private $endDate;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'boolean')]
    private $approvedByTeamLead;

    #[ORM\Column(type: 'boolean')]
    private $approvedByProjectLead;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    public function getApprovedByTeamLead()
    {
        return $this->approvedByTeamLead;
    }


    public function setApprovedByTeamLead($approvedByTeamLead)
    {
        $this->approvedByTeamLead = $approvedByTeamLead;

        return $this;
    }

    public function getApprovedByProjectLead()
    {
        return $this->approvedByProjectLead;
    }

    public function setApprovedByProjectLead($approvedByProjectLead)
    {
        $this->approvedByProjectLead = $approvedByProjectLead;

        return $this;
    }


    public function getUser()
    {
        return $this->user;
    }


    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }


}