export default class PathBaseCtrl { 
    constructor($window, $route, $routeParams, PathService) {
        this.window = $window
        this.route = $route
        this.$routeParams = $routeParams
        this.pathService = PathService

        /**
         * ID of the current path
         * @type {number}
         */
        this.id = null
        this.path = {}
        /**
         * Current step ID (used to generate edit and preview routes)
         */
        this.currentStep = {}
        this.summaryState = {}


        // Store path to make it available by all UI components
        this.pathService.setId(this.id);
        this.pathService.setPath(this.path);

        this.currentStep = $routeParams;

        this.summaryState = this.pathService.getSummaryState();

        //triggering of promise
        this.pathService.userteampromise(this.id);


        // Force reload of the route (as ng-view is deeper in the directive tree, route resolution is deferred and it causes issues)
        $route.reload();
    }

    toggleSummary() {
        this.pathService.toggleSummaryState();
    }
} 

PathBaseCtrl.$inject = ['$window', '$route', '$routeParams', 'PathService']