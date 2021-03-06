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


interface SizedMethodInterface
{
    /**
     * Get construct size
     *
     * @return float|null
     */
    public function getSize();
}
