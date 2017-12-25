CREATE TABLE IF NOT EXISTS `product_feature` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `product_id` int(11) NOT NULL COMMENT '�����',
    `volume` DECIMAL(19,2) NOT NULL COMMENT '�����/�����',
    `measurement` VARCHAR(10) NULL DEFAULT NULL COMMENT '��. ���������',
    `tare` VARCHAR(10) NULL DEFAULT NULL COMMENT '����',
    `quantity` int(11) NOT NULL COMMENT '����������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������������� ������';

CREATE TABLE IF NOT EXISTS `product_price` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `product_id` int(11) NOT NULL COMMENT '�����',
    `product_feature_id` int(11) NOT NULL COMMENT '�������������� ������',
    `purchase_price` decimal(19,2) NOT NULL COMMENT '���������� ����',
    `member_price` decimal(19,2) NOT NULL COMMENT '���� ��� ����������',
    `price` decimal(19,2) NOT NULL COMMENT '���� ��� ����',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '��������� ������';

CREATE TABLE IF NOT EXISTS `fund` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `name` VARCHAR(50) NOT NULL COMMENT '��������',
    `percent` DECIMAL(5,2) NOT NULL COMMENT '�������',
    `deduction_total` DECIMAL(19,2) NOT NULL DEFAULT 0 COMMENT '����� ����������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�����';

CREATE TABLE IF NOT EXISTS `fund_product` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `product_feature_id` int(11) NOT NULL COMMENT '�������������� ������',
    `fund_id` int(11) NOT NULL COMMENT '����',
    `percent` DECIMAL(5,2) NOT NULL COMMENT '�������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`fund_id`) REFERENCES `fund` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '����� ��� ��������';

CREATE TABLE IF NOT EXISTS `fund_common_price` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `product_feature_id` int(11) NOT NULL COMMENT '�������������� ������',
    `price` DECIMAL(5,2) NOT NULL COMMENT '����',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '���� ��� ���� ��� ��������';

CREATE TABLE IF NOT EXISTS `fund_deduction` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `fund_id` int(11) NOT NULL COMMENT '����',
    `amount`
    `message`
    `operation_date`
    PRIMARY KEY (`id`),
    FOREIGN KEY (`fund_id`) REFERENCES `fund` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '����� ��� ��������';

ALTER TABLE `order_has_product`
	ADD COLUMN `product_feature_id` INT NULL AFTER `provider_id`;
ALTER TABLE `order_has_product`
	ADD CONSTRAINT `fk_order_has_product_product_feature_id` FOREIGN KEY (`product_feature_id`) REFERENCES `product_feature` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

UPDATE `email` SET `body`='<p><strong>����������� � ����������� ��������</strong></p>\r\n\r\n<p><strong>��������:</strong> {{%message}}</p>\r\n\r\n<p><strong>�����:</strong> {{%amount}}</p>\r\n\r\n<p><strong>�������:</strong> {{%total}}</p>\r\n\r\n<p>--<br />\r\n� ���������,<br />\r\n������������� �����.</p>' WHERE  `id`=7;
