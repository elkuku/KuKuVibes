<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['feed:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['feed:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['feed:read'])]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $html_url = null;

    #[ORM\ManyToOne(inversedBy: 'feeds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['feed:read'])]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'feeds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['feed:read'])]
    private ?User $owner = null;

    /**
     * @return array{
     *     id: integer|null,
     * }
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getHtmlUrl(): ?string
    {
        return $this->html_url;
    }

    public function setHtmlUrl(?string $html_url): static
    {
        $this->html_url = $html_url;

        return $this;
    }
}
