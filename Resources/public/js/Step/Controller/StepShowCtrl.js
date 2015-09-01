/**
 * Class constructor
 * @returns {StepShowCtrl}
 * @constructor
 */
var StepShowCtrl = function StepShowCtrl(step, inheritedResources, PathService, $sce, UserProgressionService, StepConditionsService, AlertService) {
    StepBaseCtrl.apply(this, arguments);

    this.userProgressionService = UserProgressionService;
    this.userProgression = this.userProgressionService.get();
    this.stepConditionsService = StepConditionsService;
    this.alertService = AlertService;

    if (angular.isDefined(this.step) && angular.isDefined(this.step.description) && typeof this.step.description == 'string') {
        // Trust content to allow Cross Sites URL
        this.step.description = $sce.trustAsHtml(this.step.description);
    }

    // Update User progression if needed (e.g. if the User has never seen the Step, mark it as seen)
    this.progression = this.userProgressionService.getForStep(this.step);
    if (!angular.isObject(this.progression)) {
        // Create progression for User
        this.progression = this.userProgressionService.create(step);
    }

    return this;
};

// Extends the base controller
StepShowCtrl.prototype = Object.create(StepBaseCtrl.prototype);
StepShowCtrl.prototype.constructor = StepShowCtrl;

/**
 * Service that manages the User Progression in the Path
 * @type {{}}
 */
StepShowCtrl.prototype.userProgressionService = {};

/**
 * Progression of the User for the current Step (NOT the progression for the whole Path)
 * @type {null}
 */
StepShowCtrl.prototype.progression = {};

StepShowCtrl.prototype.updateProgression = function (newStatus) {
    this.userProgressionService.update(this.step, newStatus);
};

StepShowCtrl.prototype.goTo = function goTo(step) {

    var curentStepId = step.id;
    var rootStep = this.pathService.getRoot();

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