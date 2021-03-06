<?php

namespace common\models\queries;

use Yii;
use core\db\ActiveQuery;

class CustomerFavoriteQuery extends ActiveQuery
{

    /**
     * 过滤用户填写的原因 
     *
     * @param boolean $bool - 是否是用户生成的原因
     * @return $this
     */
    public function filterUserReason($bool = true)
    {
        return $this->andWhere(['is_user_reason' => $bool ? 1 : 0]);
    }


}