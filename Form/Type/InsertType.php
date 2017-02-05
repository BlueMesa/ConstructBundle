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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * InsertType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class InsertType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $name = $builder->getName();
        $label = $builder->getOption('label');

        $builder->add($name . 'Orientation', ChoiceType::class, array(
                        'label' => $label . ' orientation',
                        'horizontal'   => true,
                        'required'  => true,
                        'choices' => array(
                            'Unknown' => null,
                            'Sense' => true,
                            'Antisense' => false,
                        )));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return VectorType::class;
    }
}
