<?php

$config = [
    'id' => 'basic',
    'basePath' => \DockerEnv::APP_DIR,
    'bootstrap' => [
        'log',
        'v1'
    ],
    'aliases' => [
        '@v1' => '@app/modules/api/v1',
    ],
    'vendorPath' => \DockerEnv::VENDOR_DIR,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\ApcCache',
            'useApcu' => true,
        ],
        'db' => [
            'class' => '\yii\mongodb\Connection',
            'dsn' => \DockerEnv::dbDsn()
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => \DockerEnv::get('YII_TRACELEVEL', 0),
            'targets' => [
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stdout',
                    'levels' => ['info', 'trace'],
                    'logVars' => [],
                ],
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stderr',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'request' => [
            'cookieValidationKey' => \DockerEnv::get('COOKIE_VALIDATION_KEY', null, !YII_ENV_TEST),
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'fixerIO' => [
            'class' => 'app\components\exchange\FixerIOClient',
            'baseUrl' => 'api.fixer.io',
            'protocol' => 'https',
            'baseCurrency' => 'EUR'
        ],
        'rateFetcher' => [
            'class' => 'app\components\exchange\RateFetcher',
            'exchangeRatesComponent' => 'fixerIO',
            'currencies' => require('currencies.php')
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => \DockerEnv::get('AUTH_CLIENT_GOOGLE_CLIENT_ID', null, !YII_ENV_TEST),
                    'clientSecret' => \DockerEnv::get('AUTH_CLIENT_GOOGLE_CLIENT_SECRET', null, !YII_ENV_TEST),
                ],
            ],
        ]
    ],
    'modules' => [
        'v1' => [
            'class' => 'v1\Module'
        ]
    ],
    'params' => require('params.php'),
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
