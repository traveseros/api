<?php


namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TrackFileFormControlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', HiddenType::class,[
                'data' => $options['data']['type']->value,
            ])
            ->add('file', FileType::class,[
                'label' => 'Fichero Track',
                'mapped' => false,
                'required' => true,
            ])
            ->add('save', SubmitType::class,[
                'label' => 'Subir fichero'
            ]);
    }
}