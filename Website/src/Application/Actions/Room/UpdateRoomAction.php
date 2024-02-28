<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateRoomAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['name'], $data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (strlen($data['name']) < 2 || !is_numeric($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $roomId = (int)$data['roomId'];

        if(!$this->db->updateRoom($this->houseId, $roomId, $data['name']))
            return $this->createJsonResponse($this->response, ['message' => 'Room update failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Room updated successfully']);
    }
}
