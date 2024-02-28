<?php
declare(strict_types=1);

namespace App\Application\Actions\Rule;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteRuleAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['ruleId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (!is_numeric($data['ruleId']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $ruleId = (int)$data['ruleId'];
        if(!$this->db->deleteRule($this->houseId, $ruleId))
            return $this->createJsonResponse($this->response, ['message' => 'Rule deletion failed']);

        return $this->createJsonResponse($this->response, ['message' => 'Rule deleted successfully']);
    }
}
