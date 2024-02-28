<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;


//TODO: Check for Schedule Row collisions
class CreateScheduleRowAction extends UserAction
{

    protected array $validDays = ['Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday','All','Weekdays','Weekends'];

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['beginTimeslot'], $data['endTimeslot'], $data['day']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        if (!is_numeric($data['beginTimeslot']) || !is_numeric($data['endTimeslot']) || !in_array($data['day'], $this->validDays))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");


        $begin = (int)$data['beginTimeslot'];
        $end = (int)$data['endTimeslot'];
        $day = $data['day'];

        if(!$this->db->createScheduleRows($this->userId, $begin, $end, $day))
            return $this->createJsonResponse($this->response, ['message' => 'Schedule Row creation failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Schedule Row created successfully']);
    }
}
