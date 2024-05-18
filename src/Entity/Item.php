<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Index(name: "item_search_idx", columns: ["name"], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $tags = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid()]
    private ?ItemCollection $itemCollection = null;

    /**
     * @var Collection<int, ItemAttribute>
     */
    #[ORM\OneToMany(targetEntity: ItemAttribute::class, mappedBy: 'Item', cascade: ["persist"], orphanRemoval: true)]
    private Collection $itemAttributes;

    public function __construct()
    {
        $this->itemAttributes = new ArrayCollection();
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

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getItemCollection(): ?ItemCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(?ItemCollection $itemCollection): static
    {
        $this->itemCollection = $itemCollection;

        return $this;
    }

    /**
     * @return Collection<int, ItemAttribute>
     */
    public function getItemAttributes(): Collection
    {
        return $this->itemAttributes;
    }

    public function addItemAttribute(ItemAttribute $itemAttribute): static
    {
        if (!$this->itemAttributes->contains($itemAttribute)) {
            $this->itemAttributes->add($itemAttribute);
            $itemAttribute->setItem($this);
        }

        return $this;
    }

    public function removeItemAttribute(ItemAttribute $itemAttribute): static
    {
        if ($this->itemAttributes->removeElement($itemAttribute)) {
            // set the owning side to null (unless already changed)
            if ($itemAttribute->getItem() === $this) {
                $itemAttribute->setItem(null);
            }
        }

        return $this;
    }
}
