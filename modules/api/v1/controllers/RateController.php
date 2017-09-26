<?php

namespace v1\controllers;

use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use MongoDB\BSON\ObjectID;
use app\models\ExchangeRate;

/**
 * Class RateController
 * @package v1\controllers
 */
class RateController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = "app\models\ExchangeRate";

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();

        ArrayHelper::remove($actions, 'view');
        ArrayHelper::remove($actions, 'options');

        return $actions;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?']
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException("You are not allowed to perform this action.");
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function findModel($id, $action)
    {
        $id = new ObjectID($id);

        /** @var ExchangeRate $modelClass */
        $modelClass = $this->modelClass;

        $model = $modelClass::findOne($id);

        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }
}