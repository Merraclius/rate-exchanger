<?php

namespace tests\codeception\unit\components\exchange;

use app\components\exchange\models\Currency;
use Codeception\Module\TestCaseHelper;
use yii\codeception\TestCase;

/**
 * Class CurrencyTest
 * @package tests\codeception\unit\components\exchange
 */
class CurrencyTest extends TestCase
{
    /**
     * @dataProvider providerForConstructor
     * @param $config
     * @param $expectedValues
     */
    public function testConstructor($config, $expectedValues)
    {
        $currency = new Currency($config);

        $this->assertEquals($expectedValues['code'], $currency->getCode(), "Wrong currency code");
        $this->assertEquals($expectedValues['title'], $currency->getTitle(), "Wrong currency title");
    }

    /**
     * @return array with config for new class Currency and array with expectedValues
     */
    public function providerForConstructor()
    {
        return [
            "Config with string as code" => [
                "config" => "USD",
                "expectedValues" => [
                    "code" => "USD",
                    "title" => null
                ]
            ],
            "Config with associative array as code" => [
                "config" => ["USD"],
                "expectedValues" => [
                    "code" => "USD",
                    "title" => null
                ]
            ],
            "Config only with code" => [
                "config" => [
                    "code" => "AUD"
                ],
                "expectedValues" => [
                    "code" => "AUD",
                    "title" => null
                ]
            ],
            "Config only with title" => [
                "config" => [
                    "title" => "Bulgaria Lev"
                ],
                "expectedValues" => [
                    "code" => null,
                    "title" => "Bulgaria Lev"
                ]
            ],
            "Config with all parameters" => [
                "config" => [
                    "code" => "BRL",
                    "title" => "Brazil Real"
                ],
                "expectedValues" => [
                    "code" => "BRL",
                    "title" => "Brazil Real"
                ]
            ],
            "Config with empty array" => [
                "config" => [],
                "expectedValues" => [
                    "code" => null,
                    "title" => null
                ]
            ],
            "Config without parameters" => [
                "config" => null,
                "expectedValues" => [
                    "code" => null,
                    "title" => null
                ]
            ]
        ];
    }

    /**
     * Test that code from getter not modified before return
     */
    public function testGetCodeReturnNotModifiedValue()
    {
        $expectedCode = "EUR";

        $currency = new Currency();

        TestCaseHelper::setProperty($currency, 'code', $expectedCode);

        $this->assertEquals($expectedCode, $currency->getCode(), "Wrong currency code returned from getter");
    }

    /**
     * Test that setter of property 'code' not modifying value
     */
    public function testSetCodeNotModifyingValue()
    {
        $expectedCode = "EUR";

        $currency = new Currency();

        $currency->setCode($expectedCode);

        $actualCode = TestCaseHelper::getProperty($currency, 'code');

        $this->assertEquals($expectedCode, $actualCode, "Currency modifying code after set it");
    }

    /**
     * Test that default title is null
     */
    public function testGetTitleDefault()
    {
        $currency = new Currency();

        $this->assertNull($currency->getTitle(), "Default value of 'title' is not empty");
    }

    /**
     * Test that title from getter not modified before return
     */
    public function testGetTitleReturnNotModifiedValue()
    {
        $expectedTitle = "Euro Member Countries";

        $currency = new Currency();

        TestCaseHelper::setProperty($currency, 'title', $expectedTitle);

        $this->assertEquals($expectedTitle, $currency->getTitle(), "Wrong currency title returned from getter");
    }

    /**
     * Test that setter of property 'title' not modifying value
     */
    public function testSetTitleNotModifyingValue()
    {
        $expectedTitle = "Euro Member Countries";

        $currency = new Currency();

        $currency->setTitle($expectedTitle);

        $actualTitle = TestCaseHelper::getProperty($currency, 'title');

        $this->assertEquals($expectedTitle, $actualTitle, "Currency modifying title after set it");
    }

    /**
     * Test that method 'getLabel' returned correct label
     *
     * @dataProvider providerForGetLabel
     * @param $code string Currency code
     * @param $title string Currency title
     * @param $expectedLabel string Expected generated label
     */
    public function testGetLabel($code, $title, $expectedLabel)
    {
        $currency = new Currency([
            "code" => $code,
            "title" => $title
        ]);

        $this->assertEquals($expectedLabel, $currency->getLabel(), "Wrong label from method 'getLabel'");
    }

    /**
     * @return array with code, title and expectedLabel, generated from method getLabel
     */
    public function providerForGetLabel()
    {
        return [
            "Simple code and title" => [
                "code" => "EUR",
                "title" => "Euro Member Countries",
                "expectedLabel" => "Euro Member Countries (EUR)"
            ],
            "Code without title" => [
                "code" => "EUR",
                "title" => null,
                "expectedLabel" => "EUR"
            ],
            "Title without code" => [
                "code" => null,
                "title" => "Euro Member Countries",
                "expectedLabel" => "Euro Member Countries"
            ],
            "Without title and code" => [
                "code" => null,
                "title" => null,
                "expectedLabel" => ""
            ]
        ];
    }
}