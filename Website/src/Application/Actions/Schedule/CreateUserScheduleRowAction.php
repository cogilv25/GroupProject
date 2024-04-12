<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;


//TODO: Check for UserSchedule Row collisions
class CreateUserScheduleRowAction extends UserAction
{

    protected array $validDays = ['Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

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

        //Pre-database timeslot range validation to give users useful errors
        //TODO: The useful error messages... @ErrorHandling
        if($begin < 0 || $end < 0 || $begin>95 || $end>95 || $begin>$end)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $id = $this->db->createUserScheduleRows($this->userId, $begin, $end, $day);
        if($id === false)
            return $this->createJsonResponse($this->response, 'User Schedule Row creation failed', 500);

        return $this->createJsonDataResponse($this->response, $id, false);
    }
}
