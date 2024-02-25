<?php

declare(strict_types=1);

use App\Application\Actions\HouseHold;
use App\Application\Actions\User;
use App\Application\Middleware\AuthenticationMiddleware;
use App\Application\Middleware;

use Slim\Exception\HttpUnauthorizedException;

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
        $renderer = $this->get('renderer'); 

        $loggedIn = $request->getAttribute('loggedIn');
        if(!$loggedIn)
            return $renderer->render($response, 'Authpage.html');
        else
            return $response->withHeader('Location', '/Dashboard.php')->withStatus(302);
    })->add(AuthenticationMiddleware::class);


    $app->get('/Dashboard.php', function (Request $request, Response $response) {
        $loggedIn = $request->getAttribute('loggedIn');
        if(!$loggedIn)
            throw new HttpUnauthorizedException($request, "You must be logged in to do that");

        $renderer = $this->get('renderer');
        $db = $this->get('db');
        $link = $db->getUserInviteLink($loggedIn['userId']);
        if($link == false)
            $link = "No Link";

        $data = ['link' => $link];
        return $renderer->render($response, 'Dashboard.php', $data);
    })->add(AuthenticationMiddleware::class);

    //User Actions
    $app->post('/login', User\LoginAction::class)->add(AuthenticationMiddleware::class);
    $app->post('/signup', User\RegisterAction::class)->add(AuthenticationMiddleware::class);

    //HouseHold Actions
    $app->group('/household', function (Group $group)
    {
        $group->get('/create', HouseHold\CreateHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/join/{id}', HouseHold\JoinHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/delete', HouseHold\DeleteHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/leave', HouseHold\LeaveHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->post('/remove', Household\RemoveUserHouseHoldAction::class)->add(AuthenticationMiddleware::class);
        $group->get('/list', Household\ListHouseholdAction::class)->add(AuthenticationMiddleware::class);
    });

   $app->get("/logout", function() {
        session_unset();
        session_destroy();
        header('Location: /');
        die();
   });
};
