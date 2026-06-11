<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ODM\MongoDB\DocumentManager;

$app = new Laravel\Lumen\Application(dirname(__DIR__));

$app->singleton(DocumentManager::class, function () {
    return require __DIR__ . '/odm.php';
});

$app->router->group(['namespace' => 'App\Actions'], function ($router) {
    $router->get('/', 'GraphAction@handle');
    $router->get('/testing', 'TestingAction@handle');
});

return $app;