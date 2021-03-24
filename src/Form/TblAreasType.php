<?php

namespace App\Form;

use App\Entity\TblAreas;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TblAreasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array('attr'=>array('class'=>'form-control')))
            ->add('tipo', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array('attr'=>array('class'=>'form-control'),
            'placeholder'=>'Seleccione el área',
            'choices' => array(
                'Admisión' => 'Admisión',
                'Administración' => 'Administración',
                'Almacén Local' => 'Almacén Local',
                'Almacén Externo' => 'Almacén Externo',
                'Consultorios' => 'Consultorios',
                'Cuidados Intensivos' => 'Cuidados Intensivos',
                'Hospitalización' => 'Hospitalización',
                'Imágenes – Ecografías' => 'Imágenes – Ecografías',
                'Laboratorio' => 'Laboratorio',	
                'Recepción' => 'Recepción',     
                'Urgencias' => 'Urgencias',
                'Secretaria' => 'Secretaria',
                'Sala de Atención' => 'Sala de Atención'
            )))
            ->add('sede', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array('attr'=>array('class'=>'form-control'),                
                'placeholder' => 'Seleccione una SEDE',
                'choices' => array(
                    'SEDE CENTRAL' => '01',
                    'LA MOLINA' => '02',
                    'TORRE DE CONSULTORIOS' => '03',
                    'TORRE DR FLECK' => '04'
                )
            ))
            ->add('codzona', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array('attr'=>array('class'=>'form-control'),		
                'label'=>'Codigo de zona',
                'attr'=>array('class'=>'form-control'),
            ))
            ->add('status', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array('attr'=>array('class'=>'form-control'),
            'placeholder'=>'Seleccione un estado',
            'choices' => array(
                'Activo'=>'activo',
                'Inactivo'=> 'inactivo',
            )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TblAreas::class,
        ]);
    }
}
