<?php

namespace App\Entity;

use App\Enum\EnumCustomItemAttribute;
use App\Repository\CustomItemAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomItemAttributeRepository::class)]
#[UniqueEntity('name')]
class CustomItemAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2)]
    private ?string $name = null;

    #[ORM\Column(length: 10, enumType: EnumCustomItemAttribute::class)]
    #[Assert\Type(type: EnumCustomItemAttribute::class)]
    private ?EnumCustomItemAttribute $type = null;

    #[ORM\ManyToOne(inversedBy: 'customItemAttributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemCollection $itemCollection = null;


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

    public function getType(): ?EnumCustomItemAttribute
    {
        return $this->type;
    }

    public function setType(EnumCustomItemAttribute $type): static
    {
        $this->type = $type;

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

}
