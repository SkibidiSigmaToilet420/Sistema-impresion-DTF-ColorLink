-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-12-2025 a las 22:04:14
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
-- Base de datos: `sistema colorlink`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `Cedula_Administrador` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`Cedula_Administrador`) VALUES
('14325896'),
('15300015'),
('29856515');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `Cedula_Cliente` varchar(20) DEFAULT NULL,
  `Direccion` varchar(255) DEFAULT NULL,
  `Tipo_de_Cliente` enum('Habitual','Mayorista','Ocasional','') DEFAULT 'Ocasional'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`Cedula_Cliente`, `Direccion`, `Tipo_de_Cliente`) VALUES
('15190874', 'Japon', 'Habitual'),
('32013275', 'Valencia', 'Mayorista'),
('45612378', 'Guinea Ecuatorial', 'Habitual'),
('30195158', 'Valencia', 'Ocasional'),
('12345678', 'Valencia', 'Mayorista'),
('87654321', 'La sabana', 'Ocasional'),
('V-78945612', 'Valencia', 'Ocasional'),
('J-1234567890', 'Su casa pues ', 'Ocasional'),
('E-132465791235655', 'AFUERA PUES PQ ES UN EXTRANJERO PUES OBVIO ES UN EXTRANJERO ENTONCES VIVE AFUERA NO VIVE EN EL MISMO PAIS PORQUE ES UN EXTRANJERO POR ESO SE LLAMAN ASI', 'Ocasional'),
('J-4532178965', NULL, 'Ocasional');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `disenos`
--

CREATE TABLE `disenos` (
  `ID_Diseno` int(11) NOT NULL,
  `Nombre_Diseno` varchar(255) NOT NULL,
  `URL_Diseno` varchar(255) NOT NULL,
  `Cantidad` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `disenos`
--

INSERT INTO `disenos` (`ID_Diseno`, `Nombre_Diseno`, `URL_Diseno`, `Cantidad`) VALUES
(3, 'Nubia_30195158_2_0.PNG', 'clientes/imagenes/Nubia_30195158_2_0.PNG', 1),
(4, 'Nubia_30195158_2_1.PNG', 'clientes/imagenes/Nubia_30195158_2_1.PNG', 1),
(5, 'Nubia_30195158_2_2.PNG', 'clientes/imagenes/Nubia_30195158_2_2.PNG', 1),
(6, 'Nubia_30195158_2_3.PNG', 'clientes/imagenes/Nubia_30195158_2_3.PNG', 1),
(9, 'Shohei_15190874_4_0.PNG', 'clientes/imagenes/Shohei_15190874_4_0.PNG', 1),
(10, 'Nubia_30195158_5_0.PNG', 'clientes/imagenes/Nubia_30195158_5_0.PNG', 1),
(11, 'Nubia_30195158_6_0.PNG', 'clientes/imagenes/Nubia_30195158_6_0.PNG', 1),
(13, 'Jesus_32013275_8_0.PNG', 'clientes/imagenes/Jesus_32013275_8_0.PNG', 1),
(22, 'Ben_78945612_15_0.png', 'clientes/imagenes/Ben_78945612_15_0.png', 7),
(23, 'Ben_78945612_15_1.png', 'clientes/imagenes/Ben_78945612_15_1.png', 9),
(25, 'Jesus_32013275_17_0.png', 'clientes/imagenes/Jesus_32013275_17_0.png', 1),
(26, 'Jesus_32013275_17_1.PNG', 'clientes/imagenes/Jesus_32013275_17_1.PNG', 1),
(27, 'Jesus_32013275_17_2.PNG', 'clientes/imagenes/Jesus_32013275_17_2.PNG', 1),
(28, 'Jesus_32013275_17_3.PNG', 'clientes/imagenes/Jesus_32013275_17_3.PNG', 1),
(29, 'Jesus_32013275_17_4.png', 'clientes/imagenes/Jesus_32013275_17_4.png', 1),
(30, 'Jesus_32013275_17_5.PNG', 'clientes/imagenes/Jesus_32013275_17_5.PNG', 1),
(33, 'Jesus_32013275_18_6909530a0c34a0.57131668.PNG', 'clientes/imagenes/Jesus_32013275_18_6909530a0c34a0.57131668.PNG', 4),
(34, 'Jesus_32013275_18_6909530a0faf60.29184699.png', 'clientes/imagenes/Jesus_32013275_18_6909530a0faf60.29184699.png', 3),
(35, 'Jesus_32013275_16_690958fce85860.17747108.PNG', 'clientes/imagenes/Jesus_32013275_16_690958fce85860.17747108.PNG', 4),
(36, 'Jesus_32013275_3_6909595a586ca6.12157938.PNG', 'clientes/imagenes/Jesus_32013275_3_6909595a586ca6.12157938.PNG', 1),
(40, 'Jesus_32013275_7_690ceb9e588319.62875199.PNG', 'clientes/imagenes/Jesus_32013275_7_690ceb9e588319.62875199.PNG', 1),
(41, 'Jesus_32013275_19_0.PNG', 'clientes/imagenes/Jesus_32013275_19_0.PNG', 1),
(42, 'Jesus_32013275_20_0.PNG', 'clientes/imagenes/Jesus_32013275_20_0.PNG', 5),
(43, 'Jesus_32013275_21_0.PNG', 'clientes/imagenes/Jesus_32013275_21_0.PNG', 4),
(46, 'Jesus_32013275_24_0.png', 'clientes/imagenes/Jesus_32013275_24_0.png', 3),
(47, 'Jesus_32013275_24_1.png', 'clientes/imagenes/Jesus_32013275_24_1.png', 6),
(48, 'Jesus_32013275_24_2.png', 'clientes/imagenes/Jesus_32013275_24_2.png', 2),
(49, 'Jesus_32013275_23_692fc7af5d8b36.71549441.PNG', 'clientes/imagenes/Jesus_32013275_23_692fc7af5d8b36.71549441.PNG', 1),
(50, 'Jesus_32013275_25_0.PNG', 'clientes/imagenes/Jesus_32013275_25_0.PNG', 1),
(51, 'Jesus_32013275_26_0.PNG', 'clientes/imagenes/Jesus_32013275_26_0.PNG', 1),
(52, 'Jesus_32013275_27_0.PNG', 'clientes/imagenes/Jesus_32013275_27_0.PNG', 1),
(53, 'Jesus_32013275_28_0.PNG', 'clientes/imagenes/Jesus_32013275_28_0.PNG', 1),
(56, 'Jesus_32013275_29_6930a26406c918.01407526.PNG', 'clientes/imagenes/Jesus_32013275_29_6930a26406c918.01407526.PNG', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `Cedula_Empleado` varchar(20) DEFAULT NULL,
  `Cargo` varchar(255) DEFAULT NULL,
  `Cedula_Administrador` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`Cedula_Empleado`, `Cargo`, `Cedula_Administrador`) VALUES
('19879654', 'Administracion', '15300015'),
('15190879', 'Diseñador', '14325896'),
('30758487', 'Diseñador', '14325896');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `CODIGO_Material` int(11) NOT NULL,
  `Nombre_material` varchar(100) NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Precio_CM` decimal(10,2) NOT NULL,
  `Ingresado_Modificado_por` varchar(20) DEFAULT NULL,
  `Estado_Material` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `Fecha_Ultima_Modificacion` datetime DEFAULT current_timestamp(),
  `Motivo_Cambio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`CODIGO_Material`, `Nombre_material`, `Cantidad`, `Precio_CM`, `Ingresado_Modificado_por`, `Estado_Material`, `Fecha_Ultima_Modificacion`, `Motivo_Cambio`) VALUES
(1, 'Poliamida', 432, 15.00, '14325896', 'ACTIVO', '2025-12-03 02:47:45', 'aumento la existencia'),
(2, 'Poliamida Antimigrante', 554, 18.00, '14325896', 'ACTIVO', '2025-12-03 02:47:55', 'aumento de existencia'),
(5, 'Poliamida UV', 794, 11.32, '14325896', 'ACTIVO', '2025-11-23 18:24:10', 'se aumento la existencia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `Id_pedido` int(11) NOT NULL,
  `Estado_Pedido` enum('Pendiente','Rechazado','Produccion','Finalizado','Verificado') NOT NULL DEFAULT 'Pendiente',
  `Cedula_Cliente` varchar(20) DEFAULT NULL,
  `Empleado_Encargado` varchar(20) DEFAULT NULL,
  `Material_Pedido` int(11) DEFAULT 3,
  `Centimetros` decimal(10,2) NOT NULL,
  `Cantidades` int(11) DEFAULT 1,
  `Costo` decimal(10,2) NOT NULL,
  `Fecha_Solicitud` datetime NOT NULL,
  `Fecha_Entrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`Id_pedido`, `Estado_Pedido`, `Cedula_Cliente`, `Empleado_Encargado`, `Material_Pedido`, `Centimetros`, `Cantidades`, `Costo`, `Fecha_Solicitud`, `Fecha_Entrega`) VALUES
(2, 'Rechazado', '30195158', '19879654', 2, 100.00, 4, 1632.00, '2025-07-06 06:42:14', '2025-09-24'),
(3, 'Pendiente', '32013275', '15190879', 2, 10.00, 1, 163.20, '2025-11-03 09:39:38', '2025-09-23'),
(4, 'Verificado', '15190874', '19879654', 2, 30.00, 1, 489.60, '2025-07-13 11:55:25', '2025-09-23'),
(5, 'Finalizado', '30195158', '19879654', 1, 25.00, 1, 359.00, '2025-07-13 11:56:36', '2025-11-28'),
(6, 'Finalizado', '30195158', '19879654', 2, 100.00, 1, 1632.00, '2025-07-13 12:01:52', '2025-09-24'),
(7, 'Finalizado', '32013275', '19879654', 1, 81.00, 1, 1163.16, '2025-11-06 02:40:30', '2025-12-04'),
(8, 'Rechazado', '32013275', '19879654', 2, 56.00, 1, 913.92, '2025-07-31 01:14:39', '2025-09-24'),
(15, 'Pendiente', 'V-78945612', NULL, 1, 78.00, 2, 1120.08, '2025-09-30 04:30:19', '2025-10-17'),
(16, 'Finalizado', '32013275', '19879654', 2, 30.00, 1, 489.60, '2025-11-03 09:38:04', '2025-11-15'),
(17, 'Finalizado', '32013275', '19879654', 1, 89.00, 6, 1278.04, '2025-11-02 02:44:25', '2025-12-04'),
(18, 'Finalizado', '32013275', '19879654', 1, 32.00, 2, 459.52, '2025-11-03 09:12:42', '2026-01-02'),
(19, 'Rechazado', '32013275', '19879654', 2, 100.00, 1, 1632.00, '2025-11-14 07:29:19', NULL),
(20, 'Pendiente', '32013275', NULL, 2, 98.00, 1, 1599.36, '2025-11-14 07:39:37', NULL),
(21, 'Finalizado', '32013275', '19879654', 2, 20.00, 1, 326.40, '2025-11-14 08:15:27', '2025-12-04'),
(23, 'Finalizado', '32013275', '19879654', 5, 150.00, 1, 1698.00, '2025-12-03 01:16:31', '2025-12-06'),
(24, 'Finalizado', '32013275', '19879654', 1, 198.00, 3, 2970.00, '2025-12-03 01:15:40', '2025-12-05'),
(25, 'Pendiente', '32013275', NULL, 2, 99.00, 1, 1782.00, '2025-12-03 02:29:28', NULL),
(26, 'Pendiente', '32013275', NULL, 2, 56.00, 1, 1008.00, '2025-12-03 02:33:44', NULL),
(27, 'Pendiente', '32013275', NULL, 1, 23.00, 1, 345.00, '2025-12-03 02:36:22', NULL),
(28, 'Finalizado', '32013275', '19879654', 5, 56.00, 1, 633.92, '2025-12-03 02:37:16', '2025-12-03'),
(29, 'Produccion', '32013275', '19879654', 2, 96.00, 1, 1728.00, '2025-12-03 04:49:40', '2025-12-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_diseno`
--

CREATE TABLE `pedido_diseno` (
  `ID_Pedido_Diseno` int(11) NOT NULL,
  `ID_Pedido` int(11) NOT NULL,
  `ID_Diseno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido_diseno`
--

INSERT INTO `pedido_diseno` (`ID_Pedido_Diseno`, `ID_Pedido`, `ID_Diseno`) VALUES
(3, 2, 3),
(4, 2, 4),
(5, 2, 5),
(6, 2, 6),
(9, 4, 9),
(10, 5, 10),
(11, 6, 11),
(13, 8, 13),
(22, 15, 22),
(23, 15, 23),
(25, 17, 25),
(26, 17, 26),
(27, 17, 27),
(28, 17, 28),
(29, 17, 29),
(30, 17, 30),
(33, 18, 33),
(34, 18, 34),
(35, 16, 35),
(36, 3, 36),
(40, 7, 40),
(41, 19, 41),
(42, 20, 42),
(43, 21, 43),
(46, 24, 46),
(47, 24, 47),
(48, 24, 48),
(49, 23, 49),
(50, 25, 50),
(51, 26, 51),
(52, 27, 52),
(53, 28, 53),
(56, 29, 56);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE `tipousuario` (
  `Codigo` int(11) NOT NULL,
  `Tipo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipousuario`
--

INSERT INTO `tipousuario` (`Codigo`, `Tipo`) VALUES
(1, 'Cliente'),
(2, 'Empleado'),
(3, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `Cedula` varchar(20) NOT NULL,
  `Correo` varchar(100) NOT NULL,
  `Password_usuario` text NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `NumeroTLF` varchar(12) DEFAULT NULL,
  `Fecha_Registro` datetime DEFAULT NULL,
  `Tipo_Usuario` int(12) NOT NULL,
  `Estado` enum('ACTIVO','INACTIVO','','') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`Cedula`, `Correo`, `Password_usuario`, `Nombre`, `Apellido`, `NumeroTLF`, `Fecha_Registro`, `Tipo_Usuario`, `Estado`) VALUES
('12345678', 'altuve@gmail.com', '$2y$10$VJWvDsQ8OD/Vi8rPKdkbsOv5GntHoqqWxkmmj9ux.OzL/kJnJLE06', 'Jose ', 'Altuve', '01234567891', '2025-07-13 03:52:41', 1, 'ACTIVO'),
('14325896', 'judge@gmail.com', '$2y$10$EJr4rkGLTonvgXWTb.53ieo.oZZfLVYtyFJcb5PF.VBlb/hh49/Hu', 'Aaron', 'Judge', '04127487129', '2025-07-06 08:43:28', 3, 'ACTIVO'),
('15190874', 'ohtani@gmail.com', '$2y$10$pe8DCW427hg/JeAgbWkzh.EKriiXT41zK8ggpMANJK4M02JAOmAkK', 'Shohei', 'Ohtani', '02417894563', '2025-06-28 07:44:00', 1, 'ACTIVO'),
('15190879', 'messi@gmail.com', '$2y$10$FqtxS/7xZCti5g8k6ImqM.nT7RWgT6FWkc3dTd4cuL9jRaVEhhwyK', 'Lionel', 'Messi', '04167894566', '2025-06-28 07:44:36', 2, 'ACTIVO'),
('15300015', 'nubia@gmail.com', '$2y$10$oJR2eLkfHOmwwbHw9/Hu8uSPfX8MoeL8DPcsumU7AffUFZG7khoPW', 'Nubia', 'Ramona', '12354678901', '2025-07-06 08:42:37', 3, 'ACTIVO'),
('19879654', 'leesin@gmail.com', '$2y$10$jAUARW2nW6Y2ZljNHz4tYu5vkTDynwTHWgGzr48xh6f6IOxbfL6DS', 'Lee', 'Sin', '04241234565', '2025-06-28 07:45:48', 2, 'ACTIVO'),
('29856515', 'juan@gmail.com', '$2y$10$KV5qCFu..UPWcsb/aL1K/um6D3VDhnh2vARPRDKqdK6qRocuMYOjm', 'Juan', 'Lago', '02417894612', '2025-11-23 06:22:39', 3, 'ACTIVO'),
('30195158', 'Nubiia@gmail.com', '$2y$10$nuqTWzM9XfUmcdCpl7vCbOn9yP53jvAMKBAPOO4wd7wMS4KwnCIu6', 'Nubia', 'Blanco', NULL, '2025-07-05 04:23:07', 1, 'ACTIVO'),
('30758487', 'samuel@gmail.com', '$2y$10$TfazjnPDQgjo7fU70mgIGOSUjYBvynVan93EsA66ukTClfXRomJO6', 'Samuel', 'Rivero', '', '2025-07-06 08:46:31', 2, 'ACTIVO'),
('32013275', 'jesusblanco1905@gmail.com', '$2y$10$jFd3m/Gjym6YAOdrbbVCFOFlNm2XL04dLYW6NJvWXu2LxdgI45XbS', 'Jesus', 'Blanco', '04127487129', '2025-07-06 08:47:19', 1, 'ACTIVO'),
('45612378', 'ejemplo@gmail.com', '$2y$10$93LcsS7yerEL8lj5XBkVI.txkigBjA5Qr0moapn7pmFaNuSUXrxJa', 'asfas', 'zvzxv', NULL, '2025-07-05 16:10:29', 1, 'INACTIVO'),
('87654321', 'acuna@gmail.com', '$2y$10$FVeZo9GFdroYUDYYKsMG0O53FjLtsJPBzsC3JRSgrHXBU9HrOt.iO', 'Ronald', 'Acuña', '01234567891', '2025-07-13 03:55:39', 1, 'ACTIVO'),
('E-132465791235655', 'extranjero@gmail.com', '$2y$10$/VrCyzQxWGaV/54U09puIu6E.3J2zH4Hd1mwaRyW1rBa06Cb903Ou', 'extranjero', 'ejemplo', '04127487122', '2025-11-18 10:26:16', 1, 'ACTIVO'),
('J-1234567890', 'empresa@gmail.com', '$2y$10$IrYuJtPuyGXdrq69qudQK.yqhvo.Poaj6RpGZOcMXFYz5zyj8NhUi', 'Empresa', 'Ejemplo', '04127487126', '2025-11-18 10:19:48', 1, 'ACTIVO'),
('J-4532178965', 'Empresa2@gmail.com', '$2y$10$An9i552xtGcoJ.UwSD.bf.BTCmQrilh/l3/uBwWolGex0pTlaw/JO', 'Empres2', 'Ejempl0', '04127487133', '2025-11-18 10:56:29', 1, 'ACTIVO'),
('V-78945612', 'Ben@gmail.com', '$2y$10$Z0rE97d6T8aEbnsfDWix7ObNJjywzf9sE/utZXBvJz5ahLDltHf.K', 'Ben', 'Rice', '04127487129', '2025-09-30 03:56:46', 1, 'ACTIVO');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD KEY `Cedula_Administrador` (`Cedula_Administrador`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD KEY `Cedula_Cliente` (`Cedula_Cliente`);

--
-- Indices de la tabla `disenos`
--
ALTER TABLE `disenos`
  ADD PRIMARY KEY (`ID_Diseno`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD KEY `Cedula_Empleado` (`Cedula_Empleado`),
  ADD KEY `Cedula_Administrador` (`Cedula_Administrador`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`CODIGO_Material`),
  ADD KEY `Ingresado_Modificado_por` (`Ingresado_Modificado_por`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`Id_pedido`),
  ADD KEY `Material_Pedido` (`Material_Pedido`),
  ADD KEY `Cedula_Cliente` (`Cedula_Cliente`),
  ADD KEY `Empleado_Encargado` (`Empleado_Encargado`);

--
-- Indices de la tabla `pedido_diseno`
--
ALTER TABLE `pedido_diseno`
  ADD PRIMARY KEY (`ID_Pedido_Diseno`),
  ADD KEY `ID_Diseno` (`ID_Diseno`),
  ADD KEY `ID_Pedido` (`ID_Pedido`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`Codigo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Cedula`),
  ADD UNIQUE KEY `Correo` (`Correo`),
  ADD KEY `tipousuario` (`Tipo_Usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `disenos`
--
ALTER TABLE `disenos`
  MODIFY `ID_Diseno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `CODIGO_Material` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `Id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `pedido_diseno`
--
ALTER TABLE `pedido_diseno`
  MODIFY `ID_Pedido_Diseno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `Codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD CONSTRAINT `administradores_ibfk_1` FOREIGN KEY (`Cedula_Administrador`) REFERENCES `usuarios` (`Cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`Cedula_Cliente`) REFERENCES `usuarios` (`Cedula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`Cedula_Empleado`) REFERENCES `usuarios` (`Cedula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`Cedula_Administrador`) REFERENCES `administradores` (`Cedula_Administrador`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD CONSTRAINT `materiales_ibfk_1` FOREIGN KEY (`Ingresado_Modificado_por`) REFERENCES `administradores` (`Cedula_Administrador`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`Cedula_Cliente`) REFERENCES `clientes` (`Cedula_Cliente`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`Empleado_Encargado`) REFERENCES `empleados` (`Cedula_Empleado`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`Material_Pedido`) REFERENCES `materiales` (`CODIGO_Material`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido_diseno`
--
ALTER TABLE `pedido_diseno`
  ADD CONSTRAINT `pedido_diseno_ibfk_1` FOREIGN KEY (`ID_Diseno`) REFERENCES `disenos` (`ID_Diseno`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pedido_diseno_ibfk_2` FOREIGN KEY (`ID_Pedido`) REFERENCES `pedidos` (`Id_pedido`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `tipousuario` FOREIGN KEY (`Tipo_Usuario`) REFERENCES `tipousuario` (`Codigo`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
