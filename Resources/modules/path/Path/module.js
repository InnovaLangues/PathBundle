import PathShowDirective from './Directive/PathShowDirective'
import PathEditDirective from './Directive/PathEditDirective'
import PathService from './Service/PathService'

angular.module('PathModule', [
    'HistoryModule',
    'ClipboardModule',
    'StepModule',
    'TemplateModule',
    'UserProgressionModule'
]).directive('pathShow',  () => new PathShowDirective)
  .directive('pathEdit',  () => new PathEditDirective)
  .service('PathService', PathService)
