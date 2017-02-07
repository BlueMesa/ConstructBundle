<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Bluemesa\Bundle\ConstructBundle\Entity;

use Bluemesa\Bundle\AclBundle\Entity\SecureEntity;
use Bluemesa\Bundle\CoreBundle\Entity\MutableIdEntityInterface;
use Bluemesa\Bundle\CoreBundle\Entity\NamedTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * Construct class
 *
 * @ORM\Entity(repositoryClass="Bluemesa\Bundle\ConstructBundle\Repository\ConstructRepository")
 * @Serializer\ExclusionPolicy("all")
 * @Vich\Uploadable
 * @UniqueEntity("id")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Construct extends SecureEntity implements MutableIdEntityInterface
{ 
    use NamedTrait;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank(message = "Type must be specified")
     *
     * @var string
     */
    protected $type;
    
    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     *
     * @var float
     */
    protected $size;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @Serializer\Expose
     *
     * @var array
     */
    protected $resistances;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     * 
     * @var string
     */
    protected $notes;

    /**
     * @ORM\ManyToMany(targetEntity="ConstructTag", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="entity_tags",
     *     joinColumns={@ORM\JoinColumn(name="entity_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     * @Serializer\Expose
     *
     * @var Collection
     */
    protected $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     *
     * @var string
     */
    protected $vendor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Assert\Url()
     *
     * @var string
     */
    protected $infoURL;

    /**
     * @ORM\OneToMany(targetEntity="ConstructTube", mappedBy="construct", cascade={"persist"}, fetch="EXTRA_LAZY")
     *
     * @var Collection
     */
    protected $tubes;

    /**
     * @ORM\OneToOne(targetEntity="CloningMethod", mappedBy="construct",
     *     cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var CloningMethod
     */
    protected $method;

    /**
     * @Vich\UploadableField(mapping="sequence_file", fileNameProperty="sequenceName")
     *
     * @var File
     */
    protected $sequenceFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $sequenceName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var array
     */
    public static $types = array(
        'Plasmid' => 'plasmid',
        'Fosmid' => 'fosmid',
        'BAC' => 'BAC',
        'Cosmid' => 'cosmid',
        'Linear DNA' => 'linear',
    );

    /**
     * @var array
     */
    public static $antibiotics = array(
        'Ampicillin' => 'Amp',
        'Blasticidin' => 'Bla',
        'Bleocin' => 'Ble',
        'Chroramphenicol'  => 'Cm',
        'Coumermycin' => 'Com',
        'D-cycloserine' => 'DCS',
        'Erythromycin' => 'Ery',
        'Geneticin' => 'Gen',
        'Gentamycin' => 'Gta',
        'Hygromycin' => 'Hgr',
        'Kanamycin' => 'Kan',
        'Kasugamycin' => 'Kas',
        'Nalidixic acid' => 'Nal',
        'Neomycin' => 'Neo',
        'Penicillin' => 'Pen',
        'Puromycin' => 'Pur',
        'Rifampicin' => 'Rif',
        'Spectinomycin' => 'Spe',
        'Streptomycin' => 'Str',
        'Tetracycline' => 'Tet',
        'Triclosan' => 'Tri',
        'Trimethoprim' => 'Tmp',
        'Zeocin' => 'Zeo',
    );


    /**
     * Construct Antibody
     * 
     */
    public function __construct()
    {
        $this->tubes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->type = 'plasmid';
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get type
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set type
     * 
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Get size
     * 
     * @return float
     */
    public function getSize()
    {
        $method = $this->getMethod();
        if ($method instanceof SizedMethodInterface) {
            $size = $method->getSize();
        } else {
            $size = null;
        }

        return ($size !== null) ? $method->getSize() : $this->size;
    }

    /**
     * Set size
     * 
     * @param float $size
     */
    public function setSize($size) {
        $this->size = $size;
    }

    /**
     * Is construct size computed from cloning method
     *
     * @return bool
     */
    public function isSizeComputed()
    {
        $method = $this->getMethod();
        if ($method instanceof SizedMethodInterface) {
            $size = $method->getSize();
        } else {
            $size = null;
        }

        return ($size !== null);
    }

    /**
     * @return array
     */
    public function getResistances()
    {
        return $this->resistances;
    }

    /**
     * @return string
     */
    public function getResistancesText()
    {
        $antibiotics = array_flip(static::$antibiotics);
        $string = "";

        foreach ($this->resistances as $resistance) {
            if (array_key_exists($resistance, $antibiotics)) {
                if (! empty($string)) {
                    $string .= ", ";
                }
                $string .= $antibiotics[$resistance] .  " (" . $resistance . ")";
            }
        }

        return $string;
    }

    /**
     * @param array $resistances
     */
    public function setResistances($resistances)
    {
        $this->resistances = $resistances;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
    
    /**
     * Set notes
     *
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * Get tags
     *
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add tag
     *
     * @param ConstructTag $tag
     */
    public function addTag(ConstructTag $tag)
    {
        $tags = $this->getTags();
        if (null !== $tag) {
            if (! $tags->contains($tag)) {
                $tags->add($tag);
            }
        }
    }

    /**
     * Remove tag
     *
     * @param ConstructTag $tag
     */
    public function removeTag(ConstructTag $tag)
    {
        $this->getTags()->removeElement($tag);
    }

    /**
     * Get vendor
     *
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set vendor
     *
     * @param string
     */
    public function setVendor($stockVendor)
    {
        $this->vendor = $stockVendor;
    }

    /**
     * Get info URL
     *
     * @return string
     */
    public function getInfoURL()
    {
        return $this->infoURL;
    }

    /**
     * Set info URL
     *
     * @param string  $infoURL
     */
    public function setInfoURL($infoURL)
    {
        $this->infoURL = $infoURL;
    }
    
    /**
     * Get tubes
     *
     * @return ArrayCollection
     */
    public function getTubes()
    {
        return $this->tubes;
    }
    
    /**
     * Add tube
     *
     * @param ConstructTube $tube
     */
    public function addTube(ConstructTube $tube)
    {
        $tubes = $this->getTubes();
        if (null !== $tube) {
            if (! $tubes->contains($tube)) {
                $tubes->add($tube);
            }
            if ($tube->getConstruct() !== $this) {
                $tube->setConstruct($this);
            }
        }
    }

    /**
     * Remove tube
     *
     * @param ConstructTube $tube
     */
    public function removeTube(ConstructTube $tube)
    {
        $this->getTubes()->removeElement($tube);
    }

    /**
     * @return CloningMethod
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param CloningMethod $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
        if ($method->getConstruct() !== $this) {
            $method->setConstruct($this);
        }
    }

    /**
     * @return File
     */
    public function getSequenceFile()
    {
        return $this->sequenceFile;
    }

    /**
     * @param File $sequenceFile
     */
    public function setSequenceFile(File $sequenceFile = null)
    {
        $this->sequenceFile = $sequenceFile;

        if ($sequenceFile) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return string
     */
    public function getSequenceName()
    {
        return $this->sequenceName;
    }

    /**
     * @param string $sequenceName
     */
    public function setSequenceName($sequenceName)
    {
        $this->sequenceName = $sequenceName;
    }
}
