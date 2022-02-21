<?php

namespace App\Form\Type;

use App\Entity\Issue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class IssueType
 *
 * @author Thierry Vaudelin <tvaudelin@gmail.com>
 */
class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // We define the choices allowed for the priority field. The ones allowed for the status field are defined in
        // the controllers.

        $builder
            ->add('title', null, [
                'label' => 'Titre'
            ])
            ->add('description')
            ->add('priority', ChoiceType::class, [
                'label' => 'Urgence',
                'choices' => [
                    'faible' => 'low',
                    'normal' => 'normal',
                    'urgent' => 'urgent',
                    'critique' => 'critical'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Etat',
                'choices' => $options['status_list']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
            'status_list' => []
        ]);
    }


}
