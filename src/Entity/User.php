<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['id'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('user.index')]
    private ?int $id = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    #[Groups('user.index')]
    private ?string $login = null;

    #[ORM\Column(length: 255)]
    #[Groups('user.index')]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups('user.index')]
    private ?bool $active = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Resource::class, mappedBy: 'likes')]
    private Collection $liked;

    #[ORM\ManyToMany(targetEntity: Resource::class, mappedBy: 'saves')]
    private Collection $saved;

    #[ORM\ManyToMany(targetEntity: Resource::class, mappedBy: 'views')]
    private Collection $viewed;

    #[ORM\ManyToMany(targetEntity: Resource::class, mappedBy: 'shares')]
    private Collection $shared;

    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'user')]
    private Collection $resources;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messageSender;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $messageReceiver;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deactivationDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accountValidationToken = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->liked = new ArrayCollection();
        $this->saved = new ArrayCollection();
        $this->viewed = new ArrayCollection();
        $this->shared = new ArrayCollection();
        $this->resources = new ArrayCollection();
        $this->messageSender = new ArrayCollection();
        $this->messageReceiver = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
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
    public function getPassword(): string
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getLiked(): Collection
    {
        return $this->liked;
    }

    public function addLiked(Resource $liked): static
    {
        if (!$this->liked->contains($liked)) {
            $this->liked->add($liked);
            $liked->addLike($this);
        }

        return $this;
    }

    public function removeLiked(Resource $liked): static
    {
        if ($this->liked->removeElement($liked)) {
            $liked->removeLike($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getSaved(): Collection
    {
        return $this->saved;
    }

    public function addSaved(Resource $saved): static
    {
        if (!$this->saved->contains($saved)) {
            $this->saved->add($saved);
            $saved->addSave($this);
        }

        return $this;
    }

    public function removeSaved(Resource $saved): static
    {
        if ($this->saved->removeElement($saved)) {
            $saved->removeSave($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getViewed(): Collection
    {
        return $this->viewed;
    }

    public function addViewed(Resource $viewed): static
    {
        if (!$this->viewed->contains($viewed)) {
            $this->viewed->add($viewed);
            $viewed->addView($this);
        }

        return $this;
    }

    public function removeViewed(Resource $viewed): static
    {
        if ($this->viewed->removeElement($viewed)) {
            $viewed->removeView($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getShared(): Collection
    {
        return $this->shared;
    }

    public function addShared(Resource $shared): static
    {
        if (!$this->shared->contains($shared)) {
            $this->shared->add($shared);
            $shared->addShare($this);
        }

        return $this;
    }

    public function removeShared(Resource $shared): static
    {
        if ($this->shared->removeElement($shared)) {
            $shared->removeView($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Resource>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setUser($this);
        }

        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource)) {
            // set the owning side to null (unless already changed)
            if ($resource->getUser() === $this) {
                $resource->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSender(): Collection
    {
        return $this->messageSender;
    }

    public function addSender(Message $messageSender): static
    {
        if (!$this->messageSender->contains($messageSender)) {
            $this->messageSender->add($messageSender);
            $messageSender->setSender($this);
        }

        return $this;
    }

    public function removeSender(Message $messageSender): static
    {
        if ($this->messageSender->removeElement($messageSender)) {
            // set the owning side to null (unless already changed)
            if ($messageSender->getSender() === $this) {
                $messageSender->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessageReceiver(): Collection
    {
        return $this->messageReceiver;
    }

    public function addMessageReceiver(Message $messageReceiver): static
    {
        if (!$this->messageReceiver->contains($messageReceiver)) {
            $this->messageReceiver->add($messageReceiver);
            $messageReceiver->setReceiver($this);
        }

        return $this;
    }

    public function removeMessageReceiver(Message $messageReceiver): static
    {
        if ($this->messageReceiver->removeElement($messageReceiver)) {
            // set the owning side to null (unless already changed)
            if ($messageReceiver->getReceiver() === $this) {
                $messageReceiver->setReceiver(null);
            }
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getDeactivationDate(): ?\DateTimeInterface
    {
        return $this->deactivationDate;
    }

    public function setDeactivationDate(?\DateTimeInterface $deactivationDate): static
    {
        $this->deactivationDate = $deactivationDate;

        return $this;
    }

    public function getAccountValidationToken(): ?string
    {
        return $this->accountValidationToken;
    }

    public function setAccountValidationToken(?string $accountValidationToken): static
    {
        $this->accountValidationToken = $accountValidationToken;

        return $this;
    }

}
