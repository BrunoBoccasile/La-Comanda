-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-11-2023 a las 19:38:34
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `la comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `id` varchar(50) NOT NULL,
  `detalle` varchar(50) NOT NULL,
  `id_mesa` int(11) NOT NULL,
  `tiempo_estimado_finalizacion` smallint(6) NOT NULL,
  `costo_total` decimal(10,2) NOT NULL,
  `fecha_hora_creacion` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comandas`
--

INSERT INTO `comandas` (`id`, `detalle`, `id_mesa`, `tiempo_estimado_finalizacion`, `costo_total`, `fecha_hora_creacion`, `estado`) VALUES
('BcNDs', 'daikiri', 10000, 7, 1200.00, '23-11-07 19:22', 'concluida'),
('hyjEj', 'sorrentinos con tuco', 10002, 32, 5100.00, '23-11-07 19:28', 'pendiente'),
('QpcIA', 'milanesa a la napolitana', 10000, 32, 4920.50, '23-11-07 19:22', 'concluida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `apellido`, `usuario`, `clave`, `estado`, `tipo`) VALUES
(10, 'Carlos', 'Gutierrez', 'carlitosgutierrez22', '123456', 'borrado', 'mozo'),
(11, 'Hernan', 'Drago', 'hernandrago', '123456', 'activo', 'cocinero'),
(12, 'Matias', 'Fernandez', 'matifernan', '55555', 'activo', 'mozo'),
(13, 'Mariela', 'Herrera', 'marielaherrera', '9876', 'activo', 'bartender');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `precio` decimal(7,2) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `estado` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `nombre`, `precio`, `tipo`, `estado`) VALUES
(1, 'milanesa a caballo', 4700.00, 'comida', 'borrado'),
(2, 'milanesa a la napolitana', 4920.50, 'comida', 'activo'),
(3, 'quilmes', 850.00, 'cerveza', 'activo'),
(4, 'heineken', 966.00, 'cerveza', 'borrado'),
(5, 'daikiri', 1200.00, 'trago', 'activo'),
(6, 'volcan de chocolate', 1800.00, 'postre', 'activo'),
(7, 'sorrentinos a los cuatro quesos', 5100.00, 'comida', 'activo'),
(8, 'sorrentinos con tuco', 5100.00, 'comida', 'activo'),
(9, 'fideos a la bologniesa', 4800.00, 'comida', 'activo'),
(10, 'bife de chorizo', 6500.00, 'comida', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `estado`) VALUES
(10000, 'disponible'),
(10002, 'con cliente esperando pedido'),
(10003, 'disponible'),
(10004, 'disponible');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10005;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
