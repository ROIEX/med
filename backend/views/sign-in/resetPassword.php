<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\ResetPasswordForm */

$this->title = Yii::t('app', 'Reset password');
?>

<div class="login-box">

    <div class="login-logo">
        <?php echo Html::encode($this->title) ?>
    </div>
    <div class="header"></div>
    <div class="login-box-body">

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
        <?php echo $form->field($model, 'password')->passwordInput() ?>
        <div class="form-group">
            <?php echo Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>

