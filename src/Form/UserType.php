<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\TblUsuarios;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre')
        ->add('apellidos')
        ->add('email')
        ->add('dni')
        ->add('telefono')
        ->add('celular')
        ->add("imagen")
        ->add("Codigopais")
        ->add("departamento")
        ->add("provincia")
        ->add("distritos", ChoiceType::class, array(
            'attr'=>array('class'=>'form-control')
        ))
        ->add("direccion", TextType::class, array(
            'attr'=>array('class'=>'form-control')
        ))
        ->add("rol")
        ->add("idarea", ChoiceType::class, array(
            'label' => 'area',
            'attr'=>array('class'=>'form-control'),
            'choices' => array('Default'=>'1'),
            'choice_attr' => function ($val, $key, $index) {
                return ['class' => 'attending_'.strtolower($key)];
            },
        ))
        ->add("idhorario", ChoiceType::class, array(
            'label' => 'horario',
            'attr'=>array('class'=>'form-control'),
            'choices' => array('Default'=>'0'),
            'choice_attr' => function ($val, $key, $index) {
                return ['class' => 'attending_'.strtolower($key)];
            },
        ))
        ->add('password')
            ->add('Registrar', SubmitType::class, array(
                'attr' => array('class' => 'form-control'),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TblUsuarios::class
        ));
    }
}
