/* step1 */
ALTER TABLE `order_has_product`
    ADD COLUMN `deleted` TINYINT(1) NOT NULL DEFAULT '0' AFTER `product_feature_id`;

CREATE TABLE IF NOT EXISTS `o_view` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `section` ENUM('po', 'co') NOT NULL,
    `dts` DATE NOT NULL,
    `dte` DATE DEFAULT NULL,
    `detail` ENUM('closed', 'opened') NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* step2 */
ALTER TABLE `order`
    ADD COLUMN `order_id` INT(11) NOT NULL DEFAULT '0' AFTER `hide`;
ALTER TABLE `order`
    ADD COLUMN `purchase_order_id` INT(11) NOT NULL DEFAULT '0' AFTER `order_id`;
ALTER TABLE `order`
    CHANGE COLUMN `order_id` `order_id` INT(11) NULL DEFAULT NULL AFTER `hide`,
    CHANGE COLUMN `purchase_order_id` `purchase_order_id` INT(11) NULL DEFAULT NULL AFTER `order_id`;

/* extra 2 */
ALTER TABLE `user`
    ADD COLUMN `request` TINYINT(1) NOT NULL DEFAULT '1' AFTER `ext_phones`;

ALTER TABLE `product`
    ADD COLUMN `manufacturer_photo_id` INT(11) NULL DEFAULT NULL AFTER `auto_send`;

ALTER TABLE `product`
    ADD CONSTRAINT `FK_product_photo` FOREIGN KEY (`manufacturer_photo_id`) REFERENCES `photo` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `notice_email` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `product_feature`
    ADD COLUMN `is_weights` TINYINT(1) NOT NULL DEFAULT '0' AFTER `quantity`;

ALTER TABLE `stock_body`
    ADD COLUMN `is_weights` TINYINT(1) NOT NULL DEFAULT '0' AFTER `product_feature_id`;

ALTER TABLE `product_feature`
    CHANGE COLUMN `quantity` `quantity` DECIMAL(15,3) UNSIGNED NOT NULL COMMENT 'Количество' AFTER `tare`;

ALTER TABLE `provider_stock`
    CHANGE COLUMN `reaminder_rent` `reaminder_rent` DECIMAL(19,3) NOT NULL AFTER `total_sum`;

ALTER TABLE `stock_body`
    CHANGE COLUMN `count` `count` DECIMAL(19,3) NOT NULL AFTER `measurement`;

ALTER TABLE `provider_stock`
    CHANGE COLUMN `total_rent` `total_rent` DECIMAL(19,3) NOT NULL AFTER `stock_body_id`;
