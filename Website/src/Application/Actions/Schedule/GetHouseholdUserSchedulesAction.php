<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class GetHouseholdUserSchedulesAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->db->getUserSchedulesInHousehold($this->houseId);
        return $this->createJsonDataResponse($this->response, $data, false);
    }
}