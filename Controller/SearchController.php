<?php

/*
 * This file is part of the ConstructBundle.
 *
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\ConstructBundle\Controller;


use Bluemesa\Bundle\SearchBundle\Controller\Annotations\Search;
use Bluemesa\Bundle\SearchBundle\Controller\SearchControllerTrait;
use Bluemesa\Bundle\ConstructBundle\Search\SearchQuery;
use Bluemesa\Bundle\ConstructBundle\Form\SearchType;
use Bluemesa\Bundle\ConstructBundle\Form\AdvancedSearchType;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Search controller for the antibody bundle
 *
 * @REST\Prefix("/constructs/search")
 * @REST\NamePrefix("bluemesa_construct_search_")
 * @Search(realm="construct")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends Controller
{
    use SearchControllerTrait;

    /**
     * Render advanced search form
     *
     * @REST\Get("", defaults={"_format" = "html"}))
     * @REST\View()
     *
     * @param  Request  $request
     * @return View
     */
    public function advancedAction(Request $request)
    {
        return $this->getSearchHandler()->handleSearchAction($request);
    }

    /**
     * Render quick search form
     *
     * @REST\View()
     *
     * @param  Request  $request
     * @return View
     */
    public function searchAction(Request $request)
    {
        return $this->getSearchHandler()->handleSearchAction($request);
    }

    /**
     * Handle search result
     *
     * @REST\Get("/result", defaults={"_format" = "html"}))
     * @REST\Post("/result", defaults={"_format" = "html"}))
     * @REST\View()
     *
     * @param  Request $request
     * @return array
     */
    public function resultAction(Request $request)
    {
        return parent::resultAction($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchForm()
    {
        return SearchType::class;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getAdvancedSearchForm()
    {
        return AdvancedSearchType::class;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getSearchRealm()
    {
        return 'bluemesa_constructs';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function createSearchQuery($advanced = false)
    {
        $searchQuery = new SearchQuery($advanced);
        $searchQuery->setTokenStorage($this->getTokenStorage());
        $searchQuery->setAuthorizationChecker($this->getAuthorizationChecker());

        return $searchQuery;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadSearchQuery()
    {
        $searchQuery = parent::loadSearchQuery();
        
        if (! $searchQuery instanceof SearchQuery) {
            throw $this->createNotFoundException();
        }
        
        $searchQuery->setTokenStorage($this->getTokenStorage());
        
        return $searchQuery;
    }
}
