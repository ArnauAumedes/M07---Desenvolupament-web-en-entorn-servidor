-- SQL dump for pt04_arnau_aumedes
-- Includes DROP, CREATE and USE for the database and the articles table
DROP DATABASE IF EXISTS `pt04_arnau_aumedes`;

CREATE DATABASE IF NOT EXISTS `pt04_arnau_aumedes`;

USE `pt04_arnau_aumedes`;

-- --------------------------------------------------------
-- Tabla users 
CREATE TABLE
  IF NOT EXISTS `users` (
    `user_id` INT (11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `isAdmin` TINYINT (1) NOT NULL DEFAULT 0,
    `trn_date` DATETIME DEFAULT NULL,
    `active` TINYINT (1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla password_reset_temp
CREATE TABLE
  IF NOT EXISTS `password_reset_temp` (
    `email` VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    `expDate` DATETIME NOT NULL,
    PRIMARY KEY (`email`, `key`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla equipos
CREATE TABLE
  IF NOT EXISTS `equipos` (
    `id` INT (11) NOT NULL AUTO_INCREMENT,
    `pos` INT (11) NOT NULL,
    `equip` VARCHAR(100) NOT NULL,
    `user_id` INT (11) DEFAULT NULL,
    `escudo` VARCHAR(255) DEFAULT NULL,
    `jugados` INT (11) NOT NULL DEFAULT 0,
    `ganados` INT (11) NOT NULL DEFAULT 0,
    `empatados` INT (11) NOT NULL DEFAULT 0,
    `perdidos` INT (11) NOT NULL DEFAULT 0,
    `rendimiento` DECIMAL(5, 2) DEFAULT NULL,
    `bg` VARCHAR(255) DEFAULT NULL,
    `trofeo` VARCHAR(100) DEFAULT NULL,
    `objetivo` INT (11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `fk_equipos_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla jugadores
CREATE TABLE
  IF NOT EXISTS `jugadores` (
    `id` INT (11) NOT NULL AUTO_INCREMENT,
    `nombre_completo` VARCHAR(150) NOT NULL,
    `equipo_id` INT (11) NOT NULL,
    `valor` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `partidos` INT (11) NOT NULL DEFAULT 0,
    `goles` INT (11) NOT NULL DEFAULT 0,
    `asistencias` INT (11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `equipo_id` (`equipo_id`),
    CONSTRAINT `jugadores_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
-- Tabla tokens
CREATE TABLE user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);