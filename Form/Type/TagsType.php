<?php

/*
 * This file is part of the Construct Bundle.
 *
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\ConstructBundle\Form\Type;

use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Bluemesa\Bundle\CoreBundle\Entity\NamedInterface;
use Bluemesa\Bundle\CoreBundle\Entity\NamedTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


/**
 * Tags input control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\FormType
 */
class TagsType extends EntityType
{
    /**
     * Construct TagsType
     *
     * @DI\InjectParams({
     *     "registry" = @DI\Inject("doctrine")
     * })
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->addEventListener(
                    FormEvents::PRE_SUBMIT,
                    array($this, 'onPreSubmit'));
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();

        if ((! is_a($options['class'], Entity::class, true))&&(! is_a($options['class'], NamedTrait::class, true))) {
            return;
        }

        $data = $event->getData();
        $entities = array();

        // Check if all submitted values are valid IDs
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (!is_numeric($value)) {
                    /** @var NamedTrait $entity */
                    $entity = new $options['class']();
                    $entity->setName($value);
                    $entities[$key] = $entity;
                }
            }
        }

        // We have found some entities with missing IDs
        if(count($entities) > 0) {
            $em = $this->registry->getManagerForClass($options['class']);
            $repository = $em->getRepository($options['class']);

            // Check if we are inside a transaction and begin one if necessary
            $commit = false;
            if ($em instanceof EntityManagerInterface) {
                if (! $em->getConnection()->isTransactionActive()) {
                    $em->beginTransaction();
                    $commit = true;
                }
            }

            // Persist or load entities with missing IDs
            foreach($entities as $key => $value) {
                $entity = $repository->findOneByName($value->getName());
                if (null !== $entity) {
                    $entities[$key] = $entity;
                } else {
                    $em->persist($value);
                }
            }
            $em->flush();

            // Commit transaction if necessary
            if ($commit) {
                $em->commit();
            }

            // Update data with the correct entity IDs
            foreach($entities as $key => $value) {
                $data[$key] = $value->getId();
            }

            dump($data);

            $event->setData($data);
        }
    }
}
