-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-10-2025 a las 18:11:34
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
-- Base de datos: `pt03_arnau_aumedes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `dni` varchar(9) NOT NULL,
  `titol` varchar(255) NOT NULL,
  `cos` text NOT NULL,
  `data_creacio` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modificacio` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articles`
--

INSERT INTO `articles` (`id`, `dni`, `titol`, `cos`, `data_creacio`, `data_modificacio`) VALUES
(1, '12345678A', 'Primera Prova', 'Aquest és el primer article de prova.', '2025-10-22 15:42:01', '2025-10-22 15:42:01'),
(2, '98765432B', 'Segona Prova', 'Aquest és un altre article d\'exemple.', '2025-10-22 15:42:01', '2025-10-22 15:42:01'),
(3, '12345678A', 'La importància de la ciberseguretat', 'Aquest article explora les principals amenaces digitals actuals i com protegir-se a nivell personal i empresarial.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(4, '87654321B', 'Tendències en intel·ligència artificial', 'Analitzem les darreres innovacions en IA i el seu impacte en sectors com la salut, l’educació i la indústria.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(5, '45678912C', 'Com optimitzar el rendiment web', 'Guia pràctica per reduir temps de càrrega i millorar l’experiència d’usuari a llocs web moderns.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(6, '78912345D', 'Introducció al disseny responsive', 'Explorem tècniques de CSS i frameworks per adaptar llocs web a tot tipus de dispositius.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(7, '32165498E', 'El futur del treball remot', 'Una mirada als canvis estructurals que han provocat el teletreball i les noves oportunitats que genera.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(8, '65498732F', 'Blockchain més enllà del Bitcoin', 'Com aquesta tecnologia està revolucionant sectors com la logística, la salut i les finances.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(9, '98732165G', 'Bones pràctiques de seguretat en PHP', 'Errors habituals de seguretat en desenvolupament backend i com prevenir-los amb exemples concrets.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(10, '13579246H', 'El valor del codi obert', 'Anàlisi de l’impacte del programari lliure en la innovació i la col·laboració global.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(11, '24681357I', 'Introducció a la programació amb Python', 'Conceptes bàsics per començar amb un dels llenguatges més populars i versàtils.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(12, '35792468J', 'Eines per a la gestió de projectes', 'Comparativa entre Trello, Asana i Jira per triar la millor opció segons les necessitats de l’equip.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(13, '46813579K', 'Disseny d’interfícies accessibles', 'Principis bàsics per crear experiències digitals inclusives per a tothom.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(14, '57924680L', 'Com crear una API REST amb Laravel', 'Guia pas a pas per construir una API eficient i segura amb aquest framework PHP.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(15, '68035791M', 'Machine Learning aplicat al màrqueting', 'Casos reals on la IA millora la segmentació i personalització de campanyes.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(16, '79146802N', 'Els perills de la desinformació digital', 'Com les xarxes socials poden amplificar notícies falses i estratègies per combatre-les.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(17, '80257913O', 'L’impacte ambiental de la tecnologia', 'Explorem com reduir la petjada ecològica de l’ús massiu de dispositius i centres de dades.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(18, '91368024P', 'Introducció a Docker per desenvolupadors', 'Com contenir aplicacions i gestionar entorns de manera eficient amb Docker i Docker Compose.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(19, '02479135Q', 'La importància del testing en el cicle de vida del programari', 'Per què les proves automatitzades són essencials per garantir qualitat i mantenibilitat.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(20, '13580246R', 'Tècniques de SEO per al 2025', 'Consells pràctics per posicionar contingut en motors de cerca seguint les últimes tendències.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(21, '24691357S', 'El paper del disseny UX en l’èxit d’una aplicació', 'Com una bona experiència d’usuari pot augmentar la retenció i la satisfacció.', '2025-10-22 15:42:27', '2025-10-22 15:42:27'),
(22, '35702468T', 'La revolució del 5G', 'Analitzem com la connectivitat d’alta velocitat canviarà la manera com interactuem amb la tecnologia.', '2025-10-22 15:42:27', '2025-10-22 15:42:27');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
