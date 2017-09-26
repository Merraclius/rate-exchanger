<?php

namespace v1\controllers;

use Yii;
use yii\rest\Controller;

use app\models\ExchangeRate;
use app\components\exchange\RateFetcher;
use app\components\exchange\models\Result;
use app\components\exchange\models\Currency;

/**
 * Class PublicController
 * @package v1\controllers
 */
class PublicController extends Controller
{
    /**
     * Return array of prepared currencies
     *
     * @return array
     */
    public function actionCurrencies()
    {
        return array_map(function($currency) {
            /** @var $currency Currency */
            return [
                "label" => $currency->getLabel(),
                "code" => $currency->getCode()
            ];
        }, Yii::$app->rateFetcher->currencies);
    }

    /**
     * Fetch data with rateFetcher and return array of results
     * In case ExchangeRate model is not valid return it
     *
     * @return ExchangeRate|array
     */
    public function actionFetch()
    {
        $model = new ExchangeRate([
            "scenario" => ExchangeRate::SCENARIO_FETCH
        ]);
        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return $model;
        }

        /** @var RateFetcher $fetcher */
        $fetcher = Yii::$app->rateFetcher;
        /** @var Result[] $results */
        $results = $fetcher->fetch($model);

        return array_map(function($result) {
            /** @var Result $result */
            return [
                "date" => $result->getFormatedDate(),
                "rate" => $result->getRate()
            ];
        }, $results);
    }
}