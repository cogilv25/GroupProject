<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;


//TODO: Check for Schedule Row collisions
class UpdateScheduleRowAction extends UserAction
{

    protected array $validDays = ['Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    protected function action(): Response
    {
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

        if(!$this->db->updateScheduleRow($this->userId, $rowId, $begin, $end, $day))
            return $this->createJsonResponse($this->response, ['message' => 'Schedule update failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Schedule updated successfully']);
    }
}
