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

        return $handler->handle($request->withAttribute('userId', $_SESSION['loggedIn']));
    }
}