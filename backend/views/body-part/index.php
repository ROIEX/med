<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\BodyPart;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Body Parts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="body-part-index">


    <p>
        <?php echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Body Part',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'description',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
