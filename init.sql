DROP DATABASE IF EXISTS `swhr-obs-m27`;
CREATE DATABASE IF NOT EXISTS `swhr-obs-m27` DEFAULT CHARACTER SET utf8;
USE `swhr-obs-m27`;

CREATE TABLE `locations` (
 `id` INT NOT NULL AUTO_INCREMENT,
 `name` VARCHAR(255) NOT NULL,
 PRIMARY KEY (id)
);

CREATE TABLE `times` (
 `id` INT NOT NULL AUTO_INCREMENT,
 `time` TIMESTAMP NOT NULL,
 PRIMARY KEY (id)
);

CREATE TABLE `observations` (
 `location_id` INT NOT NULL,
 `time_id` INT NOT NULL,
 `report_ne` INT NOT NULL,
 `report_sw` INT NOT NULL,
 `count_ne` INT NOT NULL,
 `count_sw` INT NOT NULL,
 CONSTRAINT `location_id_key` FOREIGN KEY (`location_id`) REFERENCES `swhr-obs-m27`.`locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
 CONSTRAINT `time_id_key` FOREIGN KEY (`time_id`) REFERENCES `swhr-obs-m27`.`times` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
 UNIQUE KEY `location_time` (`location_id`,`time_id`)
);

GRANT ALL PRIVILEGES ON `swhr-obs-m27`.* TO 'swhr-obs-m27'@'10.0.4.%';

INSERT INTO
  `locations` (`name`)
  VALUES
  ("Pitt Roundabout { Winchester }"),
  ("Winnal Roundabout { M3 /A34 }"),
  ("Ashfield Roundabout { N of M271 }"),
  ("Jct A3090 & A27 {Romsey  }"),
  ("North Baddesley Cross roads"),
  ("M27 Thomas Lewis Way");

INSERT INTO
  `times` (`time`)
  VALUES
  ('2020-01-11 09:00:00'),
  ('2020-01-11 09:30:00'),
  ('2020-01-11 10:00:00'),
  ('2020-01-11 10:30:00'),
  ('2020-01-11 11:00:00'),
  ('2020-01-11 11:30:00'),
  ('2020-01-11 12:00:00'),
  ('2020-01-11 12:30:00'),
  ('2020-01-11 13:00:00'),
  ('2020-01-11 13:30:00'),
  ('2020-01-11 14:00:00'),
  ('2020-01-11 14:30:00'),
  ('2020-01-11 15:00:00'),
  ('2020-01-11 15:30:00'),
  ('2020-01-11 16:00:00'),
  ('2020-01-11 16:30:00'),
  ('2020-01-11 17:00:00'),
  ('2020-01-11 17:30:00'),
  ('2020-01-11 18:00:00'),
  ('2020-01-11 18:30:00'),
  ('2020-01-11 19:00:00'),
  ('2020-01-11 19:30:00');