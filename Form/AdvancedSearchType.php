<?php

/*
 * This file is part of the ConstructBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\ConstructBundle\Form;

use Bluemesa\Bundle\ConstructBundle\Search\SearchQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AdvancedSearchType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AdvancedSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('terms', TextType::class, array(
                'label' => 'Include terms',
                'required' => false,
                'attr' => array(
                    'class' => 'input-block-level',
                    'placeholder' => 'separate terms with space'
                )
            )
        )->add('excluded', TextType::class, array(
                'label' => 'Exclude terms',
                'required' => false,
                'attr' => array(
                    'class' => 'input-block-level',
                    'placeholder' => 'separate terms with space'
                )
            )
        )->add('filter', ChoiceType::class, array(
                'label' => 'Scope',
                'choices' => array(
                    'primary' => 'Primary',
                    'secondary' => 'Secondary'
                ),
                'expanded' => true,
                'placeholder' => 'All',
                'empty_data' => 'all',
                'required' => false
            )
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => SearchQuery::class
            )
        );
    }
}
