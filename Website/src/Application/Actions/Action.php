<?php
namespace App\Application\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class Action
{

    protected ContainerInterface $container;

    protected Request $request;

    protected Response $response;

    protected array $args;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->action();
    }

    abstract protected function action(): Response;

    // Function to create a json response
    protected function createJsonResponse(Response $response, $data, int $statusCode = 200): Response
    {
        $response->getBody()->write(json_encode($data,JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}
