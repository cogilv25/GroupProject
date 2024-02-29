<?php

namespace App\Application\Domain;

use Psr\Container\ContainerInterface;
use mysqli;

//TODO: Once User Validation is implemented in actions houseId and userId will be known safe values so we can remove
// some of the prepared queries making things a bit less.... big.
//TODO: Just discovered Join Delete's so that might reduce the number of queries required here and there.
//TODO: Full implementation
class DatabaseDomain
{
	private mysqli $db;

    public function __construct()
    {
        //This can throw an exception but HttpErrorHandler will catch it further up the call stack
        $this->db = new mysqli("127.0.0.1", "root", "", "cleansync");
    }

    public function __destruct()
    {
        $this->db->close();
    }

    //Call the class object to get the underlying sqli object
    public function __invoke()
    {
        return $this->db;
    }

    public function disableForeignKeyChecks()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
    }

    public function disableSafeUpdates()
    {
        $this->db->query("SET SQL_SAFE_UPDATES = 0");
    }

    public function enableForeignKeyChecks()
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function enableSafeUpdates()
    {
        $this->db->query("SET SQL_SAFE_UPDATES = 1");
    }

    public function unsafeQuery(string $queryString)
    {
        return $this->db->query($queryString);
    }

    //Create household and add user as admin
    public function createHousehold(int $userId) : bool
    {
        $query1 = "INSERT INTO `House` (`adminId`) VALUES (". $userId .")";

        $subQuery = "SELECT `houseId` FROM `House` WHERE `adminId`=".$userId;
        $query2 = "UPDATE `user` SET `House_houseId`=(". $subQuery .")  WHERE `userId`=" . $userId;

        //If the first query succeeds return the result of the second otherwise false
        return $this->db->query($query1) ? $this->db->query($query2) : false;
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

    public function addUserToHousehold(int $userId, int $houseId) : bool
    {
        $subQuery = "SELECT `houseId` FROM `House` WHERE `houseId`=". $houseId;
        $query = "UPDATE `user` SET `House_houseId`=(".$subQuery.") WHERE `userId`=".$userId;

        return $this->db->query($query);
    }

    public function removeUserFromHousehold(int $userId, int $houseId) : bool
    {
        $query = "UPDATE `user` SET `House_houseId`=NULL WHERE `House_houseId`=".$houseId." AND `userId`=".$userId;
        return $this->db->query($query);
    }

    public function getAdminHouse(int $adminId) : int | bool
    {
        $query = $this->db->prepare("SELECT `houseId` FROM `user` JOIN `House` ON `adminId`=`userId` WHERE `userId` = ?");
        $query->bind_param("i", $adminId);
        $query->execute(); 
        $query->bind_result($houseId);
        $query->fetch();
        $query->close();
        return $houseId == null ? false : $houseId;
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

        return $houseId != null ? $houseId : false;
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

    public function deleteRoom(int $roomId, int $houseId) : bool
    {
        $queryString = "DELETE `Room`, `taskpoints`, `Rule` FROM `Room` ".
            "LEFT JOIN `taskpoints` ON `Room`.`roomId`=`taskpoints`.`roomId` ".
            "LEFT JOIN `Rule` ON `Rule`.`roomId`=`Room`.`roomId`".
            "WHERE `Room`.`roomId`=? AND `Room`.`houseId`=?";

        //Delete room from household
        $this->disableForeignKeyChecks();
        $query = $this->db->prepare($queryString);
        $query->bind_param("ii", $roomId, $houseId);
        $result = $query->execute();
        $query->close();
        $this->enableForeignKeyChecks();

        return $result;
    }

    public function getRoomsInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `roomId`,`name` FROM `Room` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($roomId, $name);

        $data = [];
        while($query->fetch())
        {
            $data[$roomId] = ['name' => $name];
        }

        $query->close();

        return $data;
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

    public function deleteTask(int $taskId, int $houseId) : bool
    {
        $queryString = "DELETE `Task`, `taskpoints`, `Rule` FROM `Task` ".
            "LEFT JOIN `taskpoints` ON `Task`.`taskId`=`taskpoints`.`taskId` ".
            "LEFT JOIN `Rule` ON `Rule`.`taskId`=`Task`.`taskId`".
            "WHERE `Task`.`taskId`=? AND `Task`.`houseId`=?";

        //Delete task from house
        $this->disableForeignKeyChecks();
        $query = $this->db->prepare($queryString);
        $query->bind_param("ii", $taskId, $houseId);
        $result = $query->execute();
        $query->close();
        $this->enableForeignKeyChecks();

        return $result;
    }

    public function getTasksInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `taskId`,`name`,`description` FROM `Task` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($taskId, $name, $description);
        $data = null;

        $data = [];
        while($query->fetch())
        {
            $data[$taskId] = ['name' => $name, 'description' => $description];
        }

        $query->close();

        return $data;
    }

    private function expandDayToArray(string $day) : array
    {
        $days = [];
        if($day == 'All')
        {
            array_push($days, 'Monday','Tuesday','Wednesday','Thursday','Friday');
            array_push($days, 'Saturday','Sunday');
        }
        elseif($day == 'Weekdays')
            array_push($days, 'Monday','Tuesday','Wednesday','Thursday','Friday');
        elseif($day == 'Weekends')
            array_push($days, 'Saturday','Sunday');
        else
            $days[] = $day;
        return $days;
    }

    public function createScheduleRows(int $userId, int $begin, int $end, string $day)
    {
        $days = $this->expandDayToArray($day);

        //Create/s new row/s in a users Schedule
        $this->db->begin_transaction();
        foreach ($days as &$day) {
            $query = $this->db->prepare("INSERT INTO `Schedule` (`userId`, `day`, `beginTimeslot`, `endTimeslot`) VALUES (?, ?, ?, ?)");
            $query->bind_param("isii", $userId, $day, $begin, $end);
            $result = $query->execute();
            $query->close();

            if(!$result)
            {
                $db->rollback();
                return false;
            }
        }
        
        return $this->db->commit();
    }

    public function updateScheduleRow(int $userId, int $scheduleId, int $begin, int $end, string $day)
    {
        //Update row in a users Schedule
        $query = $this->db->prepare("UPDATE `Schedule` SET `day`=?, `beginTimeslot`=?, `endTimeslot`=? WHERE `userId`=? AND `scheduleId`=?");
        $query->bind_param("siiii", $day, $begin, $end, $userId, $scheduleId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function deleteScheduleRow(int $userId, int $scheduleId) : bool
    {
        //Delete row from a users Schedule
        $query = $this->db->prepare("DELETE FROM `Schedule` WHERE `scheduleId`=(SELECT `scheduleId` FROM `Schedule` JOIN `user` ON `Schedule`.`userId`=`user`.`userId` WHERE `scheduleId`=? AND `userId`=?)");
        $query->bind_param("ii", $scheduleId, $userId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function deleteSchedule(int $userId) : bool
    {
        //Delete row from a users Schedule
        $query = $this->db->prepare("DELETE FROM `Schedule` WHERE `userId`=?");
        $query->bind_param("i", $userId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function getSchedule(int $userId)
    {
        $query = $this->db->prepare("SELECT `scheduleId`, `day`, `beginTimeslot`,`endTimeslot` FROM `Schedule` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute(); 
        $query->bind_result($scheduleId, $day, $begin, $end);

        $data = [];
        while($query->fetch())
            $data[] = ['rowId' => $scheduleId, 'day' => $day, 'beginTimeslot' => $begin, 'endTimeslot' => $end];

        $query->close();

        return $data;
    }

    public function getUserSchedulesInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `userId`, `forename`, `surname` FROM `user` WHERE `House_houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($userId, $forename, $surname);

        $users = [];
        while($query->fetch())
        {
            $users[$userId] = ['forename' => $forename, 'surname' => $surname];
        }
        $query->close();

        foreach ($users as $id => $details)
        {
            $users[$id]['schedule'] = $this->getSchedule($userId);
        }


        return $users;
    }

    //TODO: Delete Rota rows (currently isn't linked, might not be required..)
    public function deleteHousehold(int $houseId) : bool
    {
         //If there is an issue with this, god help me!
        $queryString = "DELETE `Room`, `Task`, `Rule`, `House`, `Task_has_user`, `taskPoints` FROM `House` LEFT JOIN ".
        "`Task` ON `House`.`houseId`=`Task`.`houseId` LEFT JOIN `Room` ON `House`.`houseId`=`Room`.`houseId` LEFT JOIN ".
        "`user` ON `House`.`houseId`=`user`.`House_houseId` LEFT JOIN `Task_has_user` ON `Task_has_user`.`userId`=".
        "`user`.`userId` LEFT JOIN `Rule` ON `Rule`.`userId`=`user`.`userId` OR `Rule`.`taskId`=`Task`.`taskId` OR ".
        "`Rule`.`roomId`=`Room`.`roomId` LEFT JOIN `taskPoints` ON `taskPoints`.`Task_taskId`=`Task`.`taskId` WHERE ".
        "`House`.`houseId`=?";

        //Begin a transaction so we can rollback if anything goes wrong
        $this->db->begin_transaction();

        //Makes all users who were in the Household homeless
        $query = $this->db->prepare("UPDATE `user`SET `House_houseId`=NULL WHERE `House_houseId`=?");
        $query->bind_param("i", $houseId);
        $result = $query->execute();
        $query->close();

        if($result != true)
        {
            $this->db->rollback();
            return false;
        }

        //Deletes all rows in all tables related to the Household except the user table
        $this->db->query("SET SQL_SAFE_UPDATES = 0");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        $query = $this->db->prepare($queryString);
        $query->bind_param("i", $houseId);
        $result = $query->execute();
        $query->close();

        if($result != true)
        {
            $this->db->rollback();
            return false;
        }

        //Returns true if our changes have been committed to the database
        $this->db->query("SET SQL_SAFE_UPDATES = 1");
        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
        return $this->db->commit();
    }

    public function createUserRoomRule(int $houseId, $userId, $roomId) : bool
    {
        //Create new user_room rule in house
        $query = $this->db->prepare("INSERT INTO `Rule` (`houseId`, `userId`, `roomId`) VALUES (?, ?, ?)");
        $query->bind_param("iii", $houseId, $userId, $roomId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function createTaskTimeRule(int $houseId, $taskId, $begin, $end) : bool
    {
        //Create new task_time rule in house
        $query = $this->db->prepare("INSERT INTO `Rule` (`houseId`, `taskId`, `beginTimeslot`, `endTimeslot`) VALUES (?, ?, ?, ?)");
        $query->bind_param("iiii", $houseId, $taskId, $begin, $end);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function createRoomTimeRule(int $houseId, $roomId, $begin, $end) : bool
    {
        //Create new room_time rule in house
        $query = $this->db->prepare("INSERT INTO `Rule` (`houseId`, `roomId`, `beginTimeslot`, `endTimeslot`) VALUES (?, ?, ?, ?)");
        $query->bind_param("iiii", $houseId, $roomId, $begin, $end);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function createUserTaskRule(int $houseId, $userId, $taskId) : bool
    {
        //Create new user_task rule in house
        $query = $this->db->prepare("INSERT INTO `Rule` (`houseId`, `userId`, `taskId`) VALUES (?, ?, ?)");
        $query->bind_param("iii", $houseId, $userId, $taskId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function deleteRule(int $houseId, int $ruleId) : bool
    {
        //Delete task from house
        $query = $this->db->prepare("DELETE FROM `Rule` WHERE `houseId`=? AND `ruleId`=?");
        $query->bind_param("ii", $houseId, $ruleId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function getRulesInHousehold(int $houseId) : array | bool
    {
        $query = $this->db->prepare("SELECT `ruleId`, `userId`, `taskId`, `roomId`, `beginTimeslot`, `endTimeslot` FROM `Rule` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($ruleId, $userId, $taskId, $roomId, $begin, $end);

        $rules = [];
        while($query->fetch())
        {
            if($userId == null)
            {
                if($taskId == null)
                {
                    //Room_Time Rule
                    $rules[1][$ruleId] = ['roomId' => $roomId, 'beginTimeslot' => $begin, 'endTimeslot' => $end];
                }
                elseif($roomId == null)
                {
                    //Task_Time Rule
                    $rules[2][$ruleId] = ['taskId' => $taskId, 'beginTimeslot' => $begin, 'endTimeslot' => $end];
                }
                else
                {
                    //Unreachable, TODO: Unreachable Errors
                    $query->close();
                    return false;
                }
            }
            elseif($begin == null)
            {
                if($taskId == null)
                {
                    //User_Room Rule
                    $rules[3][$ruleId] = ['userId' => $userId, 'roomId' => $roomId];
                }
                elseif($roomId == null)
                {
                    //User_Task Rule
                    $rules[4][$ruleId] = ['userId' => $userId, 'taskId' => $taskId];
                }
                else
                {
                    //Unreachable, TODO: Unreachable Errors
                    $query->close();
                    return false;
                }
            }
            else
            {
                //Unreachable, TODO: Unreachable Errors
                $query->close();
                return false;
            }
        }
        $query->close();

        return $rules;
    }
}

?>