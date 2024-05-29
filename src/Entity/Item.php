<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use App\Validator\ItemNameUniquePerUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Index(name: "item_search_idx", columns: ["name"], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[HasLifecycleCallbacks]
class Item
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3)]
    private ?string $name = null;


    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid()]
    private ?ItemCollection $itemCollection = null;

    /**
     * @var Collection<int, ItemAttribute>
     */
    #[ORM\OneToMany(targetEntity: ItemAttribute::class, mappedBy: 'Item', cascade: ["persist"], orphanRemoval: true)]
    private Collection $itemAttributes;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\OneToMany(targetEntity: Tag::class, mappedBy: 'item', orphanRemoval: true, cascade: ["persist"])]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?User $owner = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'item', orphanRemoval: true)]
    private Collection $comments;


    public function __construct()
    {
        $this->itemAttributes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->setItem($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getItem() === $this) {
                $tag->setItem(null);
            }
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
            $comment->setItem($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getItem() === $this) {
                $comment->setItem(null);
            }
        }

        return $this;
    }

}
