ALTER TABLE `stock_head` ADD `deleted_by_admin` BOOLEAN NOT NULL  DEFAULT '0' AFTER `provider_id`;
ALTER TABLE `stock_head` ADD `deleted_by_provider` BOOLEAN NOT NULL DEFAULT '0' AFTER `deleted_by_admin`;
