<?php

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'controllerMap'=>[
        'message'=>[
            'class'=>'console\controllers\ExtendedMessageController'
        ],
        'migrate'=>[
            'class'=>'yii\console\controllers\MigrateController',
            'migrationPath'=>'@common/migrations/db',
            'migrationTable'=>'{{%system_db_migration}}'
        ],
        'rbac-migrate'=>[
            'class'=>'console\controllers\RbacMigrateController',
            'migrationPath'=>'@common/migrations/rbac/',
            'migrationTable'=>'{{%system_rbac_migration}}',
            'templateFile' => '@common/rbac/views/migration.php'
        ],
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
        ],
    ]
];
