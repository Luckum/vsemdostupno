CREATE TABLE IF NOT EXISTS `product_feature` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `product_id` int(11) NOT NULL COMMENT '�����',
    `volume` DECIMAL(19,2) NOT NULL COMMENT '�����\�����',
    `measurement` VARCHAR(10) NULL DEFAULT NULL COMMENT '��. ���������'
    `tare` VARCHAR(10) NULL DEFAULT NULL COMMENT '����',
    `quantity` int(11) NOT NULL COMMENT '����������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������������� ������';

