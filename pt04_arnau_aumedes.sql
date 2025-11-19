-- SQL dump for pt04_arnau_aumedes
-- Includes DROP, CREATE and USE for the database and the articles table

DROP DATABASE IF EXISTS `pt04_arnau_aumedes`;
CREATE DATABASE IF NOT EXISTS `pt04_arnau_aumedes`;
USE `pt04_arnau_aumedes`;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `titol` varchar(255) NOT NULL,
  `cos` text NOT NULL,
  `data_creacio` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modificacio` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `users`
-- (simple table for authentication; passwords stored as password_hash)
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `articles`

-- Todos los artículos se asignan al usuario admin (user_id = 100)
INSERT INTO `articles` (`id`, `user_id`, `titol`, `cos`, `data_creacio`, `data_modificacio`) VALUES
(1, 100, 'Primera Prova', 'Aquest és el primer article de prova.', '2025-10-22 15:42:01', '2025-10-22 15:42:01'),
(2, 100, 'Segona Prova', 'Aquest és un altre article d\'exemple.', '2025-10-22 15:42:01', '2025-10-22 15:42:01'),
(3, 100, 'La importància de la ciberseguretat', 'Aquest article explora les principals amenaces digitals actuals i com protegir-se a nivell personal i empresarial.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(4, 100, 'Tendències en intel·ligència artificial', 'Analitzem les darreres innovacions en IA i el seu impacte en sectors com la salut, l’educació i la indústria.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(5, 100, 'Com optimitzar el rendiment web', 'Guia pràctica per reduir temps de càrrega i millorar l’experiència d’usuari a llocs web moderns.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(6, 100, 'Introducció al disseny responsive', 'Explorem tècniques de CSS i frameworks per adaptar llocs web a tot tipus de dispositius.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(7, 100, 'El futur del treball remot', 'Una mirada als canvis estructurals que han provocat el teletreball i les noves oportunitats que genera.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(8, 100, 'Blockchain més enllà del Bitcoin', 'Com aquesta tecnologia està revolucionant sectors com la logística, la salut i les finances.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(9, 100, 'Bones pràctiques de seguretat en PHP', 'Errors habituals de seguretat en desenvolupament backend i com prevenir-los amb exemples concrets.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(10, 100, 'El valor del codi obert', 'Anàlisi de l’impacte del programari lliure en la innovació i la col·laboració global.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(11, 100, 'Introducció a la programació amb Python', 'Conceptes bàsics per començar amb un dels llenguatges més populars i versàtils.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(12, 100, 'Eines per a la gestió de projectes', 'Comparativa entre Trello, Asana i Jira per triar la millor opció segons les necessitats de l’equip.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(13, 100, 'Disseny d’interfícies accessibles', 'Principis bàsics per crear experiències digitals inclusives per a tothom.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(14, 100, 'Com crear una API REST amb Laravel', 'Guia pas a pas per construir una API eficient i segura amb aquest framework PHP.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(15, 100, 'Machine Learning aplicat al màrqueting', 'Casos reals on la IA millora la segmentació i personalització de campanyes.', '2025-10-22 15:42:27', '2025-10-22 15:42:27');

INSERT INTO `users`(`user_id`, `username`, `email`, `password_hash`, `active`, `created_at`) VALUES
('100', 'admin', 'admin@admin.com', '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe', 1, NOW()),
('101', 'usuario1', 'usuario1@example.com', '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe', 1, NOW()),
('102', 'usuario2', 'usuario2@example.com', '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe', 1, NOW()),
('103', 'usuario3', 'usuario3@example.com', '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe', 1, NOW()),
('104', 'usuario4', 'usuario4@example.com', '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe', 1, NOW()),
('105', 'usuario5', 'usuario5@example.com', '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe', 1, NOW());

-- Índices para tablas volcadas

-- Indices de la tabla `articles`
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT de la tabla `articles`
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;
