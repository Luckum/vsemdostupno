CREATE TABLE IF NOT EXISTS `provider_reg_data_tmp` (
    `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор',
    `ip` varchar(255) NOT NULL COMMENT 'IP-адрес создания',
    `step` tinyint NOT NULL COMMENT 'Шаг регистрации',
    `phone` varchar(255) NOT NULL COMMENT 'Телефон',
    `firstname` varchar(255) NOT NULL COMMENT 'Имя',
    `lastname` varchar(255) NOT NULL COMMENT 'Фамилия',
    `patronymic` varchar(255) NOT NULL COMMENT 'Отчество',
    `birthdate` datetime NOT NULL COMMENT 'Дата рождения',
    `citizen` varchar(50) NOT NULL COMMENT 'Гражданство',
    `registration` varchar(255) NOT NULL COMMENT 'Адрес регистрации',
    `passport` varchar(30) NOT NULL COMMENT 'Серия и номер паспорта',
    `passport_date` datetime NOT NULL COMMENT 'Дата выдачи паспорта',
    `passport_department` varchar(255) NOT NULL COMMENT 'Кем выдан паспорт',
    `ext_phones` varchar(255) DEFAULT NULL COMMENT 'Дополнительные телефоны',
    `name` varchar(255) NOT NULL DEFAULT "" COMMENT 'Название',
    `field_of_activity` TEXT NULL DEFAULT NULL COMMENT 'Сфера деятельности',
    `legal_address` VARCHAR(255) NOT NULL DEFAULT "" COMMENT 'Юридический адрес',
    `snils` CHAR(11) NOT NULL DEFAULT "" COMMENT 'СНИЛС',
    `ogrn` CHAR(13) NOT NULL DEFAULT "" COMMENT 'ОГРН',
    `site` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Сайт компании',
    `itn` varchar(30) NOT NULL DEFAULT "" COMMENT 'ИНН',
    `category` TEXT NULL DEFAULT NULL COMMENT 'Категории',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'Данные регистрации поставщика';


