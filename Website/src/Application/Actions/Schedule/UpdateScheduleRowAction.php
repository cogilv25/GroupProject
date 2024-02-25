<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateScheduleRowAction extends Action
{

    protected array $validDays = ['Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    protected function action(): Response
    {
        //Check if user is a logged in admin if not throw an exception
        $loggedIn = $this->request->getAttribute('loggedIn');
        if($loggedIn == false)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");

        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['beginTimeslot'], $data['endTimeslot'], $data['day'], $data['rowId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        if (!is_numeric($data['rowId']) || !is_numeric($data['beginTimeslot']) || 
            !is_numeric($data['endTimeslot']) || !in_array($data['day'], $this->validDays))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");


        $rowId = (int)$data['rowId'];
        $begin = (int)$data['beginTimeslot'];
        $end = (int)$data['endTimeslot'];
        $day = $data['day'];

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];

        if(!$db->updateScheduleRow($userId, $rowId, $begin, $end, $day))
            return $this->createJsonResponse($this->response, ['message' => 'Schedule update failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Schedule updated successfully']);
    }
}
