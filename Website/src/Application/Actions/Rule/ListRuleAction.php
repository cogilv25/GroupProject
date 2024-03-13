<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\MemberAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class ListRuleAction extends MemberAction
{

    protected function action(): Response
    {
        $data = $this->db->getRulesInHousehold($this->houseId);
        return $this->createJsonDataResponse($this->response, $data, false);
    }
}
