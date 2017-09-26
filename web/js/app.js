var app = angular.module('app', [
    "config",
    "ngResource",
    'chart.js'
]);

angular.element(document).ready(function() {
    angular.bootstrap(document, ['app']);
});


