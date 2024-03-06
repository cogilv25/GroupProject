<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateTaskAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['name'], $data['taskId'], $data['description']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (strlen($data['name']) < 2 || !is_numeric($data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");


        //Pre-database string length validation to give users useful errors
        //TODO: The useful error messages... @ErrorHandling
        if(strlen($data['name'])>32 || strlen($data['description'])>1024)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        //Perform Action
        if(!$this->db->updateTask($this->houseId, (int)$data['taskId'], $data['name'], $data['description']))
            return $this->createJsonResponse($this->response, ['message' => 'Task update failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Task updated successfully']);
    }
}
