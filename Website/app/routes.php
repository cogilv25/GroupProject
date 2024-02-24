<?php

declare(strict_types=1);

use App\Application\Actions\HouseHold;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    //TODO: Single page logic
   $app->map(['GET', 'POST'], '/', function (Request $request, Response $response) {
        // If a user session exists, redirect to another page 
        if (isset($_SESSION['loggedIn'])) {
            return $response->withHeader('Location', '/Dashboard.php')->withStatus(302);
        }
        // If no user session exists, show the Authpage.html
        $renderer = $this->get('renderer'); 
        return $renderer->render($response, 'Authpage.html');
    });


    $app->get('/Dashboard.php', function (Request $request, Response $response) {
        $renderer = $this->get('renderer');

        $data = ['link' => $request->getAttribute('link')];
        return $renderer->render($response, 'Dashboard.php', $data);
    })->add(\App\Application\Middleware\DashboardMiddleware::class);

    $app->post('/login', function (Request $request, Response $response) {
        return $response;
    })->add(\App\Application\Middleware\LoginMiddleware::class);
     
    $app->post('/signup', function (Request $request, Response $response) {
        return $response;
    })->add(\App\Application\Middleware\RegistrationMiddleware::class);

    //HouseHold Actions
    $app->group('/household', function (Group $group)
    {
        $group->get('/create', HouseHold\CreateHouseHoldAction::class);
        //TODO: redirect user to login/register then back to this route afterwards
        $group->get('/join/{id}', HouseHold\JoinHouseHoldAction::class);
        $group->get('/delete', HouseHold\DeleteHouseHoldAction::class);
        $group->get('/leave', HouseHold\LeaveHouseHoldAction::class);
        $group->post('/remove', Household\RemoveUserHouseHoldAction::class);
    });

   $app->get("/logout", function() {
        session_unset();
        session_destroy();
        header('Location: /');
        die();
   });
};
