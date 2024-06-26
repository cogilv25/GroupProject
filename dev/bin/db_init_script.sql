-- Schema cleansync
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `cleansync`;
CREATE SCHEMA `cleansync` DEFAULT CHARACTER SET utf8;
USE `cleansync`;

-- -----------------------------------------------------
-- Table `House`
-- -----------------------------------------------------
-- Disable fk checks as House and user reference one another
SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `House` (
  `houseId` INT NOT NULL AUTO_INCREMENT,
  `invite_link` CHAR(36) DEFAULT (UUID()),
  PRIMARY KEY (`houseId`));

SET FOREIGN_KEY_CHECKS = 1;
-- -----------------------------------------------------
-- Table `User`
-- -----------------------------------------------------
CREATE TABLE `User` (
  `userId` INT NOT NULL AUTO_INCREMENT,
  `forename` VARCHAR(32) NOT NULL,
  `surname` VARCHAR(32) NOT NULL,
  `email` VARCHAR(64) NOT NULL,
  `password` VARCHAR(256) NOT NULL,
  `houseId` INT,
  `personalPoints` INT,
  `role` ENUM('member','admin','owner') NOT NULL DEFAULT 'member',
  PRIMARY KEY (`userId`),
  INDEX `fk_user_House_idx` (`houseId` ASC),
  CONSTRAINT `fk_user_house`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`));


-- -----------------------------------------------------
-- Table `Room`
-- -----------------------------------------------------
CREATE TABLE `Room` (
  `roomId` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL,
  `capacity` INT NOT NULL DEFAULT 1,
  `houseId` INT NOT NULL,
  PRIMARY KEY (`roomId`),
  CONSTRAINT `House_RoomName_Unique` UNIQUE(`name`,`houseId`),
  INDEX `fk_Room_House1_idx` (`houseId` ASC),
  CONSTRAINT `fk_Room_House1`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`));


-- -----------------------------------------------------
-- Table `Task`
-- -----------------------------------------------------
CREATE TABLE `Task` (
  `taskId` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL,
  `description` VARCHAR(1024) NOT NULL,
  `capacity` INT NOT NULL DEFAULT 1,
  `duration` INT NOT NULL DEFAULT 4,
  `occurrencePerWeek` INT NOT NULL DEFAULT 7,
  `houseId` INT NOT NULL,
  PRIMARY KEY (`taskId`),
  CONSTRAINT `House_TaskName_Unique` UNIQUE(`name`,`houseId`),
  INDEX `fk_Task_Room1_idx` (`houseId` ASC),
  CONSTRAINT `fk_Task_House`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`));


-- -----------------------------------------------------
-- Table `User_Exempt_Task`
-- -----------------------------------------------------
CREATE TABLE `User_Exempt_Task` (
  `UETId` INT NOT NULL AUTO_INCREMENT,
  `houseId` INT NOT NULL,
  `userId` INT NOT NULL,
  `taskId` INT NOT NULL,
  `active` BOOL NOT NULL,
  PRIMARY KEY (`UETId`),
  CONSTRAINT `user_exempt_task_unique` UNIQUE(`userId`,`taskId`),
  INDEX `fk_user_exempt_task_user_idx` (`userId` ASC),
  INDEX `fk_user_exempt_task_task_idx` (`taskId` ASC),
  INDEX `fk_user_exempt_task_house_idx` (`houseId` ASC),
  CONSTRAINT `fk_user_exempt_task_user`
    FOREIGN KEY (`userId`)
    REFERENCES `User` (`userId`),
  CONSTRAINT `fk_user_exempt_task_house`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`),
  CONSTRAINT `fk_user_exempt_task_task`
    FOREIGN KEY (`taskId`)
    REFERENCES `Task` (`taskId`));

-- -----------------------------------------------------
-- Table `User_Exempt_Room`
-- -----------------------------------------------------
CREATE TABLE `User_Exempt_Room` (
  `UERId` INT NOT NULL AUTO_INCREMENT,
  `houseId` INT NOT NULL,
  `userId` INT NOT NULL,
  `roomId` INT NOT NULL,
  `active` BOOL NOT NULL,
  PRIMARY KEY (`UERId`),
  CONSTRAINT `user_exempt_room_unique` UNIQUE(`userId`,`roomId`),
  INDEX `fk_user_exempt_room_user_idx` (`userId` ASC),
  INDEX `fk_user_exempt_room_room_idx` (`roomId` ASC),
  INDEX `fk_user_exempt_room_house_idx` (`houseId` ASC),
  CONSTRAINT `fk_user_exempt_room_user`
    FOREIGN KEY (`userId`)
    REFERENCES `User` (`userId`),
  CONSTRAINT `fk_user_exempt_room_house`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`),
  CONSTRAINT `fk_user_exempt_room_room`
    FOREIGN KEY (`roomId`)
    REFERENCES `Room` (`roomId`));

-- -----------------------------------------------------
-- Table `Room_Has_Task`
-- -----------------------------------------------------
CREATE TABLE `Room_Has_Task` (
  `RHTId` INT NOT NULL AUTO_INCREMENT,
  `houseId` INT NOT NULL,
  `roomId` INT NOT NULL,
  `taskId` INT NOT NULL,
  PRIMARY KEY (`RHTId`),
  CONSTRAINT `room_has_task_unique` UNIQUE(`roomId`,`taskId`),
  INDEX `fk_room_has_task_room_idx` (`roomId` ASC),
  INDEX `fk_room_has_task_task_idx` (`taskId` ASC),
  INDEX `fk_room_has_task_house_idx` (`houseId` ASC),
  CONSTRAINT `fk_room_has_task_room`
    FOREIGN KEY (`roomId`)
    REFERENCES `Room` (`roomId`),
  CONSTRAINT `fk_room_has_task_house`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`),
  CONSTRAINT `fk_room_has_task_task`
    FOREIGN KEY (`taskId`)
    REFERENCES `Task` (`taskId`));

-- -----------------------------------------------------
-- Table `RoomSchedule`
-- -----------------------------------------------------
CREATE TABLE `RoomSchedule` (
  `scheduleId` INT NOT NULL AUTO_INCREMENT,
  -- begin to end range is inclusive
  `beginTimeslot` INT NOT NULL,
  `endTimeslot` INT NOT NULL,
  `day` ENUM('Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `roomId` INT NOT NULL,
  `houseId` INT NOT NULL,
  PRIMARY KEY (`scheduleId`),
  INDEX `fk_roomschedule_room_idx` (`roomId` ASC),
  INDEX `fk_roomschedule_house_idx` (`houseId` ASC),
  CONSTRAINT `fk_roomschedule_room`
    FOREIGN KEY (`roomId`)
    REFERENCES `Room` (`roomId`),
  CONSTRAINT `fk_roomschedule_house`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`));

-- -----------------------------------------------------
-- Table `TaskSchedule`
-- -----------------------------------------------------
CREATE TABLE `TaskSchedule` (
  `scheduleId` INT NOT NULL AUTO_INCREMENT,
  -- begin to end range is inclusive
  `beginTimeslot` INT NOT NULL,
  `endTimeslot` INT NOT NULL,
  `day` ENUM('Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `taskId` INT NOT NULL,
  `houseId` INT NOT NULL,
  PRIMARY KEY (`scheduleId`),
  INDEX `fk_taskschedule_task_idx` (`taskId` ASC),
  INDEX `fk_taskschedule_house_idx` (`houseId` ASC),
  CONSTRAINT `fk_taskschedule_task`
    FOREIGN KEY (`taskId`)
    REFERENCES `Task` (`taskId`),
  CONSTRAINT `fk_taskschedule_house`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`));

-- -----------------------------------------------------
-- Table `UserSchedule`
-- -----------------------------------------------------
CREATE TABLE `UserSchedule` (
  `scheduleId` INT NOT NULL AUTO_INCREMENT,
  -- begin to end range is inclusive
  `beginTimeslot` INT NOT NULL,
  `endTimeslot` INT NOT NULL,
  `day` ENUM('Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `userId` INT NOT NULL,
  PRIMARY KEY (`scheduleId`),
  INDEX `fk_userschedule_user_idx` (`userId` ASC),
  CONSTRAINT `fk_userschedule_user`
    FOREIGN KEY (`userId`)
    REFERENCES `User` (`userId`));


-- -----------------------------------------------------
-- Table `taskPoints`
-- -----------------------------------------------------
CREATE TABLE `TaskPoints` (
  `pointId` INT NOT NULL AUTO_INCREMENT,
  `quantity` INT NOT NULL,
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  PRIMARY KEY (`pointId`),
  INDEX `fk_Rule_task2_idx` (`taskId` ASC),
  INDEX `fk_Rule_room2_idx` (`roomId` ASC),
  CONSTRAINT `fk_Rule_task2`
    FOREIGN KEY (`taskId`)
    REFERENCES `Task` (`taskId`),
  CONSTRAINT `fk_Rule_room2`
    FOREIGN KEY (`roomId`)
    REFERENCES `Room` (`roomId`));


-- -----------------------------------------------------
-- Table `Rota`
-- -----------------------------------------------------
CREATE TABLE `Rota` (
  `rotaId` INT NOT NULL AUTO_INCREMENT,
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  `userId` INT NOT NULL,
  `houseId` INT NOT NULL,
  `status` enum('Todo','Ongoing','Complete') NOT NULL,
  `day` ENUM('Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `beginTimeslot` INT NOT NULL,
  `endTimeslot` INT NOT NULL,
  PRIMARY KEY (`rotaId`),
  INDEX `fk_Task_has_user_user1_idx` (`userId` ASC),
  INDEX `fk_Task_has_user_Task1_idx` (`taskId` ASC),
  INDEX `fk_Task_has_user_Room1_idx` (`roomId` ASC),
  INDEX `fk_Task_has_user_Rota1_idx` (`houseId` ASC),
  CONSTRAINT `fk_Task_has_user_Task1`
    FOREIGN KEY (`taskId`)
    REFERENCES `Task` (`taskId`),
  CONSTRAINT `fk_Task_has_user_Room1`
    FOREIGN KEY (`roomId`)
    REFERENCES `Room` (`roomId`),
  CONSTRAINT `fk_Task_has_user_user1`
    FOREIGN KEY (`userId`)
    REFERENCES `User` (`userId`),
  CONSTRAINT `fk_Task_has_user_Rota1`
    FOREIGN KEY (`houseId`)
    REFERENCES `House` (`houseId`));