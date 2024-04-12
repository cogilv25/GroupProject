<?php
namespace App\Application\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\Domain\DatabaseDomain;

abstract class Action
{

    protected ContainerInterface $container;

    protected Request $request;

    protected Response $response;

    protected array $args;

    protected DatabaseDomain $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->get('db');
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        return $this->action();
    }

    abstract protected function action(): Response;

    // TODO: Rename when redundant code removed
    protected function createJsonResponse(Response $response, string $message, int $statusCode = 200): Response
    {
        $data = ['statusCode' => $statusCode, 'message' => $message];
        $response->getBody()->write(json_encode($data,JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }

    protected function createJsonDataResponse(Response $response, $data, array | string | bool $errorMessageList, int $statusCode = 200): Response
    {
        $responseData = ['statusCode' => $statusCode, 'data' => $data];
        if($errorMessageList !== false)
            $responseData['errors'] = $errorMessageList;
        $response->getBody()->write(json_encode($responseData,JSON_PRETTY_PRINT));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
