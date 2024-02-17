<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\UserActionMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(UserActionMiddleware::class);
    $app->add(SessionMiddleware::class);
    $app->add(function ($request, $handler) use ($app) {
        return $handler->handle($request->withAttribute('container', $app->getContainer()));
    });
};
