<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateUserRoomRuleAction extends Action
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
        if (!isset($data['roomId'], $data['userId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['roomId']) || !is_numeric($data['userId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];
        $roomId = $data['roomId'];
        $targetUserId = $data['userId'];

        $houseId = $db->getAdminHouse($userId);
        if(!$houseId)
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        if(!$db->createUserRoomRule($houseId, $targetUserId, $roomId))
            return $this->createJsonResponse($this->response, ['message' => 'Rule creation failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Rule created successfully']);
    }
}
