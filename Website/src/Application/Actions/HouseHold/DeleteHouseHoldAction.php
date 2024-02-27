<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteHouseHoldAction extends Action
{

    protected function action(): Response
    {
        //Check if user is a logged in admin if not throw an exception
        $loggedIn = $this->request->getAttribute('loggedIn');
        if($loggedIn == false)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");
        if(!$loggedIn['admin'])
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];

        
        $houseId = $db->getAdminHouse($userId);
        if(!$houseId)
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");
        
        //Delete House
        $result = $db->deleteHousehold($houseId);

        //TODO: Create new internal error Exception
        if($result == false)
            return $this->createJsonResponse($this->response, ["message" => "Failed to delete House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Deleted house successfully"]);
    }
}
