CREATE  TABLE IF NOT EXISTS `payments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `control` VARCHAR(32) NOT NULL ,
  `amount` FLOAT(6,2) UNSIGNED NOT NULL ,
  `description` VARCHAR(255) NOT NULL ,
  `name` VARCHAR(120) NULL ,
  `service` VARCHAR(50) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `control_UNIQUE` (`control` ASC) )
ENGINE = InnoDB;

CREATE  TABLE IF NOT EXISTS `payment_incomings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `payment_id` INT(11) UNSIGNED NOT NULL ,
  `t_id` VARCHAR(60) NOT NULL ,
  `t_status` TINYINT(1) UNSIGNED NOT NULL ,
  `amount` FLOAT(6,2) NOT NULL ,
  `email` VARCHAR(160) NOT NULL ,
  `md5` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(255) NULL ,
  `service` VARCHAR(50) NULL ,
  `code` VARCHAR(32) NULL ,
  `username` VARCHAR(6) NULL ,
  `password` VARCHAR(32) NULL ,
  `created` INT(10) UNSIGNED NOT NULL ,
  `updated` INT(10) UNSIGNED NOT NULL ,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 ,
  UNIQUE INDEX `t_id_UNIQUE` (`t_id` ASC) ,
  UNIQUE INDEX `md5_UNIQUE` (`md5` ASC) ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_payment_incomings_payments1` (`payment_id` ASC) ,
  CONSTRAINT `fk_payment_incomings_payments1`
    FOREIGN KEY (`payment_id` )
    REFERENCES `payments` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;