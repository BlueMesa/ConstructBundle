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


use Bluemesa\Bundle\CoreBundle\Controller\Annotations\Paginate;
use Bluemesa\Bundle\SearchBundle\Controller\Annotations\Search;
use Bluemesa\Bundle\SearchBundle\Controller\SearchControllerTrait;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Search controller for the antibody bundle
 *
 * @REST\Prefix("/constructs/search")
 * @REST\NamePrefix("bluemesa_construct_search_")
 * @Search(realm="bluemesa_constructs")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SearchController extends Controller
{
    use SearchControllerTrait;

    /**
     * Render the search form
     *
     * @REST\Get("", defaults={"_format" = "html"}))
     * @REST\View()
     *
     * @param  Request  $request
     * @return View
     */
    public function searchAction(Request $request)
    {
        return $this->getSearchHandler()->handle($request);
    }

    /**
     * Handle search result
     *
     * @REST\Route("/result", methods={"POST", "GET"}, defaults={"_format" = "html"}))
     * @REST\View()
     * @Paginate(25)
     *
     * @param  Request $request
     * @return View
     */
    public function resultAction(Request $request)
    {
        return $this->getSearchHandler()->handle($request);
    }
}
