<?php

namespace Innova\PathBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Claroline\CoreBundle\Entity\Resource\Activity;
use Claroline\CoreBundle\Entity\Activity\ActivityParameters;

use Innova\PathBundle\Entity\StepCondition;

/**
 * Step
 *
 * @ORM\Table("innova_step")
 * @ORM\Entity
 */
class Step implements \JsonSerializable
{
    const DEFAULT_NAME = 'Step';

    /**
     * Unique identifier of the step
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Activity of this step
     * @var \Claroline\CoreBundle\Entity\Resource\Activity
     *
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\Resource\Activity", cascade={"persist"})
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $activity;

    /**
     * Parameters for this step
     * @var \Claroline\CoreBundle\Entity\Activity\ActivityParameters
     *
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\Activity\ActivityParameters", cascade={"all"})
     * @ORM\JoinColumn(name="parameters_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parameters;

    /**
     * Min height of Activity
     * @var integer
     *
     * @ORM\Column(name="activity_height", type="integer")
     */
    protected $activityHeight;

    /**
     * Depth of the step in the Path
     * @var integer
     *
     * @ORM\Column(name="lvl", type="integer")
     */
    protected $lvl;

    /**
     * Order of the steps relative to his siblings in the path
     * @var integer
     *
     * @ORM\Column(name="step_order", type="integer")
     */
    protected $order;

    /**
     * Parent step
     * @var \Innova\PathBundle\Entity\Step
     * 
     * @ORM\ManyToOne(targetEntity="Step", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * Children steps
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="Step", mappedBy="parent", indexBy="id", cascade={"persist", "remove"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    protected $children;

    /**
     * Path
     * @var \Innova\PathBundle\Entity\Path\Path
     * 
     * @ORM\ManyToOne(targetEntity="Innova\PathBundle\Entity\Path\Path", inversedBy="steps")
     */
    protected $path;

    /**
     * Condition
     * @var \Innova\PathBundle\Entity\StepCondition
     *
     * @ORM\OneToOne(targetEntity="Innova\PathBundle\Entity\StepCondition", mappedBy="step", cascade={"persist", "remove"})
     */
    protected $condition;

    /**
     * Inherited resources
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Innova\PathBundle\Entity\InheritedResource", mappedBy="step", indexBy="id", cascade={"persist", "remove"})
     * @ORM\OrderBy({"lvl" = "ASC"})
     */
    protected $inheritedResources;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->children           = new ArrayCollection();
        $this->inheritedResources = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getId() . ' - ' . $this->getName();
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get activity
     * @return \Claroline\CoreBundle\Entity\Resource\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set activity
     * @param \Claroline\CoreBundle\Entity\Resource\Activity $activity
     * @return \Innova\PathBundle\Entity\Step
     */
    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity parameters
     * @return \Claroline\CoreBundle\Entity\Activity\ActivityParameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param \Claroline\CoreBundle\Entity\Activity\ActivityParameters $parameters
     * @return \Innova\PathBundle\Entity\Step
     */
    public function setParameters(ActivityParameters $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Set lvl
     * @param integer $lvl
     * @return \Innova\PathBundle\Entity\Step
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
     * Set order
     * @param  integer $order
     * @return \Innova\PathBundle\Entity\Step
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order of the step
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set path
     * @param  \Innova\PathBundle\Entity\Path\Path $path
     * @return \Innova\PathBundle\Entity\
     */
    public function setPath(Path\Path $path = null)
    {
        $this->path = $path;

        if (!empty($path)) {
            $path->addStep($this);
        }

        return $this;
    }

    /**
     * Get path
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set parent
     * @param  \Innova\PathBundle\Entity\Step $parent
     * @return \Innova\PathBundle\Entity\Step
     */
    public function setParent(Step $parent = null)
    {
        if ($parent != $this->parent) {
            $this->parent = $parent;

            if (null !== $parent) {
                $parent->addChild($this);
            }
        }

        return $this;
    }

    /**
     * Get parent
     * @return \Innova\PathBundle\Entity\Step
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

    /**
     * Check if the Step has children
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children) && 0 < $this->children->count();
    }

    /**
     * Add new child to the step
     * @param \Innova\PathBundle\Entity\Step $step
     * @return \Innova\PathBundle\Entity\Step
     */
    public function addChild(Step $step)
    {
        if (!$this->children->contains($step)) {
            $this->children->add($step);
            $step->setParent($this);
        }
        
        return $this;
    }
    
    /**
     * Remove a step from children
     * @param \Innova\PathBundle\Entity\Step $step
     * @return \Innova\PathBundle\Entity\Step
     */
    public function removeChild(Step $step) 
    {
        if ($this->children->contains($step)) {
            $this->children->removeElement($step);
            $step->setParent(null);
        }
        
        return $this;
    }

    /**
     * Wrapper to access workspace of the Step
     * @return \Claroline\CoreBundle\Entity\Workspace\Workspace
     */
    public function getWorkspace()
    {
        $workspace = null;
        if (!empty($this->path)) {
            $workspace = $this->path->getWorkspace();
        }

        return $workspace;
    }

    /**
     * Wrapper to access Activity name
     * @return string
     */
    public function getName()
    {
        if (!empty($this->activity)) {
            return $this->activity->getResourceNode()->getName();
        }

        return '';
    }

    /**
     * Wrapper to access Activity description
     * @return string
     */
    public function getDescription()
    {
        if (!empty($this->activity) && ' ' != $this->activity->getDescription()) {
            return $this->activity->getDescription();
        }

        return '';
    }

    /**
     * Wrapper to access ResourceNode accessibleFrom property
     * @return \DateTime
     */
    public function getAccessibleFrom()
    {
        if (!empty($this->activity)) {
            return $this->activity->getResourceNode()->getAccessibleFrom();
        }

        return null;
    }

    /**
     * Wrapper to access ResourceNode accessibleUntil property
     * @return \DateTime
     */
    public function getAccessibleUntil()
    {
        if (!empty($this->activity)) {
            return $this->activity->getResourceNode()->getAccessibleUntil();
        }

        return null;
    }

    /**
     * Get min height for activity display
     * @return int
     */
    public function getActivityHeight()
    {
        return $this->activityHeight;
    }

    /**
     * Set min height for activity display
     * @param  int $activityHeight
     * @return $this
     */
    public function setActivityHeight($activityHeight)
    {
        $this->activityHeight = $activityHeight;

        return $this;
    }

    /**
     * Get inherited resources
     * @return ArrayCollection
     */
    public function getInheritedResources()
    {
        return $this->inheritedResources;
    }

    /**
     * Add an inherited resource
     * @param InheritedResource $inheritedResource
     * @return $this
     */
    public function addInheritedResource(InheritedResource $inheritedResource)
    {
        if (!$this->inheritedResources->contains($inheritedResource)) {
            $this->inheritedResources->add($inheritedResource);
        }

        $inheritedResource->setStep($this);

        return $this;
    }

    /**
     * Remove an inherited resource
     * @param InheritedResource $inheritedResource
     * @return $this
     */
    public function removeInheritedResource(InheritedResource $inheritedResource)
    {
        if ($this->inheritedResources->contains($inheritedResource)) {
            $this->inheritedResources->removeElement($inheritedResource);
        }

        $inheritedResource->setStep(null);

        return $this;
    }

    /**
     * Check if the step is already link to resource
     * @param integer $resourceId
     * @return boolean
     */
    public function hasInheritedResource($resourceId)
    {
        $result = false;

        if (!empty($this->inheritedResources)) {
            foreach ($this->inheritedResources as $inherited) {
                $resource = $inherited->getResource();
                if ($resource->getId() === $resourceId) {
                    $result = $inherited;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get propagated resources of the Step and inherited from the specified level
     * @param int $lvl
     * @return array
     */
    public function getPropagatedResources($lvl = 0)
    {
        $propagated = array ();

        /** @var \Innova\PathBundle\Entity\Step $child */
        foreach ($this->children as $child) {
            if ($child->getLvl() > $lvl) {
                // Loop over child inherited resources and grab inherited from `$lvl`
                $inheritedResources = $child->getInheritedResources();

                /** @var \Innova\PathBundle\Entity\InheritedResource $inherited */
                foreach ($inheritedResources as $inherited) {
                    if ($inherited->getLvl() == $lvl) {
                        // Resource is inherited from the searched level => get it
                        $propagated[] = $inherited->getResource()->getId();
                    }
                }
            }

            // Jump to children
            if ($child->hasChildren()) {
                $childrenPropagated = $child->getPropagatedResources($lvl);
                if (!empty($childrenPropagated)) {
                    $propagated = array_merge($propagated, $childrenPropagated);
                }
            }
        }

        $propagated = array_unique($propagated);

        return $propagated;
    }

    public function getParentsSecondaryResources()
    {
        $resources = array ();

        if (!empty($this->parent)) {
            if (!empty($this->parent->parameters)) {
                $parameters = $this->parent->parameters;
            } else if (!empty($this->parent->activity)) {
                $parameters = $this->parent->activity->getParameters();
            }

            if (!empty($parameters)) {
                $resources = $parameters->getSecondaryResources()->toArray();
            }

            // Jump to parent
            $parentResources = $this->parent->getParentsSecondaryResources();
            if (!empty($parentResources)) {
                $resources = array_merge($resources, $parentResources);
            }
        }

        return $resources;
    }

    /**
     * Set condition
     *
     * @param \Innova\PathBundle\Entity\StepCondition $condition
     *
     * @return Step
     */
    public function setCondition(StepCondition $condition = null)
    {
        if ($condition !== $this->condition) {
            $this->condition = $condition;

            if (null !== $condition) {
                $condition->setStep($this);
            }
        }

        return $this;
    }

    /**
     * Get condition
     *
     * @return \Innova\PathBundle\Entity\StepCondition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    public function jsonSerialize()
    {
        $accessibleFrom  = $this->getAccessibleFrom();
        $accessibleUntil = $this->getAccessibleUntil();

        // Initialize data array
        $jsonArray = array (
            'id'                => $this->id,               // A local ID for the step in the path (reuse step ID)
            'resourceId'        => $this->id,               // The real ID of the Step into the DB
            'activityId'        => null,
            'activityHeight'    => $this->activityHeight,
            'lvl'               => $this->lvl,              // The depth of the step in the path structure
            'name'              => $this->getName(),        // The name of the linked Activity (used as Step name)
            'description'       => $this->getDescription(), // The description of the linked Activity (used as Step description)
            'primaryResource'   => array (),
            'resources'         => array (),
            'excludedResources' => array (),
            'children'          => array (),
            'withTutor'         => false,
            'who'               => null,
            'where'             => null,
            'duration'          => null, // Duration in seconds
            'accessibleFrom'    => $accessibleFrom  instanceof \DateTime ? $accessibleFrom->format('Y-m-d H:i:s')  : null,
            'accessibleUntil'   => $accessibleUntil instanceof \DateTime ? $accessibleUntil->format('Y-m-d H:i:s') : null,
            'evaluationType'    => null, // automatic/manual
        );

        // Get activity properties
        if (!empty($this->activity)) {
            // Get activity ID
            $jsonArray['activityId']  = $this->activity->getId(); // The ID of the linked Activity

            // Get primary resource
            $primaryResource = $this->activity->getPrimaryResource();
            if (!empty($primaryResource)) {
                $jsonArray['primaryResource'] = array (
                    array (
                        'id'         => $primaryResource->getId(),
                        'resourceId' => $primaryResource->getId(),
                        'name'       => $primaryResource->getName(),
                        'type'       => $primaryResource->getResourceType()->getName(),
                        'mimeType'   => $primaryResource->getMimeType(),
                    )
                );
            }
        }

        // Get parameters
        if (!empty($this->parameters)) {
            // Get parameters of the step
            $parameters = $this->parameters;
        } else if (!empty($this->activity)) {
            // Get parameters of the Activity
            $parameters = $this->activity->getParameters();
        }

        if (!empty($parameters)) {
            // Secondary resources
            $secondaryResources = $parameters->getSecondaryResources();
            if (!empty($secondaryResources)) {
                // Get propagated resources of the current step
                $propagatedResources = $this->getPropagatedResources($this->lvl);

                foreach ($secondaryResources as $secondaryResource) {
                    $jsonArray['resources'][] = array(
                        'id'                  => $secondaryResource->getId(),
                        'resourceId'          => $secondaryResource->getId(),
                        'name'                => $secondaryResource->getName(),
                        'type'                => $secondaryResource->getResourceType()->getName(),
                        'mimeType'            => $secondaryResource->getMimeType(),
                        'propagateToChildren' => in_array($secondaryResource->getId(), $propagatedResources),
                    );
                }
            }

            // Global Parameters
            $jsonArray['withTutor']      = $parameters->isWithTutor();
            $jsonArray['who']            = $parameters->getWho();
            $jsonArray['where']          = $parameters->getWhere();
            $jsonArray['duration']       = $parameters->getMaxDuration(); // Duration in seconds
            $jsonArray['evaluationType'] = $parameters->getEvaluationType(); // manual/automatic
        }

        // Excluded resources
        $parentResources = $this->getParentsSecondaryResources();

        /** @var \Claroline\CoreBundle\Entity\Resource\ResourceNode $resource */
        foreach ($parentResources as $resource) {
            $exist = false;

            /** @var \Innova\PathBundle\Entity\InheritedResource $inherited */
            foreach ($this->inheritedResources as $inherited) {
                if ($inherited->getResource()->getId() == $resource->getId()) {
                    $exist = true;
                    break;
                }
            }

            // Parent resource not found in step
            if (!$exist) {
                $jsonArray['excludedResources'][] = $resource->getId();
            }
        }

        // Get condition
        if (!empty($this->condition)) {
            // Get condition of the step
            $jsonArray['condition'] = $this->condition;
        }

        // Get step children
        if (!empty($this->children)) {
            // Reorder children
            // The property OrderBy only works when we grab data from the DB,
            // so if we have modified the Path after it, children may be not ordered
            $iterator = $this->children->getIterator();

            $iterator->uasort(function ($a, $b) {
                /**
                 * @var \Innova\PathBundle\Entity\Step $a
                 * @var \Innova\PathBundle\Entity\Step $b
                 */
                return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
            });

            $this->children = new ArrayCollection(iterator_to_array($iterator));

            $jsonArray['children'] = array_values($this->children->toArray());
        }

        return $jsonArray;
    }
}
