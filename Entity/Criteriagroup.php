<?php

namespace Innova\PathBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Criteriagroup
 *
 * @ORM\Table(name="innova_stepcondition_criteriagroup")
 * @ORM\Entity(repositoryClass="Innova\PathBundle\Repository\CriteriagroupRepository")
 */
class Criteriagroup implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Depth of the criteriagroup in the Condition
     * @var integer
     *
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;

    /**
     * Parent criteriagroup
     * @var \Innova\PathBundle\Entity\Criteriagroup
     *
     * @ORM\ManyToOne(targetEntity="Criteriagroup", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * Children criteriagroup
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Criteriagroup", mappedBy="parent", indexBy="id", cascade={"persist", "remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    protected $children;

    /**
     * StepCondition
     * @var \Innova\PathBundle\Entity\StepCondition
     *
     * @ORM\ManyToOne(targetEntity="Innova\PathBundle\Entity\StepCondition", inversedBy="criteriagroups")
     */
    protected $stepcondition;

    /**
     * Criteria linked to the criteriagroup
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Innova\PathBundle\Entity\Criterion", mappedBy="criteriagroup", indexBy="id", cascade={"persist", "remove"})
     */
    protected $criteria;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->children     = new ArrayCollection();
        $this->criteria     = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lvl
     * @param integer $lvl
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }
    /**
     * Set parent
     * @param  \Innova\PathBundle\Entity\Criteriagroup $parent
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function setParent(Criteriagroup $parent = null)
    {
        if ($parent != $this->parent) {
            $this->parent = $parent;
            $parent->addChild($this);
        }

        return $this;
    }

    /**
     * Get parent
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get children of the step
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return !empty($this->children) && 0 < $this->children->count();
    }

    /**
     * Add new child to the criteriagroup
     * @param \Innova\PathBundle\Entity\Criteriagroup $criteriagroup
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function addChild(Criteriagroup $criteriagroup)
    {
        if (!$this->children->contains($criteriagroup)) {
            $this->children->add($criteriagroup);
            $criteriagroup->setParent($this);
        }

        return $this;
    }

    /**
     * Remove a step from children
     * @param \Innova\PathBundle\Entity\Criteriagroup $criteriagroup
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function removeChild(Criteriagroup $criteriagroup)
    {
        if ($this->children->contains($criteriagroup)) {
            $this->children->removeElement($criteriagroup);
            $criteriagroup->setParent(null);
        }

        return $this;
    }

    /**
     * Set stepcondition
     * @param  \Innova\PathBundle\Entity\StepCondition $stepcondition
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function setStepCondition(Stepcondition $stepcondition = null)
    {
        $this->stepcondition = $stepcondition;

        return $this;
    }

    /**
     * Get stepcondition
     * @return \Innova\PathBundle\Entity\StepCondition
     */
    public function getStepCondition()
    {
        return $this->stepcondition;
    }

    /**
     * Add criterion
     * @param  \Innova\PathBundle\Entity\Criterion $criterion
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function addCriterion(Criterion $criterion)
    {
        if (!$this->criteria->contains($criterion)) {
            $this->criteria->add($criterion);
        }

        $criterion->setCriteriagroup($this);

        return $this;
    }

    /**
     * Remove criterion
     * @param \Innova\PathBundle\Entity\Criterion $criterion
     * @return \Innova\PathBundle\Entity\Criteriagroup
     */
    public function removeCriterion(Criterion $criterion)
    {
        if ($this->criteria->contains($criterion)) {
            $this->criteria->removeElement($criterion);
        }

        $criterion->setCriteriagroup(null);

        return $this;
    }

    /**
     * Get criteria
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    function jsonSerialize()
    {
        // Initialize data array
        $jsonArray = array (
            'id'                => $this->id,               // A local ID for the criteriagroup in the condition
            'lvl'               => $this->lvl,              // The depth of the criteriagroup in the condition structure
            'children'          => array(),
            'criterion'         => array(),                 //list of criteria attached to a criteriagroup
        );

        // Get step children
        if (!empty($this->children)) {
            $jsonArray['children'] = array_values($this->children->toArray());
        }

        return $jsonArray;
    }

    /**
     * Add criterium
     *
     * @param \Innova\PathBundle\Entity\Criterion $criterium
     *
     * @return Criteriagroup
     */
    public function addCriterium(\Innova\PathBundle\Entity\Criterion $criterium)
    {
        $this->criteria[] = $criterium;

        return $this;
    }

    /**
     * Remove criterium
     *
     * @param \Innova\PathBundle\Entity\Criterion $criterium
     */
    public function removeCriterium(\Innova\PathBundle\Entity\Criterion $criterium)
    {
        $this->criteria->removeElement($criterium);
    }
}