ALTER TABLE `stock_body`
	ADD COLUMN `product_feature_id` INT NOT NULL AFTER `comment`;
ALTER TABLE `stock_body`
	ADD CONSTRAINT `stock_body_ibfk_3` FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
