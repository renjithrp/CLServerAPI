<?php
// DIC configuration

 
// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

//database 

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule) {
	return $capsule;
};

//validator

$validator = new Apps\Validation\Validator;

$container['validator'] = function($container) use ($validator) {
		
		return $validator;
};

/*
$app->add(new \Slim\Middleware\JwtAuthentication([
    "secure" => false,
    "passthrough" => ["/token", "/test"],
    "secret" => "Nasj23832jsdnasndak12esjkasdn",
]));
*/