<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class GetUserScheduleAction extends UserAction
{

    protected function action(): Response
    {

        $data = $this->db->getUserSchedule($this->userId);
        return $this->createJsonResponse($this->response, $data);
    }
}
