<?php

/*
 * This file is part of the ConstructBundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\ConstructBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * TopoTa class
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\ConstructBundle\Repository\CloningMethodRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class TopoTa extends VectorInsert
{
    const NAME = "topo_ta";

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose
     *
     * @var boolean
     */
    protected $blunt;


    /**
     * @return boolean
     */
    public function isBlunt()
    {
        return $this->blunt;
    }

    /**
     * @param boolean $blunt
     */
    public function setBlunt($blunt)
    {
        $this->blunt = $blunt;
    }
}
