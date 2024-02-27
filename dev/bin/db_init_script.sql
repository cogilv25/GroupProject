-- Schema cleansync
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `cleansync`;
CREATE SCHEMA `cleansync` DEFAULT CHARACTER SET utf8;
USE `cleansync`;

-- -----------------------------------------------------
-- Table `cleansync`.`House`
-- -----------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
CREATE TABLE `cleansync`.`House` (
  `houseId` INT NOT NULL AUTO_INCREMENT,
  `adminId` INT NOT NULL,
  `roomCounter` INT,
  PRIMARY KEY (`houseId`),
  INDEX `fk_House_user_idx` (`adminId` ASC),
  CONSTRAINT `fk_house_user`
    FOREIGN KEY (`adminId`)
    REFERENCES `cleansync`.`user` (`userId`));

SET FOREIGN_KEY_CHECKS = 1;
-- -----------------------------------------------------
-- Table `cleansync`.`user`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`user` (
  `userId` INT NOT NULL AUTO_INCREMENT,
  `forename` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `House_houseId` INT,
  `personalPoints` INT,
  PRIMARY KEY (`userId`),
  INDEX `fk_user_House_idx` (`House_houseId` ASC),
  CONSTRAINT `fk_user_house`
    FOREIGN KEY (`House_houseId`)
    REFERENCES `cleansync`.`House` (`houseId`));


-- -----------------------------------------------------
-- Table `cleansync`.`Room`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`Room` (
  `roomId` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45),
  `houseId` INT NOT NULL,
  PRIMARY KEY (`roomId`),
  INDEX `fk_Room_House1_idx` (`houseId` ASC),
  CONSTRAINT `fk_Room_House1`
    FOREIGN KEY (`houseId`)
    REFERENCES `cleansync`.`House` (`houseId`));


-- -----------------------------------------------------
-- Table `cleansync`.`Task`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`Task` (
  `taskId` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(1000) NOT NULL,
  `houseId` INT NOT NULL,
  PRIMARY KEY (`taskId`),
  INDEX `fk_Task_Room1_idx` (`houseId` ASC),
  CONSTRAINT `fk_Task_House`
    FOREIGN KEY (`houseId`)
    REFERENCES `cleansync`.`House` (`houseId`));


-- -----------------------------------------------------
-- Table `cleansync`.`Rule`
-- -----------------------------------------------------
-- Four types:
-- A user does not perform a certain task
-- A task is restricted from certain timeslots
-- A room is inaccessible during certain timeslots
-- A user does not clean in a certain room
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`Rule` (
  `ruleId` INT NOT NULL AUTO_INCREMENT,
  `houseId` INT NOT NULL,
  `beginTimeslot` INT,
  `endTimeslot` INT,
  `userId` INT,
  `taskId` INT,
  `roomId` INT,
  PRIMARY KEY (`ruleId`),
  INDEX `fk_Rule_user1_idx` (`userId` ASC),
  INDEX `fk_Rule_Task1_idx` (`taskId` ASC),
  CONSTRAINT `fk_Rule_user1`
    FOREIGN KEY (`userId`)
    REFERENCES `cleansync`.`user` (`userId`),
  CONSTRAINT `fk_Rule_house1`
    FOREIGN KEY (`houseId`)
    REFERENCES `cleansync`.`House` (`houseId`),
  CONSTRAINT `fk_Rule_room1`
    FOREIGN KEY (`roomId`)
    REFERENCES `cleansync`.`Room` (`roomId`),
  CONSTRAINT `fk_Rule_Task1`
    FOREIGN KEY (`taskId`)
    REFERENCES `cleansync`.`Task` (`taskId`));


-- -----------------------------------------------------
-- Table `cleansync`.`Schedule`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`Schedule` (
  `scheduleId` INT NOT NULL AUTO_INCREMENT,
  -- begin to end range is inclusive
  `beginTimeslot` INT NOT NULL,
  `endTimeslot` INT NOT NULL,
  `day` ENUM('Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `userId` INT NOT NULL,
  PRIMARY KEY (`scheduleId`),
  INDEX `fk_Schedule_user1_idx` (`userId` ASC),
  CONSTRAINT `fk_Schedule_user1`
    FOREIGN KEY (`userId`)
    REFERENCES `cleansync`.`user` (`userId`));


-- -----------------------------------------------------
-- Table `cleansync`.`taskPoints`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`taskPoints` (
  `pointId` INT NOT NULL,
  `quantity` INT NOT NULL,
  `Task_taskId` INT NOT NULL,
  `Room_roomId` INT NOT NULL,
  PRIMARY KEY (`pointId`),
  INDEX `fk_Rule_task2_idx` (`Task_taskId` ASC),
  INDEX `fk_Rule_room2_idx` (`Room_roomId` ASC),
  CONSTRAINT `fk_Rule_task2`
    FOREIGN KEY (`Task_taskId`)
    REFERENCES `cleansync`.`Task` (`taskId`),
  CONSTRAINT `fk_Rule_room2`
    FOREIGN KEY (`Room_roomId`)
    REFERENCES `cleansync`.`Room` (`roomId`));


-- -----------------------------------------------------
-- Table `cleansync`.`Rota`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`Rota` (
  `rotaId` INT NOT NULL,
  PRIMARY KEY (`rotaId`));


-- -----------------------------------------------------
-- Table `cleansync`.`Task_has_user`
-- -----------------------------------------------------
CREATE TABLE `cleansync`.`Task_has_user` (
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  `userId` INT NOT NULL,
  `rotaId` INT NOT NULL,
  `status` enum('Todo','Ongoing','Complete'),
  `startTime` VARCHAR(45) NOT NULL,
  `endTime` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`taskId`, `roomId`, `userId`, `rotaId`),
  INDEX `fk_Task_has_user_user1_idx` (`userId` ASC),
  INDEX `fk_Task_has_user_Task1_idx` (`taskId` ASC),
  INDEX `fk_Task_has_user_Room1_idx` (`roomId` ASC),
  INDEX `fk_Task_has_user_Rota1_idx` (`rotaId` ASC),
  CONSTRAINT `fk_Task_has_user_Task1`
    FOREIGN KEY (`taskId`)
    REFERENCES `cleansync`.`Task` (`taskId`),
  CONSTRAINT `fk_Task_has_user_Room1`
    FOREIGN KEY (`roomId`)
    REFERENCES `cleansync`.`Room` (`roomId`),
  CONSTRAINT `fk_Task_has_user_user1`
    FOREIGN KEY (`userId`)
    REFERENCES `cleansync`.`user` (`userId`),
  CONSTRAINT `fk_Task_has_user_Rota1`
    FOREIGN KEY (`rotaId`)
    REFERENCES `cleansync`.`Rota` (`rotaId`));
    
