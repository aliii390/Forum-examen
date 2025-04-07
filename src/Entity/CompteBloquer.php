<?php

namespace App\Entity;

use App\Repository\CompteBloquerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteBloquerRepository::class)]
class CompteBloquer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'compteBloquers')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'compteBloquers')]
    private ?User $userBlocked = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUserBlocked(): ?User
    {
        return $this->userBlocked;
    }

    public function setUserBlocked(?User $userBlocked): static
    {
        $this->userBlocked = $userBlocked;

        return $this;
    }
}
