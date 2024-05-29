<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Repository\ItemCollectionRepository;
use App\Validator\ItemNameUniquePerUser;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function __construct(private ItemCollectionRepository $ItemCollectionRepository, private Security $security){}



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $owner = $this->security->getUser();

        $builder
            ->add('name', null, [
                'constraints' => [
                    new ItemNameUniquePerUser()
                ]
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TagType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add('itemCollection', EntityType::class, [
                'class' => ItemCollection::class,
                'choice_label' => 'name',
            ]);
        } else {
            $builder->add('itemCollection', EntityType::class, [
                'class' => ItemCollection::class,
                'choice_label' => 'name',
                'query_builder' => function (ItemCollectionRepository $er) use ($owner): QueryBuilder {
                    return $er->createQueryBuilder('i')
                        ->where('i.owner = :owner')
                        ->setParameter('owner', $owner);
                },
            ]);
        }



        $builder->get('itemCollection')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event): void {
                $form = $event->getForm();
                $collection = $event->getForm()->getData();
                $customItemAttributes = $collection->getCustomItemAttributes();
                $collectionId = $event->getData();
                foreach($form->getParent() as $element) {
                    if (isset($element->getConfig()->getOptions()['row_attr']['collection-id'])) {
                        if ($collectionId != $element->getConfig()->getOptions()['row_attr']['collection-id'] ?? null) {
                            if (str_contains($element->getConfig()->getOptions()['attr']['class'] ?? null, 'pre-set')) {
                                $form->getParent()->remove($element->getName());
                            }
                        }
                    }
                }

                foreach($customItemAttributes->getValues() as $attribute) {
                    $name = $attribute->getName();
                    $name_field = str_replace(' ', '_', $name);
                    $form->getParent()->add($name_field, null, [
                        'label' => $name,
                        'attr' => ['class' => 'dynamic-field'],
                        'mapped' => false,
                    ]);
                }

            }
        );


        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($owner): void {
                $form = $event->getForm();
                $data = $event->getData();

                if ($this->security->isGranted('ROLE_ADMIN')) {
                    $collection = $this->ItemCollectionRepository->findAll()[0];
                } else {
                    $collection = $this->ItemCollectionRepository->findBy(['owner' => $owner])[0];
                }

                $collectionId = $collection->getId();
                if (isset($collection)) {
                    $customItemAttributes = $collection->getCustomItemAttributes()->getValues();
                }

                foreach($customItemAttributes as $attribute) {
                    $name = $attribute->getName();
                    $name_field = str_replace(' ', '_', $name);
                    $form->add($name_field, null, [
                        'label' => $name,
                        'attr' => ['class' => 'dynamic-field pre-set'],
                        'row_attr' => ['pre-set' => 'true', 'collection-id' => $collectionId],
                        'mapped' => false,
                    ]);
                }

            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
