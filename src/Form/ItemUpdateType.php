<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemAttribute;
use App\Entity\ItemCollection;
use App\Repository\ItemCollectionRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemUpdateType extends AbstractType

{
    public function __construct(private ItemCollectionRepository $ItemCollectionRepository, private Security $security){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $owner = $this->security->getUser();

        $builder
            ->add('name')
            ->add('tags', CollectionType::class, [
                'entry_type' => TagType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('itemCollection', EntityType::class, [
                'class' => ItemCollection::class,
                'choice_label' => 'name',
                'query_builder' => function (ItemCollectionRepository $er) use ($owner): QueryBuilder {
                    return $er->createQueryBuilder('i')
                        ->where('i.owner = :owner')
                        ->setParameter('owner', $owner);
                },
            ])
            ;

        ;

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
                    $name = strtolower(str_replace(' ', '_', $name));
                    $name_label = ucfirst(strtolower(str_replace('_', ' ', $name)));

                    $form->getParent()->add($name, null, [
                        'label' => $name_label,
                        'attr' => ['class' => 'dynamic-field post-set'],
                        'mapped' => false,
                    ]);
                }
            }


        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            $data = $event->getData();
            $itemAttributes = $data->getItemAttributes()->getValues();
            $collectionId = $data->getItemCollection()->getId();
            if(isset($itemAttributes)) {
                foreach($itemAttributes as $attribute) {

                    $name = strtolower(str_replace(' ', '_', $attribute->getName()));
                    if (isset($attribute)) {
                        if (!isset($form[$name])) {
                            $form->add($name, null, [
                                'data' => $attribute->getValue(),
                                'attr' => ['class' => 'dynamic-field pre-set'],
                                'row_attr' => ['pre-set' => 'true', 'collection-id' => $collectionId],
                                'mapped' => false,
                            ]);
                        }
                    }
                }

            }
        });

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
