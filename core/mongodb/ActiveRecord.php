<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 02.09.2017
 * Time: 16:39
 */

namespace app\core\mongodb;

use Yii;
use yii\mongodb\ActiveRecord as BaseActiveRecord;

/**
 * Class ActiveRecord
 * @package app\core\mongodb
 */
class ActiveRecord extends BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }
}