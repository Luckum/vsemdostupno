-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Авг 04 2017 г., 12:02
-- Версия сервера: 10.0.17-MariaDB
-- Версия PHP: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vsemdostupno`
--

-- --------------------------------------------------------

--
-- Структура таблицы `provider_stock`
--

CREATE TABLE `provider_stock` (
  `id` int(11) NOT NULL ,
  `stock_body_id` int NOT NULL,
  `total_rent` int NOT NULL,
  `total_sum` INT NOT NULL , 
  `reaminder_rent` INT NOT NULL , 
  `summ_reminder` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `unit_contibution`
--

CREATE TABLE `unit_contibution` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `provider_stock_id` int NOT NULL,
  `on_deposit` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `provider_stock`
--
ALTER TABLE `provider_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_stock_ibfk_1` (`stock_body_id`);
  

--
-- Индексы таблицы `unit_contibution`
--
ALTER TABLE `unit_contibution`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_contibution_ibfk_1` (`provider_stock_id`),
  ADD KEY `unit_contibution_ibfk_2` (`order_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `provider_stock`
--
ALTER TABLE `provider_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `unit_contibution`
--
ALTER TABLE `unit_contibution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `unit_contibution`
--
ALTER TABLE `unit_contibution`
  ADD CONSTRAINT `unit_contibution_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `unit_contibution_ibfk_2` FOREIGN KEY (`provider_stock_id`) REFERENCES `provider_stock` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `provider_stock`
--
ALTER TABLE `provider_stock`
  ADD CONSTRAINT `provider_stock_ibfk_1` FOREIGN KEY (`stock_body_id`) REFERENCES `stock_body` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
