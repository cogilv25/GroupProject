<?php
declare(strict_types=1);

namespace App\Application\Actions\Room;

use App\Application\Actions\AdminAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateRoomAction extends AdminAction
{

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();

        // Validation checks
        if (!isset($data['name']))
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");
        if (strlen($data['name']) < 2)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        //Pre-database string length validation to give users useful errors
        //TODO: The useful error messages... @ErrorHandling
        if(strlen($data['name'])>32)
            throw new HttpBadRequestException($this->request, "Invalid form data submitted");

        $id = $this->db->createRoom($this->houseId, $data['name']);
        if($id === false)
            return $this->createJsonResponse($this->response, 'Room creation failed', 500);

        return $this->createJsonDataResponse($this->response, $id, false);
    }
}
