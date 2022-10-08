-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-09-2021 a las 13:35:15
-- Versión del servidor: 10.1.38-MariaDB
-- Versión de PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mlywatsm_otrochat`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueos`
--

CREATE TABLE `bloqueos` (
  `id` int(11) NOT NULL,
  `fromid` int(11) NOT NULL,
  `toid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bloqueos`
--

INSERT INTO `bloqueos` (`id`, `fromid`, `toid`) VALUES
(1, 6, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colecciones`
--

CREATE TABLE `colecciones` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imagen` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `codigo` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `download`
--

CREATE TABLE `download` (
  `id` int(11) NOT NULL,
  `fotoid` int(11) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `download`
--

INSERT INTO `download` (`id`, `fotoid`, `uid`) VALUES
(1, 7, 2),
(2, 73, 2),
(3, 74, 2),
(4, 75, 2),
(5, 91, 2),
(6, 4188, 2),
(7, 4285, 2),
(8, 4280, 2),
(9, 4283, 2),
(10, 4281, 2),
(11, 6, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `farms`
--

CREATE TABLE `farms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `farm_items`
--

CREATE TABLE `farm_items` (
  `id` int(11) NOT NULL,
  `farms_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `size` varchar(20) NOT NULL DEFAULT '84px',
  `position_x` int(255) NOT NULL,
  `position_y` int(255) NOT NULL,
  `price` varchar(100) NOT NULL,
  `produces` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotosbot`
--

CREATE TABLE `fotosbot` (
  `id` int(11) NOT NULL,
  `rutadefoto` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lista_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotoscompradas`
--

CREATE TABLE `fotoscompradas` (
  `id` int(11) NOT NULL,
  `foto_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotosenventa`
--

CREATE TABLE `fotosenventa` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `imagen` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `thumb` text CHARACTER SET latin1,
  `descripcion` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `linkdedescarga` varchar(255) CHARACTER SET latin1 NOT NULL,
  `type` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT 'publico',
  `downloadable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Permitir descargar',
  `time` int(255) DEFAULT NULL,
  `category` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT 'hetero'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotosprogramadas`
--

CREATE TABLE `fotosprogramadas` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'publico',
  `category` varchar(20) NOT NULL DEFAULT 'hetero',
  `time` int(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `player1` mediumint(10) UNSIGNED NOT NULL,
  `player2` mediumint(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `friends`
--

INSERT INTO `friends` (`id`, `player1`, `player2`) VALUES
(1, 1, 2),
(2, 1, 1),
(3, 1, 2),
(4, 1, 3),
(5, 1, 4),
(6, 1, 5),
(7, 2, 1),
(8, 2, 2),
(9, 2, 3),
(10, 2, 4),
(11, 2, 5),
(12, 3, 1),
(13, 3, 2),
(14, 3, 3),
(15, 3, 4),
(16, 3, 5),
(17, 4, 1),
(18, 4, 2),
(19, 4, 3),
(20, 4, 4),
(21, 4, 5),
(22, 5, 1),
(23, 5, 2),
(24, 5, 3),
(25, 5, 4),
(26, 5, 5),
(27, 1, 1),
(28, 1, 2),
(29, 1, 3),
(30, 1, 4),
(31, 1, 5),
(33, 1, 7),
(34, 1, 8),
(35, 1, 9),
(36, 1, 10),
(37, 1, 11),
(38, 1, 12),
(39, 1, 13),
(40, 1, 14),
(41, 1, 15),
(42, 1, 16),
(43, 1, 17),
(44, 1, 18),
(45, 1, 19),
(46, 1, 20),
(47, 2, 1),
(48, 2, 2),
(49, 2, 3),
(50, 2, 4),
(51, 2, 5),
(53, 2, 7),
(54, 2, 8),
(55, 2, 9),
(56, 2, 10),
(57, 2, 11),
(58, 2, 12),
(59, 2, 13),
(60, 2, 14),
(61, 2, 15),
(63, 2, 17),
(64, 2, 18),
(65, 2, 19),
(66, 2, 20),
(67, 3, 1),
(68, 3, 2),
(69, 3, 3),
(70, 3, 4),
(71, 3, 5),
(72, 3, 6),
(73, 3, 7),
(74, 3, 8),
(75, 3, 9),
(76, 3, 10),
(77, 3, 11),
(78, 3, 12),
(79, 3, 13),
(80, 3, 14),
(81, 3, 15),
(82, 3, 16),
(83, 3, 17),
(84, 3, 18),
(85, 3, 19),
(86, 3, 20),
(87, 4, 1),
(88, 4, 2),
(89, 4, 3),
(90, 4, 4),
(91, 4, 5),
(93, 4, 7),
(94, 4, 8),
(95, 4, 9),
(96, 4, 10),
(97, 4, 11),
(98, 4, 12),
(99, 4, 13),
(100, 4, 14),
(101, 4, 15),
(102, 4, 16),
(103, 4, 17),
(104, 4, 18),
(105, 4, 19),
(106, 4, 20),
(107, 5, 1),
(108, 5, 2),
(109, 5, 3),
(110, 5, 4),
(111, 5, 5),
(112, 5, 6),
(113, 5, 7),
(114, 5, 8),
(115, 5, 9),
(116, 5, 10),
(117, 5, 11),
(118, 5, 12),
(119, 5, 13),
(120, 5, 14),
(121, 5, 15),
(122, 5, 16),
(123, 5, 17),
(124, 5, 18),
(125, 5, 19),
(126, 5, 20),
(129, 6, 3),
(131, 6, 5),
(132, 6, 6),
(133, 6, 7),
(134, 6, 8),
(135, 6, 9),
(136, 6, 10),
(137, 6, 11),
(138, 6, 12),
(139, 6, 13),
(140, 6, 14),
(141, 6, 15),
(142, 6, 16),
(143, 6, 17),
(144, 6, 18),
(145, 6, 19),
(147, 7, 1),
(148, 7, 2),
(149, 7, 3),
(150, 7, 4),
(151, 7, 5),
(152, 7, 6),
(153, 7, 7),
(154, 7, 8),
(155, 7, 9),
(156, 7, 10),
(157, 7, 11),
(158, 7, 12),
(159, 7, 13),
(160, 7, 14),
(161, 7, 15),
(162, 7, 16),
(163, 7, 17),
(164, 7, 18),
(165, 7, 19),
(166, 7, 20),
(167, 8, 1),
(168, 8, 2),
(169, 8, 3),
(170, 8, 4),
(171, 8, 5),
(172, 8, 6),
(173, 8, 7),
(174, 8, 8),
(175, 8, 9),
(176, 8, 10),
(177, 8, 11),
(178, 8, 12),
(179, 8, 13),
(180, 8, 14),
(181, 8, 15),
(182, 8, 16),
(183, 8, 17),
(184, 8, 18),
(185, 8, 19),
(186, 8, 20),
(187, 9, 1),
(188, 9, 2),
(189, 9, 3),
(190, 9, 4),
(191, 9, 5),
(192, 9, 6),
(193, 9, 7),
(194, 9, 8),
(195, 9, 9),
(196, 9, 10),
(197, 9, 11),
(198, 9, 12),
(199, 9, 13),
(200, 9, 14),
(201, 9, 15),
(202, 9, 16),
(203, 9, 17),
(204, 9, 18),
(205, 9, 19),
(206, 9, 20),
(207, 10, 1),
(208, 10, 2),
(209, 10, 3),
(210, 10, 4),
(211, 10, 5),
(212, 10, 6),
(213, 10, 7),
(214, 10, 8),
(215, 10, 9),
(216, 10, 10),
(217, 10, 11),
(218, 10, 12),
(219, 10, 13),
(220, 10, 14),
(221, 10, 15),
(222, 10, 16),
(223, 10, 17),
(224, 10, 18),
(225, 10, 19),
(226, 10, 20),
(227, 11, 1),
(228, 11, 2),
(229, 11, 3),
(230, 11, 4),
(231, 11, 5),
(232, 11, 6),
(233, 11, 7),
(234, 11, 8),
(235, 11, 9),
(236, 11, 10),
(237, 11, 11),
(238, 11, 12),
(239, 11, 13),
(240, 11, 14),
(241, 11, 15),
(242, 11, 16),
(243, 11, 17),
(244, 11, 18),
(245, 11, 19),
(246, 11, 20),
(247, 12, 1),
(248, 12, 2),
(249, 12, 3),
(250, 12, 4),
(251, 12, 5),
(252, 12, 6),
(253, 12, 7),
(254, 12, 8),
(255, 12, 9),
(256, 12, 10),
(257, 12, 11),
(258, 12, 12),
(259, 12, 13),
(260, 12, 14),
(261, 12, 15),
(262, 12, 16),
(263, 12, 17),
(264, 12, 18),
(265, 12, 19),
(266, 12, 20),
(267, 13, 1),
(268, 13, 2),
(269, 13, 3),
(270, 13, 4),
(271, 13, 5),
(272, 13, 6),
(273, 13, 7),
(274, 13, 8),
(275, 13, 9),
(276, 13, 10),
(277, 13, 11),
(278, 13, 12),
(279, 13, 13),
(280, 13, 14),
(281, 13, 15),
(282, 13, 16),
(283, 13, 17),
(284, 13, 18),
(285, 13, 19),
(286, 13, 20),
(287, 14, 1),
(288, 14, 2),
(289, 14, 3),
(290, 14, 4),
(291, 14, 5),
(292, 14, 6),
(293, 14, 7),
(294, 14, 8),
(295, 14, 9),
(296, 14, 10),
(297, 14, 11),
(298, 14, 12),
(299, 14, 13),
(300, 14, 14),
(301, 14, 15),
(302, 14, 16),
(303, 14, 17),
(304, 14, 18),
(305, 14, 19),
(306, 14, 20),
(307, 15, 1),
(308, 15, 2),
(309, 15, 3),
(310, 15, 4),
(311, 15, 5),
(312, 15, 6),
(313, 15, 7),
(314, 15, 8),
(315, 15, 9),
(316, 15, 10),
(317, 15, 11),
(318, 15, 12),
(319, 15, 13),
(320, 15, 14),
(321, 15, 15),
(322, 15, 16),
(323, 15, 17),
(324, 15, 18),
(325, 15, 19),
(326, 15, 20),
(327, 16, 1),
(329, 16, 3),
(330, 16, 4),
(331, 16, 5),
(332, 16, 6),
(333, 16, 7),
(334, 16, 8),
(335, 16, 9),
(336, 16, 10),
(337, 16, 11),
(338, 16, 12),
(339, 16, 13),
(340, 16, 14),
(341, 16, 15),
(342, 16, 16),
(343, 16, 17),
(344, 16, 18),
(345, 16, 19),
(346, 16, 20),
(347, 17, 1),
(348, 17, 2),
(349, 17, 3),
(350, 17, 4),
(351, 17, 5),
(352, 17, 6),
(353, 17, 7),
(354, 17, 8),
(355, 17, 9),
(356, 17, 10),
(357, 17, 11),
(358, 17, 12),
(359, 17, 13),
(360, 17, 14),
(361, 17, 15),
(362, 17, 16),
(363, 17, 17),
(364, 17, 18),
(365, 17, 19),
(366, 17, 20),
(367, 18, 1),
(368, 18, 2),
(369, 18, 3),
(370, 18, 4),
(371, 18, 5),
(372, 18, 6),
(373, 18, 7),
(374, 18, 8),
(375, 18, 9),
(376, 18, 10),
(377, 18, 11),
(378, 18, 12),
(379, 18, 13),
(380, 18, 14),
(381, 18, 15),
(382, 18, 16),
(383, 18, 17),
(384, 18, 18),
(385, 18, 19),
(386, 18, 20),
(387, 19, 1),
(388, 19, 2),
(389, 19, 3),
(390, 19, 4),
(391, 19, 5),
(392, 19, 6),
(393, 19, 7),
(394, 19, 8),
(395, 19, 9),
(396, 19, 10),
(397, 19, 11),
(398, 19, 12),
(399, 19, 13),
(400, 19, 14),
(401, 19, 15),
(402, 19, 16),
(403, 19, 17),
(404, 19, 18),
(405, 19, 19),
(406, 19, 20),
(407, 20, 1),
(408, 20, 2),
(409, 20, 3),
(410, 20, 4),
(411, 20, 5),
(413, 20, 7),
(414, 20, 8),
(415, 20, 9),
(416, 20, 10),
(417, 20, 11),
(418, 20, 12),
(419, 20, 13),
(420, 20, 14),
(421, 20, 15),
(422, 20, 16),
(423, 20, 17),
(424, 20, 18),
(425, 20, 19),
(426, 20, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gifcodes`
--

CREATE TABLE `gifcodes` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'creditos',
  `creditos` int(11) NOT NULL DEFAULT '0',
  `used` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `giftcredits`
--

CREATE TABLE `giftcredits` (
  `id` int(11) NOT NULL,
  `foto_id` int(11) NOT NULL,
  `used` tinyint(1) NOT NULL,
  `given` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `giftcredits_weekly`
--

CREATE TABLE `giftcredits_weekly` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL COMMENT 'Usuario',
  `credits` smallint(11) NOT NULL COMMENT 'Creditos de regalo',
  `time` int(11) NOT NULL COMMENT 'Fecha'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Regalos Semanales.';

--
-- Volcado de datos para la tabla `giftcredits_weekly`
--

INSERT INTO `giftcredits_weekly` (`id`, `player_id`, `credits`, `time`) VALUES
(1, 1, 50, 1631221958),
(2, 2, 50, 1631221958),
(3, 3, 50, 1631221958),
(4, 4, 50, 1631221958),
(5, 5, 50, 1631221958),
(7, 7, 50, 1631221958),
(8, 8, 50, 1631221958),
(9, 9, 50, 1631221958),
(10, 10, 50, 1631221958),
(11, 11, 50, 1631221958),
(12, 12, 50, 1631221958),
(13, 13, 50, 1631221958),
(14, 14, 50, 1631221958),
(15, 15, 50, 1631221958),
(16, 16, 50, 1631221958),
(17, 17, 50, 1631221958),
(18, 18, 50, 1631221958),
(19, 19, 50, 1631221958),
(20, 20, 50, 1631221958),
(21, 21, 50, 1631221958),
(22, 22, 50, 1631221958),
(23, 23, 50, 1631221958),
(24, 24, 50, 1631221958),
(26, 26, 50, 1631563598),
(27, 6, 50, 1631804110),
(28, 25, 50, 1631804110);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'images/shoes/hats/-shoes.png',
  `price` int(11) NOT NULL DEFAULT '10',
  `usos` int(11) NOT NULL DEFAULT '1',
  `value` int(11) NOT NULL DEFAULT '100',
  `type` enum('hp','energy') COLLATE utf8_unicode_ci DEFAULT 'hp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `listasfotosbot`
--

CREATE TABLE `listasfotosbot` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajesprogramados`
--

CREATE TABLE `mensajesprogramados` (
  `id` int(10) UNSIGNED NOT NULL,
  `player_id` int(11) NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci,
  `rutadefoto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Tipo de Mensaje Programado (DEFAULT 1)',
  `time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `nota` text COLLATE utf8mb4_unicode_ci,
  `created` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id`, `uid`, `title`, `nota`, `created`) VALUES
(1, 6, 'Titulo', 'Escribe aqui', 1631221937);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_fotosnuevas`
--

CREATE TABLE `notificaciones_fotosnuevas` (
  `id` int(11) NOT NULL,
  `player_notificador` int(11) NOT NULL,
  `player_notificado` int(11) NOT NULL,
  `visto` varchar(2) NOT NULL DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `notificaciones_fotosnuevas`
--

INSERT INTO `notificaciones_fotosnuevas` (`id`, `player_notificador`, `player_notificado`, `visto`) VALUES
(1, 1, 2, 'no'),
(2, 1, 2, 'no'),
(4, 20, 2, 'no'),
(5, 20, 3, 'no'),
(6, 20, 4, 'no'),
(84, 6, 4, 'no'),
(9, 20, 7, 'no'),
(10, 20, 8, 'no'),
(11, 20, 9, 'no'),
(12, 20, 10, 'no'),
(13, 20, 11, 'no'),
(14, 20, 12, 'no'),
(15, 20, 13, 'no'),
(16, 20, 14, 'no'),
(17, 20, 15, 'no'),
(131, 1, 2, 'no'),
(19, 20, 17, 'no'),
(20, 20, 18, 'no'),
(21, 20, 19, 'no'),
(124, 1, 4, 'no'),
(23, 20, 2, 'no'),
(24, 20, 3, 'no'),
(25, 20, 4, 'no'),
(83, 6, 3, 'no'),
(28, 20, 7, 'no'),
(29, 20, 8, 'no'),
(30, 20, 9, 'no'),
(31, 20, 10, 'no'),
(32, 20, 11, 'no'),
(33, 20, 12, 'no'),
(34, 20, 13, 'no'),
(35, 20, 14, 'no'),
(36, 20, 15, 'no'),
(38, 20, 17, 'no'),
(39, 20, 18, 'no'),
(40, 20, 19, 'no'),
(123, 1, 3, 'no'),
(43, 20, 2, 'no'),
(44, 20, 3, 'no'),
(45, 20, 4, 'no'),
(82, 6, 2, 'no'),
(48, 20, 7, 'no'),
(49, 20, 8, 'no'),
(50, 20, 9, 'no'),
(51, 20, 10, 'no'),
(52, 20, 11, 'no'),
(53, 20, 12, 'no'),
(54, 20, 13, 'no'),
(55, 20, 14, 'no'),
(56, 20, 15, 'no'),
(58, 20, 17, 'no'),
(59, 20, 18, 'no'),
(60, 20, 19, 'no'),
(122, 1, 2, 'no'),
(62, 20, 2, 'no'),
(63, 20, 3, 'no'),
(64, 20, 4, 'no'),
(171, 6, 3, 'no'),
(67, 20, 7, 'no'),
(68, 20, 8, 'no'),
(69, 20, 9, 'no'),
(70, 20, 10, 'no'),
(71, 20, 11, 'no'),
(72, 20, 12, 'no'),
(73, 20, 13, 'no'),
(74, 20, 14, 'no'),
(75, 20, 15, 'no'),
(128, 1, 4, 'no'),
(77, 20, 17, 'no'),
(78, 20, 18, 'no'),
(79, 20, 19, 'no'),
(120, 1, 2, 'no'),
(87, 6, 2, 'no'),
(88, 6, 3, 'no'),
(89, 6, 4, 'no'),
(92, 6, 7, 'no'),
(93, 6, 8, 'no'),
(94, 6, 9, 'no'),
(95, 6, 10, 'no'),
(96, 6, 11, 'no'),
(97, 6, 12, 'no'),
(98, 6, 13, 'no'),
(99, 6, 14, 'no'),
(100, 6, 15, 'no'),
(127, 1, 3, 'no'),
(102, 6, 17, 'no'),
(103, 6, 18, 'no'),
(104, 6, 19, 'no'),
(133, 1, 4, 'no'),
(106, 6, 7, 'no'),
(107, 6, 8, 'no'),
(108, 6, 9, 'no'),
(109, 6, 10, 'no'),
(110, 6, 11, 'no'),
(111, 6, 12, 'no'),
(112, 6, 13, 'no'),
(113, 6, 14, 'no'),
(114, 6, 15, 'no'),
(126, 1, 2, 'no'),
(116, 6, 17, 'no'),
(117, 6, 18, 'no'),
(118, 6, 19, 'no'),
(132, 1, 3, 'no'),
(136, 1, 7, 'no'),
(137, 1, 8, 'no'),
(138, 1, 9, 'no'),
(139, 1, 10, 'no'),
(140, 1, 11, 'no'),
(141, 1, 12, 'no'),
(142, 1, 13, 'no'),
(143, 1, 14, 'no'),
(144, 1, 15, 'no'),
(145, 1, 16, 'no'),
(146, 1, 17, 'no'),
(147, 1, 18, 'no'),
(148, 1, 19, 'no'),
(149, 1, 20, 'no'),
(150, 1, 2, 'no'),
(151, 1, 3, 'no'),
(152, 1, 4, 'no'),
(169, 6, 3, 'no'),
(155, 1, 7, 'no'),
(156, 1, 8, 'no'),
(157, 1, 9, 'no'),
(158, 1, 10, 'no'),
(159, 1, 11, 'no'),
(160, 1, 12, 'no'),
(161, 1, 13, 'no'),
(162, 1, 14, 'no'),
(163, 1, 15, 'no'),
(164, 1, 16, 'no'),
(165, 1, 17, 'no'),
(166, 1, 18, 'no'),
(167, 1, 19, 'no'),
(168, 1, 20, 'no'),
(200, 6, 3, 'no'),
(174, 6, 7, 'no'),
(175, 6, 8, 'no'),
(176, 6, 9, 'no'),
(177, 6, 10, 'no'),
(178, 6, 11, 'no'),
(179, 6, 12, 'no'),
(180, 6, 13, 'no'),
(181, 6, 14, 'no'),
(182, 6, 15, 'no'),
(183, 6, 16, 'no'),
(184, 6, 17, 'no'),
(185, 6, 18, 'no'),
(186, 6, 19, 'no'),
(187, 6, 7, 'no'),
(188, 6, 8, 'no'),
(189, 6, 9, 'no'),
(190, 6, 10, 'no'),
(191, 6, 11, 'no'),
(192, 6, 12, 'no'),
(193, 6, 13, 'no'),
(194, 6, 14, 'no'),
(195, 6, 15, 'no'),
(196, 6, 16, 'no'),
(197, 6, 17, 'no'),
(198, 6, 18, 'no'),
(199, 6, 19, 'no'),
(202, 6, 3, 'no'),
(205, 6, 7, 'no'),
(206, 6, 8, 'no'),
(207, 6, 9, 'no'),
(208, 6, 10, 'no'),
(209, 6, 11, 'no'),
(210, 6, 12, 'no'),
(211, 6, 13, 'no'),
(212, 6, 14, 'no'),
(213, 6, 15, 'no'),
(214, 6, 16, 'no'),
(215, 6, 17, 'no'),
(216, 6, 18, 'no'),
(217, 6, 19, 'no'),
(218, 6, 7, 'no'),
(219, 6, 8, 'no'),
(220, 6, 9, 'no'),
(221, 6, 10, 'no'),
(222, 6, 11, 'no'),
(223, 6, 12, 'no'),
(224, 6, 13, 'no'),
(225, 6, 14, 'no'),
(226, 6, 15, 'no'),
(227, 6, 16, 'no'),
(228, 6, 17, 'no'),
(229, 6, 18, 'no'),
(230, 6, 19, 'no');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_suscripcionesvencidas`
--

CREATE TABLE `notificaciones_suscripcionesvencidas` (
  `id` int(11) NOT NULL,
  `usera` int(11) NOT NULL COMMENT 'Suscriptor',
  `userb` int(11) NOT NULL COMMENT 'Suscripto a',
  `see` tinyint(1) NOT NULL COMMENT 'Visto 0-Noi / 1-Si'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nuevochat_mensajes`
--

CREATE TABLE `nuevochat_mensajes` (
  `id` int(11) NOT NULL,
  `id_chat` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `toid` int(11) NOT NULL DEFAULT '0',
  `mensaje` text COLLATE utf8mb4_unicode_ci,
  `leido` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no' COMMENT 'si/no',
  `leido_to` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no' COMMENT 'Estado de lectura, para al que se le envio el mesaje',
  `time` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `foto` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'No' COMMENT 'Yes / No',
  `rutadefoto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nuevochat_rooms`
--

CREATE TABLE `nuevochat_rooms` (
  `id` int(11) NOT NULL,
  `player1` int(11) NOT NULL,
  `player2` int(11) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `state` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open' COMMENT 'Estado del chat (Abierto: open/Cerrado: id de la persona que lo bloqueo).',
  `mensaje_chatbot` int(1) NOT NULL DEFAULT '0' COMMENT ' 0 = no se ha enviado el mensaje del bot o no aplica / 1 = ya se envió el mensaje del bot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packscomprados`
--

CREATE TABLE `packscomprados` (
  `id` int(11) NOT NULL,
  `foto_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packsenventa`
--

CREATE TABLE `packsenventa` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `precio` int(11) NOT NULL,
  `imagens` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `image_count` int(11) DEFAULT NULL COMMENT 'Cantidad de Imagenes',
  `video_length` int(11) DEFAULT NULL COMMENT 'Duracion de Video en UNIX',
  `descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `linkdedescarga` varchar(555) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ventasrealizadas` int(11) NOT NULL DEFAULT '0',
  `hidetochat` varchar(10) NOT NULL DEFAULT '4',
  `visible` text NOT NULL,
  `content_to` varchar(20) NOT NULL DEFAULT 'hetero'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_list`
--

CREATE TABLE `payment_list` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `paid` varchar(50) NOT NULL,
  `date` varchar(28) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pets`
--

CREATE TABLE `pets` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'images/pets/',
  `frases` text COLLATE utf8mb4_unicode_ci,
  `creditos` int(11) NOT NULL,
  `respect` int(11) NOT NULL,
  `hp` int(11) NOT NULL DEFAULT '1000',
  `energy` int(11) NOT NULL DEFAULT '1000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `photo_gift_credits`
--

CREATE TABLE `photo_gift_credits` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL COMMENT 'ID De la Foto',
  `given` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Usuarios a los ya que se les regalo créditos de esta imagen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players`
--

CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '8784e9a44d7aeeb228fecebb2d3c691f9dd19334d13d7bac5c3363e757d815f4',
  `permission_upload` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'images/icons/default-avatar.jpg',
  `cover-page` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `habla` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Español',
  `escribeme` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Español',
  `notificacion_encuestas` int(11) DEFAULT '0',
  `notificacion_pack` int(11) DEFAULT '0',
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Player',
  `gender` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT 'hombre/mujer',
  `description` text COLLATE utf8_unicode_ci,
  `baneado` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `follower` text COLLATE utf8_unicode_ci,
  `followers` text COLLATE utf8_unicode_ci,
  `creditos` int(11) NOT NULL DEFAULT '0',
  `eCreditos` int(11) NOT NULL DEFAULT '0',
  `puntos` int(11) NOT NULL,
  `time_joined` int(11) NOT NULL COMMENT 'Fecha de Registro',
  `timeonline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `botontime` int(11) NOT NULL DEFAULT '0',
  `countboton` int(11) NOT NULL DEFAULT '0',
  `perfiloculto` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no' COMMENT 'si/no',
  `hidden_for_old` int(11) NOT NULL COMMENT 'Perfil oculto para usuarios registrados antes de la fecha ingresada',
  `referer_id` int(11) NOT NULL DEFAULT '0' COMMENT 'id del referer',
  `ipaddres` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `Likesdados` int(11) NOT NULL DEFAULT '0',
  `LikesRecibidos` int(11) NOT NULL DEFAULT '0',
  `tiempoderespuesta` int(11) NOT NULL COMMENT 'solo aplica para bots',
  `respuesta_automatica` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n/a' COMMENT 'esto solo aplica para bots',
  `id_listadefotos` int(11) NOT NULL COMMENT 'solo aplica para bots',
  `refcodigo` int(6) NOT NULL DEFAULT '0' COMMENT 'este es el codigo para atraer referidos',
  `theme` int(6) DEFAULT '1',
  `registerfrom` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'my-great-talent',
  `hidetochat` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `category` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'hetero'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `players`
--

INSERT INTO `players` (`id`, `username`, `password`, `permission_upload`, `email`, `avatar`, `cover-page`, `habla`, `escribeme`, `notificacion_encuestas`, `notificacion_pack`, `role`, `gender`, `description`, `baneado`, `follower`, `followers`, `creditos`, `eCreditos`, `puntos`, `time_joined`, `timeonline`, `botontime`, `countboton`, `perfiloculto`, `hidden_for_old`, `referer_id`, `ipaddres`, `Likesdados`, `LikesRecibidos`, `tiempoderespuesta`, `respuesta_automatica`, `id_listadefotos`, `refcodigo`, `theme`, `registerfrom`, `hidetochat`, `category`) VALUES
(1, 'players l', '$2y$10$HKsDChWxqXt1fAv8MNhUJuUutgO6F.3ixzmlLWzakvM5BsJJ4hKfO', 1, 'players@gmail.com', 'images/avatars/players.jpg?1630041259', '', 'Español', 'Español', 0, 0, 'Admin', 'mujer', '', NULL, '{\"2\":1630998174}', '{\"6\":1630953480}', 0, 100, 0, 0, '1630599881', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 580790, 1, 'my-great-talent', 'no', 'hetero'),
(2, 'sebas', 'e157916a88ead7b7d5733f16f78a5de77527a87739982f2aeb512058b2e00e0a', 0, 'sebas@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Admin', 'hombre', NULL, NULL, NULL, '{\"1\":1630998174,\"6\":1631504651}', 0, 1028410, 0, 0, '1630036061', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 526896, 0, 'my-great-talent', 'no', 'hetero'),
(3, 'Pedro', 'e157916a88ead7b7d5733f16f78a5de77527a87739982f2aeb512058b2e00e0a', 1, 'pedro@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 1, 0, 0, 0, '1630068752', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 986462, 0, 'my-great-talent', 'no', 'hetero'),
(4, 'Momom', 'e157916a88ead7b7d5733f16f78a5de77527a87739982f2aeb512058b2e00e0a', 0, 'momom@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630068803', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 416480, 0, 'my-great-talent', 'no', 'hetero'),
(5, 'womow', 'e157916a88ead7b7d5733f16f78a5de77527a87739982f2aeb512058b2e00e0a', 1, 'ssmoms@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, '{\"6\":1632208664}', '{\"6\":1632207171}', 0, 5600, 0, 0, '1631606600', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 719765, 0, 'my-great-talent', 'no', 'hetero'),
(6, 'extreemer', '$2y$10$vuYfTYftHMEOPYCMbIcB3eSt7i4Cj2rHcoFwL35jL00cFyvBSvBfK', 1, 'hola@gmail.com', 'images/avatars/extreemer.png?1631766835', 'shout/cover-pages/cover-page-9601566332043840extreemer.png', 'Español', 'Español', 0, 0, 'Admin', 'hombre', 'Hola como estas', NULL, '{\"2\":1631504651,\"5\":1632207171}', '{\"5\":1632208664}', 10001, 99955240, 0, 0, '1632011644', 0, 0, 'no', 0, 3, '::1', 0, 0, 0, 'n/a', 0, 432138, 0, 'my-great-talent', 'no', 'hetero'),
(7, 'undefined', '$2y$10$XXuzOiWbPNs6WradrlbcleD1tYDaHbaGFku.g2txheaC4Je42e8uO', 1, 'gilmerfranco@yahoo.com.ar', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630184380', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 681910, 1, 'my-great-talent', 'no', 'hetero'),
(8, 'Franko', '$2y$10$WBCHErna4nFWX..oEWONSe3wCll6B6T5kpWTcsN8oGL68jkQV9gm2', 1, 'papapasexopornografico@mail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630197108', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 577397, 1, 'my-great-talent', 'no', 'hetero'),
(9, 'MOMOM0', '$2y$10$SsBP34WqvxenNhulr.gCtOo.HiPxppiN.tMTokmO.3l8wQrJfvq9m', 1, 'momo@ra', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630196637', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 996838, 1, 'my-great-talent', 'no', 'hetero'),
(10, 'Antonio', '$2y$10$NojC/G.JDIJVxYxBCSHcpOdLUbWmp0yO27v91WCAHCJzn2PUlX.C.', 1, 'antonio@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630196789', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 634858, 1, 'my-great-talent', 'no', 'hetero'),
(11, 'extreemere', '$2y$10$Y8cnnUI/VHLALLpIntQlie1njg0/FiYEDoXfHLLWh4qNbxtF7gkMO', 1, 'gilmerfranco@yahoo.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630196921', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 832171, 1, 'my-great-talent', 'no', 'hetero'),
(12, 'FFF', '$2y$10$waKgsgSeQk4DQF.clEdIFuuZICk82OhsIP7svElsga27s7Xov3WEa', 1, 'gmgmg.com@d.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630197076', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 425855, 1, 'my-great-talent', 'no', 'hetero'),
(13, 'Party', '$2y$10$7PVAmqpp/6IcjLqwbvDD8eGh00sPnHPAbViGvgEW8MLEXdSDyQ2Oa', 1, 'party@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630197738', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 238119, 1, 'my-great-talent', 'no', 'hetero'),
(14, 'Try', '$2y$10$qH1TfjLMe5dC2R4in5AYA.GM.H8G.paM8ov9RLPhCzdsNsoTCkA5m', 1, 'catch@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630197831', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 794136, 1, 'my-great-talent', 'no', 'hetero'),
(15, 'Comosesiente', '$2y$10$9e8YmkDl9d3vf.LW6ibxnOm0agVMQ/YlqB4Miq2PgT.jSwT99bWR.', 1, 'comosesiente@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630197971', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 301507, 1, 'my-great-talent', 'no', 'hetero'),
(16, 'Francisco', '$2y$10$BFpVdaCgACX6.XNBdQJQrO/NUTujR.Wr5cS0JJ/JehLNg/2DpQBYu', 1, 'francisco@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, '', 0, 2800, 0, 0, '1630354170', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 812554, 1, 'my-great-talent', 'no', 'hetero'),
(17, 'FFFF', '$2y$10$P8h3sjgR0myRerNkVS0TA.orzHWEwjWkDokENhwAVeDK1Krh0acCe', 1, 'FFF@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630198414', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 146747, 1, 'my-great-talent', 'no', 'hetero'),
(18, 'MMMAMA', '$2y$10$5GlZZfmYwfuBagGTvo/QKuc0avy.RMOEE1cXYMM/tQ8yhQkyzSfzO', 1, 'mamamama@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630198510', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 657260, 1, 'my-great-talent', 'no', 'hetero'),
(19, 'location', '$2y$10$pFsQKarPiZJBDi1M0uWn0.YR5B5wP1XlFsD.XzKjpS0Z5ANCH5tva', 1, 'location@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'mujer', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630199194', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 441818, 1, 'my-great-talent', 'no', 'hetero'),
(20, 'Test', '$2y$10$ndOVvMZrJP0yAmxb6ixuteCKlCPiqJF8PpMOA.SlWUfnIMV16DlK6', 1, 'test@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Admin', 'hombre', NULL, NULL, '', NULL, 1000000, 981900, 0, 0, '1630343578', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 593259, 1, 'my-great-talent', 'no', 'hetero'),
(21, 'Alter', '$2y$10$OwHuxl3yUrP7iKajKZAit./xV4mD8wJ52qsNRSaeZQbGJOhdQ.mw.', 0, 'alter@gmail.com', 'images/icons/default-avatar.jpg', 'shout/cover-pages/cover-page-277451676181164Alter.jpg', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630620440', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 545464, 1, 'my-great-talent', 'no', 'hetero'),
(22, 'Nomre', '$2y$10$AidPvU48/nU/vqrMfBait.2jgSFMZOrk1QCBCCRO38VB/LcHOdspK', 0, 'nomre', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630611705', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 152023, 1, 'my-great-talent', 'no', 'hetero'),
(23, 'lavacalola', '$2y$10$BB0Ca0QG5MK1USCBuIwxx./QNAr0r.SPXCAy3eHFtLIy3xuCJx0EG', 0, 'lavacalola@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630611816', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 309147, 1, 'my-great-talent', 'no', 'hetero'),
(24, 'Seven', '$2y$10$NKxRZpDG9sWiAB3MhOiwPe4WaYRdwBycvexTuEy1Le3yUyxh24ocG', 0, 'seven@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1630614986', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 291857, 1, 'my-great-talent', 'no', 'hetero'),
(25, 'LAMMA', '$2y$10$Wpoqd2uIrP/fCs6IcBfMQu7YFd5X7IR.YAo9odq9FXcY9GBwTeyOO', 0, 'LMA@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 50, 0, 0, '1631563232', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 774942, 1, 'my-great-talent', 'no', 'hetero'),
(26, 'ssoj', '$2y$10$y9ljrmv8gVHxyi4Xux1ha.Zl4YyNSe3gATJKqkDAQHtlyYBuiauRq', 0, 'snmsal@gmail.com', 'images/icons/default-avatar.jpg', '', 'Español', 'Español', 0, 0, 'Player', 'hombre', NULL, NULL, NULL, NULL, 0, 0, 0, 0, '1631563639', 0, 0, 'no', 0, 0, '::1', 0, 0, 0, 'n/a', 0, 819421, 1, 'my-great-talent', 'no', 'hetero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players_farms`
--

CREATE TABLE `players_farms` (
  `id` int(11) NOT NULL,
  `farms_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players_farm_items`
--

CREATE TABLE `players_farm_items` (
  `id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `time` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players_movements`
--

CREATE TABLE `players_movements` (
  `id` int(11) NOT NULL,
  `player_id` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID del Usuario',
  `credits_before` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Créditos Antes',
  `in_out` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de movimiento Ingreso/Egreso',
  `credits_after` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Créditos Después',
  `description` tinyint(4) NOT NULL COMMENT 'ID de la descripción o tipo de movimiento Ejem: "Juan compró un Pack"',
  `time` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Fecha'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `players_movements`
--

INSERT INTO `players_movements` (`id`, `player_id`, `credits_before`, `in_out`, `credits_after`, `description`, `time`) VALUES
(1, '6', '100000000', '-', '99999990', 5, '1630214572'),
(2, '2', '1000000', '+', '1000010', 5, '1630214573'),
(3, '20', '1000000', '-', '999900', 5, '1630279372'),
(4, '2', '1000010', '+', '1000110', 5, '1630279374'),
(5, '20', '999900', '-', '998900', 5, '1630280072'),
(6, '2', '1000110', '+', '1001110', 5, '1630280072'),
(7, '20', '998900', '-', '997900', 5, '1630280080'),
(8, '2', '1001110', '+', '1002110', 5, '1630280080'),
(9, '20', '997900', '-', '992900', 5, '1630282906'),
(10, '2', '1002110', '+', '1007110', 5, '1630282906'),
(11, '20', '992900', '-', '987900', 5, '1630282958'),
(12, '2', '1007110', '+', '1012110', 5, '1630282958'),
(13, '20', '987900', '-', '987800', 5, '1630286191'),
(14, '2', '1012110', '+', '1012210', 5, '1630286191'),
(15, '20', '987800', '-', '987700', 5, '1630287030'),
(16, '1', '0', '+', '100', 5, '1630287030'),
(17, '20', '987700', '-', '987200', 5, '1630287044'),
(18, '1', '100', '+', '600', 5, '1630287044'),
(19, '20', '987200', '-', '987100', 5, '1630287579'),
(20, '1', '600', '+', '700', 5, '1630287579'),
(21, '20', '987100', '-', '982100', 5, '1630287700'),
(22, '6', '99999990', '+', '100004990', 5, '1630287700'),
(23, '20', '982100', '-', '982000', 5, '1630288152'),
(24, '6', '100004990', '+', '100005090', 5, '1630288152'),
(25, '20', '982000', '-', '981900', 5, '1630289275'),
(26, '6', '100005090', '+', '100005190', 5, '1630289275'),
(27, '6', '100005190', '-', '100002190', 5, '1630346767'),
(28, '16', '0', '+', '1800', 5, '1630346767'),
(29, '6', '100002190', '-', '100001190', 5, '1630346894'),
(30, '16', '1800', '+', '2200', 5, '1630346894'),
(31, '6', '100001190', '-', '100000190', 5, '1630346911'),
(32, '16', '2200', '+', '2800', 5, '1630346911'),
(33, '6', '100000190', '-', '99999190', 5, '1630348680'),
(34, '1', '700', '+', '1300', 5, '1630348680'),
(35, '6', '99999140', '-', '99998140', 5, '1630353811'),
(36, '1', '1300', '+', '1900', 5, '1630353813'),
(37, '6', '99998140', '-', '99997140', 5, '1630353963'),
(38, '1', '1900', '+', '2500', 5, '1630353963'),
(39, '6', '99997140', '-', '99996140', 5, '1630354096'),
(40, '1', '2500', '+', '3100', 5, '1630354096'),
(41, '6', '99996140', '-', '99986140', 5, '1630354154'),
(42, '1', '3100', '+', '9100', 5, '1630354154'),
(43, '1', '9100', '-', '4100', 5, '1630365510'),
(44, '1', '4100', '-', '3100', 5, '1630393373'),
(45, '2', '1012210', '+', '1012810', 5, '1630393374'),
(46, '1', '3100', '-', '1100', 5, '1630596852'),
(47, '2', '1012810', '+', '1014010', 5, '1630596852'),
(48, '1', '1100', '-', '100', 5, '1630596924'),
(49, '2', '1014010', '+', '1014610', 5, '1630596924'),
(50, '6', '99986140', '-', '99976140', 5, '1630899850'),
(51, '2', '1014610', '+', '1020610', 5, '1630899850'),
(52, '6', '99976140', '+', '99976190', 12, '1631223483'),
(53, '6', '99976190', '+', '99976240', 12, '1631223732'),
(54, '6', '99976240', '+', '99976290', 12, '1631223828'),
(55, '6', '99976290', '+', '99976340', 12, '1631223846'),
(56, '6', '99976340', '+', '99976390', 12, '1631223908'),
(57, '6', '99976390', '+', '99976440', 12, '1631223976'),
(58, '6', '99976440', '+', '99976490', 12, '1631224041'),
(59, '6', '99976490', '+', '99976540', 12, '1631224051'),
(60, '6', '99976540', '+', '99976590', 12, '1631224255'),
(61, '6', '99976590', '-', '99973590', 5, '1631224290'),
(62, '2', '1020610', '+', '1022410', 5, '1631224290'),
(63, '6', '99973590', '-', '99963590', 5, '1631224306'),
(64, '2', '1022410', '+', '1028410', 5, '1631224306'),
(65, '6', '99963590', '+', '99963640', 12, '1631224551'),
(66, '25', '0', '+', '50', 12, '1631563003'),
(67, '6', '99963640', '-', '99953640', 5, '1631602370'),
(68, '5', '0', '+', '6000', 5, '1631602370'),
(69, '6', '99953640', '-', '99952640', 5, '1631603349'),
(70, '5', '6000', '+', '6600', 5, '1631603349'),
(71, '5', '6600', '-', '5600', 5, '1631603864'),
(72, '6, extreemer', '99952640', '+', '99953240', 5, '1631603864'),
(73, '6', '99953240', '+', '99954240', 3, '1631752864'),
(74, '6', '99954240', '+', '99955240', 3, '1631752912');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players_namesactions`
--

CREATE TABLE `players_namesactions` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL COMMENT 'ID del Usuario',
  `player_add` int(11) NOT NULL COMMENT 'ID del usuario que lo agrego',
  `time` int(16) NOT NULL COMMENT 'Fecha de agregado'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `players_namesactions`
--

INSERT INTO `players_namesactions` (`id`, `player_id`, `player_add`, `time`) VALUES
(1, 1, 6, 1632000364),
(2, 2, 6, 1632000364),
(3, 8, 6, 1632000364),
(4, 10, 6, 1632000364),
(5, 13, 6, 1632000364),
(6, 16, 6, 1632000364),
(7, 18, 6, 1632000364),
(8, 19, 6, 1632000364),
(9, 21, 6, 1632000364),
(10, 23, 6, 1632000364),
(11, 25, 6, 1632000364);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players_notifications`
--

CREATE TABLE `players_notifications` (
  `id` int(11) NOT NULL,
  `toid` int(11) NOT NULL,
  `fromid` int(11) NOT NULL,
  `not_key` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de Notificacion',
  `action` int(11) DEFAULT NULL COMMENT 'Si es un Pack, almacena el id. Lo mismo con cualquier otra accion.',
  `read_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `players_notifications`
--

INSERT INTO `players_notifications` (`id`, `toid`, `fromid`, `not_key`, `action`, `read_time`) VALUES
(1, 16, 2, 'newDonation', 1000, '1630346664'),
(2, 2, 20, 'newDonation', 100, '0'),
(3, 2, 20, 'newDonation', 1000, '0'),
(4, 2, 20, 'newDonation', 1000, '0'),
(5, 2, 20, 'newDonation', 5000, '0'),
(6, 2, 20, 'newDonation', 5000, '0'),
(7, 2, 20, 'newDonation', 100, '0'),
(8, 1, 20, 'newDonation', 100, '1630354177'),
(9, 1, 20, 'newDonation', 500, '1630354177'),
(10, 1, 20, 'newDonation', 100, '1630354177'),
(14, 16, 6, 'newDonation', 3000, '0'),
(15, 16, 6, 'newDonation', 1000, '0'),
(16, 16, 6, 'newDonation', 1000, '0'),
(22, 0, 1, 'newDonation', 5000, '0'),
(23, 2, 1, 'newDonation', 1000, '0'),
(24, 2, 1, 'newDonation', 2000, '0'),
(25, 2, 1, 'newDonation', 1000, '0'),
(27, 1, 0, 'giftWeekly', 50, '0'),
(28, 2, 0, 'giftWeekly', 50, '0'),
(29, 3, 0, 'giftWeekly', 50, '0'),
(30, 4, 0, 'giftWeekly', 50, '0'),
(31, 5, 0, 'giftWeekly', 50, '1631603437'),
(33, 7, 0, 'giftWeekly', 50, '0'),
(34, 8, 0, 'giftWeekly', 50, '0'),
(35, 9, 0, 'giftWeekly', 50, '0'),
(36, 10, 0, 'giftWeekly', 50, '0'),
(37, 11, 0, 'giftWeekly', 50, '0'),
(38, 12, 0, 'giftWeekly', 50, '0'),
(39, 13, 0, 'giftWeekly', 50, '0'),
(40, 14, 0, 'giftWeekly', 50, '0'),
(41, 15, 0, 'giftWeekly', 50, '0'),
(42, 16, 0, 'giftWeekly', 50, '0'),
(43, 17, 0, 'giftWeekly', 50, '0'),
(44, 18, 0, 'giftWeekly', 50, '0'),
(45, 19, 0, 'giftWeekly', 50, '0'),
(46, 20, 0, 'giftWeekly', 50, '0'),
(47, 21, 0, 'giftWeekly', 50, '0'),
(48, 22, 0, 'giftWeekly', 50, '0'),
(49, 23, 0, 'giftWeekly', 50, '0'),
(50, 24, 0, 'giftWeekly', 50, '0'),
(62, 26, 0, 'giftWeekly', 50, '0'),
(63, 5, 6, 'newDonation', 10000, '1631603437'),
(64, 5, 6, 'newDonation', 1000, '1631603437'),
(65, 6, 5, 'newDonation', 1000, '1631752896'),
(66, 6, 0, 'giftWeekly', 50, '1631804114'),
(67, 25, 0, 'giftWeekly', 50, '0'),
(68, 2, 6, 'newAmistad', NULL, '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players_recommendations`
--

CREATE TABLE `players_recommendations` (
  `id` int(11) NOT NULL,
  `fromid` mediumint(10) NOT NULL COMMENT 'ID de la persona que recomienda',
  `toid` mediumint(10) NOT NULL COMMENT 'ID del recomendado',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `players_recommendations`
--

INSERT INTO `players_recommendations` (`id`, `fromid`, `toid`, `time`) VALUES
(1, 6, 10, 1631596032);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_colecciones`
--

CREATE TABLE `player_colecciones` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `coleccion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_comments`
--

CREATE TABLE `player_comments` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `galeria_id` int(11) NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(555) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `player_comments`
--

INSERT INTO `player_comments` (`id`, `author_id`, `galeria_id`, `date`, `time`, `comment`) VALUES
(1, 6, 7, '30 August 2021', '11:11', 'Veracidad'),
(2, 6, 7, '30 August 2021', '11:11', 'ssss');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_items`
--

CREATE TABLE `player_items` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_items_bought`
--

CREATE TABLE `player_items_bought` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_megusta`
--

CREATE TABLE `player_megusta` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `galeria_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_pets`
--

CREATE TABLE `player_pets` (
  `id` int(10) UNSIGNED NOT NULL,
  `player_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `live` int(11) DEFAULT '1',
  `hp` int(11) NOT NULL DEFAULT '1000',
  `energy` int(11) NOT NULL DEFAULT '1000',
  `bonus` int(11) NOT NULL DEFAULT '0',
  `update_bonus` int(11) NOT NULL DEFAULT '0',
  `xp` int(11) NOT NULL DEFAULT '0',
  `nivel` int(11) NOT NULL DEFAULT '0',
  `profile` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polls`
--

CREATE TABLE `polls` (
  `id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `users_votes` text,
  `questions` text,
  `created` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `userreportado` int(11) NOT NULL,
  `mensaje` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `time` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestasbot_enespera`
--

CREATE TABLE `respuestasbot_enespera` (
  `id` int(11) NOT NULL,
  `bot_id` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_id` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `toid` int(11) NOT NULL,
  `mensaje` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `respuesta_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_automatica`
--

CREATE TABLE `respuesta_automatica` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `pregunta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `respuesta` text COLLATE utf8mb4_unicode_ci,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_sala`
--

CREATE TABLE `respuesta_sala` (
  `id` int(10) UNSIGNED NOT NULL,
  `chat_room` int(11) NOT NULL DEFAULT '0',
  `pregunta_id` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `publicado` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `retiros`
--

CREATE TABLE `retiros` (
  `id` int(11) NOT NULL,
  `usuario` int(11) NOT NULL COMMENT 'id del usuario',
  `metodo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `identificacion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `monto` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pendiente',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'vCity',
  `site` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '/' COMMENT 'poner / si es un dominio y si es un subdominio ingresar /nombredelsubdominio/',
  `name` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT 'vcity' COMMENT 'nombre para los emails, por ejemplo: vcity',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'vCity - Online Browser Game',
  `montoboton` int(11) NOT NULL DEFAULT '0' COMMENT 'monto de creditos que se da cada vez que tocas el boton',
  `mostrarprimermensaje` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'si' COMMENT 'si/no | esto se refiere a la previsualizacion de 1 primer mensaje el la lista de conversaciones',
  `linkparacopiar` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'https://linkparacopiar.com',
  `costoporchat` int(11) NOT NULL DEFAULT '10' COMMENT 'el costo en creditos a cobrar cada vez que se inicia un nuevo chat',
  `minToDonate` int(11) NOT NULL,
  `bonoref` int(11) NOT NULL DEFAULT '0',
  `limit_actions` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `limit_unlogged_users` tinyint(1) NOT NULL COMMENT 'Premitir/Denegar La entrada a los usuarios no logueados'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`id`, `title`, `site`, `name`, `description`, `montoboton`, `mostrarprimermensaje`, `linkparacopiar`, `costoporchat`, `minToDonate`, `bonoref`, `limit_actions`, `limit_unlogged_users`) VALUES
(1, 'chat', '/otrochat/', 'chat', 'consigue nuevos amigos', 10, 'si', 'https://play.google.com/store/apps/details?id=com.bellas.gram.app.android', 50, 1000, 1, 'si', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventascompradas`
--

CREATE TABLE `ventascompradas` (
  `id` int(11) NOT NULL,
  `foto_id` int(11) NOT NULL,
  `comprador_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventasenventa`
--

CREATE TABLE `ventasenventa` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `precio` int(11) NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `linkdedescarga` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ventasrealizadas` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `welcomechat`
--

CREATE TABLE `welcomechat` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `welcomechat` varchar(4) DEFAULT NULL COMMENT 'Si ya se dio el mensaje de bienvenida/si'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `welcomechat`
--

INSERT INTO `welcomechat` (`id`, `userid`, `welcomechat`) VALUES
(1, 2, 'si'),
(2, 1, 'si'),
(3, 4, 'si'),
(4, 5, 'si'),
(5, 6, 'si'),
(6, 7, 'si'),
(7, 8, 'si'),
(8, 9, 'si'),
(9, 12, 'si'),
(10, 13, 'si'),
(11, 14, 'si'),
(12, 15, 'si'),
(13, 16, 'si'),
(14, 17, 'si'),
(15, 19, 'si'),
(16, 20, 'si'),
(17, 21, 'si'),
(18, 24, 'si'),
(19, 25, 'si'),
(20, 26, 'si');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bloqueos`
--
ALTER TABLE `bloqueos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `colecciones`
--
ALTER TABLE `colecciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `download`
--
ALTER TABLE `download`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `farm_items`
--
ALTER TABLE `farm_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `fotosbot`
--
ALTER TABLE `fotosbot`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `fotoscompradas`
--
ALTER TABLE `fotoscompradas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `fotosenventa`
--
ALTER TABLE `fotosenventa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `player_id` (`player_id`);

--
-- Indices de la tabla `fotosprogramadas`
--
ALTER TABLE `fotosprogramadas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gifcodes`
--
ALTER TABLE `gifcodes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `giftcredits`
--
ALTER TABLE `giftcredits`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `giftcredits_weekly`
--
ALTER TABLE `giftcredits_weekly`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `player_id_2` (`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indices de la tabla `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `listasfotosbot`
--
ALTER TABLE `listasfotosbot`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajesprogramados`
--
ALTER TABLE `mensajesprogramados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones_fotosnuevas`
--
ALTER TABLE `notificaciones_fotosnuevas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones_suscripcionesvencidas`
--
ALTER TABLE `notificaciones_suscripcionesvencidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nuevochat_mensajes`
--
ALTER TABLE `nuevochat_mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nuevochat_rooms`
--
ALTER TABLE `nuevochat_rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `packscomprados`
--
ALTER TABLE `packscomprados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foto_id` (`foto_id`,`comprador_id`);

--
-- Indices de la tabla `packsenventa`
--
ALTER TABLE `packsenventa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`id`,`player_id`) USING BTREE;

--
-- Indices de la tabla `payment_list`
--
ALTER TABLE `payment_list`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `photo_gift_credits`
--
ALTER TABLE `photo_gift_credits`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `hidetochat` (`hidetochat`),
  ADD KEY `id` (`id`),
  ADD KEY `hidden_for_old` (`hidden_for_old`),
  ADD KEY `time_joined` (`time_joined`);

--
-- Indices de la tabla `players_farms`
--
ALTER TABLE `players_farms`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `players_farm_items`
--
ALTER TABLE `players_farm_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `players_movements`
--
ALTER TABLE `players_movements`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `players_namesactions`
--
ALTER TABLE `players_namesactions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `players_notifications`
--
ALTER TABLE `players_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `players_recommendations`
--
ALTER TABLE `players_recommendations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `player_colecciones`
--
ALTER TABLE `player_colecciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `player_comments`
--
ALTER TABLE `player_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `player_items`
--
ALTER TABLE `player_items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `player_items_bought`
--
ALTER TABLE `player_items_bought`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `player_megusta`
--
ALTER TABLE `player_megusta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `player_pets`
--
ALTER TABLE `player_pets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `respuestasbot_enespera`
--
ALTER TABLE `respuestasbot_enespera`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `respuesta_automatica`
--
ALTER TABLE `respuesta_automatica`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `respuesta_sala`
--
ALTER TABLE `respuesta_sala`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `retiros`
--
ALTER TABLE `retiros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventascompradas`
--
ALTER TABLE `ventascompradas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventasenventa`
--
ALTER TABLE `ventasenventa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `welcomechat`
--
ALTER TABLE `welcomechat`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bloqueos`
--
ALTER TABLE `bloqueos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `colecciones`
--
ALTER TABLE `colecciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `download`
--
ALTER TABLE `download`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `farm_items`
--
ALTER TABLE `farm_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotosbot`
--
ALTER TABLE `fotosbot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotoscompradas`
--
ALTER TABLE `fotoscompradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotosenventa`
--
ALTER TABLE `fotosenventa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotosprogramadas`
--
ALTER TABLE `fotosprogramadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=427;

--
-- AUTO_INCREMENT de la tabla `gifcodes`
--
ALTER TABLE `gifcodes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `giftcredits`
--
ALTER TABLE `giftcredits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `giftcredits_weekly`
--
ALTER TABLE `giftcredits_weekly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `listasfotosbot`
--
ALTER TABLE `listasfotosbot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajesprogramados`
--
ALTER TABLE `mensajesprogramados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `notificaciones_fotosnuevas`
--
ALTER TABLE `notificaciones_fotosnuevas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT de la tabla `notificaciones_suscripcionesvencidas`
--
ALTER TABLE `notificaciones_suscripcionesvencidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nuevochat_mensajes`
--
ALTER TABLE `nuevochat_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nuevochat_rooms`
--
ALTER TABLE `nuevochat_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `packscomprados`
--
ALTER TABLE `packscomprados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `packsenventa`
--
ALTER TABLE `packsenventa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `payment_list`
--
ALTER TABLE `payment_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `photo_gift_credits`
--
ALTER TABLE `photo_gift_credits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `players`
--
ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `players_farms`
--
ALTER TABLE `players_farms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `players_farm_items`
--
ALTER TABLE `players_farm_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `players_movements`
--
ALTER TABLE `players_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `players_namesactions`
--
ALTER TABLE `players_namesactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `players_notifications`
--
ALTER TABLE `players_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `players_recommendations`
--
ALTER TABLE `players_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `player_colecciones`
--
ALTER TABLE `player_colecciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `player_comments`
--
ALTER TABLE `player_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `player_items`
--
ALTER TABLE `player_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `player_items_bought`
--
ALTER TABLE `player_items_bought`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `player_megusta`
--
ALTER TABLE `player_megusta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `player_pets`
--
ALTER TABLE `player_pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `polls`
--
ALTER TABLE `polls`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuestasbot_enespera`
--
ALTER TABLE `respuestasbot_enespera`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuesta_automatica`
--
ALTER TABLE `respuesta_automatica`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuesta_sala`
--
ALTER TABLE `respuesta_sala`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `retiros`
--
ALTER TABLE `retiros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventascompradas`
--
ALTER TABLE `ventascompradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventasenventa`
--
ALTER TABLE `ventasenventa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `welcomechat`
--
ALTER TABLE `welcomechat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
