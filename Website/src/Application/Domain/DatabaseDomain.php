<?php

namespace App\Application\Domain;

use Psr\Container\ContainerInterface;
use mysqli;

//TODO: Full implementation
class DatabaseDomain
{
	private mysqli $db;

    public function __construct()
    {
        //This can throw an exception but HttpErrorHandler will catch it further up the call stack
        $this->db = new mysqli("127.0.0.1", "root", "", "cleansync");
    }

    //Call the class object to get a basic sqli object
    public function __invoke()
    {
        return $this->db;
    }

    public function getUserInviteLink(int $userId) : string | false
    {
        $query = $this->db->prepare("SELECT `houseId` FROM `user` JOIN `House` ON `adminId`=`userId` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute(); 
        $query->bind_result($houseId);
        $query->fetch();
        $query->close();

        //Fails if the user is not an admin of a Household
        if ($houseId == null)
            return false;

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/household/join/" . $houseId;
    }

    public function getUserHousehold(int $userId)
    {
        $query = $this->db->prepare("SELECT `House_houseId` FROM `user` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute(); 
        $query->bind_result($houseId);
        $result = $query->fetch();
        $query->close();

        return $result ? $houseId : false;
    }

    public function getUsersInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `adminId` FROM `House` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($adminId);
        $result = $query->fetch();
        $query->close();

        //Fails if $houseId is not a valid id
        if($result!=true)
            return false;

        $query = $this->db->prepare("SELECT `userId`, `forename`, `surname` FROM `user` WHERE `House_houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($userId, $forename, $surname);

        while($query->fetch())
        {
            $role = $userId == $adminId ? "Admin" : "Member";
            $data[$userId] = ['forename' => $forename, 'surname' => $surname, 'role' => $role];
        }

        $query->close();

        return ($data != null) ? $data : false;
    }
}

?>