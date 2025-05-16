-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 16-05-2025 a las 03:51:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_citasmedicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login`
--

CREATE TABLE `login` (
  `id_login` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `apellido` varchar(200) DEFAULT NULL,
  `numero` int(11) DEFAULT NULL,
  `rol` enum('paciente','medico','administrador') DEFAULT NULL,
  `correo` varchar(300) DEFAULT NULL,
  `contrasena` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login`
--

INSERT INTO `login` (`id_login`, `nombre`, `apellido`, `numero`, `rol`, `correo`, `contrasena`) VALUES
(1, 'Camila', 'Ramírez', 123456789, 'paciente', 'megumifushigurokokoror@gmail.com', 'megumifk69'),
(2, 'Laura', 'González', 987654321, 'medico', 'shuraboosmobal@gmail.com', 'shuraBss7'),
(3, 'Valentina', 'Morales', 555123456, 'administrador', 'gedeonstevenmendoza.t@gmail.com', 'gdmendoza1234');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE `medicos` (
  `id_medico` int(11) NOT NULL,
  `id_login` int(11) NOT NULL,
  `especialidad` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`id_medico`, `id_login`, `especialidad`) VALUES
(2, 2, 'Pediatría');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_cita`
--

CREATE TABLE `solicitudes_cita` (
  `id_solicitud` int(11) NOT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `estado` enum('pendiente','asignada','proceso','terminado') DEFAULT 'pendiente',
  `fecha_asignada` date DEFAULT NULL,
  `hora_asignada` time DEFAULT NULL,
  `id_medico` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id_login`);

--
-- Indices de la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD PRIMARY KEY (`id_medico`),
  ADD KEY `id_login` (`id_login`);

--
-- Indices de la tabla `solicitudes_cita`
--
ALTER TABLE `solicitudes_cita`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `login`
--
ALTER TABLE `login`
  MODIFY `id_login` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `id_medico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `solicitudes_cita`
--
ALTER TABLE `solicitudes_cita`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD CONSTRAINT `medicos_ibfk_1` FOREIGN KEY (`id_login`) REFERENCES `login` (`id_login`);

--
-- Filtros para la tabla `solicitudes_cita`
--
ALTER TABLE `solicitudes_cita`
  ADD CONSTRAINT `solicitudes_cita_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `login` (`id_login`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
