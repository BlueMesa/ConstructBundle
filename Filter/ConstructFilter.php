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

    /**
     * @var string
     */
    protected $sort;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $redirect;

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
            $this->redirect = ($request->get('resolver', 'off') == 'on');
        } else {
            $this->access = 'public';
            $this->sort = 'name';
            $this->order = 'asc';
            $this->type = 'all';
            $this->redirect = false;
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

    /**
     *
     */
    public function getRedirectRoute()
    {
        $currentRoute = $request->attributes->get('_route');

        if ($currentRoute == '') {
            $route = 'bluemesa_flies_vial_list_2';
        } else {
            $pieces = explode('_',$currentRoute);
            if (! is_numeric($pieces[count($pieces) - 1])) {
                $pieces[] = '2';
            }
            $route = ($currentRoute == 'default') ? 'bluemesa_flies_vial_list_2' : implode('_', $pieces);
        }

    }

    public function getRedirectParameters()
    {
        $routeParameters = ($filter instanceof VialFilter) ?
            array(
                'access' => $filter->getAccess(),
                'filter' => $filter->getFilter(),
                'sort' => $filter->getSort(),
                'order' => $filter->getOrder()) :
            array();
    }
}
