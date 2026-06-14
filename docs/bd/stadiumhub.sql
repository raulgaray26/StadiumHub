-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2026 a las 21:40:21
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
-- Base de datos: `stadiumhub`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadios`
--

CREATE TABLE `estadios` (
  `estadio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre oficial del estadio',
  `pais` varchar(100) NOT NULL COMMENT 'País sede (Estados Unidos / México / Canadá)',
  `ciudad` varchar(100) NOT NULL COMMENT 'Ciudad donde se ubica',
  `capacidad` int(11) NOT NULL DEFAULT 0 COMMENT 'Aforo máximo de espectadores',
  `dimensiones` varchar(100) DEFAULT NULL COMMENT 'Dimensiones del campo en metros (largo x ancho)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estadios sede de la Copa del Mundo FIFA 2026';

--
-- Volcado de datos para la tabla `estadios`
--

INSERT INTO `estadios` (`estadio_id`, `nombre`, `pais`, `ciudad`, `capacidad`, `dimensiones`) VALUES
(1, 'SoFi Stadium', 'Estados Unidos', 'Los Ángeles', 70240, '110 x 72 metros'),
(2, 'MetLife Stadium', 'Estados Unidos', 'East Rutherford', 82500, '110 x 72 metros'),
(3, 'AT&T Stadium', 'Estados Unidos', 'Arlington', 80000, '110 x 74 metros'),
(4, 'Estadio Azteca', 'México', 'Ciudad de México', 87523, '105 x 68 metros'),
(5, 'Estadio BBVA', 'México', 'Monterrey', 53500, '105 x 70 metros'),
(6, 'BC Place', 'Canadá', 'Vancouver', 54500, '110 x 72 metros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_tareas`
--

CREATE TABLE `historial_tareas` (
  `historial_id` bigint(20) UNSIGNED NOT NULL,
  `tarea_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → tareas.tarea_id',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → usuarios.user_id (actor de la acción)',
  `creador_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → usuarios.user_id (quien originó el evento)',
  `accion` varchar(100) NOT NULL COMMENT 'Tipo de evento: Creada | Asignada | Completada | Editada',
  `estado_anterior` varchar(100) DEFAULT NULL COMMENT 'Estado de la tarea antes del evento',
  `estado_nuevo` varchar(100) DEFAULT NULL COMMENT 'Estado de la tarea después del evento',
  `detalle` text DEFAULT NULL COMMENT 'Descripción legible del evento registrado',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora del evento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de auditoría global del sistema StadiumHub';

--
-- Volcado de datos para la tabla `historial_tareas`
--

INSERT INTO `historial_tareas` (`historial_id`, `tarea_id`, `user_id`, `creador_id`, `accion`, `estado_anterior`, `estado_nuevo`, `detalle`, `timestamp`) VALUES
(1, 1, 2, 2, 'Creada', NULL, 'Pendiente', 'Tarea \"Riego zona norte — Sectores A1-A3\" creada por el jefe Carlos Herrera López', '2026-06-13 13:30:00'),
(2, 1, 4, 2, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Miguel Ángel Reyes por el jefe Carlos Herrera López', '2026-06-13 13:35:00'),
(3, 1, 5, 2, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Laura Sofía Mendoza por el jefe Carlos Herrera López', '2026-06-13 13:36:00'),
(4, 2, 2, 2, 'Creada', NULL, 'Pendiente', 'Tarea \"Corte de césped previo al partido\" creada por el jefe Carlos Herrera López', '2026-06-12 19:00:00'),
(5, 2, 4, 2, 'Asignada', NULL, 'En Progreso', 'Tarea asignada al técnico Miguel Ángel Reyes por el jefe Carlos Herrera López', '2026-06-12 19:10:00'),
(6, 3, 2, 2, 'Creada', NULL, 'Pendiente', 'Tarea \"Aplicación preventiva de fungicida\" creada por el jefe Carlos Herrera López', '2026-06-13 14:00:00'),
(7, 3, 5, 2, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Laura Sofía Mendoza por el jefe Carlos Herrera López', '2026-06-13 14:05:00'),
(8, 4, 2, 2, 'Creada', NULL, 'Pendiente', 'Tarea \"Inspección física de sensores — Línea de media cancha\" creada por el jefe Carlos Herrera López', '2026-06-11 15:00:00'),
(9, 4, 4, 2, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Miguel Ángel Reyes por el jefe Carlos Herrera López', '2026-06-11 15:05:00'),
(10, 4, 4, 4, 'Completada', 'Pendiente', 'Completada', 'Tarea completada por el técnico Miguel Ángel Reyes. Obs: Los tres sensores inspeccionados. MC-07 y MC-08 requieren reemplazo.', '2026-06-11 21:45:00'),
(11, 5, 3, 3, 'Creada', NULL, 'Pendiente', 'Tarea \"Fertilización nitrogenada — Zonas de portería\" creada por el jefe Roberto Zúñiga Pérez', '2026-06-13 15:00:00'),
(12, 5, 6, 3, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Andrés Cisneros Vega por el jefe Roberto Zúñiga Pérez', '2026-06-13 15:10:00'),
(13, 5, 7, 3, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Patricia Olvera Ruiz por el jefe Roberto Zúñiga Pérez', '2026-06-13 15:11:00'),
(14, 6, 3, 3, 'Creada', NULL, 'Pendiente', 'Tarea \"Reposición de lámparas UV — Zona central\" creada por el jefe Roberto Zúñiga Pérez', '2026-06-12 16:30:00'),
(15, 6, 6, 3, 'Asignada', NULL, 'En Progreso', 'Tarea asignada al técnico Andrés Cisneros Vega por el jefe Roberto Zúñiga Pérez', '2026-06-12 16:40:00'),
(16, 7, 3, 3, 'Creada', NULL, 'Pendiente', 'Tarea \"Ciclo de riego nocturno — Campo completo\" creada por el jefe Roberto Zúñiga Pérez', '2026-06-12 21:00:00'),
(17, 7, 7, 3, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico Patricia Olvera Ruiz por el jefe Roberto Zúñiga Pérez', '2026-06-12 21:10:00'),
(18, 7, 7, 7, 'Completada', 'Pendiente', 'Completada', 'Tarea completada por la técnico Patricia Olvera Ruiz. Obs: Ciclo ejecutado. Aspersor B-14 con presión baja, ajustado manualmente.', '2026-06-13 09:15:00'),
(19, 8, 9, 9, 'Creada', NULL, 'Pendiente', 'Tarea \"Podar Césped en Sector 5\" creada por el jefe Raúl Garay', '2026-06-15 00:17:41'),
(20, 8, 8, 9, 'Asignada', NULL, 'Pendiente', 'Tarea asignada al técnico ID 8 por el jefe Raúl Garay', '2026-06-15 00:17:53'),
(21, 8, 8, 8, 'Completada', 'Pendiente', 'Completada', 'Tarea completada por el técnico Sebastián Gomez. Obs: Se terminó la tarea sin novedades.', '2026-06-15 00:18:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre legible del rol',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción detallada del alcance del rol'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de roles de usuario del sistema StadiumHub';

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `nombre`, `descripcion`) VALUES
(1, 'Comité FIFA', 'Acceso global de solo lectura. Visualiza estadísticas y audita todas las interacciones del sistema en todos los estadios sede.'),
(2, 'Jefe de Mantenimiento', 'Acceso completo a la plataforma, limitado al estadio asignado. Puede crear tareas, asignarlas a técnicos y supervisar el avance.'),
(3, 'Técnico de Campo', 'Acceso móvil restringido. Visualiza únicamente las tareas que le han sido asignadas y puede marcarlas como completadas.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `tarea_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_tarea_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → tipo_tarea.tipo_tarea_id',
  `estadio_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → estadios.estadio_id',
  `creador_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → usuarios.user_id (Jefe que creó la tarea)',
  `titulo` varchar(255) NOT NULL COMMENT 'Título descriptivo de la tarea',
  `descripcion` text NOT NULL COMMENT 'Instrucciones detalladas para el técnico',
  `estado` varchar(50) NOT NULL DEFAULT 'Pendiente' COMMENT 'Estado del ciclo de vida: Pendiente | En Progreso | Completada',
  `fecha_limite` date NOT NULL COMMENT 'Fecha máxima de ejecución',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Datos adicionales en JSON (zona, prioridad, etc.)' CHECK (json_valid(`metadata`)),
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tareas de mantenimiento de césped por estadio';

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`tarea_id`, `tipo_tarea_id`, `estadio_id`, `creador_id`, `titulo`, `descripcion`, `estado`, `fecha_limite`, `metadata`, `timestamp`) VALUES
(1, 1, 1, 2, 'Riego zona norte — Sectores A1-A3', 'Los sensores de humedad en los sectores A1, A2 y A3 del extremo norte del campo muestran niveles por debajo del 35% de humedad volumétrica. Activar ciclo de riego de 45 minutos con aspersores al 70% de presión. Verificar cobertura uniforme y reportar lectura post-riego.', 'Pendiente', '2026-06-18', '{\"zona\": \"Norte\", \"sectores\": [\"A1\",\"A2\",\"A3\"], \"prioridad\": \"Alta\", \"humedad_objetivo\": \"55-60%\"}', '2026-06-13 13:30:00'),
(2, 2, 1, 2, 'Corte de césped previo al partido — Campo completo', 'Realizar el corte de uniformización del césped híbrido a 28mm de altura en todo el campo, 48 horas antes del partido. Usar la segadora de bandas para evitar marcas de rodadas visibles en transmisión HD. Verificar que el patrón de corte sea en franjas alternas de 6 metros.', 'En Progreso', '2026-06-19', '{\"zona\": \"Campo completo\", \"altura_objetivo_mm\": 28, \"patron\": \"franjas_alternas\", \"prioridad\": \"Crítica\"}', '2026-06-12 19:00:00'),
(3, 6, 1, 2, 'Aplicación preventiva de fungicida — Zona sur', 'El sistema de alertas predictivas ha detectado condiciones favorables para el desarrollo de Pythium en la zona sur (temperatura de raíz >22°C con humedad >70%). Aplicar Mancozeb 80WP en concentración 2.5g/L. Cubrir sectores C1 al C4. Registrar cantidad exacta aplicada.', 'Pendiente', '2026-06-20', '{\"zona\": \"Sur\", \"sectores\": [\"C1\",\"C2\",\"C3\",\"C4\"], \"producto\": \"Mancozeb 80WP\", \"concentracion\": \"2.5g/L\", \"prioridad\": \"Alta\"}', '2026-06-13 14:00:00'),
(4, 4, 1, 2, 'Inspección física de sensores — Línea de media cancha', 'Tras el entrenamiento de ayer, se reportaron 3 sensores potencialmente dañados en la línea de media cancha (coordenadas MC-07, MC-08, MC-12). Realizar inspección física, documentar estado con fotografías y reportar si requieren reemplazo. Los sensores dañados deben desactivarse del sistema hasta su reposición.', 'Completada', '2026-06-14', '{\"zona\": \"Media cancha\", \"sensores\": [\"MC-07\",\"MC-08\",\"MC-12\"], \"prioridad\": \"Media\"}', '2026-06-11 15:00:00'),
(5, 3, 4, 3, 'Fertilización nitrogenada — Zonas de portería', 'Aplicar fertilizante nitrogenado de liberación lenta (NPK 30-0-0) en los 16.5m² de cada área de portería. La densidad del césped en estas zonas ha bajado un 15% respecto al promedio histórico. Dosis recomendada: 4kg por zona. Registrar antes y después con el sensor de densidad portátil.', 'Pendiente', '2026-06-21', '{\"zona\": \"Porterías\", \"producto\": \"NPK 30-0-0\", \"dosis_kg\": 4, \"areas\": 2, \"prioridad\": \"Alta\"}', '2026-06-13 15:00:00'),
(6, 7, 4, 3, 'Reposición de lámparas UV — Zona central', 'El análisis de DLI (Daily Light Integral) indica que el sector central del campo recibe menos de 12 mol/m²/d de luz fotosintéticamente activa por la sombra generada por el techo retráctil. Instalar y encender 6 lámparas móviles de crecimiento HPS de 1000W en la configuración 2x3 sobre el círculo central.', 'En Progreso', '2026-06-17', '{\"zona\": \"Centro\", \"lamparas\": 6, \"tipo\": \"HPS 1000W\", \"dli_actual\": 10.2, \"dli_objetivo\": 15, \"prioridad\": \"Alta\"}', '2026-06-12 16:30:00'),
(7, 1, 4, 3, 'Ciclo de riego nocturno — Campo completo', 'Programar y supervisar el ciclo de riego nocturno (02:00-04:00h) en todo el campo. Los sensores indican que la temperatura del suelo ha superado los 28°C durante el día, lo que aumenta la evapotranspiración. Verificar que todos los aspersores del circuito principal funcionan correctamente antes de iniciar.', 'Completada', '2026-06-13', '{\"zona\": \"Campo completo\", \"horario\": \"02:00-04:00\", \"prioridad\": \"Media\", \"temperatura_suelo\": \"28C\"}', '2026-06-12 21:00:00'),
(8, 2, 3, 9, 'Podar Césped en Sector 5', 'Podar césped en el sector 5 antes del partido a las 17:00.', 'Pendiente', '2026-06-15', NULL, '2026-06-15 00:17:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_user`
--

CREATE TABLE `tarea_user` (
  `tarea_user_id` bigint(20) UNSIGNED NOT NULL,
  `tarea_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → tareas.tarea_id',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → usuarios.user_id (técnico asignado)',
  `estado_asignacion` varchar(50) NOT NULL DEFAULT 'Pendiente' COMMENT 'Estado del técnico: Pendiente | En Progreso | Completada',
  `fecha_asignacion` date NOT NULL COMMENT 'Fecha en que se asignó la tarea al técnico',
  `fecha_completado` date DEFAULT NULL COMMENT 'Fecha en que el técnico marcó la tarea como completada',
  `observaciones` text DEFAULT NULL COMMENT 'Notas del técnico al completar la tarea'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Asignaciones de tareas a técnicos de campo (tabla pivote)';

--
-- Volcado de datos para la tabla `tarea_user`
--

INSERT INTO `tarea_user` (`tarea_user_id`, `tarea_id`, `user_id`, `estado_asignacion`, `fecha_asignacion`, `fecha_completado`, `observaciones`) VALUES
(1, 1, 4, 'Pendiente', '2026-06-13', NULL, NULL),
(2, 1, 5, 'Pendiente', '2026-06-13', NULL, NULL),
(3, 2, 4, 'En Progreso', '2026-06-12', NULL, NULL),
(4, 3, 5, 'Pendiente', '2026-06-13', NULL, NULL),
(5, 4, 4, 'Completada', '2026-06-11', '2026-06-11', 'Los tres sensores inspeccionados. MC-07 y MC-08 presentan daño en el cable de datos, requieren reemplazo. MC-12 solo necesita recalibración. Fotografías tomadas y enviadas al sistema.'),
(6, 5, 6, 'Pendiente', '2026-06-13', NULL, NULL),
(7, 5, 7, 'Pendiente', '2026-06-13', NULL, NULL),
(8, 6, 6, 'En Progreso', '2026-06-12', NULL, NULL),
(9, 7, 7, 'Completada', '2026-06-12', '2026-06-13', 'Ciclo de riego nocturno ejecutado correctamente. Todos los aspersores funcionando. Se detectó presión baja en aspersor B-14, se ajustó manualmente. Lecturas post-riego normales.'),
(10, 8, 8, 'Completada', '2026-06-14', '2026-06-14', 'Se terminó la tarea sin novedades.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_tarea`
--

CREATE TABLE `tipo_tarea` (
  `tipo_tarea_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre del tipo de tarea',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de qué implica este tipo de mantenimiento',
  `roles_permitidos` varchar(255) DEFAULT NULL COMMENT 'Roles que pueden ejecutar este tipo (texto libre)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de tipos de tareas de mantenimiento de césped';

--
-- Volcado de datos para la tabla `tipo_tarea`
--

INSERT INTO `tipo_tarea` (`tipo_tarea_id`, `nombre`, `descripcion`, `roles_permitidos`) VALUES
(1, 'Riego Calibrado', 'Activación de ciclos de riego en zonas con niveles de humedad volumétrica inferiores al óptimo, según datos de sensores IoT.', 'Técnico de Campo'),
(2, 'Corte y Podado', 'Igualación de la altura del césped híbrido mediante maquinaria especializada. Altura objetivo: 25-30 mm para competencia.', 'Técnico de Campo'),
(3, 'Fertilización y Nutrición', 'Aplicación de fertilizantes y nutrientes en sectores específicos del campo. Registrar cantidad y producto utilizado.', 'Técnico de Campo'),
(4, 'Inspección de Sensores IoT', 'Verificación física de los sensores de humedad, compactación y temperatura enterrados. Reporte de sensores dañados.', 'Técnico de Campo'),
(5, 'Calibración de Aspersores', 'Ajuste de la presión y cobertura de los sistemas de riego automático para garantizar distribución uniforme del agua.', 'Técnico de Campo'),
(6, 'Control de Plagas e Infestaciones', 'Aplicación preventiva de fungicidas e insecticidas en zonas alertadas por el sistema de predicción de infestaciones.', 'Técnico de Campo'),
(7, 'Mantenimiento de Iluminación UV', 'Revisión, reposicionamiento y activación de las lámparas de crecimiento UV artificial para zonas con deficiencia de luz solar.', 'Técnico de Campo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `rol_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → roles.rol_id',
  `estadio_id` bigint(20) UNSIGNED NOT NULL COMMENT 'FK → estadios.estadio_id (estadio asignado)',
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre completo del usuario',
  `email` varchar(255) NOT NULL COMMENT 'Correo electrónico (usado para login)',
  `password` varchar(255) NOT NULL COMMENT 'Hash bcrypt de la contraseña',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema (técnicos, jefes y comité FIFA)';

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`user_id`, `rol_id`, `estadio_id`, `nombre`, `email`, `password`, `timestamp`) VALUES
(1, 1, 1, 'Alejandro Montoya', 'comite@fifa.org', '$2y$12$TtsWDWX7ts0wVgeMk5nWzu0SzWKTNYhGUDtP3LxB.bKvf5B/kx6dS', '2026-01-10 13:00:00'),
(2, 2, 1, 'Carlos Herrera López', 'jefe.sofi@stadiumhub.com', '$2y$12$ZPTiSGCQtOrYNXnnTsQREu/2vlGDoW.S1KEWo299U/a6OjFzzyUSy', '2026-01-12 14:00:00'),
(3, 2, 4, 'Roberto Zúñiga Pérez', 'jefe.azteca@stadiumhub.com', '$2y$10$aMFg6SqHGVxPSvez6Wcyb.NWQryTXtr4BNwa/.BZROPyt.Y6u9Gt2', '2026-01-12 14:30:00'),
(4, 3, 1, 'Miguel Ángel Reyes', 'tecnico1@stadiumhub.com', '$2y$12$T9g72ddwhWVlBK/ReX/I8OmHf6wgWcy57kA/6gP7YGZUFkeII77C2', '2026-01-15 12:00:00'),
(5, 3, 1, 'Laura Sofía Mendoza', 'tecnico2@stadiumhub.com', '$2y$10$gWu9QJTWn9KS5uhuDW3wK.Gowwj.JREvvxEatP9sQYQefww8tnIHC', '2026-01-15 12:10:00'),
(6, 3, 4, 'Andrés Cisneros Vega', 'tecnico3@stadiumhub.com', '$2y$10$Z5V5Y8QIR.479OmM4C2lVewxwbb2WXgN.K/oJ/Y0UfRDWlWbPK.By', '2026-01-15 12:20:00'),
(7, 3, 4, 'Patricia Olvera Ruiz', 'tecnico4@stadiumhub.com', '$2y$10$EEalSljSom3xAtsqbzMOpOHP0MUP9Zhb22Mr9VCYU2gvcVT7VxhzS', '2026-01-15 12:30:00'),
(8, 3, 3, 'Sebastián Gomez', 'sebasgomez@gmail.com', '$2y$12$NqnsX8LqhNslPwXUpWOZEui3Lhrc9VuL40AEouvfURlyz/G0QPn/6', '2026-06-14 05:41:41'),
(9, 2, 3, 'Raúl Garay', 'raul@gmail.com', '$2y$12$utsAp53sh6nN/ML7pcG7x.axmpjpD4qO1zTsuzB8rpYlG57FMojZO', '2026-06-14 19:16:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estadios`
--
ALTER TABLE `estadios`
  ADD PRIMARY KEY (`estadio_id`);

--
-- Indices de la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  ADD PRIMARY KEY (`historial_id`),
  ADD KEY `fk_historial_user` (`user_id`),
  ADD KEY `fk_historial_creador` (`creador_id`),
  ADD KEY `idx_historial_tarea` (`tarea_id`),
  ADD KEY `idx_historial_timestamp` (`timestamp`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`tarea_id`),
  ADD KEY `fk_tareas_tipo` (`tipo_tarea_id`),
  ADD KEY `fk_tareas_creador` (`creador_id`),
  ADD KEY `idx_tareas_estadio` (`estadio_id`),
  ADD KEY `idx_tareas_estado` (`estado`);

--
-- Indices de la tabla `tarea_user`
--
ALTER TABLE `tarea_user`
  ADD PRIMARY KEY (`tarea_user_id`),
  ADD UNIQUE KEY `unique_tarea_user` (`tarea_id`,`user_id`) COMMENT 'Un técnico no puede estar asignado dos veces a la misma tarea',
  ADD KEY `idx_tarea_user_user` (`user_id`);

--
-- Indices de la tabla `tipo_tarea`
--
ALTER TABLE `tipo_tarea`
  ADD PRIMARY KEY (`tipo_tarea_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`),
  ADD KEY `fk_usuarios_rol` (`rol_id`),
  ADD KEY `fk_usuarios_estadio` (`estadio_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `estadios`
--
ALTER TABLE `estadios`
  MODIFY `estadio_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  MODIFY `historial_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `tarea_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tarea_user`
--
ALTER TABLE `tarea_user`
  MODIFY `tarea_user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tipo_tarea`
--
ALTER TABLE `tipo_tarea`
  MODIFY `tipo_tarea_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_tareas`
--
ALTER TABLE `historial_tareas`
  ADD CONSTRAINT `fk_historial_creador` FOREIGN KEY (`creador_id`) REFERENCES `usuarios` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historial_tarea` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`tarea_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historial_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `fk_tareas_creador` FOREIGN KEY (`creador_id`) REFERENCES `usuarios` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tareas_estadio` FOREIGN KEY (`estadio_id`) REFERENCES `estadios` (`estadio_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tareas_tipo` FOREIGN KEY (`tipo_tarea_id`) REFERENCES `tipo_tarea` (`tipo_tarea_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarea_user`
--
ALTER TABLE `tarea_user`
  ADD CONSTRAINT `fk_tarea_user_tarea` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`tarea_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tarea_user_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`user_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_estadio` FOREIGN KEY (`estadio_id`) REFERENCES `estadios` (`estadio_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
