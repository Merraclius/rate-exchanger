<?php
namespace app\components\exchange\models;

use yii\base\Object;
use yii\helpers\ArrayHelper;


/**
 * Class Currency
 * @package app\components\exchange
 */
class Currency extends Object
{
    /**
     * @var string Title of currency
     */
    private $title;

    /**
     * @param string $title Set currency title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string Get currency title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @var string Currency code
     */
    private $code;

    /**
     * @param string $code Set currency code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string Get currency code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Currency constructor.
     * If $config is associative array, we get first value and bring it to 'code'
     * If $config is string, we bring it to 'code'
     *
     * @param array|string $config
     */
    public function __construct($config = [])
    {
        if (is_string($config)) {
            $config = [
                "code" => $config
            ];
        }
        else if (!ArrayHelper::isAssociative($config) && !empty($config)) {
            $config = [
                "code" => $config[0]
            ];
        }

        parent::__construct($config);
    }

    /**
     * @return string If title isset we return title with code in other case just code or title
     */
    public function getLabel()
    {
        if (!$this->title || strlen($this->title) == 0) {
            return $this->code;
        }

        if (!$this->code || strlen($this->code) == 0) {
            return $this->title;
        }

        return $this->title . " (" . $this->code . ")";
    }
}