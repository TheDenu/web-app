-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0
-- Время создания: Дек 24 2025 г., 23:30
-- Версия сервера: 8.0.35
-- Версия PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `web_app_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `applications`
--

CREATE TABLE `applications` (
  `id_application` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `place_id` int DEFAULT NULL,
  `description` text,
  `defect_type_id` int DEFAULT NULL,
  `priority_id` int DEFAULT NULL,
  `status_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `solved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `applications`
--

INSERT INTO `applications` (`id_application`, `user_id`, `place_id`, `description`, `defect_type_id`, `priority_id`, `status_id`, `created_at`, `solved_at`) VALUES
(3, 2, 1, 'тест', 1, 1, 5, '2025-12-12 23:59:35', NULL),
(4, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:37', NULL),
(5, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:40', NULL),
(6, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:41', NULL),
(7, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:42', NULL),
(8, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:43', NULL),
(9, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:44', NULL),
(10, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:45', NULL),
(12, 2, 1, 'тест', 1, 1, 5, '2025-12-12 23:59:47', NULL),
(14, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:48', NULL),
(15, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:49', NULL),
(16, 2, 1, 'тест', 1, 1, 5, '2025-12-12 23:59:50', NULL),
(17, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:52', NULL),
(18, 2, 1, 'тест', 1, 1, 2, '2025-12-12 23:59:53', NULL),
(19, 2, 1, 'тест', 1, 1, 5, '2025-12-12 23:59:54', NULL),
(20, 2, 1, 'тест', 1, 1, 4, '2025-12-12 23:59:55', NULL),
(21, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:56', NULL),
(22, 2, 1, 'тест', 1, 1, 3, '2025-12-12 23:59:57', NULL),
(23, 2, 1, 'тест', 1, 1, 1, '2025-12-12 23:59:58', NULL),
(24, 2, 1, 'тест', 1, 1, 2, '2025-12-12 23:59:59', NULL),
(25, 2, 1, 'тест', 1, 1, 2, '2025-12-13 00:00:00', NULL),
(26, 2, 1, 'тест', 1, 1, 5, '2025-12-13 00:00:01', NULL),
(27, 2, 1, 'тест', 1, 1, 2, '2025-12-13 00:00:03', NULL),
(28, 2, 1, 'тест', 1, 1, 2, '2025-12-13 00:00:04', NULL),
(29, 2, 1, 'тест', 1, 1, 2, '2025-12-13 00:00:05', NULL),
(30, 2, 1, 'тест', 1, 1, 3, '2025-12-13 00:00:06', NULL),
(31, 2, 1, 'тест', 1, 1, 2, '2025-12-13 00:00:07', NULL),
(42, NULL, 160, 'Ну течет крыша', 3, 2, 1, '2025-12-21 15:55:03', NULL),
(43, 1, 74, 'Искрит розетка', 2, 3, 5, '2025-12-21 15:59:58', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `defect_types`
--

CREATE TABLE `defect_types` (
  `id_defect_type` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `defect_types`
--

INSERT INTO `defect_types` (`id_defect_type`, `name`) VALUES
(3, 'плотник'),
(1, 'сантехника'),
(2, 'электрика');

-- --------------------------------------------------------

--
-- Структура таблицы `names`
--

CREATE TABLE `names` (
  `id_fio` int NOT NULL,
  `fio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `names`
--

INSERT INTO `names` (`id_fio`, `fio`) VALUES
(3, 'Алексей Алексеев'),
(4, 'Дмитрий Дмитриев'),
(9, 'Егор Егоров'),
(5, 'Елена Еленова'),
(1, 'Иван Иванов'),
(6, 'Марина Маринина'),
(8, 'Наталья Натальева'),
(7, 'Олег Олегов'),
(2, 'Петр Петров');

-- --------------------------------------------------------

--
-- Структура таблицы `photos`
--

CREATE TABLE `photos` (
  `id_photo` int NOT NULL,
  `application_id` int DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `photos`
--

INSERT INTO `photos` (`id_photo`, `application_id`, `path`) VALUES
(7, 3, 'uploads/app_photo_3_693c823764062.jpg'),
(8, 3, 'uploads/app_photo_3_693c82376482f.webp'),
(9, 3, 'uploads/app_photo_3_693c8237651c4.png'),
(10, 4, 'uploads/app_photo_4_693c8239dffa5.jpg'),
(11, 4, 'uploads/app_photo_4_693c8239e0c8f.webp'),
(12, 4, 'uploads/app_photo_4_693c8239e18ba.png'),
(13, 5, 'uploads/app_photo_5_693c823c69e42.jpg'),
(14, 5, 'uploads/app_photo_5_693c823c6a653.webp'),
(15, 5, 'uploads/app_photo_5_693c823c6ad4a.png'),
(16, 6, 'uploads/app_photo_6_693c823d68d48.jpg'),
(17, 6, 'uploads/app_photo_6_693c823d693d3.webp'),
(18, 6, 'uploads/app_photo_6_693c823d698f6.png'),
(19, 7, 'uploads/app_photo_7_693c823e4a02b.jpg'),
(20, 7, 'uploads/app_photo_7_693c823e4a87e.webp'),
(21, 7, 'uploads/app_photo_7_693c823e4ad29.png'),
(22, 8, 'uploads/app_photo_8_693c823f44d4c.jpg'),
(23, 8, 'uploads/app_photo_8_693c823f4526e.webp'),
(24, 8, 'uploads/app_photo_8_693c823f45c84.png'),
(25, 9, 'uploads/app_photo_9_693c82404c2fc.jpg'),
(26, 9, 'uploads/app_photo_9_693c82404c96d.webp'),
(27, 9, 'uploads/app_photo_9_693c82404cdfe.png'),
(28, 10, 'uploads/app_photo_10_693c8241258dd.jpg'),
(29, 10, 'uploads/app_photo_10_693c824126f0b.webp'),
(30, 10, 'uploads/app_photo_10_693c8241274c6.png'),
(34, 12, 'uploads/app_photo_12_693c824306116.jpg'),
(35, 12, 'uploads/app_photo_12_693c8243067f5.webp'),
(36, 12, 'uploads/app_photo_12_693c824306e25.png'),
(40, 14, 'uploads/app_photo_14_693c8244e237b.jpg'),
(41, 14, 'uploads/app_photo_14_693c8244e28a7.webp'),
(42, 14, 'uploads/app_photo_14_693c8244e2e32.png'),
(43, 15, 'uploads/app_photo_15_693c8245da083.jpg'),
(44, 15, 'uploads/app_photo_15_693c8245da54c.webp'),
(45, 15, 'uploads/app_photo_15_693c8245daa21.png'),
(46, 16, 'uploads/app_photo_16_693c8246ea52d.jpg'),
(47, 16, 'uploads/app_photo_16_693c8246eaa58.webp'),
(48, 16, 'uploads/app_photo_16_693c8246eb0fd.png'),
(49, 17, 'uploads/app_photo_17_693c8248137d7.jpg'),
(50, 17, 'uploads/app_photo_17_693c824814115.webp'),
(51, 17, 'uploads/app_photo_17_693c82481469e.png'),
(52, 18, 'uploads/app_photo_18_693c82492d02e.jpg'),
(53, 18, 'uploads/app_photo_18_693c82492d44b.webp'),
(54, 18, 'uploads/app_photo_18_693c82492d83c.png'),
(55, 19, 'uploads/app_photo_19_693c824a40986.jpg'),
(56, 19, 'uploads/app_photo_19_693c824a40f03.webp'),
(57, 19, 'uploads/app_photo_19_693c824a41805.png'),
(58, 20, 'uploads/app_photo_20_693c824b4c814.jpg'),
(59, 20, 'uploads/app_photo_20_693c824b4cd38.webp'),
(60, 20, 'uploads/app_photo_20_693c824b4d12c.png'),
(61, 21, 'uploads/app_photo_21_693c824c7ad65.jpg'),
(62, 21, 'uploads/app_photo_21_693c824c7b593.webp'),
(63, 21, 'uploads/app_photo_21_693c824c7ba99.png'),
(64, 22, 'uploads/app_photo_22_693c824d82fb7.jpg'),
(65, 22, 'uploads/app_photo_22_693c824d835d6.webp'),
(66, 22, 'uploads/app_photo_22_693c824d83c9b.png'),
(67, 23, 'uploads/app_photo_23_693c824e93c53.jpg'),
(68, 23, 'uploads/app_photo_23_693c824e9434d.webp'),
(69, 23, 'uploads/app_photo_23_693c824e948e1.png'),
(70, 24, 'uploads/app_photo_24_693c824f9867f.jpg'),
(71, 24, 'uploads/app_photo_24_693c824f99821.webp'),
(72, 24, 'uploads/app_photo_24_693c824f99c2f.png'),
(73, 25, 'uploads/app_photo_25_693c8250a9dad.jpg'),
(74, 25, 'uploads/app_photo_25_693c8250aa2dd.webp'),
(75, 25, 'uploads/app_photo_25_693c8250aa7fd.png'),
(76, 26, 'uploads/app_photo_26_693c8251d073b.jpg'),
(77, 26, 'uploads/app_photo_26_693c8251d0c53.webp'),
(78, 26, 'uploads/app_photo_26_693c8251d12a5.png'),
(79, 27, 'uploads/app_photo_27_693c825301e16.jpg'),
(80, 27, 'uploads/app_photo_27_693c8253023e3.webp'),
(81, 27, 'uploads/app_photo_27_693c825302809.png'),
(82, 28, 'uploads/app_photo_28_693c8254238dd.jpg'),
(83, 28, 'uploads/app_photo_28_693c825423d4f.webp'),
(84, 28, 'uploads/app_photo_28_693c82542415c.png'),
(85, 29, 'uploads/app_photo_29_693c82551ae3a.jpg'),
(86, 29, 'uploads/app_photo_29_693c82551b254.webp'),
(87, 29, 'uploads/app_photo_29_693c82551b5f9.png'),
(88, 30, 'uploads/app_photo_30_693c825619e2a.jpg'),
(89, 30, 'uploads/app_photo_30_693c82561a381.webp'),
(90, 30, 'uploads/app_photo_30_693c82561ab3f.png'),
(91, 31, 'uploads/app_photo_31_693c82571fa64.jpg'),
(92, 31, 'uploads/app_photo_31_693c82571ff26.webp'),
(93, 31, 'uploads/app_photo_31_693c825720347.png'),
(103, 42, 'uploads/app_photo_42_6947ee273917b.jpg'),
(104, 43, 'uploads/app_photo_43_6947ef4e49ead.webp');

-- --------------------------------------------------------

--
-- Структура таблицы `places`
--

CREATE TABLE `places` (
  `id_place` int NOT NULL,
  `floor` int DEFAULT NULL,
  `room` varchar(10) DEFAULT NULL,
  `section` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `places`
--

INSERT INTO `places` (`id_place`, `floor`, `room`, `section`) VALUES
(1, 2, '201', 'male'),
(2, 2, '202', 'male'),
(3, 2, '203', 'male'),
(4, 2, '204', 'male'),
(5, 2, '205', 'male'),
(6, 2, '206', 'male'),
(7, 2, '207', 'male'),
(8, 2, '208', 'male'),
(9, 2, 'кухня', 'male'),
(10, 2, 'коридор', 'male'),
(11, 2, '211', 'female'),
(12, 2, '212', 'female'),
(13, 2, '213', 'female'),
(14, 2, '214', 'female'),
(15, 2, '215', 'female'),
(16, 2, '216', 'female'),
(17, 2, '217', 'female'),
(18, 2, '218', 'female'),
(19, 2, 'кухня', 'female'),
(20, 2, 'коридор', 'female'),
(21, 3, '301', 'male'),
(22, 3, '302', 'male'),
(23, 3, '303', 'male'),
(24, 3, '304', 'male'),
(25, 3, '305', 'male'),
(26, 3, '306', 'male'),
(27, 3, '307', 'male'),
(28, 3, '308', 'male'),
(29, 3, 'кухня', 'male'),
(30, 3, 'коридор', 'male'),
(31, 3, '311', 'female'),
(32, 3, '312', 'female'),
(33, 3, '313', 'female'),
(34, 3, '314', 'female'),
(35, 3, '315', 'female'),
(36, 3, '316', 'female'),
(37, 3, '317', 'female'),
(38, 3, '318', 'female'),
(39, 3, 'кухня', 'female'),
(40, 3, 'коридор', 'female'),
(41, 4, '401', 'male'),
(42, 4, '402', 'male'),
(43, 4, '403', 'male'),
(44, 4, '404', 'male'),
(45, 4, '405', 'male'),
(46, 4, '406', 'male'),
(47, 4, '407', 'male'),
(48, 4, '408', 'male'),
(49, 4, 'кухня', 'male'),
(50, 4, 'коридор', 'male'),
(51, 4, '411', 'female'),
(52, 4, '412', 'female'),
(53, 4, '413', 'female'),
(54, 4, '414', 'female'),
(55, 4, '415', 'female'),
(56, 4, '416', 'female'),
(57, 4, '417', 'female'),
(58, 4, '418', 'female'),
(59, 4, 'кухня', 'female'),
(60, 4, 'коридор', 'female'),
(61, 5, '501', 'male'),
(62, 5, '502', 'male'),
(63, 5, '503', 'male'),
(64, 5, '504', 'male'),
(65, 5, '505', 'male'),
(66, 5, '506', 'male'),
(67, 5, '507', 'male'),
(68, 5, '508', 'male'),
(69, 5, 'кухня', 'male'),
(70, 5, 'коридор', 'male'),
(71, 5, '511', 'female'),
(72, 5, '512', 'female'),
(73, 5, '513', 'female'),
(74, 5, '514', 'female'),
(75, 5, '515', 'female'),
(76, 5, '516', 'female'),
(77, 5, '517', 'female'),
(78, 5, '518', 'female'),
(79, 5, 'кухня', 'female'),
(80, 5, 'коридор', 'female'),
(81, 6, '601', 'male'),
(82, 6, '602', 'male'),
(83, 6, '603', 'male'),
(84, 6, '604', 'male'),
(85, 6, '605', 'male'),
(86, 6, '606', 'male'),
(87, 6, '607', 'male'),
(88, 6, '608', 'male'),
(89, 6, 'кухня', 'male'),
(90, 6, 'коридор', 'male'),
(91, 6, '611', 'female'),
(92, 6, '612', 'female'),
(93, 6, '613', 'female'),
(94, 6, '614', 'female'),
(95, 6, '615', 'female'),
(96, 6, '616', 'female'),
(97, 6, '617', 'female'),
(98, 6, '618', 'female'),
(99, 6, 'кухня', 'female'),
(100, 6, 'коридор', 'female'),
(101, 7, '701', 'male'),
(102, 7, '702', 'male'),
(103, 7, '703', 'male'),
(104, 7, '704', 'male'),
(105, 7, '705', 'male'),
(106, 7, '706', 'male'),
(107, 7, '707', 'male'),
(108, 7, '708', 'male'),
(109, 7, 'кухня', 'male'),
(110, 7, 'коридор', 'male'),
(111, 7, '711', 'female'),
(112, 7, '712', 'female'),
(113, 7, '713', 'female'),
(114, 7, '714', 'female'),
(115, 7, '715', 'female'),
(116, 7, '716', 'female'),
(117, 7, '717', 'female'),
(118, 7, '718', 'female'),
(119, 7, 'кухня', 'female'),
(120, 7, 'коридор', 'female'),
(121, 8, '801', 'male'),
(122, 8, '802', 'male'),
(123, 8, '803', 'male'),
(124, 8, '804', 'male'),
(125, 8, '805', 'male'),
(126, 8, '806', 'male'),
(127, 8, '807', 'male'),
(128, 8, '808', 'male'),
(129, 8, 'кухня', 'male'),
(130, 8, 'коридор', 'male'),
(131, 8, '811', 'female'),
(132, 8, '812', 'female'),
(133, 8, '813', 'female'),
(134, 8, '814', 'female'),
(135, 8, '815', 'female'),
(136, 8, '816', 'female'),
(137, 8, '817', 'female'),
(138, 8, '818', 'female'),
(139, 8, 'кухня', 'female'),
(140, 8, 'коридор', 'female'),
(141, 9, '901', 'male'),
(142, 9, '902', 'male'),
(143, 9, '903', 'male'),
(144, 9, '904', 'male'),
(145, 9, '905', 'male'),
(146, 9, '906', 'male'),
(147, 9, '907', 'male'),
(148, 9, '908', 'male'),
(149, 9, 'кухня', 'male'),
(150, 9, 'коридор', 'male'),
(151, 9, '911', 'female'),
(152, 9, '912', 'female'),
(153, 9, '913', 'female'),
(154, 9, '914', 'female'),
(155, 9, '915', 'female'),
(156, 9, '916', 'female'),
(157, 9, '917', 'female'),
(158, 9, '918', 'female'),
(159, 9, 'кухня', 'female'),
(160, 9, 'коридор', 'female');

-- --------------------------------------------------------

--
-- Структура таблицы `priorities`
--

CREATE TABLE `priorities` (
  `id_priority` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `priorities`
--

INSERT INTO `priorities` (`id_priority`, `name`) VALUES
(3, 'высокий'),
(1, 'низкий'),
(2, 'средний');

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id_role` int NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id_role`, `role_name`) VALUES
(2, 'admin'),
(1, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `statuses`
--

CREATE TABLE `statuses` (
  `id_status` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `statuses`
--

INSERT INTO `statuses` (`id_status`, `name`) VALUES
(4, 'выполнена'),
(3, 'выполняется'),
(2, 'ожидание'),
(5, 'отказано'),
(1, 'проверка');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fio_id` int NOT NULL,
  `role_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `login`, `password`, `fio_id`, `role_id`) VALUES
(1, 'denu', '$2y$10$VpGWZgR0XNq8vU.Y9JKp8eshneEZto5xK8vOUQ2Qd5mgtEPyBnHWW', 9, 2),
(2, 'leca', '$2y$10$8oSjel.zE9yrj5fRTWgN8O4yxbxRzNZC2DK/9wYCM2vZeqG6.Hbnu', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id_token` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user_tokens`
--

INSERT INTO `user_tokens` (`id_token`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(35, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjYzMjIxODUsImV4cCI6MTc2NjMyNTc4NSwiZGF0YSI6eyJ1c2VySWQiOjIsInJvbGUiOiJ1c2VyIn19.3IQtZykHvDdHsKdD0nErBStAC9OAHhSnkD51IiLlXW0', '2025-12-22 16:03:05', '2025-12-21 16:03:05'),
(38, 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjY2MDczMDQsImV4cCI6MTc2NjYxMDkwNCwiZGF0YSI6eyJ1c2VySWQiOjEsInJvbGUiOiJhZG1pbiJ9fQ.oaxGuP15F4jqBAJefCZCD9QKTrhk98pjFT17_-4_5p8', '2025-12-25 23:15:04', '2025-12-24 23:15:04');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id_application`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `place_id` (`place_id`),
  ADD KEY `defect_type_id` (`defect_type_id`),
  ADD KEY `priority_id` (`priority_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Индексы таблицы `defect_types`
--
ALTER TABLE `defect_types`
  ADD PRIMARY KEY (`id_defect_type`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `names`
--
ALTER TABLE `names`
  ADD PRIMARY KEY (`id_fio`),
  ADD UNIQUE KEY `fio` (`fio`);

--
-- Индексы таблицы `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id_photo`),
  ADD KEY `application_id` (`application_id`);

--
-- Индексы таблицы `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id_place`);

--
-- Индексы таблицы `priorities`
--
ALTER TABLE `priorities`
  ADD PRIMARY KEY (`id_priority`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Индексы таблицы `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id_status`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `fio_id` (`fio_id`);

--
-- Индексы таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id_token`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `applications`
--
ALTER TABLE `applications`
  MODIFY `id_application` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT для таблицы `defect_types`
--
ALTER TABLE `defect_types`
  MODIFY `id_defect_type` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `names`
--
ALTER TABLE `names`
  MODIFY `id_fio` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `photos`
--
ALTER TABLE `photos`
  MODIFY `id_photo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT для таблицы `places`
--
ALTER TABLE `places`
  MODIFY `id_place` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT для таблицы `priorities`
--
ALTER TABLE `priorities`
  MODIFY `id_priority` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  MODIFY `id_token` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`place_id`) REFERENCES `places` (`id_place`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`defect_type_id`) REFERENCES `defect_types` (`id_defect_type`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_ibfk_4` FOREIGN KEY (`priority_id`) REFERENCES `priorities` (`id_priority`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id_status`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id_application`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_role`) ON DELETE RESTRICT,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`fio_id`) REFERENCES `names` (`id_fio`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
