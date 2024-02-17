-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`House`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`House` (
  `houseId` INT NOT NULL,
  `roomCounter` INT NULL,
  PRIMARY KEY (`houseId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`user` (
  `forename` VARCHAR(45) NOT NULL,
  `surname` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` TINYINT NOT NULL DEFAULT 0,
  `House_houseId` INT NOT NULL,
  `personalPoints` INT NULL,
  PRIMARY KEY (`email`, `House_houseId`),
  INDEX `fk_user_House_idx` (`House_houseId` ASC) VISIBLE,
  CONSTRAINT `fk_user_House`
    FOREIGN KEY (`House_houseId`)
    REFERENCES `mydb`.`House` (`houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Room`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Room` (
  `roomId` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `houseId` INT NOT NULL,
  PRIMARY KEY (`roomId`, `houseId`),
  INDEX `fk_Room_House1_idx` (`houseId` ASC) VISIBLE,
  CONSTRAINT `fk_Room_House1`
    FOREIGN KEY (`houseId`)
    REFERENCES `mydb`.`House` (`houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Task`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Task` (
  `taskId` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `description` VARCHAR(1000) NULL,
  `Room_roomId` INT NOT NULL,
  `Room_houseId` INT NOT NULL,
  PRIMARY KEY (`taskId`, `Room_roomId`, `Room_houseId`),
  INDEX `fk_Task_Room1_idx` (`Room_roomId` ASC, `Room_houseId` ASC) VISIBLE,
  CONSTRAINT `fk_Task_Room1`
    FOREIGN KEY (`Room_roomId` , `Room_houseId`)
    REFERENCES `mydb`.`Room` (`roomId` , `houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Rule`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Rule` (
  `ruleId` INT NOT NULL,
  `startTime` DATETIME NOT NULL,
  `endTime` DATETIME NOT NULL,
  `user_email` VARCHAR(45) NOT NULL,
  `houseId` INT NOT NULL,
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  PRIMARY KEY (`ruleId`, `user_email`, `houseId`, `taskId`, `roomId`),
  INDEX `fk_Rule_user1_idx` (`user_email` ASC, `houseId` ASC) VISIBLE,
  INDEX `fk_Rule_Task1_idx` (`taskId` ASC, `roomId` ASC) VISIBLE,
  CONSTRAINT `fk_Rule_user1`
    FOREIGN KEY (`user_email` , `houseId`)
    REFERENCES `mydb`.`user` (`email` , `House_houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Rule_Task1`
    FOREIGN KEY (`taskId` , `roomId`)
    REFERENCES `mydb`.`Task` (`taskId` , `Room_roomId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Schedule`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Schedule` (
  `scheduleId` INT NOT NULL,
  `startTime` VARCHAR(45) NULL,
  `endTime` VARCHAR(45) NULL,
  `days` ENUM('Monday', 'Tuesday') NULL,
  `Schedulecol` VARCHAR(45) NULL,
  `user_email` VARCHAR(45) NOT NULL,
  `user_House_houseId` INT NOT NULL,
  PRIMARY KEY (`scheduleId`, `user_email`, `user_House_houseId`),
  INDEX `fk_Schedule_user1_idx` (`user_email` ASC, `user_House_houseId` ASC) VISIBLE,
  CONSTRAINT `fk_Schedule_user1`
    FOREIGN KEY (`user_email` , `user_House_houseId`)
    REFERENCES `mydb`.`user` (`email` , `House_houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`taskPoints`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`taskPoints` (
  `pointId` INT NOT NULL,
  `quantity` INT NOT NULL,
  `Task_taskId` INT NOT NULL,
  `Task_Room_roomId` INT NOT NULL,
  `Task_Room_houseId` INT NOT NULL,
  PRIMARY KEY (`pointId`, `Task_taskId`, `Task_Room_roomId`, `Task_Room_houseId`),
  INDEX `fk_taskPoints_Task1_idx` (`Task_taskId` ASC, `Task_Room_roomId` ASC, `Task_Room_houseId` ASC) VISIBLE,
  CONSTRAINT `fk_taskPoints_Task1`
    FOREIGN KEY (`Task_taskId` , `Task_Room_roomId` , `Task_Room_houseId`)
    REFERENCES `mydb`.`Task` (`taskId` , `Room_roomId` , `Room_houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Rota`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Rota` (
  `rotaId` INT NOT NULL,
  PRIMARY KEY (`rotaId`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Task_has_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Task_has_user` (
  `taskId` INT NOT NULL,
  `roomId` INT NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `houseId` INT NOT NULL,
  `Rota_rotaId` INT NOT NULL,
  PRIMARY KEY (`taskId`, `roomId`, `email`, `houseId`, `Rota_rotaId`),
  INDEX `fk_Task_has_user_user1_idx` (`email` ASC, `houseId` ASC) VISIBLE,
  INDEX `fk_Task_has_user_Task1_idx` (`taskId` ASC, `roomId` ASC) VISIBLE,
  INDEX `fk_Task_has_user_Rota1_idx` (`Rota_rotaId` ASC) VISIBLE,
  CONSTRAINT `fk_Task_has_user_Task1`
    FOREIGN KEY (`taskId` , `roomId`)
    REFERENCES `mydb`.`Task` (`taskId` , `Room_roomId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Task_has_user_user1`
    FOREIGN KEY (`email` , `houseId`)
    REFERENCES `mydb`.`user` (`email` , `House_houseId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Task_has_user_Rota1`
    FOREIGN KEY (`Rota_rotaId`)
    REFERENCES `mydb`.`Rota` (`rotaId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
