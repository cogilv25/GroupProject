<?php
namespace App\Application\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

//An action that can be performed by any logged in user who belongs to a house
abstract class MemberAction extends Action
{

    protected int $userId;

    protected int $houseId;

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        //Check if user is logged in.
        $this->userId = $this->request->getAttribute('userId');
        if($this->userId == 0)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");

        //Check if user belongs to a household.
        $this->houseId = $this->db->getUserHousehold($this->userId);
        if($this->houseId == false)
            throw new HttpBadRequestException($this->request, "You are not a member of a Household");

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
