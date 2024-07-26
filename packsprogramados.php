-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-05-2023 a las 19:12:28
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mlywatsm_chatnewbellas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `packsprogramados`
--

CREATE TABLE `packsprogramados` (
  `id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `video` varchar(255) DEFAULT NULL COMMENT 'Direccion de video',
  `precio` int(11) NOT NULL,
  `imagens` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_count` int(11) DEFAULT NULL COMMENT 'Cantidad de Imagenes',
  `video_length` int(11) DEFAULT NULL COMMENT 'Duracion de Video en UNIX',
  `descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `linkdedescarga` varchar(555) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ventasrealizadas` int(11) NOT NULL DEFAULT 0,
  `hidetochat` varchar(10) NOT NULL DEFAULT '4',
  `visible` text NOT NULL,
  `content_to` varchar(20) NOT NULL DEFAULT 'hetero'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `packsprogramados`
--
ALTER TABLE `packsprogramados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `player_id` (`id`,`player_id`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `packsprogramados`
--
ALTER TABLE `packsprogramados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
