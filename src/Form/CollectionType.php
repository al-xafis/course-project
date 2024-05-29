<?php

namespace App\Form;

use App\Entity\CollectionCategory;
use App\Entity\CustomItemAttribute;
use App\Entity\ItemCollection;
use App\Validator\CollectionNameUniquePerUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new CollectionNameUniquePerUser()
                ]
            ])
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => CollectionCategory::class,
                'choice_label' => 'name',
            ])
            ->add('customItemAttributes', BaseCollectionType::class, [
                'entry_type' => CustomItemAttributeType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemCollection::class,
        ]);
    }
}
