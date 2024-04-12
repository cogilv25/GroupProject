<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteRoomAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $roomId = (int)$data['roomId'];

        if(!$this->db->deleteRoom($roomId, $this->houseId))
            return $this->createJsonResponse($this->response, 'Room deletion failed');

        return $this->createJsonResponse($this->response, 'Room deleted successfully');
    }
}
