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

   $app->map(['GET', 'POST'], '/', function (Request $request, Response $response) {
        $renderer = $this->get('renderer');
        $target = 'Authpage.html';
        $session = $request->getAttribute("session");

        if(isset($_SESSION['email']))
        {
            $target = "Example.php";
            return $renderer->render($response, $target, ["name" => $_SESSION['user']]);
        }

        return $renderer->render($response, $target);
    });

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
