<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:read'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Feed>
     */
    #[ORM\OneToMany(targetEntity: Feed::class, mappedBy: 'category')]
    private Collection $feeds;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?User $owner = null;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->name;
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
            'identifier' => $this->name,
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
            $feed->setCategory($this);
        }

        return $this;
    }

    public function removeFeed(Feed $feed): static
    {
        // set the owning side to null (unless already changed)
        if ($this->feeds->removeElement($feed) && $feed->getCategory() === $this) {
            $feed->setCategory(null);
        }

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
}
