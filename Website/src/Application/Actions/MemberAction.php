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

    protected int $privilege; //0,1,2 == owner,admin,member

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
        $data = $this->db->getUserHouseAndRole($this->userId);
        if($data === false)
            throw new HttpBadRequestException($this->request, "You are not a member of a Household");


        $this->houseId = $data[0];
        switch($data[1])
        {
            case 'owner': $this->privilege = 0;
            break;
            case 'admin': $this->privilege = 1;
            break;
            default: $this->privilege = 2;
        }

        return $this->action();
    }

    abstract protected function action(): Response;
}
