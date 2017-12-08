ALTER TABLE `category`
	ADD COLUMN `for_reg` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Доступна для регистрации' AFTER `order`;
