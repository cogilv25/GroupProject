<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DashboardMiddleware implements Middleware
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($request, "You must be logged in to access this page");
        
        $userId = $_SESSION['loggedIn'];
        $container = $request->getAttribute('container');
        $request = $request->withAttribute('link', 'No Link');

        $db = $container->get('db');

        if (!$db)
            return $handler->handle($request);

        $query = $db->prepare("SELECT `houseId` FROM `user` JOIN `House` ON `adminId`=`userId` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute(); 
        $query->bind_result($houseId);
        $query->fetch();
        $query->close();


        //Fails if the user is not an admin of a Household
        if ($houseId == null)
            return $handler->handle($request);

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $link = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/household/join/" . $houseId;
        $request = $request->withAttribute('link', $link);
        return $handler->handle($request);
    }
    // Function to create a json return response
    private function returnJsonResponse(Response $response, string $message, int $statusCode = 200): Response
    {
        $responseData = ['message' => $message];
        $response->getBody()->write(json_encode($responseData));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}