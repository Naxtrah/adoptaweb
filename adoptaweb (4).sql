-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-01-2026 a las 15:32:38
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
-- Base de datos: `adoptaweb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adopciones`
--

CREATE TABLE `adopciones` (
  `id_adopcion` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_animal` int(11) DEFAULT NULL,
  `fecha_solicitud` date DEFAULT curdate(),
  `fecha_adopcion` date DEFAULT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adopciones`
--

INSERT INTO `adopciones` (`id_adopcion`, `id_usuario`, `id_animal`, `fecha_solicitud`, `fecha_adopcion`, `estado`, `notas`) VALUES
(1, 1, 6, '2025-03-25', '2025-04-01', 'Aprobada', 'Adopción completada sin incidencias'),
(2, 2, 3, '2025-04-10', NULL, 'Pendiente', 'Esperando confirmación del centro'),
(3, 4, 2, '2025-05-20', '2025-05-25', 'Aprobada', 'Seguimiento inicial en curso'),
(4, 6, 9, '2025-07-20', NULL, 'Pendiente', 'Familia con experiencia en Huskies. Tienen patio grande.'),
(5, 7, 11, '2025-07-18', '2025-07-25', 'Aprobada', 'Adoptado por pareja sin hijos. Experiencia con pastores.'),
(6, 8, 14, '2025-07-22', NULL, 'Pendiente', 'Persona mayor que busca compañía. Vive en apartamento.'),
(7, 9, 18, '2025-07-19', NULL, 'Pendiente', 'Familia activa con niños. Tienen jardín.'),
(8, 10, 20, '2025-07-21', '2025-07-28', 'Aprobada', 'Adoptado por familia con otro perro. Buen ambiente.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales`
--

CREATE TABLE `animales` (
  `id_animal` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `especie` varchar(50) DEFAULT NULL,
  `raza` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `sexo` enum('Macho','Hembra') DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `estado` enum('Disponible','Adoptado','Reservado','En tratamiento') DEFAULT 'Disponible',
  `id_centro` int(11) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `animales`
--

INSERT INTO `animales` (`id_animal`, `nombre`, `especie`, `raza`, `edad`, `sexo`, `descripcion`, `fecha_ingreso`, `estado`, `id_centro`, `imagen_url`) VALUES
(1, 'Luna', 'Perro', 'Mestizo', 2, 'Hembra', 'Cariñosa y tranquila', '2025-01-10', 'Disponible', 1, './img/animales/luna.jpg'),
(2, 'Toby', 'Perro', 'Labrador', 4, 'Macho', 'Muy sociable y juguetón', '2025-02-15', 'Disponible', 2, './img/animales/toby.jpg'),
(3, 'Misu', 'Gato', 'Europeo', 1, 'Hembra', 'Le encanta dormir al sol', '2025-03-05', 'Reservado', 3, './img/animales/misu.jpg'),
(4, 'Rocky', 'Perro', 'Pitbull', 3, 'Macho', 'Necesita dueño experimentado', '2024-12-01', 'Disponible', 1, './img/animales/rocky.jpg'),
(5, 'Nala', 'Gato', 'Siamés', 2, 'Hembra', 'Curiosa y activa', '2025-04-22', 'Disponible', 5, './img/animales/nala.jpg'),
(6, 'Max', 'Perro', 'Golden Retriever', 5, 'Macho', 'Obediente y noble', '2025-03-18', 'Adoptado', 4, './img/animales/max.jpg'),
(7, 'Pelusa', 'Gato', 'Persa', 3, 'Hembra', 'Tranquila y mimosa', '2025-05-10', 'Disponible', 2, './img/animales/pelusa.jpg'),
(8, 'Boby', 'Perro', 'Beagle', 1, 'Macho', 'Le encanta correr', '2025-06-01', 'Disponible', 3, './img/animales/boby.jpg'),
(9, 'Thor', 'Perro', 'Husky Siberiano', 4, 'Macho', 'Ojos azules, muy activo y necesita mucho ejercicio. Sociable con otros perros.', '2025-06-10', 'Disponible', 1, './img/animales/thor.jpg'),
(10, 'Kira', 'Gato', 'Mestizo', 2, 'Hembra', 'Tímida al principio pero muy cariñosa cuando confía. Prefiere hogares tranquilos.', '2025-05-20', 'Disponible', 3, './img/animales/kira.jpg'),
(11, 'Simba', 'Perro', 'Pastor Alemán', 3, 'Macho', 'Muy inteligente y leal. Necesita dueño con experiencia en razas grandes.', '2025-04-15', 'Adoptado', 2, './img/animales/simba.jpg'),
(12, 'Mika', 'Gato', 'Angora Turco', 5, 'Hembra', 'Pelo largo y sedoso. Tranquila y elegante. Ideal para apartamentos.', '2025-07-01', 'Disponible', 5, './img/animales/mika.jpg'),
(13, 'Bruno', 'Perro', 'Bulldog Francés', 2, 'Macho', 'Divertido y juguetón. Perfecto para familias. Ronca adorablemente.', '2025-06-25', 'Reservado', 4, './img/animales/bruno.jpg'),
(14, 'Lola', 'Perro', 'Chihuahua', 1, 'Hembra', 'Pequeña pero con mucho carácter. Se lleva bien con niños mayores.', '2025-07-05', 'Disponible', 1, './img/animales/lola.jpg'),
(15, 'Oliver', 'Gato', 'British Shorthair', 4, 'Macho', 'Carácter tranquilo y apacible. Le gusta la rutina y los mimos suaves.', '2025-05-30', 'Disponible', 2, './img/animales/oliver.jpg'),
(16, 'Rex', 'Perro', 'Rottweiler', 6, 'Macho', 'Maduro y equilibrado. Excelente guardián. Necesita dueño experimentado.', '2025-04-10', 'En tratamiento', 3, './img/animales/rex.jpg'),
(17, 'Maya', 'Gato', 'Sphynx', 3, 'Hembra', 'Sin pelo, necesita protección contra el frío. Muy cariñosa y activa.', '2025-07-10', 'Disponible', 4, './img/animales/maya.jpg'),
(18, 'Leo', 'Perro', 'Border Collie', 2, 'Macho', 'Extremadamente inteligente y enérgico. Necesita mucho ejercicio mental y físico.', '2025-06-15', 'Disponible', 5, './img/animales/leo.jpg'),
(19, 'Zoe', 'Gato', 'Mestizo', 1, 'Hembra', 'Gatita juguetona y curiosa. Ideal para adoptar junto a otro gato.', '2025-07-12', 'Disponible', 1, './img/animales/zoe.jpg'),
(20, 'Rocko', 'Perro', 'Boxer', 5, 'Macho', 'Energético y amigable. Se lleva bien con niños. Juguetón pero obediente.', '2025-05-05', 'Adoptado', 2, './img/animales/rocko.jpg'),
(21, 'Cleo', 'Gato', 'Persa', 6, 'Hembra', 'Tranquila y aristocrática. Necesita cepillado diario por su pelo largo.', '2025-04-20', 'Adoptado', 3, './img/animales/cleo.jpg'),
(22, 'Titan', 'Perro', 'Mastín Español', 7, 'Macho', 'Gigante gentil. Paciente con niños. Necesita espacio por su tamaño.', '2025-06-30', 'En tratamiento', 4, './img/animales/titan.jpg'),
(23, 'Lila', 'Gato', 'Europeo', 2, 'Hembra', 'Atigrada, cazadora nata. Ideal para casa con jardín seguro.', '2025-07-08', 'Disponible', 5, './img/animales/lila.jpg'),
(24, 'Sam', 'Perro', 'Cocker Spaniel', 4, 'Macho', 'Orejas largas y carácter dulce. Le encanta el agua y los paseos.', '2025-06-20', 'Reservado', 1, './img/animales/sam.jpg'),
(25, 'Mimi', 'Gato', 'Siamés', 3, 'Hembra', 'Charlatana y muy apegada a su dueño. No le gusta estar sola mucho tiempo.', '2025-07-15', 'Disponible', 2, './img/animales/mimi.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animal_vacunas`
--

CREATE TABLE `animal_vacunas` (
  `id_animal` int(11) NOT NULL,
  `id_vacuna` int(11) NOT NULL,
  `fecha_aplicacion` date DEFAULT NULL,
  `fecha_proxima` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `animal_vacunas`
--

INSERT INTO `animal_vacunas` (`id_animal`, `id_vacuna`, `fecha_aplicacion`, `fecha_proxima`) VALUES
(1, 1, '2025-01-15', '2026-01-15'),
(1, 2, '2025-01-15', '2026-01-15'),
(2, 1, '2024-12-10', '2025-12-10'),
(3, 3, '2025-03-10', '2026-03-10'),
(4, 4, '2025-01-20', '2026-01-20'),
(5, 5, '2025-04-25', '2026-04-25'),
(9, 1, '2025-06-15', '2026-06-15'),
(9, 2, '2025-06-15', '2026-06-15'),
(10, 3, '2025-05-25', '2026-05-25'),
(11, 1, '2025-04-20', '2026-04-20'),
(11, 2, '2025-04-20', '2026-04-20'),
(12, 3, '2025-07-05', '2026-07-05'),
(13, 1, '2025-06-30', '2026-06-30'),
(14, 1, '2025-07-10', '2026-07-10'),
(15, 3, '2025-06-05', '2026-06-05'),
(16, 1, '2025-04-15', '2026-04-15'),
(16, 2, '2025-04-15', '2026-04-15'),
(17, 3, '2025-07-15', '2026-07-15'),
(17, 5, '2025-07-15', '2026-07-15'),
(18, 1, '2025-06-20', '2026-06-20'),
(18, 2, '2025-06-20', '2026-06-20'),
(19, 3, '2025-07-17', '2026-07-17'),
(20, 1, '2025-05-10', '2026-05-10'),
(21, 3, '2025-04-25', '2026-04-25'),
(22, 1, '2025-07-05', '2026-07-05'),
(22, 4, '2025-07-05', '2026-07-05'),
(23, 3, '2025-07-13', '2026-07-13'),
(24, 1, '2025-06-25', '2026-06-25'),
(24, 2, '2025-06-25', '2026-06-25'),
(25, 3, '2025-07-20', '2026-07-20'),
(25, 5, '2025-07-20', '2026-07-20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `centros`
--

CREATE TABLE `centros` (
  `id_centro` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `web` varchar(150) DEFAULT NULL,
  `fecha_alta` datetime DEFAULT current_timestamp(),
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(10,8) DEFAULT NULL,
  `imagen_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `centros`
--

INSERT INTO `centros` (`id_centro`, `nombre`, `direccion`, `telefono`, `email`, `web`, `fecha_alta`, `latitud`, `longitud`, `imagen_url`) VALUES
(1, 'Refugio Patitas Felices', 'Puerta del Sol, 1, 28013 Madrid', '600123456', 'contacto@patitasfelices.org', 'www.patitasfelices.org', '2025-11-02 17:25:55', 40.41688900, -3.70336000, NULL),
(2, 'Hogar Animal Madrid', 'Calle de Bravo Murillo, 85, 28003 Madrid', '610987654', 'info@hogaranimal.org', 'www.hogaranimal.org', '2025-11-02 17:25:55', 40.42908000, -3.70156000, NULL),
(3, 'Protectora Huellitas', 'Avenida de la Albufera, 285, 28041 Madrid', '620555333', 'huellitas@protectora.org', 'www.huellitas.org', '2025-11-02 17:25:55', 40.38960000, -3.67815000, NULL),
(4, 'Asociación Paticorazones', 'Calle de Raimundo Fernández Villaverde, 65, 28003 Madrid', '644222999', 'info@paticorazones.org', 'www.paticorazones.org', '2025-11-02 17:25:55', 40.45320000, -3.68826000, NULL),
(5, 'Centro Animalife', 'Calle de la Princesa, 58, 28008 Madrid', '655444888', 'info@animalife.org', 'www.animalife.org', '2025-11-02 17:25:55', 40.43410000, -3.71262000, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id_contacto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `asunto` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_contacto` datetime DEFAULT current_timestamp(),
  `estado` enum('Pendiente','Respondido','Archivado') DEFAULT 'Pendiente',
  `respuesta` text DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `id_pago` int(11) DEFAULT NULL,
  `numero_factura` varchar(50) DEFAULT NULL,
  `fecha_emision` datetime DEFAULT current_timestamp(),
  `pdf_dirr` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id_factura`, `id_pago`, `numero_factura`, `fecha_emision`, `pdf_dirr`) VALUES
(1, 1, 'FAC-2025-001', '2025-11-02 17:25:56', '/facturas/FAC-2025-001.pdf'),
(2, 2, 'FAC-2025-002', '2025-11-02 17:25:56', '/facturas/FAC-2025-002.pdf'),
(3, 3, 'FAC-2025-003', '2025-11-02 17:25:56', '/facturas/FAC-2025-003.pdf'),
(4, 4, 'FAC-2025-004', '2025-07-20 10:05:00', '/facturas/FAC-2025-004.pdf'),
(5, 5, 'FAC-2025-005', '2025-07-18 15:35:00', '/facturas/FAC-2025-005.pdf'),
(6, 6, 'FAC-2025-006', '2025-07-22 11:50:00', '/facturas/FAC-2025-006.pdf'),
(7, 7, 'FAC-2025-007', '2025-07-19 09:20:00', '/facturas/FAC-2025-007.pdf'),
(8, 8, 'FAC-2025-008', '2025-07-21 14:25:00', '/facturas/FAC-2025-008.pdf');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newsletter`
--

CREATE TABLE `newsletter` (
  `id_newsletter` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_centro` int(11) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `concepto` varchar(255) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('Tarjeta','PayPal','Transferencia') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_usuario`, `id_centro`, `monto`, `concepto`, `fecha_pago`, `metodo_pago`) VALUES
(1, 1, 1, 25.00, 'Vacuna antirrábica', '2025-11-02 17:25:56', 'Tarjeta'),
(2, 2, 3, 30.00, 'Vacuna triple felina', '2025-11-02 17:25:56', 'PayPal'),
(3, 4, 2, 50.00, 'Donación centro refugio', '2025-11-02 17:25:56', 'Transferencia'),
(4, 6, 1, 55.00, 'Vacunas completas Thor', '2025-07-20 10:00:00', 'Tarjeta'),
(5, 7, 2, 60.00, 'Chip y vacunas Simba', '2025-07-18 15:30:00', 'PayPal'),
(6, 8, 1, 25.00, 'Vacuna rabia Lola', '2025-07-22 11:45:00', 'Transferencia'),
(7, 9, 5, 70.00, 'Vacunas y desparasitación Leo', '2025-07-19 09:15:00', 'Tarjeta'),
(8, 10, 2, 65.00, 'Vacunas Rocko', '2025-07-21 14:20:00', 'PayPal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_rol` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `password_hash`, `telefono`, `direccion`, `fecha_registro`, `id_rol`) VALUES
(1, 'Ana', 'Gómez', 'ana@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '611111111', 'Calle Falsa 123, Madrid', '2025-11-02 17:25:55', 2),
(2, 'Carlos', 'López', 'carlos@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '622222222', 'Av. del Sol 44, Valencia', '2025-11-02 17:25:55', 2),
(3, 'María', 'Fernández', 'admin@adoptaweb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '633333333', 'Calle Mayor 10, Sevilla', '2025-11-02 17:25:55', 1),
(4, 'Lucía', 'Martín', 'lucia.martin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '644444444', 'Calle Nieve 22, Madrid', '2025-11-02 17:25:55', 2),
(6, 'Roberto', 'Díaz', 'roberto.diaz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '655555555', 'Calle Primavera 15, Barcelona', '2025-07-01 10:30:00', 2),
(7, 'Elena', 'Ruiz', 'elena.ruiz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '666666666', 'Av. Central 77, Valencia', '2025-07-05 14:20:00', 2),
(8, 'David', 'Sánchez', 'david.sanchez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '677777777', 'Calle Norte 33, Madrid', '2025-07-10 09:15:00', 2),
(9, 'Isabel', 'García', 'isabel.garcia@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '688888888', 'Calle Sur 21, Sevilla', '2025-07-12 16:45:00', 2),
(10, 'Javier', 'Moreno', 'javier.moreno@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '699999999', 'Av. Libertad 89, Zaragoza', '2025-07-15 11:00:00', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunas`
--

CREATE TABLE `vacunas` (
  `id_vacuna` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacunas`
--

INSERT INTO `vacunas` (`id_vacuna`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Rabia', 'Vacuna contra la rabia', 25.00),
(2, 'Moquillo', 'Vacuna polivalente para perros', 30.00),
(3, 'Triple felina', 'Vacuna combinada para gatos', 28.00),
(4, 'Parvovirus', 'Previene el parvovirus canino', 27.00),
(5, 'Leucemia felina', 'Vacuna para gatos jóvenes', 32.00),
(6, 'Polivalente canina', 'Protección múltiple para perros', 35.00),
(7, 'Gripe felina', 'Vacuna contra la gripe en gatos', 26.00),
(8, 'Leptospirosis', 'Enfermedad bacteriana en perros', 29.00),
(9, 'Panleucopenia felina', 'Vacuna esencial para gatos', 31.00),
(10, 'Bordetella', 'Tos de las perreras', 27.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adopciones`
--
ALTER TABLE `adopciones`
  ADD PRIMARY KEY (`id_adopcion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_animal` (`id_animal`);

--
-- Indices de la tabla `animales`
--
ALTER TABLE `animales`
  ADD PRIMARY KEY (`id_animal`),
  ADD KEY `id_centro` (`id_centro`);

--
-- Indices de la tabla `animal_vacunas`
--
ALTER TABLE `animal_vacunas`
  ADD PRIMARY KEY (`id_animal`,`id_vacuna`),
  ADD KEY `id_vacuna` (`id_vacuna`);

--
-- Indices de la tabla `centros`
--
ALTER TABLE `centros`
  ADD PRIMARY KEY (`id_centro`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id_contacto`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `id_pago` (`id_pago`);

--
-- Indices de la tabla `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id_newsletter`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_centro` (`id_centro`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD PRIMARY KEY (`id_vacuna`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adopciones`
--
ALTER TABLE `adopciones`
  MODIFY `id_adopcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `animales`
--
ALTER TABLE `animales`
  MODIFY `id_animal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `centros`
--
ALTER TABLE `centros`
  MODIFY `id_centro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id_newsletter` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  MODIFY `id_vacuna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adopciones`
--
ALTER TABLE `adopciones`
  ADD CONSTRAINT `adopciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `adopciones_ibfk_2` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`);

--
-- Filtros para la tabla `animales`
--
ALTER TABLE `animales`
  ADD CONSTRAINT `animales_ibfk_1` FOREIGN KEY (`id_centro`) REFERENCES `centros` (`id_centro`);

--
-- Filtros para la tabla `animal_vacunas`
--
ALTER TABLE `animal_vacunas`
  ADD CONSTRAINT `animal_vacunas_ibfk_1` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`),
  ADD CONSTRAINT `animal_vacunas_ibfk_2` FOREIGN KEY (`id_vacuna`) REFERENCES `vacunas` (`id_vacuna`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_centro`) REFERENCES `centros` (`id_centro`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
