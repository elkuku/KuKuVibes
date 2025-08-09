<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: UserRepository::class)]
#[Table(name: 'system_user')]
#[UniqueEntity(fields: 'identifier', message: 'This identifier is already in use')]
class User implements UserInterface, Stringable
{
    final public const ROLES
        = [
            'user' => 'ROLE_USER',
            'admin' => 'ROLE_ADMIN',
        ];

    #[Column, Id, GeneratedValue(strategy: 'SEQUENCE')]
    #[Groups(['user:read'])]
    private ?int $id = 0;

    #[Column(unique: true), NotBlank]
    #[Groups(['user:read'])]
    private string $identifier = '*';

    /**
     * @var array<string>
     */
    #[Column(type: Types::JSON)]
    private array $roles = [];

    /**
     * @var array<string>|null
     */
    #[Column(type: Types::JSON, nullable: true)]
    private ?array $params = [];

    #[Column(length: 100, nullable: true)]
    private ?string $googleId = null;

    #[Column(nullable: true)]
    private ?int $gitHubId = null;

    #[Column(nullable: true)]
    private ?int $gitLabId = null;

    /**
     * @var Collection<int, Feed>
     */
    #[OneToMany(targetEntity: Feed::class, mappedBy: 'owner')]
    private Collection $feeds;

    /**
     * @var Collection<int, Category>
     */
    #[OneToMany(targetEntity: Category::class, mappedBy: 'owner')]
    private Collection $categories;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return array{
     *     id: integer|null,
     *     identifier: string|null
     * }
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
        ];
    }

    /**
     * @param array{
     *     id: int|null,
     *     identifier: string|null
     * } $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? null;
        $this->identifier = (string) ($data['identifier'] ?? null);
    }

    public function __toString(): string
    {
        return $this->identifier;
    }

    #[\Override]
    public function eraseCredentials(): void
    {
    }

    #[\Override]
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLES['user'];

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array<string>|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    public function getParam(string $name): string
    {
        return $this->params && array_key_exists($name, $this->params)
            ? $this->params[$name]
            : '';
    }

    /**
     * @param array<string> $params
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @todo this method is required by the the rememberMe functionality :(
     */
    public function getPassword(): ?string
    {
        return null;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getGitHubId(): ?int
    {
        return $this->gitHubId;
    }

    public function setGitHubId(?int $gitHubId): self
    {
        $this->gitHubId = $gitHubId;

        return $this;
    }

    public function getGitLabId(): ?int
    {
        return $this->gitLabId;
    }

    public function setGitLabId(?int $gitLabId): self
    {
        $this->gitLabId = $gitLabId;

        return $this;
    }

    /**
     * @return Collection<int, Feed>
     */
    public function getFeeds(): Collection
    {
        return $this->feeds;
    }

    public function addFeed(Feed $feed): static
    {
        if (!$this->feeds->contains($feed)) {
            $this->feeds->add($feed);
            $feed->setOwner($this);
        }

        return $this;
    }

    public function removeFeed(Feed $feed): static
    {
        // set the owning side to null (unless already changed)
        if ($this->feeds->removeElement($feed) && $feed->getOwner() === $this) {
            $feed->setOwner(null);
        }

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
            $category->setOwner($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        // set the owning side to null (unless already changed)
        if ($this->categories->removeElement($category) && $category->getOwner() === $this) {
            $category->setOwner(null);
        }

        return $this;
    }
}
