/**
 * Alert box directive
 */
(function () {
    'use strict';

    angular.module('AlertModule').directive('alertBox', [
        'AlertService',
        function (AlertService) {
            return {
                restrict: 'E',
                replace: true,
                templateUrl: EditorApp.webDir + 'bundles/innovapath/angularjs/Alert/Partial/alert-box.html',
                scope: {},
                link: function (scope) {
                    scope.alerts = AlertService.getAlerts();

                    scope.closeAlert  = function (alert) {
                        AlertService.closeAlert(alert);
                    };
                }
            }
        }
    ]);
})();