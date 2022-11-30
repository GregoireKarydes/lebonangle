<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\AdminUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
class AdminUser implements TimestampableInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[Assert\Email]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Assert\Length(min:5)]
    #[Assert\NotCompromisedPassword()]
    private ?string $plainPassword =null;


    public function getPlainPassword() : ?string {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainpassword) : self {
        $this->plainPassword = $plainpassword;
        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
	/**
	 * Returns the roles granted to the user.
	 * @return array<string>
	 */
	public function getRoles(): array {

        return ['ROLE_ADMIN'];
	}
	
	/**
	 * Removes sensitive data from the user.
	 * @return mixed
	 */
	public function eraseCredentials() {
        $this->setPlainPassword('');
	}
	
	/**
	 * Returns the identifier for this user (e.g. its username or email address).
	 * @return string
	 */
	public function getUserIdentifier(): string {
        return $this->email;
	}
}
