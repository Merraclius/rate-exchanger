<?php

/* @var yii\web\View $this */

use kartik\icons\Icon;

$this->title = 'My Yii Application';


if (!\Yii::$app->user->isGuest) {
    /** @var \app\models\User $user */
    $user = \Yii::$app->user->getIdentity();
}

Icon::map($this, Icon::FA);

?>

<div data-ng-controller="AppCtrl" data-ng-cloak>

    <div class="container-fluid">
        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label for="currencyBase">Base</label>
                    <select class="form-control"
                            id="currencyBase"
                            data-ng-model="currentRate.currencyBase"
                            data-ng-options="currency.code as currency.label for currency in currencies">
                    </select>
                </div>

                <div class="form-group">
                    <label for="currencyTarget">Target</label>
                    <select class="form-control"
                            id="currencyTarget"
                            data-ng-model="currentRate.currencyTarget"
                            data-ng-options="currency.code as currency.label for currency in currencies">
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input class="form-control" type="text" id="amount" data-ng-model="currentRate.amount"/>
                </div>

                <div class="form-group">
                    <label for="duration">Duration (in weeks)</label>
                    <input class="form-control" type="text" id="duration" data-ng-model="currentRate.duration"/>
                </div>

                <div class="row justify-content-end">
                    <? if (!Yii::$app->user->isGuest) { ?>
                        <div data-ng-if="!currentRate._id">
                            <button type="button" class="btn btn-warning" data-ng-click="saveCurrentRate()"
                                    data-ng-disabled="inProgressSave">
                                Save as favorite
                                <span data-ng-if="inProgressSave"><?= Icon::show('spin', ['class' => 'fa-refresh']) ?></span>
                            </button>
                        </div>
                        <div data-ng-if="!!currentRate._id">
                            <button type="button" class="btn btn-warning" data-ng-click="updateCurrentRate()"
                                    data-ng-disabled="inProgressSave">
                                Update
                                <span data-ng-if="inProgressSave"><?= Icon::show('spin', ['class' => 'fa-refresh']) ?></span>
                            </button>
                            <button class="btn" data-ng-click="clear()">
                                Clear
                            </button>
                        </div>
                    <? } ?>
                </div>

            </div>

            <? if (!Yii::$app->user->isGuest) { ?>
                <div class="col" data-ng-init="initFavoriteRates()">

                    <div data-ng-if="favoriteRates.length">
                        <h6 data-ng-if="favoriteRates.length">Saved rates</h6>

                        <table class="table table-striped table-hover table-sm fav-rates">
                            <thead>
                            <tr>
                                <th>Base</th>
                                <th>Target</th>
                                <th>Amount</th>
                                <th>Duration</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr data-ng-repeat="rate in favoriteRates" data-ng-click="changeCurrentRate(rate)"
                                data-ng-class="{'active': rate._id == currentRate._id}">
                                <td>{{rate.currencyBase}}</td>
                                <td>{{rate.currencyTarget}}</td>
                                <td>{{rate.amount}}</td>
                                <td>{{rate.duration}}</td>
                                <td data-ng-click="removeRate(rate, $event)">
                                <span>
                                    <?= Icon::show('times', ['class' => 'color-red']) ?>
                                </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div data-ng-if="!favoriteRates.length">
                        <h6>No saved rates</h6>
                    </div>

                </div>
            <? } ?>
        </div>
    </div>

    <hr data-ng-if="!!fetchedData && !!fetchedData.length"/>

    <div class="row justify-content-end">
        <button type="button" class="btn btn-secondary" data-ng-click="fetchData()" data-ng-disabled="inProgressFetch">
            Fetch
            <span data-ng-if="inProgressFetch"><?= Icon::show('spin', ['class' => 'fa-refresh']) ?></span>
        </button>
    </div>

    <div class="container-fluid fetched-data-container" data-ng-if="!!fetchedData && !!fetchedData.length">
        <div class="row">
            <div class="col">
                <canvas id="line" class="chart chart-line" chart-data="chartData.data"
                        chart-labels="chartData.labels" chart-options="chartData.options">
                </canvas>
            </div>
            <div class="col">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Rate</th>
                        <th>Evaluation</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-ng-repeat="data in fetchedData"
                        data-ng-class="{'table-danger': data.isMinimum, 'table-success': data.isMaximum}">
                        <td>{{data.date}}</td>
                        <td>{{data.rate}}</td>
                        <td>{{data.valuation}}</td>
                        <td class="comparison-icons">
                            <div data-ng-if="data.compareResult == COMPARE_RESULT_LESS"><?= Icon::show('long-arrow-down', ['class' => 'color-red']) ?></div>
                            <div data-ng-if="data.compareResult == COMPARE_RESULT_MORE"><?= Icon::show('long-arrow-up', ['class' => 'color-green']) ?></div>
                            <div data-ng-if="data.compareResult == COMPARE_RESULT_EQUALS"><?= Icon::show('arrows-h') ?></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert-container" id="alertContainer">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-ng-repeat="error in errors">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{error.message}}
        </div>
    </div>

</div>
