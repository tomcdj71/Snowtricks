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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Assert\NoSuspiciousCharacters]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 4,
        max: 50,
        minMessage: 'Your username should be at least {{ limit }} characters',
        maxMessage: 'Your username should not be longer than {{ limit }} characters'
    )]
    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private ?string $username;

    /**
     * @var string The hashed password
     */
    #[Assert\PasswordStrength([
        'minScore' => PasswordStrength::STRENGTH_MEDIUM,
        'message' => 'The password is too weak. Please use a stronger password.',
    ])]
    #[ORM\Column(type: Types::STRING, length: 50)]
    private ?string $password;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(
        max: 180,
        maxMessage: 'Your email should not be longer than {{ limit }} characters'
    )]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private ?string $email;

    #[Assert\Type('bool')]
    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $isVerified = false;

    #[Assert\Type('string')]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Trick::class, orphanRemoval: true)]
    private Collection $tricks;

    #[Assert\Type(Image::class)]
    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Image::class)]
    private ?Image $avatar;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tricks = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        if (null === $this->password) {
            throw new \LogicException('Password should not be accessed before it has been set.');
        }

        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        // set the owning side to null (unless already changed)
        if ($this->comments->removeElement($comment) && $comment->getAuthor() === $this) {
            $comment->setAuthor(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Trick>
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks->add($trick);
            $trick->setAuthor($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        // set the owning side to null (unless already changed)
        if ($this->tricks->removeElement($trick) && $trick->getAuthor() === $this) {
            $trick->setAuthor(null);
        }

        return $this;
    }

    public function getAvatar(): ?Image
    {
        return $this->avatar;
    }

    public function setAvatar(?Image $avatar): self
    {
        // unset the owning side of the relation if necessary
        $this->avatar = $avatar;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function __toString(): string
    {
        return $this->username ?? '';
    }
}
