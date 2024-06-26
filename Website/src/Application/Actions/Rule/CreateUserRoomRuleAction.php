<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\MemberAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateUserRoomRuleAction extends MemberAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['roomId']))

        if (!is_numeric($data['roomId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        //Admins and Owners only
        if($this->privilege < 2)
        {
            if(!isset($data['userId']))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");

            if(!is_numeric($data['userId']))
                throw new HttpBadRequestException($this->request, "Invalid form data submitted");

            $targetUserId = (int)$data['userId'];
        }
        else
            $targetUserId = $this->userId;

        $roomId = (int)$data['roomId'];

        $ruleId = $this->db->createUserRoomRule($this->houseId, $targetUserId, $roomId, $this->privilege < 2);
        if($ruleId === false)
            return $this->createJsonResponse($this->response, 'Rule creation failed', 500);

        return $this->createJsonDataResponse($this->response, $ruleId, false);
    }
}
