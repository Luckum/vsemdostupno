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

CREATE TABLE IF NOT EXISTS `mailing_category` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `name` varchar(255) NOT NULL COMMENT 'Назавние',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Категории рассылок';

CREATE TABLE IF NOT EXISTS `mailing_user` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `mailing_category_id` int(11) NOT NULL COMMENT 'Категория рассылки',
    `user_id` int(11) NOT NULL COMMENT 'Пользователь',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`mailing_category_id`) REFERENCES `mailing_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Рассылки для пользователей';

CREATE TABLE IF NOT EXISTS `mailing_news` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `for_members` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отправка для пользователей',
    `for_providers` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отправка для поставщиков',
    `for_candidates` varchar(50) NOT NULL DEFAULT '0' COMMENT 'Отправка для кандидатов',
    `subject` varchar(255) NOT NULL COMMENT 'Тема',
    `message` TEXT NOT NULL COMMENT 'Сообщение',
    `attachment` varchar(255) DEFAULT NULL COMMENT 'Приложенные файлы',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время отправки',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Рассылка новостей';

CREATE TABLE IF NOT EXISTS `mailing_product` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `product_id` int(11) NOT NULL COMMENT 'Товар',
    `for_members` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отправка для пользователей',
    `for_providers` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отправка для поставщиков',
    `for_candidates` varchar(50) NOT NULL DEFAULT '0' COMMENT 'Отправка для кандидатов',
    `mailing_category_id` int(11) NOT NULL COMMENT 'Категория рассылки',
    `subject` varchar(255) NOT NULL COMMENT 'Тема',
    `message` TEXT NOT NULL COMMENT 'Сообщение',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время отправки',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`mailing_category_id`) REFERENCES `mailing_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Рассылка о продуктах';

CREATE TABLE IF NOT EXISTS `mailing_vote` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `for_members` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отправка для пользователей',
    `for_providers` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отправка для поставщиков',
    `subject` varchar(255) NOT NULL COMMENT 'Тема',
    `attachment` varchar(255) DEFAULT NULL COMMENT 'Приложенные файлы',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время отправки',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Рассылка голосований';

CREATE TABLE IF NOT EXISTS `mailing_vote_stat`(
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `mailing_vote_id` int(11) NOT NULL COMMENT 'Голосование',
    `user_id` int(11) NOT NULL COMMENT 'Пользователь',
    `vote` enum('agree', 'against', 'hold') NOT NULL COMMENT 'Выбор',
    `vote_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время голосования',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`mailing_vote_id`) REFERENCES `mailing_vote` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Статистика голосований';

CREATE TABLE IF NOT EXISTS `mailing_message`(
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `category` enum('question', 'claim', 'proposal') NOT NULL COMMENT 'Категория сообщения',
    `user_id` int(11) NOT NULL COMMENT 'Пользователь',
    `subject` varchar(255) NOT NULL COMMENT 'Тема',
    `message` TEXT NOT NULL COMMENT 'Сообщение',
    `sent_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время отправки',
    `answered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Отвечено/прочитано',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Статистика голосований';