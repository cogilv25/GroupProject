<?php

declare(strict_types=1);

use App\Application\Middleware\AuthenticationMiddleware;
use Slim\App;

return function (App $app) {
    $app->add(AuthenticationMiddleware::class);
    $app->add(function ($request, $handler) use ($app) {
        return $handler->handle($request->withAttribute('container', $app->getContainer()));
    });
};
