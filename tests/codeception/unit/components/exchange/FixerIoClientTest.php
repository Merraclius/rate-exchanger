<?php

namespace tests\codeception\unit\components\exchange;

use Yii;
use yii\codeception\TestCase;
use yii\httpclient\Response;
use yii\web\HeaderCollection;

use app\components\exchange\RateFetcher;
use app\components\exchange\models\Result;

/**
 * Class FixerIoClientTest
 * @package tests\codeception\unit\components\exchange
 */
class FixerIoClientTest extends TestCase
{
    /**
     * Test constructor with different input currencies
     *
     * @dataProvider providerForConstructorWithCurrencies
     * @param $currencies
     * @param $expectedCurrencies
     */
    public function testConstructorWithCurrencies($currencies, $expectedCurrencies)
    {
        $exchange = new RateFetcher(
            ["currencies" => $currencies]
        );

        foreach ($exchange->getCurrencies() as $index => $currency) {
            foreach ($expectedCurrencies[$index] as $key => $expectedValue) {
                $this->assertEquals($expectedValue, $currency->{$key}, "Wrong key: {$key} or value: {$currency->{$key}}");
            }
        }
    }

    /**
     * @return array with currencies and expected currencies
     */
    public function providerForConstructorWithCurrencies()
    {
        return [
            "Simple currencies without order" => [
                "currencies" => [
                    ["code" => "CNY", "title" => "China Yuan Renminbi"],
                    ["code" => "BGN", "title" => "Bulgaria Lev"]
                ],
                "expectedCurrencies" => [
                    ["code" => "BGN", "title" => "Bulgaria Lev"],
                    ["code" => "CNY", "title" => "China Yuan Renminbi"]
                ]
            ],
            "Short currencies without order" => [
                "currencies" => [
                    ["CNY"],
                    ["BGN"]
                ],
                "expectedCurrencies" => [
                    ["code" => "BGN", "title" => ""],
                    ["code" => "CNY", "title" => ""]
                ]
            ]
        ];
    }

    /**
     * Test that method Exchange::getResult throws exception on wrong response object
     */
    public function testGetResultThrowsExceptionOnWrongResponseObject()
    {
        $this->setExpectedExceptionRegExp('app\components\exchange\exceptions\ResponseException', "[not instance of response]i");

        $exchange = Yii::$app->fixerIO;

        $exchange->mockResponse("test");
        $exchange->getResult();
    }

    /**
     * Test that method Exchange::getResult throws exception on wrong response object with message from response body
     */
    public function testGetResultThrowsExceptionOnFailedResponseWithMessage()
    {
        $this->setExpectedException('app\components\exchange\exceptions\ResponseException', "Test error message");

        $exchange = Yii::$app->fixerIO;

        $headers = new HeaderCollection();
        $headers->add("http-code", 400);
        $response = new Response(
            [
                "client" => $exchange,
                "headers" => $headers,
                "data" => [
                    "error" => "Test error message"
                ]
            ]
        );

        $exchange->mockResponse($response);
        $exchange->getResult();
    }

    /**
     * Test that method Exchange::getResult throws exception on wrong response object without message from response body
     */
    public function testGetResultThrowsExceptionOnFailedResponseWithoutMessage()
    {
        $this->setExpectedException('app\components\exchange\exceptions\ResponseException', "Response is failed");

        $exchange = Yii::$app->fixerIO;

        $headers = new HeaderCollection();
        $headers->add("http-code", 400);
        $response = new Response(
            [
                "client" => $exchange,
                "headers" => $headers
            ]
        );

        $exchange->mockResponse($response);
        $exchange->getResult();
    }

    /**
     * Test that method Exchange::getResult throws exception on wrong response object without message from response body
     *
     * @dataProvider providerForGetResultThrowsExceptionOnMalformedResponse
     * @param $responseBody
     */
    public function testGetResultThrowsExceptionOnMalformedResponse($responseBody)
    {
        $this->setExpectedException('app\components\exchange\exceptions\ResponseBodyMalformedException');

        $exchange = Yii::$app->fixerIO;

        $headers = new HeaderCollection();
        $headers->add("http-code", 200);
        $response = new Response(
            [
                "client" => $exchange,
                "headers" => $headers,
                "data" => $responseBody
            ]
        );

        $exchange->mockResponse($response);
        $exchange->getResult();
    }

    /**
     * @return array mixed without some of individual fields
     */
    public function providerForGetResultThrowsExceptionOnMalformedResponse()
    {
        return [
            "Without rates" => [
                "responseBody" => [
                    "base" => "EUR",
                    "date" => "2015-12-31 00:00:00.000000"
                ]
            ],
            "Without base" => [
                "responseBody" => [
                    "date" => "2015-12-31 00:00:00.000000",
                    "rates" => [
                        "USD" => 0.013495,
                        "EUR" => 0.012396
                    ]
                ]
            ],
            "Without date" => [
                "responseBody" => [
                    "base" => "EUR",
                    "rates" => [
                        "USD" => 0.013495,
                        "EUR" => 0.012396
                    ]
                ]
            ],
            "With wrong rates" => [
                "responseBody" => [
                    "base" => "EUR",
                    "date" => "2015-12-31 00:00:00.000000",
                    "rates" => "'USD' -> 0.013495"
                ]
            ]
        ];
    }

    /**
     * Test that method Exchange::getResult returned correct object of class Result
     */
    public function testGetResultReturnResultObject()
    {
        $exchange = Yii::$app->fixerIO;

        $resultBody = [
            "base" => "EUR",
            "date" => "2015-12-31 00:00:00.000000",
            "rates" => [
                "USD" => 0.013495,
                "EUR" => 0.012396
            ]
        ];

        $headers = new HeaderCollection();
        $headers->add("http-code", 200);
        $response = new Response(
            [
                "client" => $exchange,
                "headers" => $headers,
                "data" => $resultBody
            ]
        );

        $exchange->mockResponse($response);
        $actualResult = $exchange->getResult();
        $expectedResult = new Result($resultBody);

        $this->assertEquals($expectedResult, $actualResult, "Wrong result object returned from Exchange::getData");
    }
}