/**
 * Class constructor
 * @returns {PathSummaryShowCtrl}
 * @constructor
 */
var PathSummaryShowCtrl = function PathSummaryShowCtrl($routeParams, PathService, StepConditionsService, UserProgressionService, AlertService) {
    PathSummaryBaseCtrl.apply(this, arguments);

    // Get Progression of the current User
    this.userProgressionService = UserProgressionService;
    this.userProgression = this.userProgressionService.get();
    this.alertService= AlertService;

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
/**
 * Override for goTo method to manage conditions
 * @param step
 */
PathSummaryShowCtrl.prototype.goTo = function goTo(step) {
    var curentStepId = step.id;
    var rootStep = this.structure[0];

    //make sure root is accessible anyways
    if (typeof this.userProgression[rootStep.id]=='undefined'
        || !angular.isDefined(this.userProgression[rootStep.id].authorized)
        || !this.userProgression[rootStep.id].authorized) {
        this.userProgressionService.update(rootStep, this.userProgression[rootStep.id].status, 1);    //pass 1 (and not "true") to controller : problem in url
    }
    //previous step exists ? NO : we're on root step => access
    if (!angular.isObject(this.pathService.getPrevious(step))) {
        this.pathService.goTo(step);
        //previous step exists ? YES
    } else {
        var previousstep = this.pathService.getPrevious(step);
        //is there a flag authorized on current step ? YES => access
        if (typeof this.userProgression[curentStepId]!=='undefined'
            && angular.isDefined(this.userProgression[curentStepId].authorized)
            && this.userProgression[curentStepId].authorized) {
            this.pathService.goTo(step);
            //is there a flag authorized on current step ? NO (or because the progression is not set)
        } else {
            //is there a flag authorized on previous step ? YES
            if (typeof this.userProgression[previousstep.id]!=='undefined'
                && angular.isDefined(this.userProgression[previousstep.id].authorized)
                && this.userProgression[previousstep.id].authorized) {
                var progression = this.userProgression[step.id];
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
                        this.alertService.addAlert('error', Translator.trans('step_access_denied', {}, 'path_wizards'));
                        console.log("You can't access this step : " + step.name);
                    }
                    //is there a condition on previous step ? NO
                } else {
                    //add flag to current step
                    this.userProgressionService.update(step, progression.status, 1);
                    //grant access
                    this.pathService.goTo(step);
                }
                //is there a flag authorized on previous step ? NO => no access => message
            } else {
                this.alertService.addAlert('error', Translator.trans('step_access_denied', {}, 'path_wizards'));
                console.log("You can't access this step : " + step.name);
            }
        }
    }
};