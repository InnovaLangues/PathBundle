import PathShowCtrl from '../Controller/PathShowCtrl'

export default class PathShowDirective {
    constructor() {
        this.restrict = 'E'
        this.replace = true
        this.controller = PathShowCtrl
        this.controllerAs = 'pathShowCtrl'
        this.template = require('../Partial/show.html')
        this.scope = {
            id              : '@', // ID of the path
            path            : '=', // Data of the path
            editEnabled     : '@', // User is allowed to edit current path ?
            userProgression : '@?' // Progression of the current User
        }
        this.bindToController = true
    }
}
