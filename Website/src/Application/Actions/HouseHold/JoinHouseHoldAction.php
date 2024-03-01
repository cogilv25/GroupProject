<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class JoinHouseHoldAction extends UserAction
{
    
    //TODO: redirect user to login/register then back to this route afterwards
    protected function action(): Response
    {
        if(!is_numeric($this->args['id']))
            throw new HttpBadRequestException($this->request, "Invalid invite link");

        $inviteId = (int)$this->args['id'];

        $result = $this->db->addUserToHousehold($this->userId, $inviteId);
        
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to join House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Joined house successfully"]);
    }
}
