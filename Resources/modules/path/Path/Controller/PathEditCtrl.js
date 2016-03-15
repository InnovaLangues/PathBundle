import PathBaseCtrl from './PathBaseCtrl'

export default class PathEditCtrl extends PathBaseCtrl {
    constructor($window, $route, $routeParams, PathService, HistoryService, ConfirmService, $scope) {
        super($window, route, $routeParams, PathService)
        this.historyService = HistoryService
        this.confirmService = ConfirmService
        this.$scope = this.$scope

        this.modified = false
        this.published = false
        this.unsaved = false
        this.historyDisabled = {}

        this.historyDisabled = HistoryService.getDisabled();

        $scope.$watch(
            () => { return this.path },
            newValue => {
                const empty   = this.historyService.isEmpty();
                const updated = this.historyService.update(newValue);

                if (!empty && updated) {
                    // Initialization is already done, so mark path as unsaved for each modification
                    if (this.published!==true) {
                        this.unsaved = true;
                    }
                }
            },
            true
        );
    }

    /**
     * Undo last action
     */
    undo() {
        if (this.historyService.canUndo()) {
            // Inject history data
            this.historyService.undo(this.path);
        }
    }

    redo() {
        if (this.historyService.canRedo()) {
            // Inject history data
            this.historyService.redo(this.path);
        }
    }

    /**
     * Save the path
     */
    save() {
        if (this.unsaved) {
            //Check for condition validity
           // console.log(this.pathService.ConditionValidityCheck());
            // Save only with there is something to change
            this.pathService.save().then(() => {
                // Mark path as modified
                this.modified = true;
                this.unsaved  = false;
            })
        }
    }

    /**
     * Publish the path modifications
     */
    publish() {
        if (!this.published || this.modified) {
            // Publish if there is something to publish
            this.pathService.publish().then(() => {
                this.modified  = false;
                this.published = true;
                this.unsaved   = false;
                if (this.path.steps[0] !== 'undefined') {
                    this.pathService.goTo(this.path.steps[0]);
                }
                /* wip save/publish */
                this.unsaved=false;
                this.published=1;
            })
        }
    }

    /**
     * Preview path into player
     */
    preview() {
        if (this.modified) {
            // Path modified => modifications will not be visible before publishing so warn user
            this.confirmService.open(
                // Confirm options
                {
                    title:         Translator.trans('preview_with_pending_changes_title',   {}, 'path_wizards'),
                    message:       Translator.trans('preview_with_pending_changes_message', {}, 'path_wizards'),
                    confirmButton: Translator.trans('preview_with_pending_changes_button',  {}, 'path_wizards')
                },
                // Confirm success callback
                 () => { this.window.location.href = url }
            );
        } else {
            // Open player to preview the path
            this.window.location.href = url;
        }

        if (this.published) {
            // Path needs to be published at least once to be previewed
            var url = Routing.generate('innova_path_player_wizard', {
                id: this.id
            });

            if (angular.isObject(this.currentStep) && angular.isDefined(this.currentStep.stepId)) {
                url += '#/' + this.currentStep.stepId;
            }

            // Force save before exit Editor
            if (this.unsaved) {
                // Save only with there is something to change
                this.pathService.save().then(() => {
                    // Mark path as modified
                    this.modified = true;
                    this.unsaved  = false;

                    doPreview.call(this);
                })
            } else {
                doPreview.call(this);
            }
        }
    }
}

PathEditCtrl.$inject = ['$window', '$route', '$routeParams', 'PathService', 'HistoryService', 'ConfirmService', '$scope']






