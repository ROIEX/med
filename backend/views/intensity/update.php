<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Intensity */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Intensity',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Intensities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="intensity-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
