ALTER TABLE `order_has_product`
    ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT '0' AFTER `product_feature_id`;

CREATE TABLE IF NOT EXISTS `o_view` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `section` ENUM('po', 'co') NOT NULL,
    `dts` DATE NOT NULL,
    `dte` DATE NOT NULL,
    `detail` ENUM('closed', 'opened') NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;