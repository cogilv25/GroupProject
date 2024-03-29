<?php
namespace App\Application\Actions;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

//An action that can be performed by any logged in `User` who is the Admin of a Household
abstract class AdminAction extends Action
{

    protected int $adminId;

    protected int | bool $houseId;

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        //Check if user is logged in
        $this->adminId = $this->request->getAttribute('userId');
        if($this->adminId == 0)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");

        //Get the house this user admins, incidentally checks if the user is an admin
        $this->houseId = $this->db->getAdminHouse($this->adminId);
        if($this->houseId == false)
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        return $this->action();
    }

    abstract protected function action(): Response;
}
