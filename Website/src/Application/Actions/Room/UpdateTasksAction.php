<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateTasksAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['nOps']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['nOps']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!isset($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $nOps = (int)$data['nOps'];
        $roomId = (int)$data['roomId'];

        // Validate all ops are present and correct
        for($i = 0; $i < $nOps; $i++)
        {
            if (!isset($data['op'.$i]))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if (!($data['op' . $i] == '1' || $data['op' . $i] == '0'))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if (!isset($data['taskId'.$i]))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            if (!is_numeric($data['taskId'.$i]))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");
            $ops[$i] = (int)$data['op'.$i];
            $taskIds[$i] = (int)$data['taskId'.$i];
        }

        $status = true;
        for($i=0; $i < $nOps; $i++)
        {
            if(!($ops[$i] == 0 ?
                $this->db->deleteRoomHasTaskEntry($this->houseId, $roomId, $taskIds[$i]):
                $this->db->createRoomHasTaskEntry($this->houseId, $roomId, $taskIds[$i])))
            {
                $status = false;
            }
        }

        if(!$status)
            return $this->createJsonResponse($this->response, 'One or more tasks could not be assigned/unassigned from the room', 500);

        return $this->createJsonResponse($this->response, 'Tasks successfully assigned/unassigned');
    }
}
