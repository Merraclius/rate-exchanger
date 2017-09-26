<?php

namespace app\components\exchange;

use yii\httpclient\Client as HttpClient;
use yii\httpclient\Response as HttpResponse;
use yii\httpclient\Exception as HttpException;

use app\components\exchange\models\Result;
use app\components\exchange\exceptions\ResponseException;
use app\components\exchange\exceptions\ConnectionException;
use app\components\exchange\exceptions\ResponseBodyMalformedException;

/**
 * Class Exchange
 * @package app\components\exchange
 */
class FixerIOClient extends HttpClient implements IExchangeRate
{
    /**
     * Default protocol (http or https)
     *
     * @var string
     */
    public $protocol;

    /**
     * Base currency
     *
     * @var string
     */
    public $baseCurrency;

    /**
     * Date when an historical call is made
     *
     * @var string
     */
    private $date;

    /**
     * List of currencies to return
     *
     * @var string[]
     */
    private $symbols = [];

    /**
     * @inheritdoc
     */
    public function secure()
    {
        $this->protocol = 'https';

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function notSecure()
    {
        $this->protocol = 'http';

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function baseCurrency($currency)
    {
        $this->baseCurrency = $currency;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function symbols($currencies = null)
    {
        if (func_num_args() and !is_array(func_get_args()[0])) {
            $currencies = func_get_args();
        }

        $this->symbols = $currencies;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function historical($date)
    {
        $this->date = date('Y-m-d', strtotime($date));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->buildUrl($this->baseUrl);
    }

    /**
     * @inheritdoc
     */
    public function getResult()
    {
        $url = $this->buildUrl($this->baseUrl);

        try {
            $response = $this->makeRequest($url);

            return $this->prepareResponseResult($response);
        } catch (HttpException $e) {
            throw new ConnectionException($e->getMessage());
        }
    }

    /**
     * Forms the correct url from the different parts
     *
     * @param  string $url
     * @return string
     */
    private function buildUrl($url)
    {
        $url = $this->protocol . '://' . $url . '/';

        if ($this->date) {
            $url .= $this->date;
        } else {
            $url .= 'latest';
        }

        $url .= '?base=' . $this->baseCurrency;

        if ($symbols = $this->symbols) {
            $url .= '&symbols=' . implode(',', $symbols);
        }

        return $url;
    }

    /**
     * Makes the http request
     *
     * @param  string $url
     * @return HttpResponse
     */
    private function makeRequest($url)
    {
        $request = $this->createRequest()
            ->setMethod("GET")
            ->setUrl($url);

        return $request->send();
    }

    /**
     * Get object of type HttpResponse, check it and return object of type Result if response is correct
     *
     * @param  HttpResponse $response
     * @throws ResponseException if the response code is not start with 20 and container error message from response (if exists)
     * @throws ResponseBodyMalformedException if response body is malformed
     * @return Result
     */
    private function prepareResponseResult($response)
    {
        if (!($response instanceof HttpResponse)) {
            throw new ResponseException("Response object not instance of Response class");
        }

        $data = $response->data;

        if (!$response->isOk) {
            $errorMsg = (isset($data['error'])) ? $data["error"] : "Response is failed";

            throw new ResponseException($errorMsg);
        }

        if (
            !isset($data['rates']) ||
            !is_array($data['rates']) ||
            !isset($data['base']) ||
            !isset($data['date'])
        ) {
            throw new ResponseBodyMalformedException();
        }

        return new Result($data);
    }

}