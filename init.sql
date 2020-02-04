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
 `report` INT NOT NULL,
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
  ("Potters Heron Hotel - Hook Road / B3090 south of Hursley"),
  ("Badger farm / Romsey Road roundabout"),
  ("Winnall Roundabout"),
  ("A303 services 1 mile westbound from A34"),
  ("A343 Junction A303 towards Middle Wallop"),
  ("A345 at High Post Hotel"),
  ("A36 (Salisbury) Southbound"),
  ("Romsey West Roundabout"),
  ("North Baddesley Junction of Nutburn, Botley and Rownhams Road"),
  ("A3057 North from M271 & A27 East");

INSERT INTO
  `times` (`time`)
VALUES
  ('2020-02-01 08:00:00'),
  ('2020-02-01 08:30:00'),
  ('2020-02-01 09:00:00'),
  ('2020-02-01 09:30:00'),
  ('2020-02-01 10:00:00'),
  ('2020-02-01 10:30:00'),
  ('2020-02-01 11:00:00'),
  ('2020-02-01 11:30:00'),
  ('2020-02-01 12:00:00'),
  ('2020-02-01 12:30:00'),
  ('2020-02-01 13:00:00'),
  ('2020-02-01 13:30:00'),
  ('2020-02-01 14:00:00'),
  ('2020-02-01 14:30:00'),
  ('2020-02-01 15:00:00'),
  ('2020-02-01 15:30:00'),
  ('2020-02-01 16:00:00'),
  ('2020-02-01 16:30:00'),
  ('2020-02-01 17:00:00'),
  ('2020-02-01 17:30:00'),
  ('2020-02-01 18:00:00'),
  ('2020-02-01 18:30:00'),
  ('2020-02-01 19:00:00'),
  ('2020-02-02 08:00:00'),
  ('2020-02-02 08:30:00'),
  ('2020-02-02 09:00:00'),
  ('2020-02-02 09:30:00'),
  ('2020-02-02 10:00:00'),
  ('2020-02-02 10:30:00'),
  ('2020-02-02 11:00:00'),
  ('2020-02-02 11:30:00'),
  ('2020-02-02 12:00:00'),
  ('2020-02-02 12:30:00'),
  ('2020-02-02 13:00:00'),
  ('2020-02-02 13:30:00'),
  ('2020-02-02 14:00:00'),
  ('2020-02-02 14:30:00'),
  ('2020-02-02 15:00:00'),
  ('2020-02-02 15:30:00'),
  ('2020-02-02 16:00:00'),
  ('2020-02-02 16:30:00'),
  ('2020-02-02 17:00:00'),
  ('2020-02-02 17:30:00'),
  ('2020-02-02 18:00:00'),
  ('2020-02-02 18:30:00'),
  ('2020-02-02 19:00:00');

