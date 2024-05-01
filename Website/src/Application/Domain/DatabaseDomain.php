<?php

namespace App\Application\Domain;

use Psr\Container\ContainerInterface;
use mysqli;


enum ScheduleType
{
    case User;
    case Task;
    case Room;
}

//TODO: Once User Validation is implemented in actions houseId and userId will be known safe values so we can remove
// some of the prepared queries making things a bit less.... big.
//TODO: Just discovered Join Delete's so that might reduce the number of queries required here and there.
//TODO: Compress (Compression Oriented Programming)
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

    //Create household making the user the owner
    public function createHousehold(int $userId) : bool
    {
        $result = $this->db->query("INSERT INTO `House` VALUES ()");
        $id = $this->db->insert_id;

        if(!$result) return false;

        return $this->db->query("UPDATE `User` SET `houseId`=". $id .", `role`='owner' WHERE `userId`=" . $userId);
    }

    public function getUserIdAndPasswordHash(string $email) : array | false
    {
        $query = $this->db->prepare("SELECT `userId`, `password` FROM `User` WHERE `email` = ?");
        $query->bind_param("s", $email);
        $query->execute(); 
        $query->bind_result($id, $hashedPassword);
        $query->fetch();
        $query->close();

        return !isset($id) ? false : ['passwordHash' => $hashedPassword, 'id' => $id];
    }

    public function getUserId(string $email) : int | false
    {
        $query = $this->db->prepare("SELECT `userId` FROM `User` WHERE `email` = ?");
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
        $query = $this->db->prepare("INSERT INTO `User` (`forename`, `surname`, `email`, `password`) VALUES (?, ?, ?, ?)");
        $query->bind_param("ssss", $forename, $surname, $email, $hashedPassword);
        if(!$query->execute())
            return false;

        $userId = $this->db->insert_id;

        $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

        foreach ($days as $day)
        {
            $this->db->query("INSERT INTO `UserSchedule` (`beginTimeslot`,`endTimeslot`,`day`,`userId`) VALUES".
                             "(36,68,'" . $day . "'," . $userId . ")");
        }

        return $userId;
    }

    public function addUserToHousehold(int $userId, int $houseId) : bool
    {
        $subQuery = "SELECT `houseId` FROM `House` WHERE `houseId`=". $houseId;
        $query = "UPDATE `User` SET `houseId`=(".$subQuery.") WHERE `userId`=".$userId;

        return $this->db->query($query);
    }

    public function removeUserFromHousehold(int $userId, int $houseId, int $adminLevel) : bool
    {
        //Owner can delete anyone except themselves
        $query = "UPDATE `User` SET `houseId`= NULL AND `role`='member' WHERE `houseId`=".$houseId." AND `userId`=".$userId . 
        " AND NOT `role`='owner'";

        if($adminLevel == 1)
            $query .= " AND NOT `role`='admin'";

        $this->db->begin_transaction();

        if($result = $this->db->query($query) == false)
        {
            $this->db->rollback();
            return false;
        }

        if($this->db->affected_rows < 1)
        {
            $this->db->rollback();
            return false;
        }

        //Remove any rules relating to the user, remember users have only one house
        $query = "DELETE `User_Exempt_Room`, `User_Exempt_Task` FROM `User` ".
        "LEFT JOIN `User_Exempt_Room` ON `User_Exempt_Room`.`userId`=`User`.`userId` ".
        "LEFT JOIN `User_Exempt_Task` ON `User_Exempt_Task`.`userId`=`User`.`userId` ".
        "WHERE `User`.`userId`=" . $userId;

        if($this->db->query($query) == false)
        {
            $this->db->rollback();
            return false;
        }

        return $this->db->commit();
    }

    public function getUserHouseAndRole(int $userId)
    {
        $query = $this->db->prepare("SELECT `houseId`,`role` FROM `User` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute(); 
        $query->bind_result($houseId, $role);
        $query->fetch();
        $query->close();
        if(!isset($houseId))
            return false;
        return [$houseId, $role];
    }

    public function getAdminHouse(int $adminId) : int | bool
    {
        $query = $this->db->prepare("SELECT `houseId` FROM `User` WHERE `userId` = ? AND (`role`='owner' OR `role`='admin')");
        $query->bind_param("i", $adminId);
        $query->execute(); 
        $query->bind_result($houseId);
        $query->fetch();
        $query->close();
        if($query->num_rows < 1) return false;
        return $houseId;
    }

    public function getOwnerHouse(int $ownerId) : int | bool
    {
        $query = $this->db->prepare("SELECT `houseId` FROM `User` WHERE `userId` = ? AND `role`='owner'");
        $query->bind_param("i", $ownerId);
        $query->execute(); 
        $query->bind_result($houseId);
        $query->fetch();
        $query->close();
        return $houseId == null ? false : $houseId;
    }

    public function isUserAdmin(int $userId) : bool
    {
        return ($this->getAdminHouse($userId) != false);
    }

    public function promoteUser(int $houseId, int $userId) : bool
    {
        return $this->db->query("UPDATE `User` SET `role`='admin' WHERE ".
        "`houseId`=" . $houseId . " AND `userId`=" . $userId);
    }

    public function demoteUser(int $houseId, int $userId) : bool
    {
        return $this->db->query("UPDATE `User` SET `role`='member' WHERE ".
        "`houseId`=" . $houseId . " AND `userId`=" . $userId);
    }

    public function transferOwnership(int $houseId, int $memberId, int $ownerId) : bool
    {
        $this->db->begin_transaction();

        $result = $this->db->query("UPDATE `User` SET `role`='owner' WHERE `userId`=" . $memberId);

        if($result === false)
        {
            $this->db->rollback();
            return false;
        }

        if($this->db->affected_rows != 1)
        {
            $this->db->rollback();
            return false;
        }

        $result = $this->db->query("UPDATE `User` SET `role`='admin' WHERE `userId`=" . $ownerId);

        if($result === false)
        {
            $this->db->rollback();
            return false;
        }

        if($this->db->affected_rows != 1)
        {
            $this->db->rollback();
            return false;
        }

        return $this->db->commit();
    }

    public function getUserInviteLink(int $userId) : string
    {
        $result = $this->db->query("SELECT `User`.`houseId`, `invite_link`, `role` FROM `User` JOIN ".
            "`House` on `User`.`houseId`=`House`.`houseId` WHERE `userId`=" . $userId);

        if($result === false) return "No Link";
        if($result->num_rows == 0) return "No Link";

        $row = $result->fetch_row();
        $role = $row[2];
        if($role == 'member') return "No Link";

        $houseId = $row[0];
        $inviteLink = $row[1];

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/household/join/" . $houseId . "/" . $inviteLink;
    }

    public function validateInviteLink(int $houseId, string $inviteLink)
    {
        $result = $this->db->query("SELECT `houseId` FROM `House` WHERE `houseId`=" . $houseId .
                                   " AND `invite_link`='" . $inviteLink . "'");

        if($result === false) return false;

        return ($result->num_rows > 0) ? true : false;
    }

    public function getUserHousehold(int $userId) : int | false
    {
        $query = $this->db->prepare("SELECT `houseId` FROM `User` WHERE `userId` = ?");
        $query->bind_param("i", $userId);
        $query->execute(); 
        $query->bind_result($houseId);
        $result = $query->fetch();
        $query->close();

        return $houseId != null ? $houseId : false;
    }

    public function getUsersInHousehold(int $houseId)
    {
        $query = $this->db->prepare("SELECT `userId`, `forename`, `surname`, `email`, `role` FROM `User` WHERE `houseId` = ?");
        $query->bind_param("i", $houseId);
        $query->execute(); 
        $query->bind_result($userId, $forename, $surname, $email, $role);

        while($query->fetch())
        {
            $data[$userId] = ['userId' => $userId, 'forename' => $forename, 'surname' => $surname, 'role' => $role, 'email' => $email];
        }

        $query->close();

        return ($data != null) ? $data : false;
    }

    public function getUsersNamesInHousehold(int $houseId) : bool | array
    {
        $result = $this->db->query("SELECT `userId`, `forename`, `surname` FROM ".
            "`User` WHERE `houseId`=" . $houseId);

        if($result === false) return false;

        $data = [];
        while($row = $result->fetch_row())
        {
            $data[$row[0]] = ['forename' => $row[1], 'surname' => $row[2]];
        }
        return $data;
    }

    public function createRoom(int $houseId, string $name) : bool | int
    {
        //Create new room in house
        $query = $this->db->prepare("INSERT INTO `Room` (`name`, `houseId`) VALUES (?, ?)");
        $query->bind_param("si", $name, $houseId);
        $result = $query->execute();
        $query->close();

        return $result ? $this->db->insert_id : false;
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
        //TODO: Delete affected Room_Has_Task entries.
        $queryString = "DELETE `Room`, `TaskPoints`, `User_Exempt_Room`, `RoomSchedule` FROM `Room` ".
            "LEFT JOIN `TaskPoints` ON `Room`.`roomId`=`TaskPoints`.`roomId` ".
            "LEFT JOIN `User_Exempt_Room` ON `User_Exempt_Room`.`roomId`=`Room`.`roomId`".
            "LEFT JOIN `RoomSchedule` ON `RoomSchedule`.`roomId`=`Room`.`roomId`".
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

    public function getHouseholdTaskDetails(int $houseId)
    {
        $query = "SELECT `roomId`,`name` FROM `Room` WHERE `houseId`=". $houseId;

        $result = $this->db->query($query);       

        if($result == false)
            return $result;

        $details = ['rooms' => [], 'tasks' => []];
        while($row = $result->fetch_row())
            $details['rooms'][$row[0]] = $row[1];

        $query = "SELECT `Task`.`taskId`, `Task`.`name`, `Task`.`description`, `RHTId`, `Room`.`roomId` FROM `Task` ".
                 "LEFT JOIN `Room` ON `Room`.`houseId`=`Task`.`houseId` LEFT JOIN ".
                 "`Room_Has_Task` ON (`Task`.`taskId` = `Room_Has_Task`.`taskId` AND ".
                 "`Room`.`roomId`=`Room_Has_Task`.`roomId`) WHERE `Task`.`houseId`=" . $houseId;

        $result = $this->db->query($query);       

        if($result == false)
            return $result;

        while($row = $result->fetch_row())
        {
            $details['tasks'][$row[0]]['name'] = $row[1];
            $details['tasks'][$row[0]]['description'] = $row[2];
            $details['tasks'][$row[0]]['rooms'][$row[4]] = (bool)($row[3] != null);
        }

        return $details;
    }

    public function getHouseholdRoomDetails(int $houseId)
    {
        $query = "SELECT `taskId`,`name` FROM `Task` WHERE `houseId`=". $houseId;

        $result = $this->db->query($query);       

        if($result == false)
            return $result;

        $details = ['tasks' => [], 'rooms' => []];
        while($row = $result->fetch_row())
            $details['tasks'][$row[0]] = $row[1];

        $query = "SELECT `Room`.`roomId`, `Room`.`name`, `RHTId`, `Task`.`taskId` FROM `Room` ".
                 "LEFT JOIN `Task` ON `Room`.`houseId`=`Task`.`houseId` LEFT JOIN ".
                 "`Room_Has_Task` ON (`Room`.`roomId`=`Room_Has_Task`.`roomId` AND ".
                 "`Task`.`taskId`=`Room_Has_Task`.`taskId`) WHERE `Room`.`houseId`=" . $houseId;

        $result = $this->db->query($query);       

        if($result == false)
            return $result;

        while($row = $result->fetch_row())
        {
            $details['rooms'][$row[0]]['name'] = $row[1];
            $details['rooms'][$row[0]]['tasks'][$row[3]] = (bool)($row[2] != null);
        }

        return $details;
    }

    public function createTask(int $houseId, string $name, string $description) : bool | int
    {
        //Create new task in house
        $query = $this->db->prepare("INSERT INTO `Task` (`name`, `description`, `houseId`) VALUES (?, ?, ?)");
        $query->bind_param("ssi", $name, $description, $houseId);
        $result = $query->execute();
        $query->close();

        return $result ? $this->db->insert_id : false;
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
        $queryString = "DELETE `Task`, `TaskPoints`, `User_Exempt_Task`, `TaskSchedule` FROM `Task` ".
            "LEFT JOIN `TaskPoints` ON `Task`.`taskId`=`TaskPoints`.`taskId` ".
            "LEFT JOIN `User_Exempt_Task` ON `User_Exempt_Task`.`taskId`=`Task`.`taskId`".
            "LEFT JOIN `TaskSchedule` ON `TaskSchedule`.`taskId`=`Task`.`taskId`".
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

    //Check if the provided row overlaps with any other rows in the schedule.
    //can be used for Task, Room and User Schedules
    public function checkScheduleRowCollides(ScheduleType $t, int $t_id, string $day, int $begin, int $end)
    {
        switch ($t) {
            case ScheduleType::User:
            $table = "UserSchedule"; $column = "userId";
                break;
            case ScheduleType::Task:
            $table = "TaskSchedule"; $column = "taskId";
                break;
            case ScheduleType::Room:
            $table = "RoomSchedule"; $column = "roomId";
                break;
        }

        // basically a 1D AABB algorithm in SQL + the extra check for if the new
        // row fully encompassing a previously existing row.
        // Note: choosing beginTimeslot is arbitrary we just need to count rows
        $result = $this->db->query("SELECT `beginTimeslot` FROM `". $table ."` WHERE".
            "`" . $column . "`=" . $t_id . " AND `day`='" . $day . "' AND (".
            "(`beginTimeslot` <= ". $begin ." AND `endTimeslot` >= ". $begin .") OR ". // Check for collision straddling begin
            "(`beginTimeslot` <= ".  $end  ." AND `endTimeslot` >= ".  $end  .") OR ". // Check for collision straddling end
            "(`beginTimeslot` >= ".  $begin  ." AND `endTimeslot` <= ". $end ."))"   );// Check for collision straddling full range

        //Unreachable
        if($result == false)
            return true;

        //We should get exactly 1 row
        return ($result->num_rows != 1) ? true : false;
    }

    public function checkScheduleForCollisions(ScheduleType $t, int $t_id)
    {
        switch ($t) {
            case ScheduleType::User:
            $table = "UserSchedule";
                break;
            case ScheduleType::Task:
            $table = "TaskSchedule";
                break;
            case ScheduleType::Room:
            $table = "RoomSchedule";
                break;
        }

        // This query returns a count of the schedule rows that collide.
        $query = "SELECT count(*) FROM `". $table ."` as t1 WHERE 1 > ".
        "(SELECT count(*) FROM `". $table ."` WHERE `t1`.`day`=`day` AND ". 
        "((`t1`.`beginTimeslot` <= `beginTimeslot` AND `t1`.`endTimeslot` >= `beginTimeslot`) OR ".
        "(`t1`.`beginTimeslot` <= `endTimeslot` AND `t1`.`endTimeslot` >= `endTimeslot`) OR ".
        "(`t1`.`beginTimeslot` >= `beginTimeslot` AND `t1`.`endTimeslot` <= `endTimeslot`)))";

        $result = $this->db->query($query);

        if($result === false)
            return true;

        if($result->fetch_row()[0] != 0)
            return true;

        return false;
    }

    // $rows should be in the form [[begin, end, day], ...]
    public function overwriteUserSchedule(int $userId, array $rows) : bool
    {
        //Get existing row id's
        $result = $this->db->query("SELECT `scheduleId` FROM `UserSchedule` WHERE `userId`=" . $userId);
        
        if($result === false)
            return false; // Theoretically unreachable.

        $rowIds = [];
        while($row = $result->fetch_row())
            $rowIds[] = $row[0];

        $i = 0;
        $range = min(count($rowIds), count($rows));
        $delete = (count($rowIds) > count($rows));
        $this->db->begin_transaction();
        for(;$i<$range;++$i)
        {
            $query = "UPDATE `UserSchedule` SET `day`='" . $rows[$i][2] . "', `beginTimeslot`=" .
            $rows[$i][0] . ", `endTimeslot`=" . $rows[$i][1] . " WHERE `scheduleId`=" . $rowIds[$i];

            $result = $this->db->query($query);
            if($result === false)
            {
                $this->db->rollback();
                return false;
            }
        }

        if($delete)
        {
            for(;$i < count($rowIds);++$i)
            {
                $result = $this->query("DELETE FROM `UserSchedule` WHERE `scheduleId`=" . $rowIds[$i]);
                if($result === false)
                {
                    $this->db->rollback();
                    return false;
                }
            }
        }
        else
        {
            for(;$i < count($rows);++$i)
            {
                $query = "INSERT INTO `UserSchedule` (`userId`, `day`, `beginTimeslot`, `endTimeslot`) ".
                "VALUES (" . $userId . ",'" . $rows[$i][2] . "'," . $rows[$i][0] . "," .$rows[$i][1] . ")";

                $result = $this->db->query($query);

                if($result === false)
                {
                    $this->db->rollback();
                    return false;
                }
            }
        }

        if($this->checkScheduleForCollisions(ScheduleType::User, $userId) === true)
        {
            $this->db->rollback();
            return false;
        }

        return $this->db->commit();
    }

    public function createUserScheduleRow(int $userId, int $begin, int $end, string $day) : bool | int
    {

        //Creates a new row in a UserSchedule
        $this->db->begin_transaction();
        $query = $this->db->prepare("INSERT INTO `UserSchedule` (`userId`, `day`, `beginTimeslot`, `endTimeslot`) VALUES (?, ?, ?, ?)");
        $query->bind_param("isii", $userId, $day, $begin, $end);
        $result = $query->execute();
        $query->close();

        if(!$result)
        {
            $this->db->rollback();
            return false;
        }

        //Check if our new row overlaps with any other rows and if so rollback
        if($this->checkScheduleRowCollides(ScheduleType::User, $userId, $day, $begin, $end))
        {
            $this->db->rollback();
            return false;
        }
        
        return $this->db->commit() ? $this->db->insert_id : false;
    }



    public function updateUserScheduleRow(int $userId, int $scheduleId, int $begin, int $end, string $day)
    {
        //Start a transaction and update the row in ernest.
        $this->db->begin_transaction();
        $query = $this->db->prepare("UPDATE `UserSchedule` SET `day`=?, `beginTimeslot`=?, `endTimeslot`=? WHERE `userId`=? AND `scheduleId`=?");
        $query->bind_param("siiii", $day, $begin, $end, $userId, $scheduleId);
        $result = $query->execute();
        $query->close();

        //If the query fails rollback
        if($result == false)
        {
            $this->db->rollback();
            return false;
        }

        //Check if our new row overlaps with any other rows and if so rollback
        if($this->checkScheduleRowCollides(ScheduleType::User, $userId, $day, $begin, $end))
        {
            $this->db->rollback();
            return false;
        }


        //Finally if all went well commit and return
        return $this->db->commit();
    }

    public function deleteUserScheduleRow(int $userId, int $scheduleId) : bool
    {
        //Delete row from a UserSchedule
        $query = $this->db->prepare("DELETE FROM `UserSchedule` WHERE `scheduleId`=(SELECT `scheduleId` FROM `UserSchedule` JOIN `User` ON `UserSchedule`.`userId`=`User`.`userId` WHERE `scheduleId`=? AND `userId`=?)");
        $query->bind_param("ii", $scheduleId, $userId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function deleteUserSchedule(int $userId) : bool
    {
        //Delete row from a users UserSchedule
        $query = $this->db->prepare("DELETE FROM `UserSchedule` WHERE `userId`=?");
        $query->bind_param("i", $userId);
        $result = $query->execute();
        $query->close();

        return $result;
    }

    public function getUserSchedule(int $userId)
    {
        $query = $this->db->prepare("SELECT `scheduleId`, `day`, `beginTimeslot`,`endTimeslot` FROM `UserSchedule` WHERE `userId` = ?");
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
        $query = $this->db->prepare("SELECT `userId`, `forename`, `surname` FROM `User` WHERE `houseId` = ?");
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
            $users[$id]['schedule'] = $this->getUserSchedule($id);
        }


        return $users;
    }

    public function getUserSchedulesFlat(int $houseId)
    {
        $query = "SELECT `User`.`userId`, `day`, `beginTimeslot`,`endTimeslot` FROM `UserSchedule` ". 
        "JOIN `User` ON `User`.`userId`=`UserSchedule`.`userId` ".
        "WHERE `houseId`=" . $houseId;

        $result = $this->db->query($query);

        $flatSchedules = [];
        while($row = $result->fetch_row())
            $flatSchedules[] = $row;

        return $flatSchedules;
    }

    //TODO: Delete Rota rows (currently isn't linked, might not be required..)
    public function deleteHousehold(int $houseId) : bool
    {
        $queryString = "DELETE `Room`, `Task`, `House`, `Rota`, ".
        "`taskPoints`, `RoomSchedule`, `TaskSchedule`, `User_Exempt_Room`, ".
        "`User_Exempt_Task` ".
        "FROM `House` LEFT JOIN `Task` ON `House`.`houseId`=`Task`.`houseId`".
        "LEFT JOIN `Room` ON `House`.`houseId`=`Room`.`houseId` ".
        "LEFT JOIN `User` ON `House`.`houseId`=`User`.`houseId` ".
        "LEFT JOIN `Rota` ON `Rota`.`userId`=`User`.`userId` ".
        "LEFT JOIN `TaskSchedule` ON `TaskSchedule`.`houseId`=`House`.`houseId`".
        "LEFT JOIN `RoomSchedule` ON `RoomSchedule`.`houseId`=`House`.`houseId`".
        "LEFT JOIN `User_Exempt_Task` ON `User_Exempt_Task`.`houseId`=`House`.`houseId`".
        "LEFT JOIN `User_Exempt_Room` ON `User_Exempt_Room`.`houseId`=`House`.`houseId`".
        "LEFT JOIN `taskPoints` ON `taskPoints`.`taskId`=`Task`.`taskId` ".
        "WHERE `House`.`houseId`=?";

        //Begin a transaction so we can rollback if anything goes wrong
        $this->db->begin_transaction();

        //Makes all users who were in the Household homeless
        $query = $this->db->prepare("UPDATE `User`SET `houseId`=NULL, `role`='member' WHERE `houseId`=?");
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

    public function createUserRoomRule(int $houseId, $userId, $roomId, $active = false) : bool | int
    {
        $active = $active ? 1 : 0;

        //Create new user_room rule in house
        $query = $this->db->prepare("INSERT INTO `User_Exempt_Room` (`houseId`, `userId`, `roomId`, `active`) VALUES (?, ?, ?, ?)");
        $query->bind_param("iiis", $houseId, $userId, $roomId, $active);
        $result = $query->execute();
        $query->close();

        return $result ? $this->db->insert_id : false;
    }

    public function createUserTaskRule(int $houseId, $userId, $taskId, $active = false) : bool | int
    {
        $active = $active ? 1 : 0;

        //Create new user_task rule in house
        $query = $this->db->prepare("INSERT INTO `User_Exempt_Task` (`houseId`, `userId`, `taskId`, `active`) VALUES (?, ?, ?, ?)");
        $query->bind_param("iiis", $houseId, $userId, $taskId, $active);
        $result = $query->execute();
        $query->close();

        return $result ? $this->db->insert_id : false;
    }

    public function toggleUserTaskRule(int $houseId, int $ruleId, bool $state) : bool
    {
        $active = $state ? 1 : 0; 
        $query = "UPDATE `User_Exempt_Task` SET `active`=" . $active . " WHERE ".
            "houseId=" . $houseId . " AND `UETId`=" . $ruleId;

        return $this->db->query($query);
    }

    public function toggleUserRoomRule(int $houseId, int $ruleId, bool $state) : bool
    {
        $active = $state ? 1 : 0; 
        $query = "UPDATE `User_Exempt_Room` SET `active`=" . $active . " WHERE ".
            "houseId=" . $houseId . " AND `UERId`=" . $ruleId;

        return $this->db->query($query);
    }


    public function createTaskTimeRule(int $houseId, $taskId, $day, $begin, $end) : bool | int
    {
        //Start a transaction and update the row in ernest.
        $this->db->begin_transaction();
        $query = $this->db->prepare("INSERT INTO `TaskSchedule` (`houseId`,`taskId`, `day`, `beginTimeslot`, `endTimeslot`) VALUES (?,?,?,?,?)");
        $query->bind_param("iisii", $houseId, $taskId, $day, $begin, $end);
        $result = $query->execute();
        $query->close();

        //If the query fails rollback
        if($result == false)
        {
            $this->db->rollback();
            return false;
        }

        //Check if our new row overlaps with any other rows and if so rollback
        if($this->checkScheduleRowCollides(ScheduleType::Task, $taskId, $day, $begin, $end))
        {
            $this->db->rollback();
            return false;
        }


        //Finally if all went well commit and return
        return $this->db->commit() ? $this->db->insert_id : false;
    }

    public function createRoomTimeRule(int $houseId, $roomId, $day, $begin, $end) : bool | int
    {
        //Start a transaction and update the row in ernest.
        $this->db->begin_transaction();
        $query = $this->db->prepare("INSERT INTO `RoomSchedule` (`houseId`,`roomId`, `day`, `beginTimeslot`, `endTimeslot`) VALUES (?,?,?,?,?)");
        $query->bind_param("iisii", $houseId, $roomId, $day, $begin, $end);
        $result = $query->execute();
        $query->close();

        //If the query fails rollback
        if($result == false)
        {
            $this->db->rollback();
            return false;
        }

        //Check if our new row overlaps with any other rows and if so rollback
        if($this->checkScheduleRowCollides(ScheduleType::Room, $roomId, $day, $begin, $end))
        {
            $this->db->rollback();
            return false;
        }


        //Finally if all went well commit and return
        return $this->db->commit() ? $this->db->insert_id : false;
    }

    public function deleteRule(int $houseId, int $ruleType, int $ruleId) : bool | int
    {
        $idColumn = "scheduleId";
        switch($ruleType)
        {
            case 1:
                $table = "RoomSchedule";
                break;
            case 2:
                $table = "TaskSchedule";
                break;
            case 3:
                $table = "User_Exempt_Room";
                $idColumn = "UERId";
                break;
            case 4:
                $table = "User_Exempt_Task";
                $idColumn = "UETId";
                break;
        }

        $query = $this->db->prepare("DELETE FROM `". $table ."` WHERE `". $idColumn ."`=? AND `houseId`=?");
        $query->bind_param("ii", $ruleId, $houseId);
        $result = $query->execute();
        $query->close();

        return $result ? $this->db->insert_id : false;
    }

    public function createRoomHasTaskEntry(int $houseId, int $roomId, int $taskId) : bool
    {
        // If the room and task ids are not for the correct house, although this shouldn't happen,
        //   they won't be used by the rest of the application.
        $query = "INSERT INTO `Room_Has_Task` (`houseId`,`roomId`,`taskId`) VALUES (" .
            $houseId . ", " . $roomId . ", " . $taskId .")";
        return $this->db->query($query);
    }

    public function deleteRoomHasTaskEntry(int $houseId, int $roomId, int $taskId) : bool
    {
        // Including houseId in the query prevents nefarious requests from deleting arbitrary
        //   household's room_has_task entries.
        $query = "DELETE FROM `Room_Has_Task` WHERE `houseId`=" . $houseId . " AND `roomId`= " .
        $roomId . " AND `taskId`=" . $taskId;
        return $this->db->query($query);
    }

    public function getExemptionRules(int $houseId) : array | bool
    {
        //TODO: Add justification to the database to allow users to give details
        //        to the admins about why they want a rule applied.

        //Get User_Exempt_Task rules
        $query = "SELECT `forename`,`surname`,`Task`.`name`,`UETId`,`active` FROM `User_Exempt_Task` JOIN ".
                 "`User` ON `User`.`userId`=`User_Exempt_Task`.`userId` JOIN `Task` ON `Task`.`taskId`=".
                 "`User_Exempt_Task`.`taskId` WHERE `Task`.`houseId`=" . $houseId;

        $result = $this->db->query($query);

        if($result === false)
            return false;

        $rules = [];
        while($row = $result->fetch_row())
        {
            $rules[] = ['forename' => $row[0], 'surname' => $row[1], 'name' => $row[2], 'just' => "",
                'type' => "user_task", 'ruleId' => $row[3], 'active' => ((bool)$row[4])];
        }

        //Get User_Exempt_Room rules
        $query = "SELECT `forename`,`surname`,`Room`.`name`,`UERId`,`active` FROM `User_Exempt_Room` JOIN ".
                 "`User` ON `User`.`userId`=`User_Exempt_Room`.`userId` JOIN `Room` ON `Room`.`roomId`=".
                 "`User_Exempt_Room`.`roomId` WHERE `Room`.`houseId`=" . $houseId;

        $result = $this->db->query($query);

        if($result === false)
            return false;

        while($row = $result->fetch_row())
        {
            $rules[] = ['forename' => $row[0], 'surname' => $row[1], 'name' => $row[2], 'just' => "",
                'type' => "user_room", 'ruleId' => $row[3], 'active' => ((bool)$row[4])];
        }

        return $rules;
    }

    public function getRulesInHousehold(int $houseId) : array | bool
    {
        $tables = ["RoomSchedule" => "scheduleId", "TaskSchedule" => "scheduleId",
        "User_Exempt_Room" => "UERId", "User_Exempt_Task" => "UETId"];
        $rules = [];

        $result = $this->db->query("SELECT `scheduleId`, `roomId`, `day`, `beginTimeslot`, `endTimeslot` FROM `RoomSchedule` WHERE `houseId`=" . $houseId);

        while($row = $result->fetch_row())
        {
            $rules["Schedules"]["Rooms"][] = ['roomId' => $row[1], $row[2] => ['id'=>$row[0],'beginTimeslot'=>$row[3],'endTimeslot'=>$row[4]]];
        }

        $result = $this->db->query("SELECT `scheduleId`, `taskId`, `day`, `beginTimeslot`, `endTimeslot` FROM `TaskSchedule` WHERE `houseId`=" . $houseId);

        while($row = $result->fetch_row())
        {
            $rules["Schedules"]["Tasks"][] = ['taskId' => $row[1], $row[2] => ['id'=>$row[0],'beginTimeslot'=>$row[3],'endTimeslot'=>$row[4]]];
        }

        $result = $this->db->query("SELECT `UERId`, `roomId`, `userId` FROM `User_Exempt_Room` WHERE `houseId`=" . $houseId);

        while($row = $result->fetch_row())
        {
            $rules["Rules"]["User_Exempt_Room"][] = ['id'=>$row[0], 'roomId' => $row[1], 'userId'=>$row[2]];
        }

        $result = $this->db->query("SELECT `UETId`, `taskId`, `userId` FROM `User_Exempt_Task` WHERE `houseId`=" . $houseId);

        while($row = $result->fetch_row())
        {
            $rules["Rules"]["User_Exempt_Task"][] = ['id'=>$row[0], 'taskId' => $row[1], 'userId'=>$row[2]];
        }

        return $rules;
    }

    public function getValidUserRoomTaskCombinations(int $houseId) : array
    {
        //This query gets all tasks in all rooms for all users where:
        // - The task can be performed in the room (RHT)
        // - The user is not exempt from the task (UET IS NULL)
        // - The user is not exempt from the room (UER IS NULL)
        $query = "SELECT `User`.`userId`, RHT.`roomId`, RHT.`taskId` FROM `User` ".
        "JOIN `Room_Has_Task` RHT ON `User`.`houseId`=RHT.`houseId` ".
        "LEFT JOIN `User_Exempt_Task` UET ON UET.`taskId`=RHT.`taskId` AND UET.`userId`=`User`.`userId` ".
        "LEFT JOIN `User_Exempt_Room` UER ON UER.`roomId`=RHT.`roomId` AND UER.`userId`=`User`.`userId` ".
        "WHERE UER.`UERId` IS NULL AND UET.`UETId` IS NULL AND `User`.`houseId`=" . $houseId;

        $result = $this->db->query($query);

        $validRoomTaskUsers = [];
        while($row = $result->fetch_row())
        {
            $validRoomTaskUsers[] = $row;
        }

        return $validRoomTaskUsers;
    }

}

?>