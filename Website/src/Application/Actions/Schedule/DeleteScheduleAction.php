<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteScheduleAction extends UserAction
{

    protected function action(): Response
    {
        if(!$this->db->deleteSchedule($this->userId))
            return $this->createJsonResponse($this->response, ['message' => 'Schedule deletion failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Schedule deleted successfully']);
    }
}
