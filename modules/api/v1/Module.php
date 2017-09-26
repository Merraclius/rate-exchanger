<?php

namespace v1;

/**
 * Class Module
 * @package v1
 */
class Module extends \yii\base\Module
{
    /**
     * Initialize api module with parser for requests and url rules
     */
    public function init()
    {
        parent::init();

        \Yii::$app->request->parsers['application/json'] = 'yii\web\JsonParser';

        \Yii::$app->urlManager->addRules([
            [
                'class' => 'yii\rest\UrlRule',
                'patterns' => [
                    'GET' => 'index',
                    'POST' => 'create',
                    'PUT {id}' => 'update',
                    'DELETE {id}' => 'delete'
                ],
                'controller' => 'v1/rate',
                'tokens' => [
                    '{id}' => '<id:\\w+>',
                ]
            ]
        ]);
    }
}