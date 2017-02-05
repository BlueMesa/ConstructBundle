<?php

/*
 * This file is part of the ConstructBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\ConstructBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * VectorType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VectorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $name = $builder->getName();
        $label = $builder->getOption('label');

        $builder->add($name, TextType::class, array(
                        'label'     => $label,
                        'horizontal'   => true))
                ->add($name . 'Size', NumberType::class, array(
                        'label'     => $label . ' size',
                        'horizontal'   => true,
                        'attr'      => array('class' => 'input-small'),
                        'widget_addon_append' => array(
                            'text' => 'kb',
                        )));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data'      => true,
            'horizontal'        => false,
            'label_render'      => false,
            'widget_form_group' => false
        ));
    }
}
