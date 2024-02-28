<?php
namespace App\Application\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

//An action that can be performed by any logged in `User`
abstract class UserAction extends Action
{

    protected int $userId;

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        //Check if user is logged in if not throw an exception
        $this->userId = $this->request->getAttribute('userId');
        if($this->userId == 0)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");

        return $this->action();
    }

    abstract protected function action(): Response;
}
