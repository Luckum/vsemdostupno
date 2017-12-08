CREATE TABLE IF NOT EXISTS `provider_notification` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время отправки',
    `order_date` DATE NULL NULL COMMENT 'Дата заявки',
    `provider_id` INT(11) NOT NULL COMMENT 'Провайдер',
    `product_id` INT(11) NOT NULL COMMENT 'Товар',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`provider_id`) REFERENCES `provider` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Отправка заявок поставщикам';