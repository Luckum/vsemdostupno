ALTER TABLE `product_new_price`
	ADD COLUMN `product_feature_id` INT NOT NULL AFTER `date`;
ALTER TABLE `product_new_price`
	ADD CONSTRAINT `product_new_price_ibfk_2` FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
