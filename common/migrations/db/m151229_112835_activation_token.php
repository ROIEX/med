<?php

use yii\db\Schema;
use yii\db\Migration;

class m151229_112835_activation_token extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\User::tableName(),'activation_token',$this->string(64));
    }

    public function down()
    {
        echo "m151229_112835_activation_token cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
