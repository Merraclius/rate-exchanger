<?php
/**
 * This is the configuration for the Yii app shared by all tests
 */
return [
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            // Uncomment to generate fixtures in another language
            //'language' => 'de_DE',
            'fixtureDataPath' => '@tests/codeception/fixtures/data',
            'templatePath' => '@tests/codeception/fixtures/templates',
            'namespace' => 'tests\codeception\fixtures',
        ],
    ],
    'components' => [
        'fixerIO' => [
            'class' => 'tests\codeception\_mocks\FixerIOClientMock',
            'baseUrl' => 'test.base.url',
            'protocol' => 'https',
            'baseCurrency' => 'EUR',
        ],
        'rateFetcher' => [
            'class' => 'tests\codeception\_mocks\RateFetcherMock',
            'exchangeRatesComponent' => 'fixerIO',
            'currencies' => [
                "code" => "TEST",
                "title" => "Test currency"
            ]
        ]
    ],
];
