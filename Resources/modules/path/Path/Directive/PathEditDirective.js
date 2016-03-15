import PathEditCtrl from '../Controller/PathEditCtrl'

export default class PathEditDirective {
    constructor() {
        this.restrict = 'E'
        this.replace = true
        this.controller = PathEditCtrl
        this.controllerAs = 'pathEditCtrl'
        this.template = require('../Partial/edit.html')
        this.scope = {
            id        : '@', // ID of the path
            path      : '@', // Data of the path
            modified  : '@', // Is Path have pending modifications ?
            published : '@'  // Is path published ?
        }
        this.bindToController = true
    }
}