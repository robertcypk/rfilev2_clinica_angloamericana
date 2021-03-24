<?php

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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\CallbackTransformer;
use App\Entity\TblHorarios;
use App\Entity\TblAreas;

class TblUsuariosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('apellidos', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('dni', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('email', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('telefono', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('celular', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('password', PasswordType::class, array(
                'label' => 'Password',
                'attr'=>array('class'=>'form-control'),
                'mapped' => false,
                'required' => false
            ))
            ->add('imagen', FileType::class, array(
                'attr'=>array('class'=>'inputfile', 'accept'=> 'image/*'),
                'data_class'=> null,
                'mapped' => false,
                'required' => false
            ))
            ->add('codigopais', ChoiceType::class, array(
                'attr'=>array('class'=>'form-control custom-select select2'),
                'label'=>'Pais',
                'mapped' => false
            ))
            ->add('departamento', ChoiceType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                'mapped' => false
            ))
            ->add('provincia', ChoiceType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                'mapped' => false
            ))
            ->add('distritos', ChoiceType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                'mapped' => false
            ))
            ->add('direccion', TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('status', ChoiceType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                'placeholder'=>'Seleccione un estado de cuenta',
                'choices' => array(
                    'Activo' => 'activo',
                    'Inactivo' => 'inactivo',
                    'pendiente' => 'pendiente'
                )
            ))
            ->add('rol', ChoiceType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                'placeholder'=>'Seleccione un rol',
                'choices' => array(
                    'Administrador'=>'ROLE_ADMIN',
                    'Almacen' => 'ROLE_ALMACEN',
                    'Mensajero' => 'ROLE_MENSAJERO',
                    'Supervisor' => 'ROLE_SUPERVISOR',
                    'Solicitante' => 'ROLE_SOLICITANTE'
                )
            ))
            ->add('idhorario', EntityType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                //'placeholder'=>'Seleccione un horario',
                'class' => TblHorarios::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.idhorario', 'ASC');
                },
                'choice_label' => 'dias',
                'mapped' => false
            ))
            ->add('idarea', EntityType::class, array(
                'attr'=>array('class'=>'form-control custom-select'),
                'placeholder'=>'Seleccione la zona',
                'class' => TblAreas::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.idarea', 'ASC');
                },
                'choice_label' => 'nombre',
                'mapped' => false
            ))
            ->add('registro', HiddenType::class, array(
                'data' => date('Y-m-d H:i:s'),
            ));
        /*$builder->get('idhorario')->addModelTransformer(new CallbackTransformer(
            function ($tagsAsArray) {
                // transform the array to a string
                file_put_contents(__DIR__.'/test.txt', json_encode($tagsAsArray));
                return (int) $tagsAsArray->getIdhorario();
            },
            function ($tagsAsString) {
                // transform the string back to an array
                return 0;
            }
        ));*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TblUsuarios::class,
        ]);
    }
}
