<?php

namespace App\Entity;

use App\Repository\BloquerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BloquerRepository::class)]
class Bloquer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bloquers')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'bloquers')]
    private ?User $autreUser = null;

    #[ORM\Column]
    private ?bool $bloquer = null;

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

    public function getAutreUser(): ?User
    {
        return $this->autreUser;
    }

    public function setAutreUser(?User $autreUser): static
    {
        $this->autreUser = $autreUser;

        return $this;
    }

    public function isBloquer(): ?bool
    {
        return $this->bloquer;
    }

    public function setBloquer(bool $bloquer): static
    {
        $this->bloquer = $bloquer;

        return $this;
    }
}
