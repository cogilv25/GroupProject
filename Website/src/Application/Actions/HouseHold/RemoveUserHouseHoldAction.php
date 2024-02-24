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

        $email = $_SESSION['loggedIn'];
        $targetUser = $_POST['userId'];
        $db = $this->container->get('db');

        //Get users of the house administrated by the acting user excluding the acting user.
        //This will return null if the user is not an admin of a house or is the only member of their house.
        $query = $db->prepare("SELECT `email` FROM `user` JOIN `House` ON `House_houseId`=`houseId` WHERE `adminEmail`=? AND NOT `email`=?");
        $query->bind_param("ss", $email, $email);
        $query->execute();
        $query->bind_result($memberEmail);

        //Attempt to find the targerUser specified in the query results
        $targetUserInHouse = false;
        while($query->fetch())
        {
            if($targetUser == $memberEmail)
            {
                $targetUserInHouse = true;
                break;
            }
        }
        $query->close();

        if(!$targetUserInHouse)
            throw new HttpBadRequestException($this->request, "Target user is not a member of a house administered by acting users");
        

        //Remove targetUser from House
        $query = $db->prepare("UPDATE `user` SET `House_houseId`=null  WHERE `email`=?");
        $query->bind_param("s", $targetUser);
        $result = $query->execute();
        $query->close();
        $db->close();

        //TODO: Create new internal error Exception
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to remove user from House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Removed user from house successfully"]);
    }
}
