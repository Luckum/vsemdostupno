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

CREATE TABLE IF NOT EXISTS `mailing_category` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `name` varchar(255) NOT NULL COMMENT '��������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '��������� ��������';

CREATE TABLE IF NOT EXISTS `mailing_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `mailing_category_id` int(11) NOT NULL COMMENT '��������� ��������',
    `user_id` int(11) NOT NULL COMMENT '������������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`mailing_category_id`) REFERENCES `mailing_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������� ��� �������������';

CREATE TABLE IF NOT EXISTS `mailing_news` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `for_members` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� ��� �������������',
    `for_providers` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� ��� �����������',
    `for_candidates` varchar(50) NOT NULL DEFAULT '0' COMMENT '�������� ��� ����������',
    `subject` varchar(255) NOT NULL COMMENT '����',
    `message` TEXT NOT NULL COMMENT '���������',
    `attachment` varchar(255) DEFAULT NULL COMMENT '����������� �����',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� ��������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������� ��������';

CREATE TABLE IF NOT EXISTS `mailing_product` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `product_id` int(11) NOT NULL COMMENT '�����',
    `for_members` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� ��� �������������',
    `for_providers` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� ��� �����������',
    `for_candidates` varchar(50) NOT NULL DEFAULT '0' COMMENT '�������� ��� ����������',
    `mailing_category_id` int(11) NOT NULL COMMENT '��������� ��������',
    `subject` varchar(255) NOT NULL COMMENT '����',
    `message` TEXT NOT NULL COMMENT '���������',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� ��������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`mailing_category_id`) REFERENCES `mailing_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������� � ���������';

CREATE TABLE IF NOT EXISTS `mailing_vote` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `for_members` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� ��� �������������',
    `for_providers` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� ��� �����������',
    `subject` varchar(255) NOT NULL COMMENT '����',
    `attachment` varchar(255) DEFAULT NULL COMMENT '����������� �����',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� ��������',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '�������� �����������';

CREATE TABLE IF NOT EXISTS `mailing_vote_stat`(
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `mailing_vote_id` int(11) NOT NULL COMMENT '�����������',
    `user_id` int(11) NOT NULL COMMENT '������������',
    `vote` enum('agree', 'against', 'hold') NOT NULL COMMENT '�����',
    `vote_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� �����������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`mailing_vote_id`) REFERENCES `mailing_vote` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '���������� �����������';

CREATE TABLE IF NOT EXISTS `mailing_message`(
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '�������������',
    `category` enum('question', 'claim', 'proposal') NOT NULL COMMENT '��������� ���������',
    `user_id` int(11) NOT NULL COMMENT '������������',
    `subject` varchar(255) NOT NULL COMMENT '����',
    `message` TEXT NOT NULL COMMENT '���������',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '����� ��������',
    `answered` tinyint(1) NOT NULL DEFAULT '0' COMMENT '��������/���������',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '���������� �����������';