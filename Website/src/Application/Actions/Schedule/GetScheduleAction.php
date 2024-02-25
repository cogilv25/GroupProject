<?php
declare(strict_types=1);

namespace App\Application\Actions\Schedule;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class GetScheduleAction extends Action
{

    protected function action(): Response
    {
        //Check if user is logged in if not throw an exception
        $loggedIn = $this->request->getAttribute('loggedIn');
        if($loggedIn == false)
            throw new HttpMethodNotAllowedException($this->request, "You must be logged in to do that");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];

        $data = $db->getSchedule($userId);

        if(!$data)
            return $this->createJsonResponse($this->response, []);

        return $this->createJsonResponse($this->response, $data);
    }
}
