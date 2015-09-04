(function () {
    'use strict';

    angular.module('PathNavigationModule').directive('pathNavigationItemShow', [
        function PathNavigationShowDirective() {
            return {
                restrict: 'E',
                replace: true,
                controller: PathNavigationShowCtrl,
                controllerAs: 'pathNavigationItemShowCtrl',
                templateUrl: AngularApp.webDir + 'bundles/innovapath/js/PathNavigation/Partial/show-item.html',
                scope: {
                    parent:  '=?',
                    element: '=',
                    current: '='
                },
                bindToController: true
            };
        }
    ]);
})();