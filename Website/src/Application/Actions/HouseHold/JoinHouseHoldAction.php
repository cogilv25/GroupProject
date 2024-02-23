<?php
declare(strict_types=1);

namespace App\Application\Actions\HouseHold;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class JoinHouseHoldAction extends Action
{

    protected function action(): Response
    {
        $id = $this->args['id'];
        if(!is_numeric($id))
            throw new HttpBadRequestException($this->request, "Invalid invite link");

        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($this->request, "You need to be logged in to do this");
            
        $email = $_SESSION['loggedIn'];
        $db = $this->container->get('db');

        //Check if the user is an admin of a house
        $query = $db->prepare("SELECT `houseId` FROM `user` right join `House` ON `email`=`adminEmail` WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $query->bind_result($house);
        $query->fetch();
        $query->close();

        if($house!=null)
            throw new HttpMethodNotAllowedException($this->request, "You must delete your house before leaving it");

        //Check the house exists
        $result = $db->query("SELECT `houseId` FROM `House` WHERE `houseId`=" . $id);
        if($result->fetch_row() == null)
            throw new HttpBadRequestException($this->request, "Invalid invite link");


        //Add user to House
        $query = $db->prepare("UPDATE `user` SET `House_houseId`=?  WHERE `email`=?");
        $query->bind_param("is", $id, $email);
        $result = $query->execute();
        $query->close();
        $db->close();

        //TODO: Create new internal error Exception
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to join House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Joined house successfully"]);
    }
}
