<?php

namespace app\components\exchange;

use app\components\exchange\models\Result;
use app\components\exchange\exceptions\ResponseException;
use app\components\exchange\exceptions\ConnectionException;

/**
 * Interface IExchangeRate
 * @package app\components\exchange
 */
interface IExchangeRate
{
    /**
     * Sets the protocol to https
     *
     * @return IExchangeRate
     */
    public function secure();

    /**
     * Sets the protocol to http
     *
     * @return IExchangeRate
     */
    public function notSecure();

    /**
     * Sets the base currency
     *
     * @param  string $currency
     * @return IExchangeRate
     */
    public function baseCurrency($currency);

    /**
     * Sets the currencies to return.
     * Expects either a list of arguments or
     * a single argument as array
     *
     * @param  string[] $currencies
     * @return IExchangeRate
     */
    public function symbols($currencies = null);

    /**
     * Defines that the api call should be
     * historical, meaning it will return rates
     * for any day since the selected date
     *
     * @param  string $date
     * @return IExchangeRate
     */
    public function historical($date);

    /**
     * Returns the correctly formatted url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Makes the request and returns the response
     * with the rates, as a Result object
     *
     * @throws ConnectionException if the request is incorrect or times out
     * @throws ResponseException if the response is malformed
     * @return Result
     */
    public function getResult();
}