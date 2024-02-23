<?php
declare(strict_types=1);

namespace App\Application\Actions\House;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Exception\HttpMethodNotAllowedException;

class CreateHouseAction extends Action
{

    protected function action(): Response
    {
        if(!isset($_SESSION['loggedIn']))
            throw new HttpUnauthorizedException($this->request, "You need to be logged in to do this");
            
        $email = $_SESSION['loggedIn'];
        $db = $this->container->get('db');

        //Check if the user already has a house
        $query = $db->prepare("SELECT House_houseId FROM user WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $query->bind_result($house);
        $query->fetch();
        $query->close();

        if($house!=null)
            throw new HttpMethodNotAllowedException($this->request, "You are already a member of a household");

        //Create new House
        $query = $db->prepare("INSERT INTO `House` (`adminEmail`) VALUES (?)");
        $query->bind_param("s", $email);
        $result = $query->execute();
        $query->close();

        //TODO: Create new internal error Exception
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to create House"], 500);

        //Add user to House
        $query = $db->prepare("UPDATE `user` SET `House_houseId`=(SELECT `houseId` FROM `House` WHERE `adminEmail`=?)  WHERE `email`=?");
        $query->bind_param("ss", $email, $email);
        $result = $query->execute();
        $query->close();
        $db->close();

        //TODO: Create new internal error Exception
        if(!$result)
            return $this->createJsonResponse($this->response, ["message" => "Failed to create House"], 500);

        return $this->createJsonResponse($this->response, ["message" => "Created house successfully"]);
    }
}
