-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 02 2025 г., 21:57
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cat_shelter`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cats`
--

CREATE TABLE `cats` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date NOT NULL,
  `mother_id` int(10) DEFAULT NULL,
  `photo_filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `cats`
--

INSERT INTO `cats` (`id`, `name`, `gender`, `birth_date`, `mother_id`, `photo_filename`) VALUES
(12, 'Барсик', 'male', '2025-06-23', 13, 'cat-1.webp'),
(13, 'Фиса', 'female', '2022-07-25', NULL, 'cat-2.webp'),
(14, 'Марсик', 'male', '2020-06-16', NULL, 'cat-3.webp'),
(15, 'Сема', 'male', '2017-06-30', NULL, 'cat-4.webp');

-- --------------------------------------------------------

--
-- Структура таблицы `kitten_possible_fathers`
--

CREATE TABLE `kitten_possible_fathers` (
  `kitten_id` int(10) NOT NULL,
  `father_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `kitten_possible_fathers`
--

INSERT INTO `kitten_possible_fathers` (`kitten_id`, `father_id`) VALUES
(12, 14),
(12, 15);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cats`
--
ALTER TABLE `cats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mother_id` (`mother_id`);

--
-- Индексы таблицы `kitten_possible_fathers`
--
ALTER TABLE `kitten_possible_fathers`
  ADD PRIMARY KEY (`kitten_id`,`father_id`),
  ADD KEY `father_id` (`father_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cats`
--
ALTER TABLE `cats`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cats`
--
ALTER TABLE `cats`
  ADD CONSTRAINT `cats_ibfk_1` FOREIGN KEY (`mother_id`) REFERENCES `cats` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `kitten_possible_fathers`
--
ALTER TABLE `kitten_possible_fathers`
  ADD CONSTRAINT `kitten_possible_fathers_ibfk_1` FOREIGN KEY (`kitten_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kitten_possible_fathers_ibfk_2` FOREIGN KEY (`father_id`) REFERENCES `cats` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
