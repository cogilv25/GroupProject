<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteTaskAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $taskId = (int)$data['taskId'];

        if(!$this->db->deleteTask($taskId, $this->houseId))
            return $this->createJsonResponse($this->response, ['message' => 'Task deletion failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Task deleted successfully']);
    }
}
