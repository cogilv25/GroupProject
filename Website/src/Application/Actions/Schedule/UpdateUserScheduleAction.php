<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;


//TODO: Check for UserSchedule Row collisions
class UpdateUserScheduleAction extends UserAction
{

    protected array $validDays = ['Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['schedules']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        if (!is_array($data['schedules']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $schedules = $data['schedules'];
        $scheduleRows = [];
        foreach ($schedules as $row)
        {
            if(!isset($row['day'],$row['startSegment'],$row['endSegment']))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if(!(in_array($row['day'], $this->validDays) && is_numeric($row['startSegment']) 
              && is_numeric($row['endSegment'])))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");

            $begin = (int)$row['startSegment'];
            $end = (int)$row['endSegment'];
            $day = $row['day'];

            if($begin < 0 || $end < 0 || $begin>95 || $end>95 || $begin>$end)
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");

            $scheduleRows[] = [$begin, $end, $day];
        }


        if(!$this->db->overwriteUserSchedule($this->userId, $scheduleRows))
            return $this->createJsonResponse($this->response, 'User Schedule update failed');

        return $this->createJsonResponse($this->response, 'User Schedule updated successfully');
    }
}
