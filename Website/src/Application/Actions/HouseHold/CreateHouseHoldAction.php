<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateHouseHoldAction extends UserAction
{
    protected function action(): Response
    {
        //Fail if the user is a member of a household
        $houseId = $this->db->getUserHousehold($this->userId);
        if($houseId != false)
            throw new HttpMethodNotAllowedException($this->request, "You are already a member of a household");

        //Otherwise create a new household
        $result = $this->db->createHousehold($this->userId);
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to create House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Created house successfully"]);
    }
}
 