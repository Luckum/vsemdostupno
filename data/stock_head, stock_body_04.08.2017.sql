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
-- Структура таблицы `stock_body`
--

CREATE TABLE `stock_body` (
  `id` int(11) NOT NULL,
  `stock_head_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `tare` varchar(10) NOT NULL,
  `weight` float NOT NULL,
  `measurement` varchar(10) NOT NULL,
  `count` int(11) NOT NULL,
  `summ` int(11) NOT NULL,
  `total_summ` float NOT NULL,
  `deposit` tinyint(1) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `stock_head`
--

CREATE TABLE `stock_head` (
  `id` int(11) NOT NULL,
  `who` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `provider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `stock_body`
--
ALTER TABLE `stock_body`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_body_ibfk_1` (`stock_head_id`),
  ADD KEY `stock_body_ibfk_2` (`product_id`);

--
-- Индексы таблицы `stock_head`
--
ALTER TABLE `stock_head`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_head_ibfk_1` (`provider_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `stock_body`
--
ALTER TABLE `stock_body`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `stock_head`
--
ALTER TABLE `stock_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `stock_body`
--
ALTER TABLE `stock_body`
  ADD CONSTRAINT `stock_body_ibfk_1` FOREIGN KEY (`stock_head_id`) REFERENCES `stock_head` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `stock_body_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `stock_head`
--
ALTER TABLE `stock_head`
  ADD CONSTRAINT `stock_head_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
