ALTER TABLE `order`
	ADD COLUMN `hide` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Скрыть из истории' AFTER `order_status_id`;
