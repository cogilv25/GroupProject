<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateTaskTimeRuleAction extends AdminAction
{

    protected function action(): Response
    {

        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['taskId'], $data['beginTimeslot'], $data['endTimeslot']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['taskId']) || !is_numeric($data['beginTimeslot']) || !is_numeric($data['endTimeslot']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $taskId = $data['taskId'];
        $begin = $data['beginTimeslot'];
        $end = $data['endTimeslot'];

        if(!$this->db->createTaskTimeRule($this->houseId, $taskId, $begin, $end))
            return $this->createJsonResponse($this->response, ['message' => 'Rule creation failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Rule created successfully']);
    }
}
