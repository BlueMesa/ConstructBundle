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
use Bluemesa\Bundle\ConstructBundle\Search\SearchQuery;
use Bluemesa\Bundle\CoreBundle\Filter\SortFilterInterface;
use Bluemesa\Bundle\SearchBundle\Repository\SearchableRepository;
use Bluemesa\Bundle\SearchBundle\Search\SearchQueryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Parameter;

/**
 * ConstructRepository
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ConstructRepository extends SearchableRepository
{
    /**
     * {@inheritdoc}
     */
    public function createIndexQueryBuilder()
    {
        $qb = parent::createIndexQueryBuilder();

        if ($this->filter instanceof ConstructFilter) {
            $expr = $this->getConstructFilterExpression($this->filter->getType());
            if (null !== $expr) {
                $qb->andWhere($expr);
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
    protected function getSearchExpression(SearchQueryInterface $search)
    {
        $expr = parent::getSearchExpression($search);

        if (($search instanceof SearchQuery)&&($expr instanceof Andx)) {
            $expr->add($this->getConstructFilterExpression($search->getFilter()));
        }

        return $expr;
    }

    /**
     * @param  string $type
     * @return Expr
     */
    protected function getConstructFilterExpression($type)
    {
        $eb = $this->getEntityManager()->getExpressionBuilder();

        switch($type) {
            case 'all':
                $expr = null;
                break;
            case 'plasmids':
                $expr = $eb->eq('e.type', 'plasmid');
                break;
            case 'genomic':
                $expr = $eb->orX(
                    $eb->eq('e.type', '\'fosmid\''),
                    $eb->eq('e.type', '\'cosmid\''),
                    $eb->eq('e.type', '\'BAC\'')
                );
                break;
            case 'linear':
                $expr = $eb->eq('e.type', '\'linear\'');
                break;
            default:
                $expr = $eb->eq('e.type', '\'' . $type . '\'');
        }

        return $expr;
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
