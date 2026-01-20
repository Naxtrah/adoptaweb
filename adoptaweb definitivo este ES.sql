-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-01-2026 a las 16:10:44
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `asignar_vacunas_aleatorias` ()   BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE v_id_animal INT;
  DECLARE v_cantidad_vacunas INT;
  DECLARE v_fecha_aplicacion DATE;
  DECLARE v_fecha_proxima DATE;
  DECLARE v_id_vacuna INT;
  DECLARE v_contador INT;

  DECLARE cur CURSOR FOR SELECT id_animal FROM temp_animales;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur;

  read_loop: LOOP
    FETCH cur INTO v_id_animal;
    IF done THEN
      LEAVE read_loop;
    END IF;

    -- Número aleatorio de vacunas entre 2 y 5
    SET v_cantidad_vacunas = FLOOR(RAND() * (@max_vacunas - @min_vacunas + 1)) + @min_vacunas;

    -- Seleccionar vacunas aleatorias sin repetir
    SET v_contador = 0;
    WHILE v_contador < v_cantidad_vacunas DO
      -- Vacuna aleatoria
      SELECT id_vacuna INTO v_id_vacuna
      FROM temp_vacunas
      ORDER BY RAND()
      LIMIT 1;

      -- Fecha aleatoria de aplicación
      SET v_fecha_aplicacion = DATE_ADD(@fecha_inicio, INTERVAL FLOOR(RAND() * DATEDIFF(@fecha_fin, @fecha_inicio)) DAY);
      SET v_fecha_proxima = DATE_ADD(v_fecha_aplicacion, INTERVAL FLOOR(RAND() * 335 + 30) DAY);

      -- Insertar si no existe
      INSERT IGNORE INTO animal_vacunas (id_animal, id_vacuna, fecha_aplicacion, fecha_proxima)
      VALUES (v_id_animal, v_id_vacuna, v_fecha_aplicacion, v_fecha_proxima);

      SET v_contador = v_contador + 1;
    END WHILE;

  END LOOP;

  CLOSE cur;
END$$

DELIMITER ;

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
(4, 6, 9, '2025-07-20', '2026-01-20', 'Aprobada', 'Familia con experiencia en Huskies. Tienen patio grande.'),
(5, 7, 11, '2025-07-18', '2025-07-25', 'Aprobada', 'Adoptado por pareja sin hijos. Experiencia con pastores.'),
(6, 8, 14, '2025-07-22', NULL, 'Rechazada', 'Persona mayor que busca compañía. Vive en apartamento.'),
(7, 9, 18, '2025-07-19', NULL, 'Pendiente', 'Familia activa con niños. Tienen jardín.'),
(8, 10, 20, '2025-07-21', '2025-07-28', 'Aprobada', 'Adoptado por familia con otro perro. Buen ambiente.'),
(9, 1, 25, '2026-01-19', NULL, 'Aprobada', 'Motivación: awerfqwer\nExperiencia: qwerqwer\nTipo de vivienda: Casa con jardín\nOtros animales: qwqwerqw\nHoras solo al día: 4-8 horas'),
(10, 1, 17, '2026-01-19', '2026-01-19', 'Aprobada', 'Motivación: asdfasdfas\nExperiencia: afdasfdas\nTipo de vivienda: Casa con jardín\nOtros animales: asdfasdfasd\nHoras solo al día: 4-8 horas'),
(11, 1, 19, '2026-01-20', '2026-01-20', 'Aprobada', 'Motivación: asdfasfd\nExperiencia: asdfasdfas\nTipo de vivienda: Casa con jardín\nOtros animales: asdfasfdas\nHoras solo al día: 4-8 horas'),
(12, 11, 10, '2026-01-20', '2026-01-20', 'Aprobada', 'Motivación: Porque tengo una gatita que ya es mayor y ha vivido toda su vida con hermanitos pero ahora que me he mudado se siente un poco sola\nExperiencia: Si he tenido gatos desde que era pequeña y tengo el titulo de aux vet\nTipo de vivienda: Piso\nOtros animales: Si una  gatita de 6 años\nHoras solo al día: 4-8 horas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adopciones_pagos`
--

CREATE TABLE `adopciones_pagos` (
  `id` int(11) NOT NULL,
  `id_adopcion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Pagado','Cancelado') DEFAULT 'Pendiente',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `fecha_pago` datetime DEFAULT NULL,
  `token_pago` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adopciones_pagos`
--

INSERT INTO `adopciones_pagos` (`id`, `id_adopcion`, `id_usuario`, `monto`, `estado`, `fecha_creacion`, `fecha_pago`, `token_pago`) VALUES
(1, 9, 1, 145.00, 'Pagado', '2026-01-19 17:59:19', '2026-01-20 05:15:37', '2425356ae7572ea7af885c26b21a06977bb63518f9f32d6a75a429c707486bf2'),
(2, 10, 1, 113.00, 'Pagado', '2026-01-19 23:37:30', '2026-01-19 23:39:59', '190f1058f27566e9ca335b0e2a20d1450a2bc44c7376fc692fe119a0aa514e1a'),
(3, 11, 1, 128.00, 'Pagado', '2026-01-20 11:19:08', '2026-01-20 11:21:26', '34f6b9d3e1f9b42220b30aaab9696f28765d1dcd75d6ec4adb26c6d84cabd135'),
(4, 12, 11, 116.00, 'Pagado', '2026-01-20 15:14:52', '2026-01-20 15:17:20', '050840c99e2d5595aca4029503c45738f5ea390154713ec4f1b85cfd64e400df'),
(5, 4, 6, 130.00, 'Pendiente', '2026-01-20 16:06:08', NULL, 'b45353aacc69aedc996eae348b762a1c82ce2703d05b9b98205f1c361bb757eb');

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
(9, 'Thor', 'Perro', 'Husky Siberiano', 4, 'Macho', 'Ojos azules, muy activo y necesita mucho ejercicio. Sociable con otros perros.', '2025-06-10', 'Adoptado', 1, './img/animales/thor.jpg'),
(10, 'Kira', 'Gato', 'Mestizo', 2, 'Hembra', 'Tímida al principio pero muy cariñosa cuando confía. Prefiere hogares tranquilos.', '2025-05-20', 'Adoptado', 3, './img/animales/kira.jpg'),
(11, 'Simba', 'Perro', 'Pastor Alemán', 3, 'Macho', 'Muy inteligente y leal. Necesita dueño con experiencia en razas grandes.', '2025-04-15', 'Adoptado', 2, './img/animales/simba.jpg'),
(12, 'Mika', 'Gato', 'Angora Turco', 5, 'Hembra', 'Pelo largo y sedoso. Tranquila y elegante. Ideal para apartamentos.', '2025-07-01', 'Disponible', 5, './img/animales/mika.jpg'),
(13, 'Bruno', 'Perro', 'Bulldog Francés', 2, 'Macho', 'Divertido y juguetón. Perfecto para familias. Ronca adorablemente.', '2025-06-25', 'Reservado', 4, './img/animales/bruno.jpg'),
(14, 'Lola', 'Perro', 'Chihuahua', 1, 'Hembra', 'Pequeña pero con mucho carácter. Se lleva bien con niños mayores.', '2025-07-05', 'Disponible', 1, './img/animales/lola.jpg'),
(15, 'Oliver', 'Gato', 'British Shorthair', 4, 'Macho', 'Carácter tranquilo y apacible. Le gusta la rutina y los mimos suaves.', '2025-05-30', 'Disponible', 2, './img/animales/oliver.jpg'),
(16, 'Rex', 'Perro', 'Rottweiler', 6, 'Macho', 'Maduro y equilibrado. Excelente guardián. Necesita dueño experimentado.', '2025-04-10', 'En tratamiento', 3, './img/animales/rex.jpg'),
(17, 'Maya', 'Gato', 'Sphynx', 3, 'Hembra', 'Sin pelo, necesita protección contra el frío. Muy cariñosa y activa.', '2025-07-10', 'Reservado', 4, './img/animales/maya.jpg'),
(18, 'Leo', 'Perro', 'Border Collie', 2, 'Macho', 'Extremadamente inteligente y enérgico. Necesita mucho ejercicio mental y físico.', '2025-06-15', 'Disponible', 5, './img/animales/leo.jpg'),
(19, 'Zoe', 'Gato', 'Mestizo', 1, 'Hembra', 'Gatita juguetona y curiosa. Ideal para adoptar junto a otro gato.', '2025-07-12', 'Adoptado', 1, './img/animales/zoe.jpg'),
(20, 'Rocko', 'Perro', 'Boxer', 5, 'Macho', 'Energético y amigable. Se lleva bien con niños. Juguetón pero obediente.', '2025-05-05', 'Adoptado', 2, './img/animales/rocko.jpg'),
(21, 'Cleo', 'Gato', 'Persa', 6, 'Hembra', 'Tranquila y aristocrática. Necesita cepillado diario por su pelo largo.', '2025-04-20', 'Adoptado', 3, './img/animales/cleo.jpg'),
(22, 'Titan', 'Perro', 'Mastín Español', 7, 'Macho', 'Gigante gentil. Paciente con niños. Necesita espacio por su tamaño.', '2025-06-30', 'En tratamiento', 4, './img/animales/titan.jpg'),
(23, 'Lila', 'Gato', 'Europeo', 2, 'Hembra', 'Atigrada, cazadora nata. Ideal para casa con jardín seguro.', '2025-07-08', 'Disponible', 5, './img/animales/lila.jpg'),
(24, 'Sam', 'Perro', 'Cocker Spaniel', 4, 'Macho', 'Orejas largas y carácter dulce. Le encanta el agua y los paseos.', '2025-06-20', 'Reservado', 1, './img/animales/sam.jpg'),
(25, 'Mimi', 'Gato', 'Siamés', 3, 'Hembra', 'Charlatana y muy apegada a su dueño. No le gusta estar sola mucho tiempo.', '2025-07-15', 'Adoptado', 2, './img/animales/mimi.jpg');

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
(1, 3, '2025-07-17', '2026-05-07'),
(1, 19, '2025-07-18', '2025-10-08'),
(2, 2, '2025-06-03', '2025-12-12'),
(2, 13, '2025-09-10', '2025-12-01'),
(2, 14, '2025-04-08', '2026-02-18'),
(3, 1, '2025-08-11', '2026-06-03'),
(3, 3, '2025-05-30', '2026-02-02'),
(3, 9, '2025-11-14', '2026-09-03'),
(3, 14, '2025-03-11', '2026-01-03'),
(3, 15, '2025-05-08', '2026-01-08'),
(4, 5, '2025-07-10', '2025-09-26'),
(4, 6, '2025-06-21', '2026-02-09'),
(5, 3, '2025-02-18', '2025-08-26'),
(5, 14, '2025-09-21', '2026-07-06'),
(5, 18, '2025-02-03', '2025-11-28'),
(5, 19, '2025-08-26', '2026-01-28'),
(6, 12, '2025-03-14', '2025-12-14'),
(6, 19, '2025-01-27', '2025-12-30'),
(7, 3, '2025-07-19', '2026-01-08'),
(7, 12, '2025-05-09', '2025-12-06'),
(7, 17, '2025-07-31', '2026-07-10'),
(8, 10, '2025-01-08', '2025-12-11'),
(8, 18, '2025-10-10', '2026-01-06'),
(9, 2, '2025-04-14', '2025-11-01'),
(9, 9, '2025-08-12', '2026-02-28'),
(9, 15, '2025-04-19', '2025-07-07'),
(9, 17, '2025-04-30', '2026-01-31'),
(10, 4, '2025-05-10', '2025-10-19'),
(10, 8, '2025-12-09', '2026-06-15'),
(10, 17, '2025-07-10', '2026-04-07'),
(10, 18, '2025-02-14', '2025-10-06'),
(11, 1, '2025-10-24', '2026-09-02'),
(11, 11, '2025-03-26', '2025-08-17'),
(11, 18, '2025-01-06', '2025-12-27'),
(11, 19, '2025-01-03', '2025-06-08'),
(12, 2, '2025-12-25', '2026-11-23'),
(12, 5, '2025-12-12', '2026-12-07'),
(12, 16, '2025-09-08', '2026-03-07'),
(12, 18, '2025-06-09', '2026-04-14'),
(12, 20, '2025-07-04', '2026-01-11'),
(13, 4, '2025-04-18', '2025-11-06'),
(13, 16, '2025-05-03', '2025-07-15'),
(14, 1, '2025-02-20', '2025-09-24'),
(14, 2, '2025-02-03', '2025-07-05'),
(14, 6, '2025-04-11', '2025-05-29'),
(14, 20, '2025-04-22', '2025-06-24'),
(15, 6, '2025-12-28', '2026-02-10'),
(15, 9, '2025-03-10', '2025-09-11'),
(15, 11, '2025-07-01', '2025-11-16'),
(15, 17, '2025-05-29', '2026-02-25'),
(15, 19, '2025-11-23', '2025-12-31'),
(16, 11, '2025-10-31', '2026-07-18'),
(16, 15, '2025-10-25', '2026-01-30'),
(17, 10, '2025-12-19', '2026-09-01'),
(17, 12, '2025-01-18', '2025-03-06'),
(17, 18, '2025-10-24', '2026-04-23'),
(17, 20, '2025-05-05', '2025-12-21'),
(18, 16, '2025-11-03', '2026-08-20'),
(18, 17, '2025-07-08', '2026-02-16'),
(18, 18, '2025-11-29', '2026-01-30'),
(18, 19, '2025-03-08', '2025-12-30'),
(19, 5, '2025-09-09', '2026-08-08'),
(19, 7, '2025-09-13', '2025-10-15'),
(19, 15, '2025-06-25', '2026-03-09'),
(19, 20, '2025-06-21', '2025-09-02'),
(20, 14, '2025-12-30', '2026-05-11'),
(20, 15, '2025-04-21', '2025-11-19'),
(20, 17, '2025-11-11', '2026-03-20'),
(21, 7, '2025-01-04', '2025-11-13'),
(21, 11, '2025-04-26', '2025-07-24'),
(21, 12, '2025-10-27', '2026-10-11'),
(21, 16, '2025-04-08', '2025-08-01'),
(21, 19, '2025-01-29', '2025-06-04'),
(22, 2, '2025-07-25', '2026-02-28'),
(22, 11, '2025-11-04', '2025-12-29'),
(22, 12, '2025-01-02', '2025-12-15'),
(23, 5, '2025-01-14', '2025-10-21'),
(23, 13, '2025-10-18', '2026-09-24'),
(23, 19, '2025-06-03', '2026-05-25'),
(24, 2, '2025-07-03', '2025-11-30'),
(24, 20, '2025-06-25', '2025-10-28'),
(25, 7, '2025-12-30', '2026-08-16'),
(25, 11, '2025-09-21', '2026-03-13'),
(25, 13, '2025-05-21', '2025-06-30'),
(25, 19, '2025-08-29', '2026-06-18'),
(25, 20, '2025-12-10', '2026-06-04');

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
(4, 'Asociación Paticorazon', 'Calle de Raimundo Fernández Villaverde, 65, 28003 Madrid', '644222998', 'info@paticorazones.org', 'www.paticorazones.org', '2025-11-02 17:25:55', 40.45320000, -3.68826000, ''),
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

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id_contacto`, `nombre`, `email`, `telefono`, `asunto`, `mensaje`, `fecha_contacto`, `estado`, `respuesta`, `fecha_respuesta`) VALUES
(1, 'Mike', 'mike@gmail.com', '666666666', 'Consulta general', 'asdfdasfasdfsdf', '2026-01-19 20:02:18', 'Archivado', NULL, NULL);

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
(10, 14, 'FAC-2026-00014', '2026-01-20 05:15:37', 'FAC-2026-00014.pdf'),
(11, 15, 'FAC-2026-00015', '2026-01-20 11:21:26', 'FAC-2026-00015.pdf'),
(12, 16, 'FAC-2026-00016', '2026-01-20 15:17:20', 'FAC-2026-00016.pdf');

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

--
-- Volcado de datos para la tabla `newsletter`
--

INSERT INTO `newsletter` (`id_newsletter`, `email`, `fecha_registro`, `activo`) VALUES
(20, 'maria.gonzalez@gmail.com', '2025-01-15 10:30:00', 1),
(21, 'juan.perez@outlook.com', '2025-01-10 14:20:00', 0),
(22, 'laura.martinez@yahoo.com', '2025-01-20 09:15:00', 1),
(23, 'carlos.rodriguez@gmail.com', '2025-02-01 11:45:00', 1),
(24, 'ana.lopez@hotmail.com', '2025-02-05 16:30:00', 1),
(25, 'miguel.sanchez@gmail.com', '2025-02-10 09:00:00', 1),
(26, 'elena.garcia@yahoo.com', '2025-02-12 14:15:00', 0),
(27, 'david.fernandez@gmail.com', '2025-02-15 10:30:00', 1),
(28, 'sofia.ruiz@outlook.com', '2025-02-18 16:45:00', 1),
(29, 'javier.moreno@gmail.com', '2025-02-20 11:20:00', 1),
(30, 'erikaexposito2004@gmail.com', '2026-01-20 15:13:10', 1);

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
(9, 1, 4, 60.00, 'Adopción de Maya', '2026-01-19 23:39:59', 'Tarjeta'),
(14, 1, 2, 145.00, 'Vacunas de Mimi', '2026-01-20 05:15:37', 'PayPal'),
(15, 1, 1, 128.00, 'Vacunas de Zoe', '2026-01-20 11:21:26', 'PayPal'),
(16, 11, 3, 116.00, 'Vacunas de Kira', '2026-01-20 15:17:20', 'PayPal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_detalle`
--

CREATE TABLE `pagos_detalle` (
  `id` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `id_vacuna` int(11) NOT NULL,
  `cantidad` tinyint(4) DEFAULT 1,
  `precio_unitario` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_detalle`
--

INSERT INTO `pagos_detalle` (`id`, `id_pago`, `id_animal`, `id_vacuna`, `cantidad`, `precio_unitario`) VALUES
(1, 14, 25, 7, 1, 26.00),
(2, 14, 25, 11, 1, 28.00),
(3, 14, 25, 13, 1, 26.00),
(4, 14, 25, 19, 1, 35.00),
(5, 14, 25, 20, 1, 30.00),
(6, 15, 19, 5, 1, 32.00),
(7, 15, 19, 7, 1, 26.00),
(8, 15, 19, 15, 1, 40.00),
(9, 15, 19, 20, 1, 30.00),
(10, 16, 10, 4, 1, 27.00),
(11, 16, 10, 8, 1, 29.00),
(12, 16, 10, 17, 1, 29.00),
(13, 16, 10, 18, 1, 31.00);

--
-- Disparadores `pagos_detalle`
--
DELIMITER $$
CREATE TRIGGER `trg_calcular_monto_pago` AFTER INSERT ON `pagos_detalle` FOR EACH ROW BEGIN
  UPDATE pagos
  SET monto = (
    SELECT SUM(cantidad * precio_unitario)
    FROM pagos_detalle
    WHERE id_pago = NEW.id_pago
  )
  WHERE id_pago = NEW.id_pago;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_prevent_duplicado_vacuna` BEFORE INSERT ON `pagos_detalle` FOR EACH ROW BEGIN
  IF EXISTS (
    SELECT 1
    FROM pagos_detalle
    WHERE id_pago = NEW.id_pago
      AND id_animal = NEW.id_animal
      AND id_vacuna = NEW.id_vacuna
  ) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Error: Esta vacuna ya fue agregada al pago para este animal.';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `pagos_detalle_validados`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `pagos_detalle_validados` (
`id_pago` int(11)
,`id_animal` int(11)
,`animal_nombre` varchar(100)
,`id_vacuna` int(11)
,`vacuna_nombre` varchar(100)
,`descripcion` text
,`cantidad` tinyint(4)
,`precio_unitario` decimal(8,2)
,`subtotal` decimal(11,2)
);

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
  `tarjeta_last4` char(4) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `id_rol` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `password_hash`, `telefono`, `direccion`, `tarjeta_last4`, `fecha_registro`, `id_rol`) VALUES
(1, 'Ana', 'Gómez', 'ana@gmail.com', '$2y$10$BcxS3FsMVev7WCnd5UW1IeiEDtfyuIFu6IDY30LoJOUmMWGLhdYTO', '611111111', 'Calle Falsa 123, Madrid', NULL, '2025-11-02 17:25:55', 2),
(2, 'Carlos', 'López', 'carlos@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '622222222', 'Av. del Sol 44, Valencia', NULL, '2025-11-02 17:25:55', 2),
(3, 'Mike', 'Rinaldi', 'administrador.adoptaweb@gmail.com', '$2y$10$pWwD1TLSvoFOV1o1xcZBne77x/kOecC0WcILWEviXi.owVdNtc8HG', '633333333', 'Calle Mayor 10, Sevilla', NULL, '2025-11-02 17:25:55', 1),
(4, 'Lucía', 'Martín', 'lucia.martin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '644444444', 'Calle Nieve 22, Madrid', NULL, '2025-11-02 17:25:55', 2),
(6, 'Roberto', 'Díaz', 'roberto.diaz@gmail.com', '$2y$10$TaHZ2M3aIqUuABfMiS8P2OM3p18qbM5WhIQrE/Sx7/UriVb5TG71u', '655555555', 'Calle Primavera 15, Barcelona', NULL, '2025-07-01 10:30:00', 2),
(7, 'Elena', 'Ruiz', 'elena.ruiz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '666666666', 'Av. Central 77, Valencia', NULL, '2025-07-05 14:20:00', 2),
(8, 'David', 'Sánchez', 'david.sanchez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '677777777', 'Calle Norte 33, Madrid', NULL, '2025-07-10 09:15:00', 2),
(9, 'Isabel', 'García', 'isabel.garcia@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '688888888', 'Calle Sur 21, Sevilla', NULL, '2025-07-12 16:45:00', 2),
(10, 'Javier', 'Moreno', 'javier.moreno@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '699999999', 'Av. Libertad 89, Zaragoza', NULL, '2025-07-15 11:00:00', 2),
(11, 'Erika', 'Exposito', 'erikaexposito2004@gmail.com', '$2y$10$/mfsLCrPByvjnDIrosnYBulub9jmbqGASRczWjQ/NxF6EUmoiuFxK', '657776236', 'URB Pryconsa 13 1º3', NULL, '2026-01-20 15:13:10', 2);

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
(3, 'Triple felina', 'Vacuna combinada para gatos', 26.00),
(4, 'Parvovirus', 'Previene el parvovirus canino', 27.00),
(5, 'Leucemia felina', 'Vacuna para gatos jóvenes', 32.00),
(6, 'Polivalente canina', 'Protección múltiple para perros', 35.00),
(7, 'Gripe felina', 'Vacuna contra la gripe en gatos', 26.00),
(8, 'Leptospirosis', 'Enfermedad bacteriana en perros', 29.00),
(9, 'Panleucopenia felina', 'Vacuna esencial para gatos', 31.00),
(10, 'Bordetella', 'Tos de las perreras', 27.00),
(11, 'Hepatitis canina', 'Vacuna contra la hepatitis infecciosa en perros', 28.00),
(12, 'Parainfluenza canina', 'Componente de la vacuna polivalente', 25.00),
(13, 'Coronavirus canino', 'Prevención de enteritis viral canina', 26.00),
(14, 'Giardia', 'Vacuna contra giardia en perros', 24.00),
(15, 'Peritonitis infecciosa felina', 'Vacuna contra FIP en gatos', 40.00),
(16, 'Calicivirus felino', 'Componente de la triple felina', 27.00),
(17, 'Herpesvirus felino', 'Rinotraqueítis viral', 29.00),
(18, 'Clamidiosis felina', 'Vacuna contra clamidia en gatos', 31.00),
(19, 'Tétanos animal', 'Vacuna contra el tétanos en animales grandes', 35.00),
(20, 'Parvovirus felino', 'Panleucopenia felina reforzada', 30.00);

-- --------------------------------------------------------

--
-- Estructura para la vista `pagos_detalle_validados`
--
DROP TABLE IF EXISTS `pagos_detalle_validados`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pagos_detalle_validados`  AS SELECT `pd`.`id_pago` AS `id_pago`, `pd`.`id_animal` AS `id_animal`, `a`.`nombre` AS `animal_nombre`, `v`.`id_vacuna` AS `id_vacuna`, `v`.`nombre` AS `vacuna_nombre`, `v`.`descripcion` AS `descripcion`, `pd`.`cantidad` AS `cantidad`, `pd`.`precio_unitario` AS `precio_unitario`, `pd`.`cantidad`* `pd`.`precio_unitario` AS `subtotal` FROM ((`pagos_detalle` `pd` left join `vacunas` `v` on(`v`.`id_vacuna` = `pd`.`id_vacuna`)) left join `animales` `a` on(`a`.`id_animal` = `pd`.`id_animal`)) ;

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
-- Indices de la tabla `adopciones_pagos`
--
ALTER TABLE `adopciones_pagos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token_pago` (`token_pago`),
  ADD KEY `id_adopcion` (`id_adopcion`),
  ADD KEY `id_usuario` (`id_usuario`);

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
-- Indices de la tabla `pagos_detalle`
--
ALTER TABLE `pagos_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pago` (`id_pago`),
  ADD KEY `id_vacuna` (`id_vacuna`),
  ADD KEY `fk_pagos_detalle_animal_vacuna` (`id_animal`,`id_vacuna`);

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
  MODIFY `id_adopcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `adopciones_pagos`
--
ALTER TABLE `adopciones_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id_contacto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id_newsletter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `pagos_detalle`
--
ALTER TABLE `pagos_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  MODIFY `id_vacuna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
-- Filtros para la tabla `adopciones_pagos`
--
ALTER TABLE `adopciones_pagos`
  ADD CONSTRAINT `adopciones_pagos_ibfk_1` FOREIGN KEY (`id_adopcion`) REFERENCES `adopciones` (`id_adopcion`),
  ADD CONSTRAINT `adopciones_pagos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

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
-- Filtros para la tabla `pagos_detalle`
--
ALTER TABLE `pagos_detalle`
  ADD CONSTRAINT `fk_pagos_detalle_animal_vacuna` FOREIGN KEY (`id_animal`,`id_vacuna`) REFERENCES `animal_vacunas` (`id_animal`, `id_vacuna`),
  ADD CONSTRAINT `pagos_detalle_ibfk_1` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`),
  ADD CONSTRAINT `pagos_detalle_ibfk_2` FOREIGN KEY (`id_vacuna`) REFERENCES `vacunas` (`id_vacuna`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
