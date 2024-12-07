-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 07-12-2024 a las 14:34:21
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
-- Base de datos: `to-do`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE `grupo` (
  `idGrupo` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subtareas`
--

CREATE TABLE `subtareas` (
  `idSubtarea` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `realizada` tinyint(1) NOT NULL,
  `idTarea` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `idTarea` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fechaFin` date NOT NULL,
  `completada` tinyint(1) NOT NULL,
  `idGrupo` int(11) DEFAULT NULL,
  `idUsuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea`
--

INSERT INTO `tarea` (`idTarea`, `nombre`, `descripcion`, `fechaFin`, `completada`, `idGrupo`, `idUsuario`) VALUES
(13, 'comer', 'lechuga', '2024-12-14', 0, NULL, 1),
(14, 'visitar abu', 'urgente', '2024-12-20', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `email` varchar(321) NOT NULL,
  `contrasena` varchar(200) NOT NULL,
  `token` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `email`, `contrasena`, `token`) VALUES
(1, 'diego@gmail.com', '$2y$10$kgAKF6/lLGZc9q8TRrsEN.qsMOV5Frov6tMlqzsnkKulp4JO7f6Om', ''),
(3, 'tirso@gmail.com', '$2y$10$3NB/KITH9Ri6WES1S3zQzOAU3ujukUEew9LDDKbwjEFPTFf9ubyTC', ''),
(4, 'carlos@gmail.com', '$2y$10$YnMFrU24IqZ.fJGL3kotxuwyXu2dVR1N7Uce7Ba091RUWKsY0QjO6', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_grupos`
--

CREATE TABLE `usuarios_grupos` (
  `idGrupo` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`idGrupo`),
  ADD UNIQUE KEY `nombre_unique` (`nombre`);

--
-- Indices de la tabla `subtareas`
--
ALTER TABLE `subtareas`
  ADD PRIMARY KEY (`idSubtarea`),
  ADD KEY `fk_id_tarea_subtarea` (`idTarea`);

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`idTarea`),
  ADD KEY `fk_id_grupo_tarea` (`idGrupo`),
  ADD KEY `fk_id_usuario_tarea` (`idUsuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indices de la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD PRIMARY KEY (`idGrupo`,`idUsuario`),
  ADD KEY `fk_id_usuario` (`idUsuario`),
  ADD KEY `fk_id_grupo` (`idGrupo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `grupo`
--
ALTER TABLE `grupo`
  MODIFY `idGrupo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subtareas`
--
ALTER TABLE `subtareas`
  MODIFY `idSubtarea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarea`
--
ALTER TABLE `tarea`
  MODIFY `idTarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `subtareas`
--
ALTER TABLE `subtareas`
  ADD CONSTRAINT `fk_id_tarea_subtarea` FOREIGN KEY (`idTarea`) REFERENCES `tarea` (`idTarea`);

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `fk_id_grupo_tarea` FOREIGN KEY (`idGrupo`) REFERENCES `grupo` (`idGrupo`),
  ADD CONSTRAINT `fk_id_usuario_tarea` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`);

--
-- Filtros para la tabla `usuarios_grupos`
--
ALTER TABLE `usuarios_grupos`
  ADD CONSTRAINT `fk_id_grupo` FOREIGN KEY (`idGrupo`) REFERENCES `grupo` (`idGrupo`),
  ADD CONSTRAINT `fk_id_usuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
