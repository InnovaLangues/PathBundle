/**
 * StepConditions Service
 */
(function () {
    'use strict';

    angular.module('StepConditionsModule').factory('StepConditionsService', [
        '$http',
        '$q',
        'IdentifierService',
        'PathService',
        function StepConditionsService($http, $q, IdentifierService, PathService) {
            var useringroup = null;
            /**
             * Evaluation data from \CoreBundle\Entity\Activity\Evaluation
             * @type {null}
             */
            var evaluation = null;
            var criterialist = new Array();
            /**
             * StepConditions object
             *
             * @constructor
             */
            var StepConditions = function StepConditions() {
                // Initialize step properties
                this.id                = IdentifierService.generateUUID();
                //id of the stepcondition in entity
                this.scid              = null;
                //list of criteria group
                this.criteriagroups    = [];
            };

            /**
             * Criterion object
             *
             * @constructor
             */
            var Criterion = function Criterion() {
                // criteria group identifier
                this.id                  = IdentifierService.generateUUID();
                //id of the criterion in entity
                this.critid              = null;
                //type : activity status, user group, primary resource repeat
                this.type                = null;
                this.data                = null;
            };
            /**
             * Criteria group object
             *
             * @constructor
             */
            var CriteriaGroup = function CriteriaGroup(parent) {
                var lvl;
                if (parent) {
                    lvl  = parent.lvl + 1;
                } else {
                    lvl = 0;
                }
                // criteria group identifier
                this.id = IdentifierService.generateUUID();
                //id of the criteriagroup in entity
                this.cgid = null;
                this.lvl = lvl;
                //contains array of criterion and/or criteria group
                this.criterion = [];
                this.criteriagroup = [];
            };
            return {
                getEvaluation: function getEvaluation(){
                    return this.evaluation;
                },
                /**
                 * Retrieve usergroup list from DB
                 *
                 * @returns {*|usergrouplist}
                 */
                getUseringroupFromController: function getUseringroupFromController(){
                    this.getUseringroup();
                    return this.useringroup;
                },
                setUseringroup: function setUseringroup(uig){
                    this.useringroup = uig;
                },
                /**
                 * Retrieve the groups in which the user is registered to
                 *
                 */
                getUserBelongsTo: function getUserBelongsTo() {
                    this.useringroup = [];
                },
                /**
                 * Generates a new empty stepConditions
                 *
                 * @param {object} [step]
                 * @returns {StepConditions}
                 */
                initialize: function initialize(step) {
                    //create an empty Condition for the given step
                    var newStepConditions = new StepConditions();
                    //create an structure : empty criteriagroup
                    var newCriteriaGroup = new CriteriaGroup();
                    //add the empty criteriagroup to the empty condition
                    newStepConditions.criteriagroups.push(newCriteriaGroup);
                    //attach condition to step
                    step.condition = newStepConditions;
                    return newStepConditions;
                },
                /**
                 * Adds a new criteria group
                 *
                 * @returns {CriteriaGroup}
                 */
                addCriteriagroup: function (cgroup) {
                    //create a new criteriagroup object structure
                    var newCriteriaGroup = new CriteriaGroup(cgroup);
                    //adds the criteriagroup to a criteriagroup
                    if (cgroup.criteriagroup) {
                        cgroup.criteriagroup.push(newCriteriaGroup);
                    } else {
                        //if this is root
                        cgroup.criteriagroups.push(newCriteriaGroup);
                    }
                    return newCriteriaGroup;
                },
                /**
                 * Adds a new criterion
                 * @param {object} [criterion]
                 * @returns {Criterion}
                 */
                addCriterion: function (cgroup) {
                    //create a new criterion object structure
                    var newCriterion = new Criterion(cgroup);
                    //adds the criterion to the criteriagroup
                    cgroup.criterion.push(newCriterion);
                    return newCriterion;
                },
                /**
                 * Get the list of group the user is registered in
                 *
                 * @returns {object}
                 */
                getUseringroup: function getUseringroup() {
                    var deferred = $q.defer();
                    var params = {};
                    $http
                        .get(Routing.generate('innova_path_criteria_groupsforuser', params))
                        .success(function (response) {
                            this.setUseringroup(response);
                            deferred.resolve(response);
                        }
                            .bind(this)) //to access StepConditionsService object method and attributes
                        .error(function (response) {
                            deferred.reject(response);
                        });
                    return deferred.promise;
                },
                /**
                 * Retrieve activity evaluation data from a activity
                 *
                 * @param activityId
                 */
                getActivityEvaluation: function getActivityEvaluation(activityId) {
                    var deferred = $q.defer();
                    var params = {'activityId':activityId};
                    $http
                        .get(Routing.generate('innova_path_activity_eval', params))
                        .success(function (response) {
                            this.evaluation = response;
                            deferred.resolve(response);
                        }.bind(this))
                        .error(function (response) {
                            deferred.reject(response);
                        });
                    return deferred.promise;
                },
                /**
                 * Test a condition from a step
                 *
                 * @param step
                 * @returns {boolean}
                 */
                testCondition: function testCondition(step, evaluation) {
                    var result=false;
                    //get root criteriagroup
                    var criteriagroups=step.condition.criteriagroups;
                    this.evaluation = evaluation;
                    criterialist = new Array();
                    criterialist.push("[");
                    //criteriagroup : OR test
                    for(var i=0;i<criteriagroups.length;i++){
                        result=this.testCriteriagroup(criteriagroups[i])||result;
                    }
                    criterialist.push("]");
                    return result;
                },
                /**
                 * Test a criteriagroup from a criteriagroup in condition
                 *
                 * @param cgroup
                 * @returns {boolean}
                 */
                testCriteriagroup: function testCriteriagroup(cgroup) {
                    criterialist.push("(");
                    var result=true;
                    //First, get all the criteria from this group
                    var crit=cgroup.criterion;
                    criterialist.push("(");
                    //test all criteria of the criteriagroup : AND TEST
                    var cl= crit.length;
                    for(var i=0;i<cl;i++){
                        result=this.testCriterion(crit[i])&&result;
                        if (i<cl-1){criterialist.push(Translator.trans('condition_and', {}, 'path_wizards'));}
                    }
                    criterialist.push(")");
                    var cgl=cgroup.criteriagroup.length;
                    if(cgl>0){
                        criterialist.push(Translator.trans('condition_or', {}, 'path_wizards')+ "(");
                        //then test all criteriagroup inside this criteriagroup (recursive part) : OR test
                        for(var j=0;j<cgl;j++){
                            result=this.testCriteriagroup(cgroup.criteriagroup[j])||result;
                            if (j<cgl-1){criterialist.push(Translator.trans('condition_or', {}, 'path_wizards'));}
                        }
                        criterialist.push(")");
                    }
                    criterialist.push(")");
                    return result;
                },
                /**
                 * Test a criterion in a condition
                 *
                 * @param criterion
                 * @returns {boolean}
                 */
                testCriterion: function testCriterion(criterion) {
                    var test=false;
                    var data="";
                    //retrieve evaluation data to check against (evaluation must be retrieved when step is loaded)
                    var evaluationResultToCheck=this.getEvaluation();
                    //if there is data
                    if(angular.isDefined(evaluationResultToCheck)||criterion.type=="usergroup"){
                        switch(criterion.type){
                            case"activityrepetition":
                                test=(criterion.data===evaluationResultToCheck.attempts);
                                data = Translator.trans('condition_criterion_test_repetition', {activityRep:criterion.data, userRep:evaluationResultToCheck.attempts}, 'path_wizards');
                                break;
                            case"activitystatus":
                                test=(criterion.data===evaluationResultToCheck.status);
                                data = Translator.trans('condition_criterion_test_status', {activityStatus:criterion.data, userStatus:evaluationResultToCheck.status}, 'path_wizards');
                                break;
                            case"usergroup":
                                var usergroup = new Array();
                                var group = new Array();
                                var ug = PathService.getUseringroupData();
                                var test_tmp;
                                //to retrieve group names
                                var g=PathService.getUsergroupData();
                                if (angular.isObject(g)){
                                    for (var k in g){
                                        if (angular.isDefined(g[criterion.data])){
                                            group.push(g[criterion.data]);
                                            break;
                                        }
                                    }
                                }
                                //to retrieve user group names
                                for (var group in ug) {
                                    usergroup.push(ug[group]);
                                    test_tmp=(criterion.data===group);
                                    if (test_tmp == true){test = true;}
                                }
                                data=Translator.trans('condition_criterion_test_usergroup', {activityGroup:criterion.data, userGroup:usergroup.join(",")}, 'path_wizards');
                                break;
                            default:break;
                        }
                    }
                    criterialist.push(Translator.trans(data, {}, 'path_wizards'));
                    return test;
                },

                getParent: function getParent(cgroup) {
                    var parentCriteriagroup = null;
                    this.browseCriteriagroups(path.steps, function (parent, current) {
                        if (cgroup.id == current.id) {
                            parentCriteriagroup = parent;
                            return true;
                        }
                        return false
                    });
                    return parentCriteriagroup;
                },
                /**
                 * Loop over all criteriagroup of a condition and execute callback
                 * Iteration stops when callback returns true
                 * (Based on browseStep in pathService.js)
                 *
                 * @param {array}    criteriagroups    - an array of criteriagroup to browse
                 * @param {function} callback - a callback to execute on each criteriagroup (called with args `parentCriteriagroup`, `currentCriteriagroup`)
                 */
                browseCriteriagroups: function browseCriteriagroups(criteriagroups, callback) {
                    /**
                     * Recursively loop through the criteriagroups to execute callback on each criteriagroup
                     * @param   {object} parentCriteriagroup
                     * @param   {object} currentCriteriagroup
                     * @returns {boolean}
                     */
                    function recursiveLoop(parentCriteriagroup, currentCriteriagroup) {
                        var terminated = false;

                        // Execute callback on current criteriagroup
                        if (typeof callback === 'function') {
                            terminated = callback(parentCriteriagroup, currentCriteriagroup);
                        }

                        if (!terminated && typeof currentCriteriagroup.criteriagroup !== 'undefined' && currentCriteriagroup.criteriagroup.length !== 0) {
                            for (var i = 0; i < currentCriteriagroup.criteriagroup.length; i++) {
                                terminated = recursiveLoop(currentCriteriagroup, currentCriteriagroup.criteriagroup[i]);
                            }
                        }
                        return terminated;
                    }

                    if (typeof criteriagroups !== 'undefined' && criteriagroups.length !== 0) {
                        for (var j = 0; j < criteriagroups.length; j++) {
                            var terminated = recursiveLoop(null, criteriagroups[j]);

                            if (terminated) {
                                break;
                            }
                        }
                    }
                },
                /**
                 * Remove a criteriagroup from the path's tree
                 * @param {array}  criteriagroups        - an array of criteriagroups to browse
                 * @param {object} criteriagroupToDelete - the criteriagroup to delete
                 */
                removeCriteriagroup: function removeCriteriagroup(criteriagroups, criteriagroupToDelete) {
                    this.browseCriteriagroups(criteriagroups, function (parent, group) {
                        var deleted = false;
                        //if current criteriagroup is the one to be deleted
                        if (group === criteriagroupToDelete) {
                            if (typeof parent !== 'undefined' && null !== parent) {
                                var pos = parent.criteriagroup.indexOf(criteriagroupToDelete);
                                if (-1 !== pos) {
                                    parent.criteriagroup.splice(pos, 1);

                                    deleted = true;
                                }
                            } else {
                                // We are deleting the root criteriagroup
                                var pos = criteriagroups.indexOf(criteriagroupToDelete);
                                if (-1 !== pos) {
                                    criteriagroups.splice(pos, 1);

                                    deleted = true;
                                }
                            }
                        }
                        return deleted;
                    });
                },
                /**
                 * Do some condition checking before adding to step
                 */
                cleanCondition: function(condition){
                    //TODO : Check stuff
                    return condition;
                },
                /**
                 * get the list of condition criteria for a step
                 *
                 * @param step
                 * @returns {string}
                 */
                getConditionList:function getConditionList(){
                    return criterialist.join("\n");
                }
            };
        }
    ]);
})();