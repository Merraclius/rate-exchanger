<?php

namespace tests\codeception\_mocks;

use yii\base\InvalidConfigException;
use yii\httpclient\Response as HttpResponse;

use app\components\exchange\FixerIOClient;

/**
 * Class FixerIOClientMock
 * @package tests\codeception\_mocks
 */
class FixerIOClientMock extends FixerIOClient
{
    /**
     * Mocked response which return from send method
     *
     * @var HttpResponse
     */
    protected $mockedResponse = null;

    /**
     * Mock method Exchange::send and return from it some mocked response
     *
     * @param \yii\httpclient\Request $request
     * @return HttpResponse
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function send($request)
    {
        if ($this->mockedResponse === null) {
            throw new InvalidConfigException('Response not mocked. Call method mockResponse first');
        }

        if ($this->mockedResponse instanceof \Exception) {
            throw $this->mockedResponse;
        }

        return $this->mockedResponse;
    }

    /**
     * @param $response mixed Any response which we want to get from method Exchange::send
     */
    public function mockResponse($response)
    {
        $this->mockedResponse = $response;
    }
}