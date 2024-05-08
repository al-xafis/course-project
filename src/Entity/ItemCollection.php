<?php

namespace App\Entity;

use App\Repository\ItemCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemCollectionRepository::class)]
class ItemCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CollectionCategory $category = null;

    /**
     * @var Collection<int, CustomItemAttribute>
     */
    #[ORM\OneToMany(targetEntity: CustomItemAttribute::class, mappedBy: 'itemCollection', cascade: ["persist"], orphanRemoval: true)]
    private Collection $customItemAttributes;



    public function __construct()
    {
        $this->customItemAttributes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?CollectionCategory
    {
        return $this->category;
    }

    public function setCategory(?CollectionCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, CustomItemAttribute>
     */
    public function getCustomItemAttributes(): Collection
    {
        return $this->customItemAttributes;
    }

    public function addCustomItemAttribute(CustomItemAttribute $customItemAttribute): static
    {
        if (!$this->customItemAttributes->contains($customItemAttribute)) {
            $this->customItemAttributes->add($customItemAttribute);
            $customItemAttribute->setItemCollection($this);
        }

        return $this;
    }

    public function removeCustomItemAttribute(CustomItemAttribute $customItemAttribute): static
    {
        if ($this->customItemAttributes->removeElement($customItemAttribute)) {
            if ($customItemAttribute->getItemCollection() === $this) {
                $customItemAttribute->setItemCollection(null);
            }
        }

        return $this;
    }
}
