<?php

/*
 * This file is part of the ConstructBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\ConstructBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Bluemesa\Bundle\AclBundle\Filter\SecureListFilter;
use Bluemesa\Bundle\CoreBundle\Filter\SortFilterInterface;

/**
 * ConstructFilter
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class ConstructFilter extends SecureListFilter implements SortFilterInterface {
    
    protected $sort;
    
    protected $order;

    protected $type;

    /**
     * {@inheritdoc}
     */
    public function __construct(Request $request = null,
                                AuthorizationCheckerInterface $authorizationChecker = null,
                                TokenStorageInterface $tokenStorage = null)
    {
        parent::__construct($request, $authorizationChecker, $tokenStorage);
        if (null !== $request) {
            $this->setAccess($request->get('access', 'public'));
            $this->setSort($request->get('sort', 'name'));
            $this->setOrder($request->get('order', 'asc'));
            $this->setType($request->get('type', 'all'));
        } else {
            $this->access = 'public';
            $this->sort = 'name';
            $this->order = 'asc';
            $this->type = 'all';
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSort() {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sort) {
        $this->sort = $sort;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($order) {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
