<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('summary')
            ->add('priority', ChoiceType::class, [
                'choices'  => [
                    'Low' => 'Low',
                    'Average' => 'Average',
                    'High' => 'High',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Opened' => 'Opened',
                    'In progress' => 'In progress',
                    'Fixed' => 'Fixed',
                    'Rejected' => 'Rejected',
                ],
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
