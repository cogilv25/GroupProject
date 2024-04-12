<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteUserScheduleRowAction extends UserAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['scheduleId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['scheduleId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $scheduleId = (int)$data['scheduleId'];

        if(!$this->db->deleteUserScheduleRow($this->userId, $scheduleId))
            return $this->createJsonResponse($this->response, 'User Schedule Row deletion failed');

        return $this->createJsonResponse($this->response, 'User Schedule Row deleted successfully');
    }
}
