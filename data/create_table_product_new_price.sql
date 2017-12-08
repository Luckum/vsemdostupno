CREATE TABLE IF NOT EXISTS `product_new_price` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_id` int(11) NOT NULL COMMENT 'Товар',
    `price` DECIMAL(19,2) NOT NULL COMMENT 'Цена',
    `quantity` int(11) NOT NULL COMMENT 'Количество',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Новая цена на товар';