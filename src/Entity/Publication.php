<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Category $category = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $title = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'publication')]
    private Collection $commentaires;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, PostLiker>
     */
    #[ORM\OneToMany(targetEntity: PostLiker::class, mappedBy: 'publication')]
    private Collection $postLikers;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->postLikers = new ArrayCollection();
    }

  

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setPublication($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPublication() === $this) {
                $commentaire->setPublication(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, PostLiker>
     */
    public function getPostLikers(): Collection
    {
        return $this->postLikers;
    }

    public function addPostLiker(PostLiker $postLiker): static
    {
        if (!$this->postLikers->contains($postLiker)) {
            $this->postLikers->add($postLiker);
            $postLiker->setPublication($this);
        }

        return $this;
    }

    public function removePostLiker(PostLiker $postLiker): static
    {
        if ($this->postLikers->removeElement($postLiker)) {
            // set the owning side to null (unless already changed)
            if ($postLiker->getPublication() === $this) {
                $postLiker->setPublication(null);
            }
        }

        return $this;
    }

  
}
