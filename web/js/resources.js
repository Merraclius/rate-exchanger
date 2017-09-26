(function (angular) {
    var app = angular.module('app');

    app.factory("ExchangeRate", ["$resource", "API_ENDPOINT", function ($resource, API_ENDPOINT) {
        return $resource([API_ENDPOINT, "rates", ":rateId"].join("/"),
            {
                "rateId": "@id"
            },
            {
                "update": {
                    method: "PUT"
                }
            });
    }]);

    app.factory("Public", ["$resource", "API_ENDPOINT", function ($resource, API_ENDPOINT) {
        return $resource([API_ENDPOINT, "public"].join("/"),
            {},
            {
                "currencies": {
                    url: [API_ENDPOINT, "public", "currencies"].join("/"),
                    method: "GET",
                    isArray: true
                },
                "fetch": {
                    url: [API_ENDPOINT, "public", "fetch"].join("/"),
                    method: "POST",
                    isArray: true
                }
            });
    }])
})(angular);