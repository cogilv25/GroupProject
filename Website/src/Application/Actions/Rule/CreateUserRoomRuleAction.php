<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateUserRoomRuleAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['roomId'], $data['userId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['roomId']) || !is_numeric($data['userId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $roomId = $data['roomId'];
        $targetUserId = $data['userId'];

        $id = $this->db->createUserRoomRule($this->houseId, $targetUserId, $roomId);
        if($id === false)
            return $this->createJsonResponse($this->response, 'Rule creation failed', 500);

        return $this->createJsonDataResponse($this->response, $id, false);
    }
}
