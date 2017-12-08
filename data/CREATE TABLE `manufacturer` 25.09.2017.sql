CREATE TABLE `manufacturer` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `field_of_activity` TEXT NOT NULL ,
    `offered_goods` TEXT NOT NULL ,
    `company_name` TEXT NOT NULL ,
    `fio` TEXT NOT NULL ,
    `itn` INT NOT NULL ,
    `insurance_certificate` TEXT NOT NULL ,
    `ogrn` TEXT NOT NULL ,
    `email` TEXT NOT NULL ,
    `company_site` TEXT NOT NULL ,
    `phone` TEXT NOT NULL ,
    `additional_phone` TEXT NOT NULL ,
    `company_description` TEXT NOT NULL ,
    `adress` TEXT NOT NULL ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;