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
 * Gateway class
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\ConstructBundle\Repository\CloningMethodRepository")
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Gateway extends VectorInsert
{
    const NAME = "gateway";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $destinationVector;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     *
     * @var float
     */
    protected $destinationVectorSize;


    /**
     * @return string
     */
    public function getDestinationVector()
    {
        return $this->destinationVector;
    }

    /**
     * @param string $destinationVector
     */
    public function setDestinationVector($destinationVector)
    {
        $this->destinationVector = $destinationVector;
    }

    /**
     * @return float
     */
    public function getDestinationVectorSize()
    {
        return $this->destinationVectorSize;
    }

    /**
     * @param float $destinationVectorSize
     */
    public function setDestinationVectorSize($destinationVectorSize)
    {
        $this->destinationVectorSize = $destinationVectorSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $vector = (null !== $this->getDestinationVector()) ? $this->getDestinationVectorSize() : $this->getVectorSize();
        $insert = $this->getInsertSize();

        return ((null !== $vector)&&(null !== $insert)) ? ($vector + $insert) : null;
    }
}
