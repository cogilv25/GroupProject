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

    public function getUserIdAndPasswordHash(string $email) : array | false
    {
        $query = $this->db->prepare("SELECT `userId`, `password` FROM `user` WHERE `email` = ?");
        $query->bind_param("s", $email);
        $query->execute(); 
        $query->bind_result($data['id'], $data['passwordHash']);
        $query->fetch();
        $query->close();

        return !$data ? false : $data;
    }

    public function getUserId(string $email) : int | false
    {
        $query = $this->db->prepare("SELECT `userId` FROM `user` WHERE `email` = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $query->bind_result($id);
        $result = $query->fetch();
        $query->close();

        return ($result == null) ? false : $id;
    }

    public function createUser(string $forename, string $surname, string $email, string $hashedPassword) : int | false
    {
        //Create new user
        $query = $this->db->prepare("INSERT INTO `user` (`forename`, `surname`, `email`, `password`) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $forename, $surname, $email, $hashedPassword);
        if(!$query->execute())
            return false;

        //Return new userId
        $query = $this->db->prepare("SELECT `userId` FROM `user` WHERE `email` = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $query->bind_result($id);
        $result = $query->fetch();
        $query->close();

        return $result ? $id : false;
    }

    public function getAdminHouse(int $adminId) : int | null
    {
        $query = $this->db->prepare("SELECT `houseId` FROM `user` JOIN `House` ON `adminId`=`userId` WHERE `userId` = ?");
        $query->bind_param("i", $adminId);
        $query->execute(); 
        $query->bind_result($houseId);
        $query->fetch();
        $query->close();
        return $houseId;
    }

    public function isUserAdmin(int $userId) : bool
    {
        return $this->getAdminHouse($userId) == null ? false : true;
    }

    public function getUserInviteLink(int $userId) : string | false
    {
        $houseId = $this->getAdminHouse($userId);
        if($houseId == null)
            return false;

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/household/join/" . $houseId;
    }

    public function getUserHousehold(int $userId) : int | false
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

    public function createRoom(int $houseId, string $name) : bool
    {
        //Create new room in house
        $query = $this->db->prepare("INSERT INTO `Room` (`name`, `houseId`) VALUES (?, ?)");
        $query->bind_param("si", $name, $houseId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function updateRoom(int $houseId, int $roomId, string $name) : bool
    {
        //Create new room in house
        $query = $this->db->prepare("UPDATE `Room` SET `name`=? WHERE `houseId`=? AND `roomId`=?");
        $query->bind_param("sii", $name, $houseId, $roomId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function deleteRoom(int $roomId, int $adminId) : bool
    {
        //Create new room in house
        $query = $this->db->prepare("DELETE FROM `Room` WHERE `roomId`=(SELECT `roomId` FROM `Room` JOIN `House` ON `Room`.`houseId`=`House`.`houseId` WHERE `roomId`=? AND `adminId`=?)");
        $query->bind_param("ii", $roomId, $adminId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function getRoomsInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `roomId`,`name` FROM `Room` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($roomId, $name);

        while($query->fetch())
        {
            $data[$roomId] = ['name' => $name];
        }

        $query->close();

        return ($data != null) ? $data : false;
    }

    public function createTask(int $houseId, string $name, string $description) : bool
    {
        //Create new task in house
        $query = $this->db->prepare("INSERT INTO `Task` (`name`, `description`, `houseId`) VALUES (?, ?, ?)");
        $query->bind_param("ssi", $name, $description, $houseId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function updateTask(int $houseId, int $taskId, string $name, string $description) : bool
    {
        //Update task in house
        $query = $this->db->prepare("UPDATE `Task` SET `name`=?, `description`=? WHERE `houseId`=? AND `taskId`=?");
        $query->bind_param("ssii", $name, $description, $houseId, $taskId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function deleteTask(int $taskId, int $adminId) : bool
    {
        //Delete task from house
        $query = $this->db->prepare("DELETE FROM `Task` WHERE `taskId`=(SELECT `taskId` FROM `Task` JOIN `House` ON `Task`.`houseId`=`House`.`houseId` WHERE `taskId`=? AND `adminId`=?)");
        $query->bind_param("ii", $taskId, $adminId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function getTasksInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `taskId`,`name`,`description` FROM `Task` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($taskId, $name, $description);

        while($query->fetch())
        {
            $data[$taskId] = ['name' => $name, 'description' => $description];
        }

        $query->close();

        return ($data != null) ? $data : false;
    }
}

?>