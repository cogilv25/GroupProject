<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateTaskAction extends Action
{

    protected function action(): Response
    {
        //Check if user is a logged in admin if not throw an exception
        $loggedIn = $this->request->getAttribute('loggedIn');
        if($loggedIn == false)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");
        if(!$loggedIn['admin'])
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['name'], $data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (strlen($data['name']) < 2 || !is_numeric($data['taskId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];
        $taskId = (int)$data['taskId'];

        $houseId = $db->getAdminHouse($userId);
        if(!$houseId)
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        if(!$db->updateTask($houseId, $taskId, $data['name'], $data['description']))
            return $this->createJsonResponse($this->response, ['message' => 'Task update failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Task updated successfully']);
    }
}
