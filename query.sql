DROP DATABASE IF EXISTS `loto`;
CREATE DATABASE `loto`;
USE `loto`;


CREATE TABLE `loto`.`tickets` (
  `id` SERIAL PRIMARY KEY,
  `ticket` BIGINT UNSIGNED NULL,
  `combination` VARCHAR(50),  
  INDEX (`combination`)
  );
DROP TABLE IF EXISTS `loto`.`tickets`;