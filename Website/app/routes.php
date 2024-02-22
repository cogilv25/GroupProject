<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    //TODO: Single page logic
   $app->map(['GET', 'POST'], '/', function (Request $request, Response $response) {
        // If a user session exists, redirect to another page 
        if (isset($_SESSION['loggedIn'])) {
            return $response->withHeader('Location', '/Example.php')->withStatus(302);
        }
        // If no user session exists, show the Authpage.html
        $renderer = $this->get('renderer'); 
        return $renderer->render($response, 'Authpage.html');
    });

    $app->get('/Example.php', function (Request $request, Response $response) {
        $renderer = $this->get('renderer');
        return $renderer->render($response, 'Example.php');
    });

    $app->post('/login', function (Request $request, Response $response) {
        return $response;
    })->add(\App\Application\Middleware\LoginMiddleware::class);
     
    $app->post('/signup', function (Request $request, Response $response) {
        return $response;
    })->add(\App\Application\Middleware\RegistrationMiddleware::class);

    //TODO: Move logic to Middleware
   $app->get("/join_house/{id}", function($request, $response, $args)
    {
        if(!isset($_SESSION['email']))
            throw new HttpUnauthorizedException($request, "You need to be logged in to do this");

        if(!is_numeric($args['id']))
            throw new HttpBadRequestException($request, 'Bad Url');

        $db = $this->get('db');
        if($db == null)
            throw new HttpNotFoundException($request, 'Database unavailable!');

        //Prepare the query
        $query = $db->prepare("select houseId from House where houseId = ?");
        $query->bind_param("i",$args['id']);
        $query->bind_result($house);

        //Execute query
        $query->execute();
        $result = $query->fetch();
        $query->close();

        if($result == null)
            throw new HttpBadRequestException($request, 'Invalid Url');
        if($result == false)
            throw new HttpBadRequestException($request, 'Unknown Error');

        $query = $db->prepare("update `user` set House_houseId = ? where `email` = ?");
        $query->bind_param("is", $args['id'], $_SESSION['email']);


        $result = $query->execute();
        $query->close();

        if(!$result)
            throw new HttpNotFoundException($request, "Unkown Error");


        $response->getBody()->write(json_encode(['statusCode' => 200]));
        return $response;
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
