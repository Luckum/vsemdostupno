CREATE TABLE IF NOT EXISTS `product_feature` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_id` int(11) NOT NULL COMMENT 'Товар',
    `volume` DECIMAL(19,2) NOT NULL COMMENT 'Масса/Объем',
    `measurement` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Ед. измерения',
    `tare` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Тара',
    `quantity` int(11) NOT NULL COMMENT 'Количество',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Характеристики товара';

CREATE TABLE IF NOT EXISTS `product_price` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_id` int(11) NOT NULL COMMENT 'Товар',
    `product_feature_id` int(11) NOT NULL COMMENT 'Характеристика товара',
    `purchase_price` decimal(19,2) NOT NULL COMMENT 'Закупочная цена',
    `member_price` decimal(19,2) NOT NULL COMMENT 'Цена для участников',
    `price` decimal(19,2) NOT NULL COMMENT 'Цена для всех',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Стоимость товара';

CREATE TABLE IF NOT EXISTS `fund` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `name` VARCHAR(50) NOT NULL COMMENT 'Название',
    `percent` DECIMAL(5,2) NOT NULL COMMENT 'Процент',
    `deduction_total` DECIMAL(19,2) NOT NULL DEFAULT 0 COMMENT 'Сумма отчислений',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Фонды';

CREATE TABLE IF NOT EXISTS `fund_product` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_feature_id` int(11) NOT NULL COMMENT 'Характеристика товара',
    `fund_id` int(11) NOT NULL COMMENT 'Фонд',
    `percent` DECIMAL(5,2) NOT NULL COMMENT 'Процент',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`fund_id`) REFERENCES `fund` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Фонды для продукта';

CREATE TABLE IF NOT EXISTS `fund_common_price` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_feature_id` int(11) NOT NULL COMMENT 'Характеристика товара',
    `price` DECIMAL(5,2) NOT NULL COMMENT 'Цена',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Цена для всех для продукта';

CREATE TABLE IF NOT EXISTS `fund_deduction` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `fund_id` int(11) NOT NULL COMMENT 'Фонд',
    `amount`
    `message`
    `operation_date`
    PRIMARY KEY (`id`),
    FOREIGN KEY (`fund_id`) REFERENCES `fund` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Фонды для продукта';

ALTER TABLE `order_has_product`
	ADD COLUMN `product_feature_id` INT NULL AFTER `provider_id`;
ALTER TABLE `order_has_product`
	ADD CONSTRAINT `fk_order_has_product_product_feature_id` FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

UPDATE `email` SET `body`='<p><strong>Уведомление о проведенной операции</strong></p>\r\n\r\n<p><strong>Действие:</strong> {{%message}}</p>\r\n\r\n<p><strong>Сумма:</strong> {{%amount}}</p>\r\n\r\n<p><strong>Остаток:</strong> {{%total}}</p>\r\n\r\n<p>--<br />\r\nС уважением,<br />\r\nАдминистрация сайта.</p>' WHERE  `id`=7;
