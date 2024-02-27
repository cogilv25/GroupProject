<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateTaskTimeRuleAction extends Action
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
        if (!isset($data['taskId'], $data['beginTimeslot'], $data['endTimeslot']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['taskId']), !is_numeric($data['beginTimeslot']), !is_numeric($data['endTimeslot']));
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];
        $taskId = $data['taskId'];
        $begin = $data['beginTimeslot'];
        $end = $data['endTimeslot'];

        $houseId = $db->getAdminHouse($userId);
        if(!$houseId)
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        if(!$db->createTaskTimeRule($houseId, $taskId, $begin, $end))
            return $this->createJsonResponse($this->response, ['message' => 'Rule creation failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Rule created successfully']);
    }
}
