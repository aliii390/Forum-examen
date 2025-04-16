<?php

namespace App\Entity;

use App\Repository\AjoutAmiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AjoutAmiRepository::class)]
class AjoutAmi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ajoutAmis')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'ajoutAmis')]
    private ?User $userAjoutez = null;

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

    public function getUserAjoutez(): ?User
    {
        return $this->userAjoutez;
    }

    public function setUserAjoutez(?User $userAjoutez): static
    {
        $this->userAjoutez = $userAjoutez;

        return $this;
    }
}
