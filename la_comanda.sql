-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2023 a las 01:30:26
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
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `usuario`, `clave`, `estado`) VALUES
(2, 'Bruno', 'bruno', 'bruno', 'activo'),
(3, 'Guillermo', 'guille44', '12345', 'activo'),
(4, 'cliente', 'cliente', 'cliente', 'activo'),
(5, 'Gerardo', 'gerardo', 'hola', 'borrado'),
(6, 'Gerard', 'gerardo', 'hola', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comandas`
--

CREATE TABLE `comandas` (
  `id` varchar(5) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(50) NOT NULL,
  `id_producto` int(11) NOT NULL,
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

INSERT INTO `comandas` (`id`, `id_cliente`, `nombre_cliente`, `id_producto`, `detalle`, `id_mesa`, `tiempo_estimado_finalizacion`, `costo_total`, `fecha_hora_creacion`, `estado`) VALUES
('Ycbzy', 2, 'Bruno', 82, 'heineken', 10003, 7, 10166.00, '23-11-17 02:23', 'concluida'),
('Ycbzy', 2, 'Bruno', 83, 'milanesa a caballo', 10003, 25, 10166.00, '23-11-17 02:23', 'concluida'),
('Ycbzy', 2, 'Bruno', 84, 'hamburguesa', 10003, 25, 10166.00, '23-11-17 02:23', 'concluida'),
('t7FY4', 3, 'Guillermo', 85, 'milanesa a la napolitana', 10003, 25, 4920.50, '23-11-17 02:26', 'concluida'),
('3IkKG', 3, 'Guillermo', 86, 'milanesa a la napolitana', 10003, 25, 4920.50, '23-11-17 04:40', 'concluida'),
('M5buL', 3, 'Guillermo', 87, 'milanesa a caballo', 10003, 25, 4700.00, '23-11-17 06:50', 'concluida'),
('dxi7H', 2, 'Bruno', 88, 'quilmes', 10003, 7, 5350.00, '23-11-17 20:02', 'concluida'),
('dxi7H', 2, 'Bruno', 89, 'hamburguesa', 10003, 25, 5350.00, '23-11-17 20:02', 'concluida'),
('7T0bA', 2, 'Bruno', 90, 'quilmes', 10003, 0, 5350.00, '23-11-17 21:41', 'concluida'),
('7T0bA', 2, 'Bruno', 91, 'hamburguesa', 10003, 0, 5350.00, '23-11-17 21:41', 'concluida'),
('sQ3sh', 2, 'Bruno', 92, 'quilmes', 10003, 0, 5350.00, '23-11-17 21:45', 'concluida'),
('sQ3sh', 2, 'Bruno', 93, 'hamburguesa', 10003, 20, 5350.00, '23-11-17 21:45', 'concluida'),
('S8dy8', 4, 'cliente', 94, 'quilmes', 10004, 0, 5350.00, '23-11-18 03:22', 'concluida'),
('S8dy8', 4, 'cliente', 95, 'hamburguesa', 10004, 0, 5350.00, '23-11-18 03:22', 'concluida'),
('K93SO', 2, 'Bruno', 96, 'hamburguesa', 10004, 0, 9000.00, '23-11-18 04:14', 'concluida'),
('K93SO', 2, 'Bruno', 97, 'hamburguesa', 10004, 0, 9000.00, '23-11-18 04:14', 'concluida'),
('82Bsd', 4, 'cliente', 98, 'hamburguesa', 10004, 20, 9000.00, '23-11-18 04:32', 'concluida'),
('82Bsd', 4, 'cliente', 99, 'hamburguesa', 10004, 20, 9000.00, '23-11-18 04:32', 'concluida'),
('wulzW', 4, 'cliente', 100, 'milanesa a caballo', 10005, 0, 12881.00, '23-11-20 04:23', 'concluida'),
('wulzW', 4, 'cliente', 101, 'hamburguesa de garbanzo', 10005, 0, 12881.00, '23-11-20 04:23', 'concluida'),
('wulzW', 4, 'cliente', 102, 'hamburguesa de garbanzo', 10005, 0, 12881.00, '23-11-20 04:23', 'concluida'),
('wulzW', 4, 'cliente', 103, 'corona', 10005, 0, 12881.00, '23-11-20 04:23', 'concluida'),
('wulzW', 4, 'cliente', 104, 'daikiri', 10005, 0, 12881.00, '23-11-20 04:23', 'concluida'),
('p2xan', 4, 'cliente', 105, 'milanesa a caballo', 10004, 19, 9800.00, '23-11-21 00:35', 'concluida'),
('p2xan', 4, 'cliente', 106, 'sorrentinos con tuco', 10004, 0, 9800.00, '23-11-21 00:35', 'concluida'),
('tw25S', 4, 'cliente', 107, 'milanesa a caballo', 10007, 0, 12881.00, '23-11-21 01:04', 'concluida'),
('tw25S', 4, 'cliente', 108, 'hamburguesa de garbanzo', 10007, 0, 12881.00, '23-11-21 01:04', 'concluida'),
('tw25S', 4, 'cliente', 109, 'hamburguesa de garbanzo', 10007, 0, 12881.00, '23-11-21 01:04', 'concluida'),
('tw25S', 4, 'cliente', 110, 'corona', 10007, 0, 12881.00, '23-11-21 01:04', 'concluida'),
('tw25S', 4, 'cliente', 111, 'daikiri', 10007, 0, 12881.00, '23-11-21 01:04', 'concluida');

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
(11, 'Hernan', 'Drago', 'hernandrago', '123456', 'borrado', 'cocinero'),
(12, 'Matias', 'Fernandez', 'matifernan', '55555', 'activo', 'cocinero'),
(13, 'Mariela', 'Herrera', 'marielaherrera', '9876', 'activo', 'bartender'),
(14, 'Nicolas', 'Rodriguez', 'nicorod', '1234', 'activo', 'cocinero'),
(15, 'Esteban', 'Raed', 'kanyi', '999777', 'activo', 'cervecero'),
(16, 'Kevin', 'Gauto', 'kevog', '12345', 'borrado', 'socio'),
(17, 'Luis', 'Kun', 'luisalberto', '551', 'activo', 'socio'),
(18, 'Bruno', 'Boccasile', 'brunosocio', '123', 'activo', 'socio'),
(19, 'admin', 'admin', 'admin', 'admin', 'activo', 'socio'),
(20, 'Jorge', 'Espinoza', 'jorgito', '123', 'activo', 'bartender'),
(21, 'bartender', 'bartender', 'bartender', 'bartender', 'activo', 'bartender'),
(22, 'cocinero', 'cocinero', 'cocinero', 'cocinero', 'activo', 'cocinero'),
(23, 'cervecero', 'cervecero', 'cervecero', 'cervecero', 'activo', 'cervecero'),
(24, 'mozo', 'mozo', 'mozo', 'mozo', 'activo', 'mozo'),
(25, 'Julia', 'Martinez', 'julia', '123', 'activo', 'mozo'),
(26, 'Paula', 'Estevez', 'paula', '123', 'activo', 'mozo');

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
(10, 'bife de chorizo', 6500.00, 'comida', 'activo'),
(11, 'papas con cheddar', 3300.00, 'comida', 'activo'),
(12, 'hamburguesa', 4500.00, 'comida', 'activo'),
(13, 'gin de maracuya', 4999.91, 'trago', 'activo'),
(14, 'chorizo', 2590.50, 'comida', 'activo'),
(15, 'hamburguesa de garbanzo', 2590.50, 'comida', 'activo'),
(16, 'corona', 1800.00, 'cerveza', 'activo');

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
(10002, 'cerrada'),
(10003, 'disponible'),
(10004, 'disponible'),
(10005, 'cerrada'),
(10006, 'disponible'),
(10007, 'disponible');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comandas`
--
ALTER TABLE `comandas`
  ADD PRIMARY KEY (`id_producto`);

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
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `comandas`
--
ALTER TABLE `comandas`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10008;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
