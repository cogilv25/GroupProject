<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class ToggleUserTaskRuleAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['ruleId'], $data['state']))

        if (!(is_numeric($data['ruleId']) && is_bool($data['state'])))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        
        $state = (bool)$data['state'];

        $ruleId = (int)$data['ruleId'];

        $ruleId = $this->db->toggleUserTaskRule($this->houseId, $ruleId, $state);

        if($ruleId === false)
            return $this->createJsonResponse($this->response, 'Rule toggle failed', 500);

        return $this->createJsonDataResponse($this->response, $ruleId, false);
    }
}
