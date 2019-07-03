<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2DqGpm3yu1K7aHIVQqH5kxi4mtD1Ltzu',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'main/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'main/index',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/main',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET auth' => 'auth',
                        'GET checkauth' => 'checkauth',
                        'GET getprofiletouser' => 'getprofiletouser',
                        'GET getposts' => 'getposts',
                        'GET getcountnotviewedgroupmsgs' => 'getcountnotviewedgroupmsgs',
                        'GET getbase64fromurlimage' => 'getbase64fromurlimage',
                        'GET getpollanswervoted' => 'getpollanswervoted',
                        'POST cancelvotepoll' => 'cancelvotepoll',
                        'POST votepoll' => 'votepoll',
                        'POST addpost' => 'addpost',
                        'POST removepost' => 'removepost',
                        'POST updatephoto' => 'updatephoto',
                        'POST addblacklist' => 'addblacklist',
                        'POST removeblacklist' => 'removeblacklist',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/news',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getnews' => 'getnews',
                        'GET getonenews' => 'getonenews',
                        'GET getevents' => 'getevents',
                        'POST add' => 'add',
                        'POST remove' => 'remove',
                        'POST votepoll' => 'votepoll',
                        'POST cancelvotepoll' => 'cancelvotepoll',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/messages',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getdialogs' => 'getdialogs',
                        'GET getconversations' => 'getconversations',
                        'GET getdialog' => 'getdialog',
                        'GET getconversation' => 'getconversation',
                        'GET getmembersofconversation' => 'getmembersofconversation',
                        'GET getnewmessagesfromdialog' => 'getnewmessagesfromdialog',
                        'POST sendtodialog' => 'sendtodialog',
                        'POST sendtoconversation' => 'sendtoconversation',
                        'POST createconversation' => 'createconversation',
                        'POST removeconversation' => 'removeconversation',
                        'POST renameconversation' => 'renameconversation',
                        'POST refreshphotoconversation' => 'refreshphotoconversation',
                        'POST leaveconversation' => 'leaveconversation',
                        'POST changemembersconversation' => 'changemembersconversation',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/album',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getalbum' => 'getalbum',
                        'POST add' => 'add',
                        'POST remove' => 'remove',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/favorites',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getmyfavorites' => 'getmyfavorites',
                        'POST add' => 'add',
                        'POST remove' => 'remove',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/search',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST users' => 'users',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/settings',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getdataprivacy' => 'getdataprivacy',
                        'GET getdatablacklist' => 'getdatablacklist',
                        'GET getdataprofile' => 'getdataprofile',
                        'POST saveprofile' => 'saveprofile',
                        'POST saveprivacy' => 'saveprivacy',
                        'POST changepassword' => 'changepassword',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/adminpanel',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getgroups' => 'getgroups',
                        'GET getadmins' => 'getadmins',
                        'POST registrationaccount' => 'registrationaccount',
                        'POST blockaccount' => 'blockaccount',
                        'POST unblockaccount' => 'unblockaccount',
                        'POST creategroup' => 'creategroup',
                        'POST renamegroup' => 'renamegroup',
                        'POST moveusertodiferentgroup' => 'moveusertodiferentgroup',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/files',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET getfiles' => 'getfiles',
                        'POST load' => 'load',
                        'POST remove' => 'remove',
                    ],
                ],
            ],
        ],

    ],
    'params' => $params,
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',

        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
