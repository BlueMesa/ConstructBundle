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
 * RestrictionLigation class
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\ConstructBundle\Repository\CloningMethodRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RestrictionLigation extends VectorInsert
{
    const NAME = "restriction_ligation";

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $vectorUpstreamSite;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $vectorDownstreamSite;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $insertUpstreamSite;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $insertDownstreamSite;


    /**
     * @return string
     */
    public function getVectorUpstreamSite()
    {
        return $this->vectorUpstreamSite;
    }

    /**
     * @param string $vectorUpstreamSite
     */
    public function setVectorUpstreamSite($vectorUpstreamSite)
    {
        $this->vectorUpstreamSite = $vectorUpstreamSite;
    }

    /**
     * @return string
     */
    public function getVectorDownstreamSite()
    {
        return $this->vectorDownstreamSite;
    }

    /**
     * @param string $vectorDownstreamSite
     */
    public function setVectorDownstreamSite($vectorDownstreamSite)
    {
        $this->vectorDownstreamSite = $vectorDownstreamSite;
    }

    /**
     * @return string
     */
    public function getInsertUpstreamSite()
    {
        return $this->insertUpstreamSite;
    }

    /**
     * @param string $insertUpstreamSite
     */
    public function setInsertUpstreamSite($insertUpstreamSite)
    {
        $this->insertUpstreamSite = $insertUpstreamSite;
    }

    /**
     * @return string
     */
    public function getInsertDownstreamSite()
    {
        return $this->insertDownstreamSite;
    }

    /**
     * @param string $insertDownstreamSite
     */
    public function setInsertDownstreamSite($insertDownstreamSite)
    {
        $this->insertDownstreamSite = $insertDownstreamSite;
    }
}