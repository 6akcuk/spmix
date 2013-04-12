<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SPmix Console',

	// preloading 'log' component
	'preload'=>array('log'),

  // autoloading model and component classes
  'import'=>array(
    'application.models.*',
    'application.components.*',
    'application.modules.users.models.*',
    'application.modules.users.components.*',
    'application.modules.users.components.views.*',
    'application.modules.purchases.models.*',
    'ext.ActiveHtml.*',
    'ext.SmsDelivery.*',
    'ext.Excel.*',
  ),

  'modules'=>array(
    'users' => array(
      'onlineInterval' => 10, // сколько минут считать пользователя онлайн
    ),
    'purchases',
    'search',
    'im',
    'mail',
  ),

	// application components
	'components'=>array(
    'mutex' => array(
      'class' => 'application.extensions.EMutex',
    ),
    'db'=>array(
      'connectionString' => 'mysql:host=localhost;dbname=common',
      'emulatePrepare' => true,
      'username' => 'root',
      'password' => 'oh64pb39Ac',
      'charset' => 'utf8',
    ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
			),
		),
	),
);