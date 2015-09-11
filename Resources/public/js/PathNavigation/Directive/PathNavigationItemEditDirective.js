(function () {
    'use strict';

    angular.module('PathNavigationModule').directive('pathNavigationItemEdit', [
        function PathNavigationEditDirective() {
            return {
                restrict: 'E',
                replace: true,
                controller: function PathNavigationItemEditCtrl() {},
                controllerAs: 'pathNavigationItemEditCtrl',
                templateUrl: AngularApp.webDir + 'bundles/innovapath/js/PathNavigation/Partial/edit-item.html',
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