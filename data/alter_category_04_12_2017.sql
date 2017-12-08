ALTER TABLE `category`
	ADD COLUMN `collapsed` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Свернута при показе' AFTER `for_reg`;
