<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

   $app->map(['GET', 'POST'], '/', function (Request $request, Response $response) {
        $renderer = $this->get('renderer');
        $target = 'Authpage.html';
        $session = $request->getAttribute("session");

        if(isset($_SESSION['user']))
        {
            $response->getBody()->write("You have successfully logged in {$_SESSION['user']}!");
            $response->getBody()->write("<hr><a href=\"/logout\">Logout</a>");
            return $response;
        }

        return $renderer->render($response, $target);
    });

   $app->get("/logout", function() {
        session_unset();
        session_destroy();
        header('Location: /');
        die();
   });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
