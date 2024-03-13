<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class RemoveUserHouseHoldAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        //Validation
        if(!isset($data['userId']))
            throw HttpBadRequestException("Invalid form data submitted");
        if(!is_numeric($data['userId']))
            throw HttpBadRequestException("Invalid form data submitted");

        $memberId = (int)$data['userId'];

        //Remove member from users household
        $result = $this->db->removeUserFromHousehold($memberId, $this->houseId);

        if(!$result)
            return $this->createJsonResponse($this->response, "Failed to remove user from House", 500);

        return $this->createJsonResponse($this->response, "Removed user from house successfully");
    }
}
