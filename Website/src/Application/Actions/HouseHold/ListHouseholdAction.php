<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class ListHouseholdAction extends Action
{

    protected function action(): Response
    {
        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($this->request, "You need to be logged in to do this");
            
        $userId = $_SESSION['loggedIn'];
        $db = $this->container->get('db');

        $houseId = $db->getUserHousehold($userId);
        if(!$houseId)
            throw new HttpBadRequestException($this->request, "You are not a member of any house");

        $userList = $db->getUsersInHousehold($houseId);
        if(!$userList)
            return $this->createJsonResponse($this->response, ["message" => "Failed to list House"], 500);

        return $this->createJsonResponse($this->response, $userList);
    }
}
