CREATE TABLE IF NOT EXISTS `provider_notification` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� ��������',
    `order_date` DATE NULL NULL COMMENT '���� ������',
    `provider_id` INT(11) NOT NULL COMMENT '���������',
    `product_id` INT(11) NOT NULL COMMENT '�����',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`provider_id`) REFERENCES `provider` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������� ������ �����������';