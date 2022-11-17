<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    private $username;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'integer')]
    private $vacationDaysLeft;

    #[ORM\Column(type: 'string', length: 255)]
    private $role;

    #[ManyToOne(targetEntity: Team::class, inversedBy: 'users')]
    private $team;

    #[ManyToOne(targetEntity: Project::class, inversedBy: 'users')]
    private $project;

    #[OneToMany(mappedBy: 'user', targetEntity: VacationRequest::class)]
    private Collection $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection([]);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }



    public function getRequests()
    {
        return $this->requests;
    }

    public function setRequests($requests): self
    {
        $this->requests = $requests;
        return $this;
    }



    public function setRole($role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function setTeam($team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject($project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getVacationDaysLeft()
    {
        return $this->vacationDaysLeft;
    }

    public function setVacationDaysLeft($vacationDaysLeft): self
    {
        $this->vacationDaysLeft = $vacationDaysLeft;
        return $this;
    }
}