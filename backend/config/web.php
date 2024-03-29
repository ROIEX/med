<?php
$config = [
    'homeUrl'=>Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute'=>'timeline-event/index',
    'controllerMap'=>[
        'file-manager-elfinder' => [
            'class' => 'mihaildev\elfinder\Controller',
            'access' => ['manager'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl' => '@storageUrl',
                    'basePath' => '@storage',
                    'path'   => '/',
                    'access' => ['read' => 'manager', 'write' => 'manager']
                ]
            ]
        ]
    ],
    'components'=>[
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => getenv('BACKEND_COOKIE_VALIDATION_KEY')
        ],
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior'
        ],
    ],
    'modules'=>[
        'i18n' => [
            'class' => 'backend\modules\i18n\Module',
            'defaultRoute'=>'i18n-message/index'
        ]
    ],
    'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'rules'=>[
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?'],
                'actions'=>['login']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions'=>['logout']
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['reset-password', 'request-password-reset']
            ],
            [
                'controllers' => ['doctor'],
                'allow' => true,
                'roles' => ['?'],
                'actions' => ['create']
            ],
            [
                'controllers' => ['payment'],
                'allow' => true,
                'roles' => ['manager'],
                'actions' => ['payment-status']
            ],
            [
                'controllers' =>['doctor'],
                'allow' => true,
                'roles' => ['manager'],
                'actions' => ['update', 'change-status']
            ],
            [
                'controllers' => ['patient'],
                'allow' => true,
                'roles' => ['manager'],
            ],
            [
                'controllers' => ['booking'],
                'allow' => true,
                'roles' => ['manager'],
            ],
            [
                'controllers' => ['invoice'],
                'allow' => true,
                'roles' => ['manager'],
            ],
            [
                'controllers' => ['invoice-item'],
                'allow' => true,
                'roles' => ['manager'],
            ],
            [
                'controllers' => ['file-storage'],
                'allow' => true,
                'roles' => ['?', 'manager'],
                //'actions'=>['upload', 'upload-delete', 'uploadPhoto']
            ],
            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['error']
            ],
            [
                'controllers' => ['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers'=>['timeline-event'],
                'allow' => true,
                'roles' => ['administrator'],
            ],
            [
                'controllers'=>['user'],
                'allow' => false,
            ],
            [
                'controllers'=>['inquiry'],
                'allow' => true,
                'roles' => ['manager'],
            ],
            [
                'controllers'=>['sign-in'],
                'allow' => true,
                'roles' => ['manager'],
            ],
            [
                'controllers' => ['mail-chimp'],
                'allow' => true,
                'roles' => ['administrator']
            ]
        ]
    ]
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class'=>'yii\gii\generators\crud\Generator',
                'templates'=>[
                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates')
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'backend'
            ]
        ]
    ];
}

return $config;
