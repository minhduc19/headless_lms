CREATE SCHEMA IF NOT EXISTS `necampus` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `necampus` ;

CREATE USER 'necampus'@'localhost';
SET PASSWORD FOR 'necampus'@'localhost' = PASSWORD('!@#QAZ!@abc123');
GRANT ALL ON `necampus`.* to 'necampus'@'localhost';

CREATE USER 'necampus'@'%';
SET PASSWORD FOR 'necampus'@'%' = PASSWORD('!@#QAZ!@abc123');
GRANT ALL ON `necampus`.* to 'necampus'@'%';
use necampus;
