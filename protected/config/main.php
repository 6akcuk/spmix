<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SPMix',
    'language' => 'ru',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.modules.users.models.*',
        'application.modules.users.components.*',
        'application.modules.users.components.views.*',
        'ext.ActiveHtml.*',
        'ext.SmsDelivery.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'s1a55j7',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1','136.169.156.108','92.50.166.78'),
		),
        'users' => array(
            'onlineInterval' => 10, // сколько минут считать пользователя онлайн
        ),
        'purchases',
    ),

	// application components
	'components'=>array(
        'clientScript' => array(
            'class' => 'ext.ActiveHtml.ClientScript',
        ),

		'user'=>array(
            'class' => 'application.modules.users.components.WebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format

        'authManager' => array(
            'class' => 'CDbAuthManager',
            'itemTable' => 'rbac_items',
            'itemChildTable' => 'rbac_item_childs',
            'assignmentTable' => 'rbac_assignments',
        ),

		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName' => false,
			'rules'=>array(
                'login' => 'site/login',
                'logout' => 'site/logout',
                'register' => 'site/register',
                'register/step<step:\d+>' => 'site/register',
                'register/sendSMS' => 'site/sendSMSRegister',
                'id<id:\d+>' => 'users/profiles',
                'edit' => 'users/profiles/edit',
                '<controller:(users)>' => 'users/users/index',
                '<controller:(users)>/<action:\w+>' => 'users/users/<action>',
                '<controller:(goods)>/<action:\w+>' => 'purchases/goods/<action>',
                'good<purchase_id:\d+>_<good_id:\d+>' => 'purchases/goods/show',
                'good<purchase_id:\d+>_<good_id:\d+>/<action:\w+>' => 'purchases/goods/<action>',
                'purchase<id:\d+>/<action:\w+>' => 'purchases/purchases/<action>',
                'purchase<id:\d+>' => 'purchases/purchases/show',
                'order<order_id:\d+>' => 'purchases/orders/show',
                'orders' => 'purchases/orders',
                'orders<purchase_id:\d+>' => 'purchases/orders/purchase',
                'orders/<action:\w+>' => 'purchases/orders/<action>',
                '<controller:(purchases)>' => 'purchases/purchases/index',
                '<controller:(purchases)>/<action:\w+>' => 'purchases/purchases/<action>',
                '<controller:(purchases)>/<action:\w+>/<id:\d+>' => 'purchases/purchases/<action>',
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database

		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=common',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'oh64pb39Ac',
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
        'smsUsername' => '24314_spmix',
        'smsPassword' => 'jbS!D?z',
        'smsNumber' => 'SPMIX',
	),
);