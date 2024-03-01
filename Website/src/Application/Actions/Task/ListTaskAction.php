<?php
declare(strict_types=1);

namespace App\Application\Actions\Task;

use App\Application\Actions\MemberAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class ListTaskAction extends MemberAction
{

    protected function action(): Response
    {
        $data = $this->db->getTasksInHousehold($this->houseId);
        return $this->createJsonResponse($this->response, $data);
    }
}
