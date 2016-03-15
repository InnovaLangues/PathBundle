import PathBaseCtrl from './PathBaseCtrl'

export default class PathShowCtrl extends PathBaseCtrl {
    constructor($window, $route, $routeParams, PathService, UserProgressionService) {
        super($window, $route, $routeParams, PathService)
        this.editEnabled = false
        this.userProgressionService = UserProgressionService

        // Store UserProgression
        if (angular.isObject(this.userProgression)) {
            this.userProgressionService.set(this.userProgression)
        }

        this.pathService = PathService;
    }


    /**
     * Open Path editor
     */
    edit() {
        let url = Routing.generate('innova_path_editor_wizard', {
            id: this.id
        })

        if (angular.isObject(this.currentStep) && angular.isDefined(this.currentStep.stepId)) {
            url += '#/' + this.currentStep.stepId
        }

        this.window.location.href = url
    }
}

PathShowCtrl.$inject = ['$window', '$route', '$routeParams', 'PathService', 'UserProgressionService']




