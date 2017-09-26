(function (angular) {
    var app = angular.module('app');

    app.controller("AppCtrl", [
        "$scope",
        "$http",
        "ExchangeRate",
        "Public",
        function ($scope,
                  $http,
                  ExchangeRate,
                  Public) {
            var self = this;

            /** Constants for comparision with today exchange rate */
            $scope.COMPARE_RESULT_LESS = "less";
            $scope.COMPARE_RESULT_MORE = "more";
            $scope.COMPARE_RESULT_EQUALS = "equals";

            /** Array with errors, must contains object with key _message_ */
            $scope.errors = [];
            /** Array of currencies */
            $scope.currencies = [];
            /** Array of favorite rates (only for authenticated) */
            $scope.favoriteRates = [];
            /** Current selected exchange rate pair */
            $scope.currentRate = {};
            /** Array of fetched data (each object contains of _date_ and _rate_) */
            $scope.fetchedData = [];
            /** Object of data for chart with some default options */
            $scope.chartData = {
                options: {
                    cubicInterpolationMode: 'monotone',
                    scales: {
                        yAxes: [
                            {
                                id: 'y-axis-1',
                                type: 'linear',
                                display: true,
                                position: 'left'
                            }
                        ]
                    }
                }
            };

            /** Flags for indicate, that load in progress */
            $scope.inProgressFetch = false;
            $scope.inProgressSave = false;

            /**
             * Initialization function
             */
            self.init = function () {
                Public.currencies(function (currencies) {
                    $scope.currencies = currencies;
                }, self.handleErrors);
            };

            /**
             * Initialization function for favorite rates (invokes only for authenticated user)
             */
            $scope.initFavoriteRates = function () {
                ExchangeRate.query(function (rates) {
                    $scope.favoriteRates = rates;
                }, self.handleErrors);
            };

            /**
             * Remove rate. On success also remove it from favorite list and unset in case it current selected rate
             * @param rate Exchange rate pair for deleting
             * @param $event MouseEvent
             */
            $scope.removeRate = function (rate, $event) {
                $event.stopPropagation();

                ExchangeRate.delete({"rateId": rate._id}, function (result) {
                    self.cleatErrors();

                    $scope.favoriteRates = $scope.favoriteRates.filter(function (favRate) {
                        return rate._id !== favRate._id;
                    });

                    if ($scope.currentRate._id === rate._id) {
                        $scope.currentRate = null;
                    }
                }, self.handleErrors);
            };

            /**
             * Save current exchange rate pair as favorite, set it as current active and push it into list favorite rates
             */
            $scope.saveCurrentRate = function () {
                $scope.inProgressSave = true;

                ExchangeRate.save($scope.currentRate, function (rate) {
                    self.cleatErrors();

                    $scope.currentRate = rate;
                    $scope.favoriteRates.push(rate);
                    $scope.inProgressSave = false;
                }, self.handleErrors)
            };

            /**
             * Update current rate pair
             */
            $scope.updateCurrentRate = function () {
                $scope.inProgressSave = true;
                ExchangeRate.update({"rateId": $scope.currentRate._id}, $scope.currentRate, function (rate) {
                    $scope.currentRate = rate;

                    $scope.favoriteRates = $scope.favoriteRates.map(function (favRate) {
                        if ($scope.currentRate._id === favRate._id) {
                            favRate = angular.copy($scope.currentRate);
                        }

                        return favRate;
                    });
                    $scope.inProgressSave = false;

                    self.cleatErrors();
                }, self.handleErrors)
            };

            /**
             * Change current active exchage rate pair
             * @param rate New active rate
             */
            $scope.changeCurrentRate = function (rate) {
                $scope.currentRate = angular.copy(rate);
                self.cleatErrors();
            };

            /**
             * Fetched data from server and prepared it for use it in _fetchedData_.
             * After preparing, updated chart data
             */
            $scope.fetchData = function () {
                $scope.inProgressFetch = true;

                Public.fetch($scope.currentRate, function (fetchedData) {
                    self.cleatErrors();

                    if (!!fetchedData.length) {
                        var todayRate = fetchedData[0].rate;

                        $scope.fetchedData = fetchedData.map(function (data) {
                            data.valuation = (data.rate * $scope.currentRate.amount).toFixed(2);
                            data.compareResult =
                                todayRate === data.rate ?
                                    $scope.COMPARE_RESULT_EQUALS : todayRate < data.rate ?
                                    $scope.COMPARE_RESULT_MORE : $scope.COMPARE_RESULT_LESS;

                            return data;
                        });

                        self.updateExtremeValues($scope.fetchedData);
                        self.updateChartData($scope.fetchedData);
                    }

                    $scope.inProgressFetch = false;
                }, self.handleErrors)
            };

            /**
             * Clear current active rate and clear errors
             */
            $scope.clear = function () {
                $scope.currentRate = null;
                self.cleatErrors();
            };

            /**
             * Preparing and updating data for chart
             * @param fetchedData
             */
            self.updateChartData = function (fetchedData) {
                fetchedData = angular.copy(fetchedData).reverse();

                $scope.chartData.labels = fetchedData.map(function (data) {
                    return data.date;
                });

                $scope.chartData.data = [];
                $scope.chartData.data.push(fetchedData.map(function (data) {
                    return data.rate;
                }));
            };

            /**
             * Detect extreme (min, max) values in fetchedData and marked it with flags
             * @param fetchedData
             */
            self.updateExtremeValues = function (fetchedData) {
                var min = fetchedData.reduce(function (prev, current) {
                    return prev.rate < current.rate ? prev : current;
                }, {rate: +Infinity});

                var max = fetchedData.reduce(function (prev, current) {
                    return prev.rate > current.rate ? prev : current;
                }, {rate: -Infinity});

                // We get values from reduce function by link, so we can add new property to it
                min.isMinimum = true;
                max.isMaximum = true;
            };

            /**
             * Handle request errors
             * @param result _result.data_ can be array ob objects with key _message_ ot simple object with key _message_
             */
            self.handleErrors = function (result) {
                var errors = result.data;
                if (angular.isArray(errors)) {
                    $scope.errors = errors;
                }
                else if (!!errors.message && !!errors.message.length) {
                    $scope.errors.push(errors);
                }
                else {
                    $scope.errors.push({message: "Request is failed"});
                }

                $scope.inProgressSave = false;
                $scope.inProgressFetch = false;
            };

            /**
             * Clear errors array
             */
            self.cleatErrors = function () {
                $scope.errors = [];
            };

            self.init();
        }]);
})(angular);