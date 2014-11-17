'use strict';

/**
 * Template Resources modal
 */
function TemplateResourcesModalCtrl($scope, $modalInstance) {
    $scope.manageResources = 'keep';

    /**
     * Confirm copy
     */
    $scope.paste = function () {
        $modalInstance.close($scope.manageResources);
    };

    /**
     * Abort copy
     */
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}