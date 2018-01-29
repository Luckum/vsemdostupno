CREATE TABLE IF NOT EXISTS `module` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `name` varchar(100) NOT NULL COMMENT '��� ������',
    `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '���������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '������';

ALTER TABLE `user`
    CHANGE COLUMN `role` `role` ENUM('admin','member','partner','provider','superadmin') NOT NULL COMMENT '����' AFTER `id`;

ALTER TABLE `module`
    ADD COLUMN `description` VARCHAR(255) NULL DEFAULT NULL COMMENT '��������' AFTER `state`;

CREATE TABLE IF NOT EXISTS `candidate_group` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `name` varchar(255) NOT NULL COMMENT '��������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '������ ����������';

CREATE TABLE IF NOT EXISTS `candidate` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `email` varchar(100) DEFAULT NULL COMMENT 'Email',
    `firstname` varchar(255) DEFAULT NULL COMMENT '���',
    `lastname` varchar(255) DEFAULT NULL COMMENT '�������',
    `patronymic` varchar(255) DEFAULT NULL COMMENT '��������',
    `birthdate` DATETIME DEFAULT NULL COMMENT '���� ��������',
    `phone` varchar(20) DEFAULT NULL COMMENT '�������',
    `block_mailing` tinyint(1) NOT NULL DEFAULT '0' COMMENT '����������� ��������',
    `group_id` int(11) NOT NULL COMMENT '������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`group_id`) REFERENCES `candidate_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '���������';