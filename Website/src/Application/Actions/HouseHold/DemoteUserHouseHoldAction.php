<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\OwnerAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class DemoteUserHouseHoldAction extends OwnerAction
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

        $result = $this->db->demoteUser($this->houseId, $memberId);

        if(!$result)
            return $this->createJsonResponse($this->response, "Failed to demote user", 500);

        return $this->createJsonResponse($this->response, "User demoted");
    }
}
