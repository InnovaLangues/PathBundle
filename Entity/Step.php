<?php

namespace Innova\PathBundle\Entity;

use Claroline\CoreBundle\Entity\Resource\AbstractResource;
use Innova\PathBundle\Entity\Step2ResourceNode;
use Doctrine\ORM\Mapping as ORM;

/**
 * Step
 *
 * @ORM\Table("innova_step")
 * @ORM\Entity
 */
class Step extends AbstractResource
{

    /**
     * @var integer
     *
     * @ORM\Column(name="stepOrder", type="integer")
     */
    private $stepOrder;

    /**
    * @ORM\ManyToOne(targetEntity="Step")
    */
    protected $parent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="expanded", type="boolean")
     */
    private $expanded;

    /**
     * @var string
     *
     * @ORM\Column(name="instructions", type="text", nullable=true)
     */
    private $instructions = null;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var boolean
     *
     * @ORM\Column(name="withTutor", type="boolean")
     */
    private $withTutor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="withComputer", type="boolean")
     */
    private $withComputer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duration", type="datetime")
     */
    private $duration;

    /**
    * @ORM\ManyToOne(targetEntity="Path", inversedBy="steps")
    */
    protected $path;

    /**
    * @ORM\ManyToOne(targetEntity="StepType", inversedBy="steps")
    */
    protected $stepType;

    /**
    * @ORM\ManyToOne(targetEntity="StepWho", inversedBy="steps")
    */
    protected $stepWho;

    /**
    * @ORM\ManyToOne(targetEntity="StepWhere", inversedBy="steps")
    */
    protected $stepWhere;

    /**
    * @ORM\OneToMany(targetEntity="Step2ResourceNode", mappedBy="step", cascade={"remove"})
    */
    protected $step2ResourceNodes;

    /**
     * Set expanded
     *
     * @param  boolean $expanded
     * @return Step
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;

        return $this;
    }

    /**
     * Get expanded
     *
     * @return boolean
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * Set instructions
     *
     * @param  string $instructions
     * @return Step
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Get instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set image
     *
     * @param  string $image
     * @return Step
     */
    public function setImage($image)
    {
        $this->image= $image;
    
        return $this;
    }
    
    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set withTutor
     *
     * @param  boolean $withTutor
     * @return Step
     */
    public function setWithTutor($withTutor)
    {
        $this->withTutor = $withTutor;

        return $this;
    }

    /**
     * Get withTutor
     *
     * @return boolean
     */
    public function getWithTutor()
    {
        return $this->withTutor;
    }

    /**
     * Set withComputer
     *
     * @param  boolean $withComputer
     * @return Step
     */
    public function setWithComputer($withComputer)
    {
        $this->withComputer = $withComputer;

        return $this;
    }

    /**
     * Get withComputer
     *
     * @return boolean
     */
    public function getWithComputer()
    {
        return $this->withComputer;
    }

    /**
     * Set duration
     *
     * @param  \DateTime $duration
     * @return Step
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return \DateTime
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Get deployable
     *
     * @return boolean
     */
    public function getDeployable()
    {
        return $this->deployable;
    }

    /**
     * Set stepOrder
     *
     * @param  integer $stepOrder
     * @return Step
     */
    public function setStepOrder($stepOrder)
    {
        $this->stepOrder = $stepOrder;

        return $this;
    }

    /**
     * Get stepOrder
     *
     * @return integer
     */
    public function getStepOrder()
    {
        return $this->stepOrder;
    }

  
    /**
     * Set stepType
     *
     * @param  \Innova\PathBundle\Entity\StepType $stepType
     * @return Step
     */
    public function setStepType(\Innova\PathBundle\Entity\StepType $stepType = null)
    {
        $this->stepType = $stepType;

        return $this;
    }

    /**
     * Get stepType
     *
     * @return \Innova\PathBundle\Entity\StepType
     */
    public function getStepType()
    {
        return $this->stepType;
    }

    /**
     * Set stepWho
     *
     * @param  \Innova\PathBundle\Entity\StepWho $stepWho
     * @return Step
     */
    public function setStepWho(\Innova\PathBundle\Entity\StepWho $stepWho = null)
    {
        $this->stepWho = $stepWho;

        return $this;
    }

    /**
     * Get stepWho
     *
     * @return \Innova\PathBundle\Entity\StepWho
     */
    public function getStepWho()
    {
        return $this->stepWho;
    }

    /**
     * Set stepWhere
     *
     * @param  \Innova\PathBundle\Entity\StepWhere $stepWhere
     * @return Step
     */
    public function setStepWhere(\Innova\PathBundle\Entity\StepWhere $stepWhere = null)
    {
        $this->stepWhere = $stepWhere;

        return $this;
    }

    /**
     * Get stepWhere
     *
     * @return \Innova\PathBundle\Entity\StepWhere
     */
    public function getStepWhere()
    {
        return $this->stepWhere;
    }

    /**
     * Set path
     *
     * @param  \Innova\PathBundle\Entity\Path $path
     * @return Step
     */
    public function setPath(\Innova\PathBundle\Entity\Path $path = null)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return \Innova\PathBundle\Entity\Path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set parent
     *
     * @param  \Innova\PathBundle\Entity\Step $parent
     * @return Step
     */
    public function setParent(\Innova\PathBundle\Entity\Step $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Innova\PathBundle\Entity\Step
     */
    public function getParent()
    {
        return $this->parent;
    }

}
