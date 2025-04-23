<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
// #[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
// #[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
// #[UniqueEntity(fields: ['name'], message: 'Ce nom est déjà utilisé')] // Ajoutez cette ligne
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255, nullable: true )]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $name = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'user')]
    private Collection $categories;

    /**
     * @var Collection<int, Publication>
     */
    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'user')]
    private Collection $publications;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'user')]
    private Collection $commentaires;

    /**
     * @var Collection<int, Reponse>
     */
    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'user')]
    private Collection $reponses;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, PostLiker>
     */
    #[ORM\OneToMany(targetEntity: PostLiker::class, mappedBy: 'user')]
    private Collection $postLikers;

    /**
     * @var Collection<int, CompteBloquer>
     */
    #[ORM\OneToMany(targetEntity: CompteBloquer::class, mappedBy: 'user')]
    private Collection $compteBloquers;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, AjoutAmi>
     */
    #[ORM\OneToMany(targetEntity: AjoutAmi::class, mappedBy: 'user')]
    private Collection $ajoutAmis;

    #[ORM\Column(nullable: true)]
    private ?int $googleId = null;


 

  




    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->postLikers = new ArrayCollection();
        $this->compteBloquers = new ArrayCollection();
        $this->ajoutAmis = new ArrayCollection();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setUser($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getUser() === $this) {
                $category->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setUser($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getUser() === $this) {
                $publication->setUser(null);
            }
        }

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
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setUser($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getUser() === $this) {
                $reponse->setUser(null);
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
            $postLiker->setUser($this);
        }

        return $this;
    }

    public function removePostLiker(PostLiker $postLiker): static
    {
        if ($this->postLikers->removeElement($postLiker)) {
            // set the owning side to null (unless already changed)
            if ($postLiker->getUser() === $this) {
                $postLiker->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bloquer>
     */

    /**
     * @return Collection<int, CompteBloquer>
     */
    public function getCompteBloquers(): Collection
    {
        return $this->compteBloquers;
    }

    public function addCompteBloquer(CompteBloquer $compteBloquer): static
    {
        if (!$this->compteBloquers->contains($compteBloquer)) {
            $this->compteBloquers->add($compteBloquer);
            $compteBloquer->setUser($this);
        }

        return $this;
    }

    public function removeCompteBloquer(CompteBloquer $compteBloquer): static
    {
        if ($this->compteBloquers->removeElement($compteBloquer)) {
            // set the owning side to null (unless already changed)
            if ($compteBloquer->getUser() === $this) {
                $compteBloquer->setUser(null);
            }
        }

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, AjoutAmi>
     */
    public function getAjoutAmis(): Collection
    {
        return $this->ajoutAmis;
    }

    public function addAjoutAmi(AjoutAmi $ajoutAmi): static
    {
        if (!$this->ajoutAmis->contains($ajoutAmi)) {
            $this->ajoutAmis->add($ajoutAmi);
            $ajoutAmi->setUser($this);
        }

        return $this;
    }

    public function removeAjoutAmi(AjoutAmi $ajoutAmi): static
    {
        if ($this->ajoutAmis->removeElement($ajoutAmi)) {
            // set the owning side to null (unless already changed)
            if ($ajoutAmi->getUser() === $this) {
                $ajoutAmi->setUser(null);
            }
        }

        return $this;
    }

    public function getGoogleId(): ?int
    {
        return $this->googleId;
    }

    public function setGoogleId(int $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
    }

  
   

  

 
  
}
