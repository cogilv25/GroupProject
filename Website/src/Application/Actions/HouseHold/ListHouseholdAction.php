<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\MemberAction;
use Psr\Http\Message\ResponseInterface as Response;

class ListHouseholdAction extends MemberAction
{
    protected function action(): Response
    {
        $userList = $this->db->getUsersInHousehold($this->houseId);
        return $this->createJsonDataResponse($this->response, $userList, false);
    }
}
