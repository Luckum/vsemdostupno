ALTER TABLE `provider`
	ADD COLUMN `field_of_activity` TEXT NOT NULL COMMENT 'Сфера деятельности' AFTER `name`,
	ADD COLUMN `offered_goods` TEXT NOT NULL COMMENT 'Предлагаемые товары' AFTER `field_of_activity`,
	ADD COLUMN `legal_address` VARCHAR(255) NOT NULL COMMENT 'Юридический адрес' AFTER `offered_goods`,
	ADD COLUMN `snils` CHAR(11) NOT NULL COMMENT 'СНИЛС' AFTER `legal_address`,
	ADD COLUMN `ogrn` CHAR(13) NOT NULL COMMENT 'ОГРН' AFTER `snils`,
	ADD COLUMN `site` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Сайт компании' AFTER `ogrn`,
	ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT 'Описание предложений' AFTER `site`;