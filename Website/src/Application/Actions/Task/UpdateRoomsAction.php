<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateRoomsAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['nOps']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['nOps']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!isset($data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $nOps = (int)$data['nOps'];
        $taskId = (int)$data['taskId'];

        // Validate all ops are present and correct
        for($i = 0; $i < $nOps; $i++)
        {
            if (!isset($data['op'.$i]))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if (!($data['op' . $i] == '1' || $data['op' . $i] == '0'))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if (!isset($data['roomId'.$i]))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if (!is_numeric($data['roomId'.$i]))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            $ops[$i] = (int)$data['op'.$i];
            $roomIds[$i] = (int)$data['roomId'.$i];
        }

        $status = true;
        for($i=0; $i < $nOps; $i++)
        {
            if(!($ops[$i] == 0 ?
                $this->db->deleteRoomHasTaskEntry($this->houseId, $roomIds[$i], $taskId):
                $this->db->createRoomHasTaskEntry($this->houseId, $roomIds[$i], $taskId)))
            {
                $status = false;
            }
        }

        if(!$status)
            return $this->createJsonResponse($this->response, 'One or more rooms could not be assigned/unassigned from the task', 500);

        return $this->createJsonResponse($this->response, 'Rooms successfully assigned/unassigned');
    }
}
