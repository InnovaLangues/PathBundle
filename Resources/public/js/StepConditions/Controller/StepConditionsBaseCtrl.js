/**
 * Class constructor
 * @returns {StepConditionsBaseCtrl}
 * @constructor
 */
    //StepService
var StepConditionsBaseCtrl = function StepConditionsBaseCtrl($route, $routeParams, PathService, StepConditionsService) {
    this.webDir = AngularApp.webDir;

    this.pathService = PathService;
    this.stepConditionsService = StepConditionsService;

    this.current = $routeParams;
    var path = this.pathService.getPath();
    if (angular.isObject(path)) {
        // Set the structure of the path
        this.structure = path.steps;
    }

    //Get the current step
    var step = this.pathService.getStep(this.current.stepId);
    if (angular.isObject(step)) {
        //get the current condition
        this.conditionstructure = (step.condition) ? [step.condition] : [];
    }

    return this;
};

/**
 * Current step
 * @type {object}
 */
StepConditionsBaseCtrl.prototype.step = null;

/**
 * Path to the symfony web directory (where are stored our partials)
 * @type {null}
 */
StepConditionsBaseCtrl.prototype.webDir = null;

/**
 * Structure of the current path
 * @type {object}
 */
StepConditionsBaseCtrl.prototype.structure = {};

/**
 * Structure of the current condition
 * @type {object}
 */
StepConditionsBaseCtrl.prototype.conditionstructure = [];

/**
 * Current displayed Step
 * @type {object}
 */
StepConditionsBaseCtrl.prototype.current = {};