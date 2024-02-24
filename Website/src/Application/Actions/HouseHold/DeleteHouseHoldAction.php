<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class DeleteHouseHoldAction extends Action
{

    protected function action(): Response
    {
        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($this->request, "You need to be logged in to do this");
            
        $userId = $_SESSION['loggedIn'];
        $db = $this->container->get('db');

        //Check if the user is an admin of a house
        $query = $db->prepare("SELECT `House_houseId`, `adminId` from `user` left join `House` on `user`.`House_houseId` = `House`.`houseId` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute();
        $query->bind_result($house, $adminId);
        $query->fetch();
        $query->close();

        if($house==null)
            throw new HttpBadRequestException($this->request, "You are not a member of any house!");
        else if($adminId != $userId)
            throw new HttpUnauthorizedException($this->request, "You are not the admin of your house!");

        //Remove all users from House
        $result = $db->query("UPDATE `user`SET `House_houseId`=null WHERE `House_houseId` = " . $house);

        //TODO: Create new internal error Exception
        if($result == false)
            return $this->createJsonResponse($this->response, ["message" => "Failed to remove users from House"], 500);

        //Delete House
        $result = $db->query("DELETE FROM `House` WHERE `houseId`=" . $house);

        //TODO: Create new internal error Exception
        if($result == false)
            return $this->createJsonResponse($this->response, ["message" => "Failed to delete House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Deleted house successfully"]);
    }
}
