CREATE TABLE IF NOT EXISTS `provider_reg_data_tmp` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `ip` varchar(255) NOT NULL COMMENT 'IP-����� ��������',
    `step` tinyint NOT NULL COMMENT '��� �����������',
    `phone` varchar(255) NOT NULL COMMENT '�������',
    `firstname` varchar(255) NOT NULL COMMENT '���',
    `lastname` varchar(255) NOT NULL COMMENT '�������',
    `patronymic` varchar(255) NOT NULL COMMENT '��������',
    `birthdate` datetime NOT NULL COMMENT '���� ��������',
    `citizen` varchar(50) NOT NULL COMMENT '�����������',
    `registration` varchar(255) NOT NULL COMMENT '����� �����������',
    `passport` varchar(30) NOT NULL COMMENT '����� � ����� ��������',
    `passport_date` datetime NOT NULL COMMENT '���� ������ ��������',
    `passport_department` varchar(255) NOT NULL COMMENT '��� ����� �������',
    `ext_phones` varchar(255) DEFAULT NULL COMMENT '�������������� ��������',
    `name` varchar(255) NOT NULL DEFAULT "" COMMENT '��������',
    `field_of_activity` TEXT NULL DEFAULT NULL COMMENT '����� ������������',
    `legal_address` VARCHAR(255) NOT NULL DEFAULT "" COMMENT '����������� �����',
    `snils` CHAR(11) NOT NULL DEFAULT "" COMMENT '�����',
    `ogrn` CHAR(13) NOT NULL DEFAULT "" COMMENT '����',
    `site` VARCHAR(100) NULL DEFAULT NULL COMMENT '���� ��������',
    `itn` varchar(30) NOT NULL DEFAULT "" COMMENT '���',
    `category` TEXT NULL DEFAULT NULL COMMENT '���������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '������ ����������� ����������';


