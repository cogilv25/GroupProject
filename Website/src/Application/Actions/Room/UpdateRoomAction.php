<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class UpdateRoomAction extends Action
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
        if (!isset($data['name'], $data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (strlen($data['name']) < 2 || !is_numeric($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $db = $this->container->get('db');
        $userId = $loggedIn['userId'];
        $roomId = (int)$data['roomId'];

        $houseId = $db->getAdminHouse($userId);
        if(!$houseId)
            throw new HttpMethodNotAllowedException($this->request, "You must be a house admin to do that");

        if(!$db->updateRoom($houseId, $roomId, $data['name']))
            return $this->createJsonResponse($this->response, ['message' => 'Room update failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Room updated successfully']);
    }
}
