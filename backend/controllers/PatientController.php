<?php

namespace backend\controllers;

use Yii;
use common\models\Inquiry;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use common\models\User;
use yii\web\NotFoundHttpException;
use common\models\PromoUsed;

class PatientController extends Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->can('administrator')) {
            $dataProvider = new ActiveDataProvider([
                'query' => User::find()->patientList()
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => User::find()->doctorPatientList()
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel((int)$id);
        if (Yii::$app->user->can('administrator')) {
            $query = Inquiry::find()->where(['user_id' => $id])->orderBy('created_at')->all();
        }
        else {
            $query = Inquiry::find()
                ->where(['inquiry.user_id' => $id])
                ->join('LEFT JOIN', 'inquiry_doctor_list as list', 'list.inquiry_id = inquiry.id')
                ->andWhere(['list.user_id' => Yii::$app->user->id])
                ->orderBy('created_at')
                ->all();
        }

        $inquiry_list = new ArrayDataProvider([
            'allModels' => $query
        ]);

        $used_promo = PromoUsed::getUsedPromocodeList($model->id);

        return $this->render('view', [
            'model' => $model,
            'inquiry_list' => $inquiry_list,
            'used_promo' => $used_promo,
        ]);
    }

    /**
     * Finds the UserProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserProfile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}