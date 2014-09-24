CREATE TABLE IF NOT EXISTS `familynet`.`system_contents` (
  `id` CHAR(36) NOT NULL,
  `identifier` VARCHAR(255) NOT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `identifier_UNIQUE` (`identifier` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;