<?php

namespace app\models;

use app\models\behaviors\TimestampBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use MongoDB\BSON\ObjectID;

use app\core\mongodb\ActiveRecord;

/**
 * Class ExchangeRate
 * @package app\models
 *
 * @property ObjectID $_id
 * @property ObjectID $userId
 * @property string $currencyBase
 * @property string $currencyTarget
 * @property integer $amount
 * @property integer $duration
 */
class ExchangeRate extends ActiveRecord
{
    const SCENARIO_FETCH = "fetch";

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'amount', 'currencyBase', 'currencyTarget', 'duration'], 'required'],
            [['amount', 'duration'], 'integer'],
            ['duration', 'integer', 'min' => 1, 'max' => 25],
            [['currencyBase', 'currencyTarget'], 'string'],
            ['currencyBase', function ($attribute, $params, $validator) {
                if ($this->$attribute === $this->currencyTarget) {
                    $this->addError($attribute, "Base and target currency can't be the same");
                }
            }],
            [['currencyBase', 'currencyTarget'], 'in', 'range' => ArrayHelper::getColumn(Yii::$app->rateFetcher->currencies, 'code')],
            [['amount', 'currencyBase', 'currencyTarget', 'duration'], 'safe'],
            ['userId', function ($attribute, $params, $validator) {
                if (!($this->$attribute instanceof ObjectID)) {
                    $this->$attribute = new ObjectID($this->$attribute);
                }
            }],
            ['currencyBase', 'unique', 'targetAttribute' => ['amount', 'currencyBase', 'currencyTarget', 'duration'], 'on' => self::SCENARIO_DEFAULT],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return parent::find()->where(
            [
                "userId" => new ObjectID(Yii::$app->user->id)
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_FETCH => ['amount', 'currencyBase', 'currencyTarget', 'duration']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ['_id', 'userId', 'amount', 'currencyBase', 'currencyTarget', 'duration', 'created', 'updated'];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['userId' => '_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!$this->userId) {
            $this->userId = Yii::$app->user->id;
        }

        return parent::beforeValidate();
    }
}