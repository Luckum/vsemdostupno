CREATE TABLE IF NOT EXISTS `module` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `name` varchar(100) NOT NULL COMMENT 'Имя модуля',
    `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Состояние',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Модули';

ALTER TABLE `user`
    CHANGE COLUMN `role` `role` ENUM('admin','member','partner','provider','superadmin') NOT NULL COMMENT 'Роль' AFTER `id`;

ALTER TABLE `module`
    ADD COLUMN `description` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Описание' AFTER `state`;

CREATE TABLE IF NOT EXISTS `candidate_group` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `name` varchar(255) NOT NULL COMMENT 'Название',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Группы кандидатов';

CREATE TABLE IF NOT EXISTS `candidate` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `email` varchar(100) DEFAULT NULL COMMENT 'Email',
    `firstname` varchar(255) DEFAULT NULL COMMENT 'Имя',
    `lastname` varchar(255) DEFAULT NULL COMMENT 'Фамилия',
    `patronymic` varchar(255) DEFAULT NULL COMMENT 'Отчество',
    `birthdate` DATETIME DEFAULT NULL COMMENT 'Дата рождения',
    `phone` varchar(20) DEFAULT NULL COMMENT 'Телефон',
    `block_mailing` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Блокировать рассылку',
    `group_id` int(11) NOT NULL COMMENT 'Группа',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`group_id`) REFERENCES `candidate_group` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Кандидаты';