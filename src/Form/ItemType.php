<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Form\Type\CustomCollectionType;
use App\Repository\ItemCollectionRepository;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function __construct(private ItemCollectionRepository $ItemCollectionRepository){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('tags')
            ->add('itemCollection', EntityType::class, [
                'class' => ItemCollection::class,
                'choice_label' => 'name',
            ])
            ;


        $builder->get('itemCollection')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event): void {
                $form = $event->getForm();
                $collection = $event->getForm()->getData();
                $customItemAttributes = $collection->getCustomItemAttributes();

                foreach($customItemAttributes->getValues() as $attribute) {
                    $name = $attribute->getName();
                    $name_field = str_replace(' ', '_', $name);
                    $form->getParent()->add($name_field, null, [
                        'label' => $name,
                        'mapped' => false,
                    ]);
                }

            }
        );


        // $collection = $this->ItemCollectionRepository->findAll();
        // $customItemAttributes = $collection[0]->getCustomItemAttributes()->getValues();


        // $builder->addEventListener(
        //     FormEvents::POST_SET_DATA,
        //     function (FormEvent $event) use ($customItemAttributes): void {
        //         $form = $event->getForm();
        //         $data = $event->getData();
        //         // dd($form->getData());
        //         foreach($customItemAttributes as $attribute) {
        //             $name = $attribute->getName();
        //             $name_field = str_replace(' ', '_', $name);
        //             $form->add($name_field, null, [
        //                 'label' => $name,
        //                 'mapped' => false,
        //             ]);
        //         }

        //     }
        // );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'csrf_protection' => false
        ]);
    }
}
