<?php

namespace Innova\PathBundle\Controller;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Manager\GroupManager;
use Innova\PathBundle\Manager\StepConditionsGroupManager;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Claroline\CoreBundle\Entity\Activity\AbstractEvaluation;

/**
 * Class StepConditionsController
 *
 * @Route(
 *      "/stepconditions",
 *      name    = "innova_path_stepconditions",
 *      service = "innova_path.controller.step_conditions"
 * )
 */
class StepConditionsController extends Controller
{
    /**
     * Object manager
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;
    private $groupManager;
    private $evaluationRepo;
    /**
     * Security Token
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $securityToken
     */
    protected $securityToken;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param GroupManager $groupManager
     * @param TokenStorageInterface $securityToken
     */
    public function __construct(
        ObjectManager $objectManager,
        GroupManager $groupManager,
        TokenStorageInterface $securityToken
    )
    {
        $this->groupManager = $groupManager;
        $this->om = $objectManager;
        $this->securityToken   = $securityToken;
    }
    /**
     * Get user group for criterion
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route(
     *     "/usergroup",
     *     name         = "innova_path_criteria_usergroup",
     *     options      = { "expose" = true }
     * )
     * @Method("GET")
     */
    public function getUserGroups()
    {
        $data = array();
//        $groupmanager = $this->container->get('claroline.manager.group_manager');
        $usergroup = $this->groupManager->getAllGroupsWithoutPager();
        if ($usergroup != null) {
            //data needs to be explicitly set because Group does not extends Serializable
            foreach($usergroup as $ug) {
                $data[$ug->getId()] = $ug->getName();
            }
        }
        return new JsonResponse($data);
    }

    /**
     * Get list of groups a user belongs to
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route(
     *     "/groupsforuser",
     *     name         = "innova_path_criteria_groupsforuser",
     *     options      = { "expose" = true }
     * )
     * @Method("GET")
     */
    public function getGroupsForUser()
    {
        //retrieve current user
        $user = $this->securityToken->getToken()->getUser();
        $userId = $user->getId();
        $data = array();
        //retrieve list of groups object for this user
        $groupforuser = $this->om->getRepository("InnovaPathBundle:StepConditionsGroup")->getAllForUser($userId, true, 'id');
        if ($groupforuser != null) {
            //data needs to be explicitly set because Group does not extends Serializable
            foreach($groupforuser as $ug) {
                $data[$ug->getId()] = $ug->getName();
            }
        }
        return new JsonResponse($data);
    }

    /**
     * Get evaluation data for an activity
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route(
     *     "/activityeval/{activityId}",
     *     name         = "innova_path_activity_eval",
     *     options      = { "expose" = true }
     * )
     * @Method("GET")
     */
    public function getActivityEvaluation($activityId)
    {
        $data = array(
            'status' => 'NA',
            'attempts' => 'NA'
        );
        //retrieve activity
        $this->activityRepo = $this->om->getRepository('ClarolineCoreBundle:Resource\Activity');
        $activity = $this->activityRepo->findOneBy(array('id'=>$activityId));
        if ($activity !== null)
        {
            //retrieve evaluation data for this activity
            $this->evaluationRepo = $this->om->getRepository('ClarolineCoreBundle:Activity\Evaluation');
            $evaluation = $this->evaluationRepo->findOneBy(array('activityParameters'=> $activity->getParameters()));
            //return relevant data
            if ($evaluation !== null){
                $data = array(
                    'status' => $evaluation->getStatus(),
                    'attempts' => $evaluation->getAttemptsCount()
                );
            }
        }
        return new JsonResponse($data);
    }

    /**
     * Get list of Evaluation statuses to display in select
     * (data from \CoreBundle\Entity\Activity\AbstractEvaluation.php)
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getEvaluationStatuses()
    {
        $statuses = array(
            AbstractEvaluation::STATUS_COMPLETED,
            AbstractEvaluation::STATUS_FAILED,
            AbstractEvaluation::STATUS_INCOMPLETE,
            AbstractEvaluation::STATUS_NOT_ATTEMPTED,
            AbstractEvaluation::STATUS_PASSED,
            AbstractEvaluation::STATUS_UNKNOWN
        );
        /* also possible :
        $r = new ReflectionClass('\CoreBundle\Entity\Activity\AbstractEvaluation');
        //Get class constants
        $const = $r->getConstants();
        $statuses = array();
        foreach($const as $k => $v) {
            //Only get constants begining with STATUS
            if (strpos($k, 'STATUS') !== false)
                $statuses[$k] => $v;
        }
        */
        return new JsonResponse($statuses);
    }
}