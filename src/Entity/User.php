<?php

namespace App\Entity;

use App\Enum\UserAccountStatusEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(enumType: UserAccountStatusEnum::class)]
    private ?UserAccountStatusEnum $accountStatus = UserAccountStatusEnum::INACTIVE;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Subscription $currentSubscription = null;

    private ?array $roles = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'publisher')]
    private Collection $comments;

    #[ORM\Column(length: 255)]
    private ?string $profilePicture = '';

    // Additional fields for relationships...

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        // Initialize other collections if required...
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the unique identifier for the user.
     */
    public function getUserIdentifier(): string
    {
        return $this->email; // Use a unique field, like email.
    }

    /**
     * Returns the roles granted to the user.
     */
    public function getRoles(): array
    {
        // Return default roles if roles are not dynamically set.
        return $this->roles ?? ['ROLE_USER'];
    }

    /**
     * Sets roles for the user (if dynamically managed).
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        // If you store temporary sensitive data, clear it here.
    }

    /**
     * Optional: If using bcrypt/argon2i, salt is not needed.
     */
    public function getSalt(): ?string
    {
        return null;
    }

    // Getter and setter methods for other properties...

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
