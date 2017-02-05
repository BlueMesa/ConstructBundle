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
 * VectorInsert class
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\ConstructBundle\Repository\CloningMethodRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class VectorInsert extends CloningMethod implements SizedMethodInterface
{
    const NAME = "vector_insert";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $vector;

    /**
     * @ORM\Column(name="c_insert", type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $insert;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     *
     * @var float
     */
    protected $vectorSize;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     *
     * @var float
     */
    protected $insertSize;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose
     *
     * @var boolean
     */
    protected $insertOrientation;


    /**
     * @return string
     */
    public function getVector()
    {
        return $this->vector;
    }

    /**
     * @param string $vector
     */
    public function setVector($vector)
    {
        $this->vector = $vector;
    }

    /**
     * @return string
     */
    public function getInsert()
    {
        return $this->insert;
    }

    /**
     * @param string $insert
     */
    public function setInsert($insert)
    {
        $this->insert = $insert;
    }

    /**
     * @return float
     */
    public function getVectorSize()
    {
        return $this->vectorSize;
    }

    /**
     * @param float $vectorSize
     */
    public function setVectorSize($vectorSize)
    {
        $this->vectorSize = $vectorSize;
    }

    /**
     * @return float
     */
    public function getInsertSize()
    {
        return $this->insertSize;
    }

    /**
     * @param float $insertSize
     */
    public function setInsertSize($insertSize)
    {
        $this->insertSize = $insertSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $vector = $this->getVectorSize();
        $insert = $this->getInsertSize();

        return ((null !== $vector)&&(null !== $insert)) ? ($vector + $insert) : null;
    }


    /**
     * @return boolean
     */
    public function getInsertOrientation()
    {
        return $this->insertOrientation;
    }

    /**
     * @param boolean $insertOrientation
     */
    public function setInsertOrientation($insertOrientation)
    {
        $this->insertOrientation = $insertOrientation;
    }

    /**
     * @return boolean
     */
    public function getInsertOrientationtext()
    {
        if (empty($this->insertOrientation)) {
            return 'Unknown';
        } else {
            return $this->insertOrientation ? 'Positive' : 'Negative';
        }
    }
}
