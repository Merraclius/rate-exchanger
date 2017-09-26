<?php
namespace app\models\behaviors;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior as BaseTimestampBehavior;
use MongoDB\BSON\UTCDateTime;

/**
 * Class TimestampBehavior
 * @package app\models\behaviors
 */
class TimestampBehavior extends BaseTimestampBehavior
{
    /**
     * @inheritdoc
     */
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
        ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
    ];

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        return $this->value !== null ? call_user_func($this->value, $event) : new UTCDateTime(round(microtime(true) * 1000));
    }
}
