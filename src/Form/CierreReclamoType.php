<?php

namespace App\Form;

use App\Entity\Reclamo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CierreReclamoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('causa', ChoiceType::class, [
                'choices' => [
                    'Diagnóstico' => 'diagnostico',
                    'Corte de energía' => 'corte_energia',
                    'Caño de agua roto' => 'caño_agua_roto',
                ],
                 'placeholder' => 'Seleccione una causa...',

                'required' => true,
            ])
        ->add('guardar', SubmitType::class, [
            'label' => 'Guardar',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamo::class,
        ]);
    }
}
