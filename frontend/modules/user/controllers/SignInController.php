<?php

namespace frontend\modules\user\controllers;

use common\commands\command\SendEmailCommand;
use common\models\User;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class SignInController extends Controller
{

    public function actions()
    {
        return [
            'oauth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successOAuthCallback']
            ]
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['signup', 'login', 'request-password-reset', 'reset-password', 'oauth', 'activate', 'reset-success'],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'actions' => ['signup', 'login', 'oauth'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function () {
                            return Yii::$app->controller->redirect(['/user/default/index']);
                        }
                    ],
                    [
                        'actions' => ['logout', 'request-password-reset', 'reset-password', 'reset-success'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

//    public function actionLogin()
//    {
//        $model = new LoginForm();
//        if (Yii::$app->request->isAjax) {
//            $model->load($_POST);
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            return ActiveForm::validate($model);
//        }
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                'model' => $model
//            ]);
//        }
//    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

//    public function actionSignup()
//    {
//        $model = new SignupForm();
//        if ($model->load(Yii::$app->request->post())) {
//            $user = $model->signup();
//            if ($user && Yii::$app->getUser()->login($user)) {
//                return $this->goHome();
//            }
//        }
//
//        return $this->render('signup', [
//            'model' => $model
//        ]);
//    }

//    public function actionRequestPasswordReset()
//    {
//        $model = new PasswordResetRequestForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail()) {
//                Yii::$app->getSession()->setFlash('alert', [
//                    'body' => Yii::t('frontend', 'Check your email for further instructions.'),
//                    'options' => ['class' => 'alert-success']
//                ]);
//
//                return $this->goHome();
//            } else {
//                Yii::$app->getSession()->setFlash('alert', [
//                    'body' => Yii::t('frontend', 'Sorry, we are unable to reset password for email provided.'),
//                    'options' => ['class' => 'alert-danger']
//                ]);
//            }
//        }
//
//        return $this->render('requestPasswordResetToken', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Activating account by token.
     * @param $token
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionActivate($token)
    {
        $model = User::findOne(['activation_token' => $token]);
        if (!$model) {
            throw new NotFoundHttpException;
        }
        /* @var $model User */
        $model->status = $model::STATUS_ACTIVE;
        $model->save(false);
        Yii::$app->getSession()->setFlash('alert', [
            'body'=>Yii::t('frontend', 'Activation successful! Thank you for registration.'),
            'options'=>['class'=>'alert-success']
        ]);
        return $this->redirect('https://www.dermdash.com/user-registration-confirmation');
    }

    public function actionResetPassword($token)
    {
        $this->layout = '/_clear';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            return $this->redirect('reset-success');
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionResetSuccess()
    {
        $this->layout = '/_clear';
        return $this->render('success');
    }

    /**
     * @param $client \yii\authclient\BaseClient
     * @return bool
     * @throws Exception
     */
    public function successOAuthCallback($client)
    {
        // use BaseClient::normalizeUserAttributeMap to provide consistency for user attribute`s names
        $attributes = $client->getUserAttributes();
        $user = User::find()->where([
                'oauth_client'=>$client->getName(),
                'oauth_client_user_id'=>ArrayHelper::getValue($attributes, 'id')
            ])
            ->one();
        if (!$user) {
            $user = new User();
            $user->scenario = 'oauth_create';
            $user->username = ArrayHelper::getValue($attributes, 'login');
            $user->email = ArrayHelper::getValue($attributes, 'email');
            $user->oauth_client = $client->getName();
            $user->oauth_client_user_id = ArrayHelper::getValue($attributes, 'id');
            $password = Yii::$app->security->generateRandomString(8);
            $user->setPassword($password);
            if ($user->save()) {
                $profileData = [];
                if ($client->getName() === 'facebook') {
                    $profileData['firstname'] = ArrayHelper::getValue($attributes, 'first_name');
                    $profileData['lastname'] = ArrayHelper::getValue($attributes, 'last_name');
                }
                $user->afterSignup($profileData);
                $sentSuccess = Yii::$app->commandBus->handle(new SendEmailCommand([
                    'view' => 'oauth_welcome',
                    'params' => ['user'=>$user, 'password'=>$password],
                    'subject' => Yii::t('frontend', '{app-name} | Your login information', ['app-name'=>Yii::$app->name]),
                    'to' => $user->email
                ]));
                if ($sentSuccess) {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                            'options'=>['class'=>'alert-success'],
                            'body'=>Yii::t('frontend', 'Welcome to {app-name}. Email with your login information was sent to your email.', [
                                'app-name'=>Yii::$app->name
                            ])
                        ]
                    );
                }

            } else {
                // We already have a user with this email. Do what you want in such case
                if ($user->email && User::find()->where(['email'=>$user->email])->count()) {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                            'options'=>['class'=>'alert-danger'],
                            'body'=>Yii::t('frontend', 'We already have a user with email {email}', [
                                'email'=>$user->email
                            ])
                        ]
                    );
                } else {
                    Yii::$app->session->setFlash(
                        'alert',
                        [
                            'options'=>['class'=>'alert-danger'],
                            'body'=>Yii::t('frontend', 'Error while oauth process.')
                        ]
                    );
                }

            };
        }
        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            return true;
        } else {
            throw new Exception('OAuth error');
        }
    }
}
