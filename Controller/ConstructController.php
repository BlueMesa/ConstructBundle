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
use Bluemesa\Bundle\CrudBundle\Controller\Annotations as CRUD;
use Bluemesa\Bundle\CrudBundle\Controller\CrudControllerTrait;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ConstructController
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * @REST\Prefix("/constructs")
 * @REST\NamePrefix("bluemesa_construct_")
 * @CRUD\Controller()
 */
class ConstructController extends Controller
{
    use CrudControllerTrait;

    /**
     * @CRUD\Action("index")
     * @CRUD\Filter("Bluemesa\Bundle\ConstructBundle\Filter\ConstructFilter", redirectRoute="bluemesa_construct_index_type_sort")
     * @REST\View()
     * @REST\Get("", defaults={"_format" = "html"}))
     * @REST\Get("/{type}", name="_type",
     *     requirements={"type" = "plasmid|genomic|synthetic|all"},
     *     defaults={"_format" = "html"}))
     * @REST\Get("/sort/{sort}/{order}", name="_sort",
     *     requirements={"sort" = "id|name|type"},
     *     defaults={"_format" = "html", "type" = "all", "sort" = "id", "order" = "asc"})
     * @REST\Get("/{type}/sort/{sort}/{order}", name="_type_sort",
     *     requirements={"type" = "plasmid|genomic|synthetic|all", "sort" = "id|name|type"},
     *     defaults={"_format" = "html", "type" = "all", "order" = "asc"})
     * @Paginate(25)
     *
     * @param  Request     $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        return $this->getCrudHandler()->handle($request);
    }

    /**
     * @CRUD\Action("show")
     * @REST\View()
     * @REST\Get("/{id}", requirements={"id"="\d+"}, defaults={"_format" = "html"})
     *
     * @param  Request     $request
     * @return View
     */
    public function showAction(Request $request)
    {
        return $this->getCrudHandler()->handle($request);
    }

    /**
     * @CRUD\Action("new")
     * @REST\View()
     * @REST\Route("/new", methods={"GET", "POST"}, defaults={"_format" = "html"})
     * @REST\Post("", name="_rest", defaults={"_format" = "html"})
     *
     * @param  Request     $request
     * @return View
     */
    public function newAction(Request $request)
    {
        return $this->getCrudHandler()->handle($request);
    }

    /**
     * @CRUD\Action("edit")
     * @REST\View()
     * @REST\Route("/{id}/edit", methods={"GET", "PUT"}, requirements={"id"="\d+"}, defaults={"_format" = "html"})
     * @REST\Put("/{id}", name="_rest", requirements={"id"="\d+"}, defaults={"_format" = "html"})
     *
     * @param  Request     $request
     * @return View
     */
    public function editAction(Request $request)
    {
        return $this->getCrudHandler()->handle($request);
    }

    /**
     * @CRUD\Action("delete")
     * @REST\View()
     * @REST\Route("/{id}/delete", methods={"DELETE"}, requirements={"id"="\d+"}, defaults={"_format" = "html"})
     * @REST\Delete("/{id}", name="_rest", requirements={"id"="\d+"}, defaults={"_format" = "html"})
     *
     * @param  Request     $request
     * @return View
     */
    public function deleteAction(Request $request)
    {
        return $this->getCrudHandler()->handle($request);
    }

    /**
     * @REST\Post("/_ajax/method/form", defaults={"_format" = "html"}, requirements={"_format" = "html"})
     * @REST\RequestParam(name="entity")
     * @REST\RequestParam(name="form")
     *
     * @param  Request      $request
     * @param  ParamFetcher $paramFetcher
     * @return Response
     */
    public function ajaxFormAction(Request $request, ParamFetcher $paramFetcher)
    {
        $entityClass = $paramFetcher->get('entity');
        $formClass = $paramFetcher->get('form');

        $data = array('method' => new $entityClass());
        $builder = $this->container->get('form.factory')
            ->createNamedBuilder('construct', FormType::class, $data)
            ->add('method', $formClass, array(
                'horizontal_label_offset_class' => null,
                'horizontal_input_wrapper_class' => 'construct_method_placeholder',
                'label_render'      => false,
                'widget_form_group' => false
            ))
            ;

        return new Response($this->renderView('BluemesaConstructBundle:Construct:ajaxform.html.twig',
            array('form' => $builder->getForm()->createView())));
    }
}
