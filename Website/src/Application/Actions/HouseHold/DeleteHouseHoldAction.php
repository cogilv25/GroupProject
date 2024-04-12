<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteHouseHoldAction extends AdminAction
{

    protected function action(): Response
    {
        //Delete House
        $result = $this->db->deleteHousehold($this->houseId);

        if($result == false)
            return $this->createJsonResponse($this->response, "Failed to delete House", 500);

        return $this->createJsonResponse($this->response, "Deleted house successfully");
    }
}
