<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class RemoveUserHouseHoldAction extends Action
{

    protected function action(): Response
    {
        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($this->request, "You need to be logged in to do this");

        if(!isset($_POST['userId']))
            throw new HttpBadRequestException($this->request, "No userId provided");

        $userId = $_SESSION['loggedIn'];
        $targetUserId = $_POST['userId'];
        $db = $this->container->get('db')();

        //Get the target user of the house administrated by the acting user.
        //This will return null if the user is not an admin of a house or the target user isn't part of their house.
        $query = $db->prepare("SELECT `userId` FROM `user` JOIN `House` ON `House_houseId`=`houseId` WHERE `adminId`=? AND `userId`=?");
        $query->bind_param("ii", $userId, $targetUserId);
        $query->execute();
        $query->bind_result($memberId);
        $query->fetch();
        $query->close();

        if($memberId == null)
            throw new HttpBadRequestException($this->request, "Target user is not a member of a house administered by the acting user");
        if($memberId == $userId)
            throw new HttpBadRequestException($this->request, "Cannot remove the admin from a house, house must always have an admin");
        

        //Remove targetUser from House
        $query = $db->prepare("UPDATE `user` SET `House_houseId`=null  WHERE `userId`=?");
        $query->bind_param("i", $memberId);
        $result = $query->execute();
        $query->close();
        $db->close();

        //TODO: Create new internal error Exception
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to remove user from House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Removed user from house successfully"]);
    }
}
