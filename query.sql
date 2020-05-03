DROP DATABASE IF EXISTS `lotto`;
CREATE DATABASE `lotto`;
USE `lotto`;

DROP TABLE IF EXISTS `lotto`.`tickets`;
CREATE TABLE `lotto`.`tickets` (
  `id` SERIAL PRIMARY KEY,
  `ticket` BIGINT UNSIGNED NULL,
  `combination` VARCHAR(50),
  `count_guessed` INT(2) DEFAULT 0,
  `win_summ` BIGINT(10) NULL DEFAULT NULL,
  INDEX (`combination`)
  );

LOAD DATA  INFILE '/file.txt'
INTO TABLE `lotto`.`tickets` 
FIELDS TERMINATED BY ','
ENCLOSED BY '"'
(`ticket`, `combination`);

SELECT count(*) FROM `lotto`.`tickets`;
SELECT * FROM `lotto`.`tickets`;

SELECT 
	COUNT(*) - COUNT(DISTINCT `ticket`) AS 'distinct names' 
FROM 
	`lotto`.`tickets`; -- количество разных 


ALTER TABLE `lotto`.`tickets` ADD INDEX (`combination`, `count_guessed`, `win_summ`);

