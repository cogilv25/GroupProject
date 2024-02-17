-- Schema mydb
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mydb`;
CREATE SCHEMA `mydb` DEFAULT CHARACTER SET utf8;
USE `mydb`;

-- -----------------------------------------------------
-- Table `mydb`.`House`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`House` (
  `houseId` INT NOT NULL,
  `roomCounter` INT,
  PRIMARY KEY (`houseId`));


-- -----------------------------------------------------
-- Table `mydb`.`user`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`user` (
  `forename` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` TINYINT NOT NULL DEFAULT 0,
  `House_houseId` INT,
  `personalPoints` INT,
  PRIMARY KEY (`email`),
  INDEX `fk_user_House_idx` (`House_houseId` ASC),
  CONSTRAINT `fk_user_House`
    FOREIGN KEY (`House_houseId`)
    REFERENCES `mydb`.`House` (`houseId`));


-- -----------------------------------------------------
-- Table `mydb`.`Room`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`Room` (
  `roomId` INT NOT NULL,
  `name` VARCHAR(45),
  `houseId` INT NOT NULL,
  PRIMARY KEY (`roomId`),
  INDEX `fk_Room_House1_idx` (`houseId` ASC),
  CONSTRAINT `fk_Room_House1`
    FOREIGN KEY (`houseId`)
    REFERENCES `mydb`.`House` (`houseId`));


-- -----------------------------------------------------
-- Table `mydb`.`Task`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`Task` (
  `taskId` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(1000),
  `Room_roomId` INT NOT NULL,
  `Room_houseId` INT NOT NULL,
  PRIMARY KEY (`taskId`),
  INDEX `fk_Task_Room1_idx` (`Room_roomId` ASC),
  CONSTRAINT `fk_Task_Room1`
    FOREIGN KEY (`Room_roomId`)
    REFERENCES `mydb`.`Room` (`roomId`));


-- -----------------------------------------------------
-- Table `mydb`.`Rule`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`Rule` (
  `ruleId` INT NOT NULL,
  `startTime` DATETIME NOT NULL,
  `endTime` DATETIME NOT NULL,
  `user_email` VARCHAR(45) NOT NULL,
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  PRIMARY KEY (`ruleId`),
  INDEX `fk_Rule_user1_idx` (`user_email` ASC),
  INDEX `fk_Rule_Task1_idx` (`taskId` ASC),
  CONSTRAINT `fk_Rule_user1`
    FOREIGN KEY (`user_email`)
    REFERENCES `mydb`.`user` (`email`),
  CONSTRAINT `fk_Rule_Task1`
    FOREIGN KEY (`taskId`)
    REFERENCES `mydb`.`Task` (`taskId`));


-- -----------------------------------------------------
-- Table `mydb`.`Schedule`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`Schedule` (
  `scheduleId` INT NOT NULL,
  `startTime` VARCHAR(45),
  `endTime` VARCHAR(45),
  `days` ENUM('Monday', 'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NULL,
  `Schedulecol` VARCHAR(45),
  `user_email` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`scheduleId`),
  INDEX `fk_Schedule_user1_idx` (`user_email` ASC),
  CONSTRAINT `fk_Schedule_user1`
    FOREIGN KEY (`user_email`)
    REFERENCES `mydb`.`user` (`email`));


-- -----------------------------------------------------
-- Table `mydb`.`taskPoints`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`taskPoints` (
  `pointId` INT NOT NULL,
  `quantity` INT NOT NULL,
  `Task_taskId` INT NOT NULL,
  `Room_roomId` INT NOT NULL,
  PRIMARY KEY (`pointId`),
  INDEX `fk_Rule_task2_idx` (`Task_taskId` ASC),
  INDEX `fk_Rule_room2_idx` (`Room_roomId` ASC),
  CONSTRAINT `fk_Rule_task2`
    FOREIGN KEY (`Task_taskId`)
    REFERENCES `mydb`.`Task` (`taskId`),
  CONSTRAINT `fk_Rule_room2`
    FOREIGN KEY (`Room_roomId`)
    REFERENCES `mydb`.`Room` (`roomId`));


-- -----------------------------------------------------
-- Table `mydb`.`Rota`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`Rota` (
  `rotaId` INT NOT NULL,
  PRIMARY KEY (`rotaId`));


-- -----------------------------------------------------
-- Table `mydb`.`Task_has_user`
-- -----------------------------------------------------
CREATE TABLE `mydb`.`Task_has_user` (
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `rotaId` INT NOT NULL,
  `startTime` VARCHAR(45) NOT NULL,
  `endTime` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`taskId`, `roomId`, `email`, `rotaId`),
  INDEX `fk_Task_has_user_user1_idx` (`email` ASC),
  INDEX `fk_Task_has_user_Task1_idx` (`taskId` ASC),
  INDEX `fk_Task_has_user_Room1_idx` (`roomId` ASC),
  INDEX `fk_Task_has_user_Rota1_idx` (`rotaId` ASC),
  CONSTRAINT `fk_Task_has_user_Task1`
    FOREIGN KEY (`taskId`)
    REFERENCES `mydb`.`Task` (`taskId`),
  CONSTRAINT `fk_Task_has_user_Room1`
    FOREIGN KEY (`roomId`)
    REFERENCES `mydb`.`Room` (`roomId`),
  CONSTRAINT `fk_Task_has_user_user1`
    FOREIGN KEY (`email`)
    REFERENCES `mydb`.`user` (`email`),
  CONSTRAINT `fk_Task_has_user_Rota1`
    FOREIGN KEY (`rotaId`)
    REFERENCES `mydb`.`Rota` (`rotaId`));
    
