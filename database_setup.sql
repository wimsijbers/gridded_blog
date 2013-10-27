CREATE DATABASE `mysqldb` ;
CREATE USER 'dbuser'@'localhost' IDENTIFIED BY 'password';
GRANT SELECT, INSERT, UPDATE, DROP ON `mysqldb`.* TO 'dbuser'@'localhost';

CREATE TABLE `mysqldb`.`ws_members` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
  `username` VARCHAR(30) NOT NULL, 
  `password` CHAR(128) NOT NULL, 
  `salt` CHAR(128) NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `mysqldb`.`ws_login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` VARCHAR(30) NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mysqldb`.`ws_posts` (
  `post_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
  `post_author` bigint(20) unsigned NOT NULL DEFAULT 0, 
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_rows` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `post_columns` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `post_contentType` varchar(20) NOT NULL DEFAULT 'general',
  `post_format` varchar(20) NOT NULL DEFAULT 'text',
  `post_title` text NOT NULL,
  `post_content` longtext NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'show',
  `post_islink` BOOL DEFAULT FALSE,
	PRIMARY KEY (`post_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mysqldb`.`ws_rest` (
  `rest_name` varchar(30) NOT NULL, 
  `rest_data` text NOT NULL,
	PRIMARY KEY (`rest_name`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `mysqldb`.`ws_rest` 	(`rest_name`, `rest_data`) VALUES ("last_tweet_update", NOW());
INSERT INTO `mysqldb`.`ws_rest` 	(`rest_name`, `rest_data`) VALUES ("tw_consumer_key", "keyhere");
INSERT INTO `mysqldb`.`ws_rest` 	(`rest_name`, `rest_data`) VALUES ("tw_consumer_secret", "secrethere");
INSERT INTO `mysqldb`.`ws_rest` 	(`rest_name`, `rest_data`) VALUES ("tw_user_token", "tokenhere");
INSERT INTO `mysqldb`.`ws_rest` 	(`rest_name`, `rest_data`) VALUES ("tw_user_secret", "usersecrethere");

CREATE TABLE `mysqldb`.`ws_tweets` (
  `tweet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
  `tweet_author` text NOT NULL, 
  `tweet_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tweet_content` longtext NOT NULL,
	PRIMARY KEY (`tweet_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;