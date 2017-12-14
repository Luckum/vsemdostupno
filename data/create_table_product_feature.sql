CREATE TABLE IF NOT EXISTS `product_feature` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_id` int(11) NOT NULL COMMENT 'Товар',
    `volume` DECIMAL(19,2) NOT NULL COMMENT 'Масса\Объем',
    `measurement` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Ед. измерения'
    `tare` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Тара',
    `quantity` int(11) NOT NULL COMMENT 'Количество',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Характеристики товара';

