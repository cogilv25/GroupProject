<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteScheduleRowAction extends Action
{

    protected function action(): Response
    {
        //Check if user is a logged in admin if not throw an exception
        $loggedIn = $this->request->getAttribute('loggedIn');
        if($loggedIn == false)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");

        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['scheduleId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['scheduleId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];
        $scheduleId = (int)$data['scheduleId'];

        if(!$db->deleteScheduleRow($userId, $scheduleId))
            return $this->createJsonResponse($this->response, ['message' => 'Schedule Row deletion failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Schedule Row deleted successfully']);
    }
}
