CREATE DATABASE `doxentral`;

USE `doxentral`;

CREATE TABLE `doxentral_users` (
  `user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(64) NOT NULL,
  `user_password` VARCHAR(16) DEFAULT NULL,
  `user_email` VARCHAR(64) DEFAULT NULL,
  `user_firstname` VARCHAR(32) DEFAULT NULL,
  `user_lastname` VARCHAR(32) DEFAULT NULL,
  `user_type` INT(10) UNSIGNED NOT NULL DEFAULT '2',
  `user_status` TINYINT(4) NOT NULL DEFAULT '0',
  `user_created` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MYISAM;

CREATE TABLE `doxentral_files` (
  `file_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_on_disk` VARCHAR(128) NOT NULL,
  `file_title` VARCHAR(128) DEFAULT NULL,
  `file_desc` TEXT,
  `file_status` TINYINT(4) NOT NULL DEFAULT '0',
  `file_created` DATETIME NOT NULL,
  `file_owner` INT(11) NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `file_owner` (`file_owner`)
) ENGINE=MYISAM;

CREATE TABLE `doxentral_file_acl` (
  `acl_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` INT(10) UNSIGNED NOT NULL,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `acl_created` DATETIME NOT NULL,
  PRIMARY KEY (`acl_id`),
  UNIQUE KEY `file_id-user_id` (`file_id`,`user_id`)
) ENGINE=MYISAM;