<?php
/**
 * Created by PhpStorm.
 * User: rexit
 * Date: 13.01.16
 * Time: 21:01
 */

namespace frontend\modules\api\v1\resources;


use common\components\Yelp;

class UserProfile extends \common\models\UserProfile
{
    public function fields()
    {
        return [
            'gender',
            'date_of_birth',
            'phone',
            'state_id',
            'city',
            'zipcode',
            'email',
            'state_notification'
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->email = $this->user->email;
        $yelp = new Yelp();
        $dataYelp = $yelp->searchByPhone($this->phone);
        $this->rating = $dataYelp['rating'];
        $this->reviews = $dataYelp['reviews'];
        $this->mobile_url = $dataYelp['mobile_url'];
        if ($this->date_of_birth) {
            $this->date_of_birth = date('m/d/Y',strtotime($this->date_of_birth));
        }
    }
}