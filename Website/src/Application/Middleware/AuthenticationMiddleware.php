<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthenticationMiddleware implements Middleware
{
    private function isAdmin(Request $request)
    {
        $db = $request->getAttribute('container')->get('db');
        return $db->isUserAdmin($_SESSION['loggedIn']);
    }

    private function isUser(Request $request)
    {
        return isset($_SESSION['loggedIn']);
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $userId = 0;

        if(isset($_SESSION['loggedIn']))
            $userId = $_SESSION['loggedIn'];

        $request = $request->withAttribute('userId', $userId);

        if($this->isUser($request))
        {
            $request = $request->withAttribute('loggedIn', ['userId' => $_SESSION['loggedIn'], 'admin' => $this->isAdmin($request)]);
        }
        else
            $request = $request->withAttribute('loggedIn', false);

        return $handler->handle($request);
    }
}