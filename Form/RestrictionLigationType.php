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

use Bluemesa\Bundle\ConstructBundle\Entity\RestrictionLigation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RestrictionLigationType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RestrictionLigationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vector', Type\VectorType::class, array(
                        'label'     => 'Vector'))
                ->add('insert', Type\InsertType::class, array(
                        'label'     => 'Insert'))
                ->add('vectorUpstreamSite', TextType::class, array(
                        'label'     => 'Upstream vector site'))
                ->add('vectorDownstreamSite', TextType::class, array(
                        'label'     => 'Downstream vector site'))
                ->add('insertUpstreamSite', TextType::class, array(
                        'label'     => 'Upstream insert site'))
                ->add('insertDownstreamSite', TextType::class, array(
                        'label'     => 'Downstream insert site'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RestrictionLigation::class,
        ));
    }
}
