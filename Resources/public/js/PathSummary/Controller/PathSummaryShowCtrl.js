/**
 * Class constructor
 * @returns {PathSummaryShowCtrl}
 * @constructor
 */
var PathSummaryShowCtrl = function PathSummaryShowCtrl($routeParams, PathService, StepConditionsService, UserProgressionService) {
    PathSummaryBaseCtrl.apply(this, arguments);

    // Get Progression of the current User
    this.userProgressionService = UserProgressionService;
    this.userProgression = this.userProgressionService.get();
console.log("i'm inside PathSummaryShowCtrl");
//console.log(this.structure);
//console.log("pathServicegetAllEvaluationsForPath");console.log(this.pathService.getAllEvaluationsForPath(this.pathService.getId()));

    return this;
};

// Extends the base controller
PathSummaryShowCtrl.prototype = Object.create(PathSummaryBaseCtrl.prototype);
PathSummaryShowCtrl.prototype.constructor = PathSummaryShowCtrl;

/**
 * Progression of the current User into the Path
 * @type {object}
 */
PathSummaryShowCtrl.prototype.userProgression = {};

PathSummaryShowCtrl.prototype.updateProgression = function (step, newStatus) {
    if (!angular.isObject(this.userProgression[step.id])) {
        this.userProgressionService.create(step, newStatus);
    } else {
        this.userProgressionService.update(step, newStatus);
    }
};

PathSummaryShowCtrl.prototype.goTo = function goTo(step) {
console.log("i'm really inside PathSummaryShowCtrl !");
    //to avoid empty step
    if (typeof this.current.stepId == 'undefined'){this.current.stepId = this.structure[0].id}
    var progression = this.userProgression[this.current.stepId];
    //previous step exists ? NO : we're on root step => access
    if (!angular.isObject(this.pathService.getPrevious(step))) {
        if (typeof progression=='undefined' || !angular.isDefined(progression.authorized) || !progression.authorized){
console.log("root update");
            this.userProgressionService.update(step, progression.status, 1);    //pass 1 (and not "true") to controller : problem in url
        }
        this.pathService.goTo(step);
        //previous step exists ? YES
    } else {
        var previousstep = this.pathService.getPrevious(step);
        //is there a flag authorized on current step ? YES => access
        if (typeof progression!=='undefined' && angular.isDefined(progression.authorized) && progression.authorized) {
            this.pathService.goTo(step);
            //is there a flag authorized on current step ? NO (or because the progression is not set)
        } else {
            //is there a flag authorized on previous step ? NO => no access => message
            if (typeof this.userProgression[previousstep.id]=='undefined' || !angular.isDefined(this.userProgression[previousstep.id].authorized) || !this.userProgression[previousstep.id].authorized) {
                console.log("You can't access this step : " + step.name);
                //is there a flag authorized on previous step ? YES
            } else {
                //is there a condition on previous step ? YES
                if (angular.isDefined(previousstep.condition) && angular.isObject(previousstep.condition)) {
                    // validate condition on previous step ? YES
                    if (this.stepConditionsService.testCondition(previousstep)) {
                        //add flag to current step
                        this.userProgressionService.update(step, progression.status, 1);
                        //grant access
                        this.pathService.goTo(step);
                        // validate condition on previous step ? NO
                    } else {
                        console.log("You can't access this step : " + step.name);
                    }
                    //is there a condition on previous step ? NO
                } else {
                    //add flag to current step
                    this.userProgressionService.update(step, progression.status, 1);
                    //grant access
                    this.pathService.goTo(step);
                }
            }
        }
    }
};
