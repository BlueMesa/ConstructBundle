<?php

/*
 * This file is part of the ConstructBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\ConstructBundle\Repository;

use Bluemesa\Bundle\ConstructBundle\Filter\ConstructFilter;
use Bluemesa\Bundle\CoreBundle\Filter\SortFilterInterface;
use Bluemesa\Bundle\SearchBundle\Repository\SearchableRepository;
use Bluemesa\Bundle\SearchBundle\Search\SearchQueryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * ConstructRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ConstructRepository extends SearchableRepository
{
    public function createIndexQueryBuilder()
    {
        $qb = parent::createIndexQueryBuilder();

        if ($this->filter instanceof ConstructFilter) {
            switch ($this->filter->getType()) {
                case 'all':
                    break;
                case 'plasmids':
                    $qb->andWhere('e.type == :type')
                        ->setParameter('type', 'plasmid');
                    break;
                case 'genomic':
                    $qb->andWhere($qb->expr()->orX(
                                $qb->expr()->eq('e.type', ':type_1'),
                                $qb->expr()->eq('e.type', ':type_2'),
                                $qb->expr()->eq('e.type', ':type_3')
                        ))
                        ->setParameters(new ArrayCollection(array(
                            new Parameter('type_1', 'fosmid'),
                            new Parameter('type_2', 'cosmid'),
                            new Parameter('type_3', 'BAC')
                        )));
                    break;
                case 'synthetic':
                    $qb->andWhere('e.type == :type')
                        ->setParameter('type', 'linear');
                    break;
            }
        }

        if ($this->filter instanceof SortFilterInterface) {
            $order = ($this->filter->getOrder() == 'desc') ? 'DESC' : 'ASC';
            switch ($this->filter->getSort()) {
                case 'name':
                    $qb->orderBy('e.name', $order);
                    break;
                case 'type':
                    $qb->orderBy('e.type', $order);
                    break;
            }
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchFields(SearchQueryInterface $search)
    {
        $fields = array('e.name');
        
        return $fields;
    }
}
