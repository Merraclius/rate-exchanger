<?php

namespace app\models;

use app\core\mongodb\ActiveRecord;
use MongoDB\BSON\ObjectID;

/**
 * Class Auth
 * @package app\models
 *
 * @property ObjectID $_id
 * @property ObjectID $userId
 * @property string $source
 * @property string $sourceId
 */
class Auth extends ActiveRecord
{
    /** @var ObjectID Mongo unique id */
    public $_id;
    /** @var ObjectID Mongo unique id of user */
    public $userId;
    /** @var string Source of auth */
    public $source;
    /** @var string Id of identity source */
    public $sourceId;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ["_id", "userId", "source", "sourceId"];
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['_id' => 'userId']);
    }
}