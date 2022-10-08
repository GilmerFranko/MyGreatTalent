-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-09-2021 a las 02:44:34
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
-- Base de datos: `mlywatsm_chatnewbellas`
--

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
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `giftcredits_weekly`
--
ALTER TABLE `giftcredits_weekly`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `player_id_2` (`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `giftcredits_weekly`
--
ALTER TABLE `giftcredits_weekly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
