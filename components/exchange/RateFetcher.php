<?php

namespace app\components\exchange;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use app\models\ExchangeRate;
use app\components\exchange\models\Result;
use app\components\exchange\models\Currency;

/**
 * Class RateFetcher
 * @package app\components\exchange
 */
class RateFetcher extends Component
{
    /**
     * @var \app\components\exchange\IExchangeRate $exchangeRatesComponent
     */
    private $exchangeRatesComponent;

    /**
     * @var Currency[] Array with supported currencies
     */
    private $currencies;

    /**
     * Convert input array of arrays into array of object of classes Currency
     * sort it by code ascending and assign it to property 'currencies'
     *
     * @param array $currencies Array of currencies with keys 'code' and optional 'title'
     */
    public function setCurrencies($currencies)
    {
        $currenciesModels = [];

        foreach ($currencies as $currency) {
            $currenciesModels[] = new Currency($currency);
        }

        usort($currenciesModels, function ($a, $b) {
            return $a->code <=> $b->code;
        });

        $this->currencies = $currenciesModels;
    }

    /**
     * @return Currency[]
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * Find component by name in application and set it to $exchangeRatesComponent
     *
     * @param $componentName
     * @throws InvalidConfigException In case component not found or component not instance of IExchangeRate
     */
    public function setExchangeRatesComponent($componentName)
    {
        if (!Yii::$app->has($componentName) || !(Yii::$app->get($componentName) instanceof IExchangeRate)) {
            throw new InvalidConfigException("Wrong exchange rate component in Converter component");
        }

        $this->exchangeRatesComponent = Yii::$app->get($componentName);
    }

    /**
     * @var string Base currency code
     */
    private $from;

    /**
     * Set base currency to property $from
     *
     * @param $currencyName
     * @return $this
     */
    public function from($currencyName)
    {
        $this->from = $currencyName;

        return $this;
    }

    /**
     * @var string Target currency code
     */
    private $to;

    /**
     * Set target currency to property $to
     *
     * @param $currencyName
     * @return $this
     */
    public function to($currencyName)
    {
        $this->to = $currencyName;

        return $this;
    }

    /**
     * @var integer Count of weeks to parse
     */
    private $weeks;

    /**
     * Set count of weeks to $weeks
     *
     * @param $weeks
     * @return $this
     */
    public function weeks($weeks)
    {
        $this->weeks = $weeks;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException In case we missing available currencies
     */
    public function init()
    {
        parent::init();

        if (!$this->currencies || count($this->currencies) == 0) {
            throw new InvalidConfigException("Please add currencies in configuration of class 'Exchange'");
        }
    }

    /**
     * Fetch results from exchange rate component and returned result
     *
     * @param ExchangeRate|null $exchangeRate If passed, we populate current instance with data from it
     * @return Result[] Fetched results from exchange rate component
     * @throws Exception In case we missing exchange component
     */
    public function fetch(ExchangeRate $exchangeRate = null)
    {
        if (!$this->exchangeRatesComponent) {
            throw new Exception("Missing exchange component");
        }

        if ($exchangeRate !== null) {
            $this->populate($exchangeRate);
        }

        /** @var \app\components\exchange\IExchangeRate $exchange */
        $exchange = $this->exchangeRatesComponent
            ->baseCurrency($this->from)
            ->symbols($this->to);

        $dates = $this->generateDates();

        /** @var Result[] $rates */
        $rates = [];

        foreach ($dates as $date) {
            $exchange->historical($date);

            /** @var \app\components\exchange\models\Result $result */
            $rates[] = $exchange->getResult();
        }

        return $rates;
    }

    /**
     * Generate array of date, based on count of weeks
     *
     * @return string[]
     */
    private function generateDates()
    {
        $begin = new \DateTime();
        $interval = \DateInterval::createFromDateString("-1 week");
        $range = new \DatePeriod($begin, $interval, $this->weeks);

        $dates = [];

        foreach($range as $date){
            $dates[] = $date->format("d-m-Y");
        }

        return $dates;
    }

    /**
     * Populate current instance with values from $exchangeRate
     *
     * @param ExchangeRate $exchangeRate
     * @throws InvalidConfigException In case $exchangeRate is not valid
     */
    private function populate(ExchangeRate $exchangeRate)
    {
        if (!$exchangeRate->validate()) {
            throw new InvalidConfigException("Not valid exchange rate to populate: " . json_encode($exchangeRate->errors));
        }

        $this->from($exchangeRate->currencyBase);
        $this->to($exchangeRate->currencyTarget);
        $this->weeks($exchangeRate->duration);
    }
}