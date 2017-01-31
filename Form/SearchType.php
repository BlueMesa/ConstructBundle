<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Bluemesa\Bundle\ConstructBundle\Form;

use Bluemesa\Bundle\ConstructBundle\Search\SearchQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SearchType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['simple']) {
            $builder->add('terms', TextType::class, array(
                    'required' => false,
                    'horizontal' => false,
                    'label_render' => false,
                    'attr'     => array(
                        'form'        => 'search-form',
                        'placeholder' => 'Search'
                    )
                )
            )->add('filter', HiddenType::class, array('required' => false));
        } else {
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
                        'Plasmids'   => 'plasmid',
                        'Genomic constructs' => 'genomic',
                        'Linear DNA' => 'linear',
                    ),
                    'expanded' => true,
                    'placeholder' => 'All',
                    'empty_data' => 'all',
                    'required' => false
                )
            );
        }

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_merge($view->vars, array(
            'simple' => $options['simple']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => SearchQuery::class,
                'simple' => false
            )
        );
    }
}
