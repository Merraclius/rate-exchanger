<?php

namespace app\components\exchange\models;

use DateTime;
use yii\base\Object;

/**
 * Class Result
 * @package app\components\exchange\models
 */
class Result extends Object
{
    /**
     * The Base currency the result was returned in
     * @var string
     */
    private $base;

    /**
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @param string $base
     */
    public function setBase($base)
    {
        $this->base = $base;
    }

    /**
     * The date the result was generated
     * @var DateTime
     */
    private $date;

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getFormatedDate()
    {
        return $this->date->format('d-m-Y');
    }

    /**
     * Convert input string into DateTime format
     * and set it into $this->date
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = new DateTime($date);
    }

    /**
     * All of the rates returned
     * @var array
     */
    private $rates;

    /**
     * @return array
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * @param array $rates
     */
    public function setRates($rates)
    {
        $this->rates = $rates;
    }

    /**
     * Get an individual rate by Currency code
     * Will return null if currency is not found in the result
     *
     * @param string $code
     * @return float|null
     */
    public function getRate($code = null)
    {
        if ($code == $this->getBase()) {
            return 1.0;
        }

        if (is_null($code) && count($this->rates) == 1) {
            return reset($this->rates);
        }
        else if (isset($this->rates[$code])) {
            return $this->rates[$code];
        }

        return null;
    }
}