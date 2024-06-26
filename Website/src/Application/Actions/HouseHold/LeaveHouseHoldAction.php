<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\MemberAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class LeaveHouseHoldAction extends MemberAction
{

    protected function action(): Response
    {
        if($this->db->getOwnerHouse($this->userId) != false)
            throw HttpMethodNotAllowedException("Owner can not leave their household!");

        //Remove user from their House
        $result = $this->db->removeUserFromHousehold($this->userId, $this->houseId, 0);

        if(!$result)
            return $this->createJsonResponse($this->response, "Failed to leave House", 500);

        return $this->createJsonResponse($this->response, "Left house successfully");
    }
}
