<?php

namespace App\Controller\Admin;

use App\Entity\CollectionCategory;
use App\Entity\ItemCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ItemCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemCollection::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $itemCollection = new ItemCollection();
        $itemCollection->setOwner($this->getUser());

        return $itemCollection;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextareaField::new('description'),
            AssociationField::new('category')
                ->setFormTypeOption('choice_label', fn($choice) => $choice->getName())
        ];
    }
}
