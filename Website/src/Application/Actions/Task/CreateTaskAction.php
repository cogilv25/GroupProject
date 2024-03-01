<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateTaskAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['name'], $data['description']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (strlen($data['name']) < 2)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        if(!$this->db->createTask($this->houseId, $data['name'], $data['description']))
            return $this->createJsonResponse($this->response, ['message' => 'Task creation failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Task created successfully']);
    }
}
