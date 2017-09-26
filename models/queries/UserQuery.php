<?php
namespace app\models\queries;

use yii\mongodb\ActiveQuery;

/**
 * Class UserQuery
 * @package app\models\queries
 */
class UserQuery extends ActiveQuery
{
    /**
     * @return UserQuery the query with condition for given email applied
     */
    public function email($email)
    {
        return $this->andWhere(['email' => $email]);
    }
}
