<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class LeaveHouseHoldAction extends Action
{

    protected function action(): Response
    {
        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($this->request, "You need to be logged in to do this");
            
        $userId = $_SESSION['loggedIn'];
        $db = $this->container->get('db')();

        //Check if the user is in a house and if the user is an admin of a house
        $query = $db->prepare("SELECT `House_houseId`, `houseId` FROM `user` left join `House` ON `userId`=`adminId` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute();
        $query->bind_result($userHouse, $adminHouse);
        $query->fetch();
        $query->close();

        if($adminHouse!=null)
            throw new HttpMethodNotAllowedException($this->request, "You cannot leave a house you administrate");
        elseif($userHouse==null)
            throw new HttpBadRequestException($this->request, "You are not a member of any house");

        //Remove user from House
        $query = $db->prepare("UPDATE `user` SET `House_houseId`=null  WHERE `userId`=?");
        $query->bind_param("i", $userId);
        $result = $query->execute();
        $query->close();
        $db->close();

        //TODO: Create new internal error Exception
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to leave House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Left house successfully"]);
    }
}
