<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateUserTaskRuleAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['taskId'], $data['userId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['taskId']) || !is_numeric($data['userId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $taskId = $data['taskId'];
        $targetUserId = $data['userId'];

        $id = $this->db->createUserTaskRule($this->houseId, $targetUserId, $taskId);
        if($id === false)
            return $this->createJsonResponse($this->response, 'Rule creation failed', 500);

        return $this->createJsonDataResponse($this->response, $id, false);
    }
}
