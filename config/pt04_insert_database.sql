-- Selecciona la base de datos si existe
USE pt04_arnau_aumedes;

-- Datos iniciales para la tabla users
INSERT INTO
  `users` (
    `user_id`,
    `username`,
    `email`,
    `password`,
    `isAdmin`,
    `active`,
    `created_at`
  )
VALUES
  (
    '100',
    'admin',
    'admin@admin.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    1,
    1,
    CURRENT_TIMESTAMP
  ),
  (
    '101',
    'usuario1',
    'usuario1@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    0,
    1,
    CURRENT_TIMESTAMP
  ),
  (
    '102',
    'usuario2',
    'usuario2@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    0,
    1,
    CURRENT_TIMESTAMP
  ),
  (
    '103',
    'usuario3',
    'usuario3@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    0,
    1,
    CURRENT_TIMESTAMP
  ),
  (
    '104',
    'usuario4',
    'usuario4@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    0,
    1,
    CURRENT_TIMESTAMP
  ),
  (
    '105',
    'usuario5',
    'usuario5@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    0,
    1,
    CURRENT_TIMESTAMP
  );

-- Datos iniciales actualizados para la tabla equipos con columna "objetivo"
INSERT INTO
  `equipos` (
    `id`,
    `equip`,
    `user_id`,
    `escudo`,
    `jugados`,
    `ganados`,
    `empatados`,
    `perdidos`,
    `rendimiento`,
    `bg`,
    `trofeo`,
    `objetivo`
  )
VALUES
  (
    1,
    'ARSENAL',
    100,
    'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
    38,
    22,
    10,
    6,
    65.79,
    NULL,
    0,
    2
  ),
  (
    2,
    'MANCHESTER CITY',
    101,
    'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
    38,
    28,
    6,
    4,
    78.95,
    NULL,
    0,
    1
  ),
  (
    3,
    'TOTTENHAM',
    102,
    'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
    38,
    21,
    10,
    7,
    60.53,
    NULL,
    0,
    4
  ),
  (
    4,
    'LIVERPOOL',
    103,
    'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
    38,
    24,
    8,
    6,
    68.42,
    NULL,
    0,
    3
  ),
  (
    5,
    'CHELSEA',
    104,
    'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
    38,
    20,
    9,
    9,
    60.53,
    NULL,
    0,
    5
  ),
  (
    6,
    'MANCHESTER UNITED',
    105,
    'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
    38,
    22,
    10,
    6,
    65.79,
    NULL,
    0,
    6
  ),
  (
    7,
    'NEWCASTLE UNITED',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
    38,
    21,
    10,
    7,
    60.53,
    NULL,
    0,
    7
  ),
  (
    8,
    'ASTON VILLA',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/f/f9/Aston_Villa_FC_crest_%282016%29.svg',
    38,
    19,
    12,
    7,
    57.89,
    NULL,
    0,
    8
  ),
  (
    9,
    'WEST HAM',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/c/c2/West_Ham_United_FC_logo.svg',
    38,
    18,
    11,
    9,
    55.26,
    NULL,
    0,
    9
  ),
  (
    10,
    'BRIGHTON',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
    38,
    17,
    12,
    9,
    55.26,
    NULL,
    0,
    10
  ),
  (
    11,
    'BRENTFORD',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
    37,
    15,
    10,
    12,
    54.05,
    NULL,
    0,
    12
  ),
  (
    12,
    'FULHAM',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
    37,
    16,
    9,
    12,
    56.76,
    NULL,
    0,
    11
  ),
  (
    13,
    'CRYSTAL PALACE',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/0/0c/Crystal_Palace_FC_logo.svg',
    37,
    14,
    10,
    13,
    51.35,
    NULL,
    0,
    13
  ),
  (
    14,
    'WOLVES',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
    37,
    13,
    11,
    13,
    48.65,
    NULL,
    0,
    14
  ),
  (
    15,
    'BOURNEMOUTH',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/e/e5/AFC_Bournemouth.svg',
    37,
    12,
    11,
    14,
    45.95,
    NULL,
    0,
    15
  ),
  (
    16,
    'EVERTON',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
    37,
    11,
    10,
    16,
    43.24,
    NULL,
    0,
    16
  ),
  (
    17,
    'NOTTINGHAM FOREST',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/6/6b/Nottingham_Forest_FC_logo.svg',
    37,
    10,
    12,
    15,
    40.54,
    NULL,
    0,
    17
  ),
  (
    18,
    'SOUTHAMPTON',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/c/c9/FC_Southampton.svg',
    37,
    9,
    11,
    17,
    37.84,
    NULL,
    0,
    18
  ),
  (
    19,
    'LEICESTER CITY',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/6/6d/Leicester_City_crest.svg',
    37,
    10,
    10,
    17,
    40.54,
    NULL,
    0,
    19
  ),
  (
    20,
    'IPSWICH TOWN',
    NULL,
    'https://upload.wikimedia.org/wikipedia/en/4/43/Ipswich_Town.svg',
    37,
    9,
    11,
    17,
    37.84,
    NULL,
    0,
    20
  );

-- Datos iniciales para la tabla jugadores
INSERT INTO
  jugadores (
    id,
    nombre_completo,
    equipo_id,
    valor,
    partidos,
    goles,
    asistencias
  )
VALUES
  -- ARSENAL (1)
  (1, 'Bukayo Saka', 1, 95000000, 38, 14, 11),
  (2, 'Martin Odegaard', 1, 90000000, 36, 12, 10),
  (3, 'Gabriel Martinelli', 1, 85000000, 37, 13, 6),
  -- MANCHESTER CITY (2)
  (4, 'Erling Haaland', 2, 180000000, 35, 36, 8),
  (5, 'Kevin De Bruyne', 2, 100000000, 32, 10, 18),
  (6, 'Phil Foden', 2, 110000000, 37, 15, 9),
  -- TOTTENHAM (3)
  (7, 'Heung-Min Son', 3, 90000000, 38, 23, 7),
  (8, 'James Maddison', 3, 70000000, 34, 9, 11),
  (9, 'Dejan Kulusevski', 3, 60000000, 36, 8, 6),
  -- LIVERPOOL (4)
  (10, 'Mohamed Salah', 4, 120000000, 38, 19, 12),
  (11, 'Luis Diaz', 4, 75000000, 34, 11, 5),
  (12, 'Darwin Nunez', 4, 80000000, 35, 14, 8),
  -- CHELSEA (5)
  (13, 'Cole Palmer', 5, 70000000, 37, 16, 9),
  (14, 'Enzo Fernandez', 5, 75000000, 36, 6, 8),
  (15, 'Nicolas Jackson', 5, 60000000, 34, 10, 5),
  -- MANCHESTER UNITED (6)
  (16, 'Bruno Fernandes', 6, 85000000, 38, 10, 13),
  (17, 'Marcus Rashford', 6, 90000000, 36, 12, 6),
  (18, 'Alejandro Garnacho', 6, 65000000, 35, 7, 4),
  -- NEWCASTLE UNITED (7)
  (19, 'Bruno Guimaraes', 7, 80000000, 37, 6, 8),
  (20, 'Alexander Isak', 7, 85000000, 34, 17, 4),
  (21, 'Anthony Gordon', 7, 60000000, 36, 9, 7),
  -- ASTON VILLA (8)
  (22, 'Ollie Watkins', 8, 75000000, 38, 19, 13),
  (23, 'Douglas Luiz', 8, 65000000, 36, 7, 6),
  (24, 'Leon Bailey', 8, 55000000, 34, 10, 5),
  -- WEST HAM (9)
  (25, 'Jarrod Bowen', 9, 70000000, 38, 16, 6),
  (26, 'Lucas Paqueta', 9, 65000000, 35, 8, 7),
  (27, 'Michail Antonio', 9, 40000000, 33, 9, 4),
  -- BRIGHTON (10)
  (28, 'Kaoru Mitoma', 10, 70000000, 34, 10, 8),
  (29, 'Joao Pedro', 10, 60000000, 32, 9, 5),
  (30, 'Pascal Gross', 10, 50000000, 38, 7, 10),
  -- BRENTFORD (11)
  (31, 'Ivan Toney', 11, 65000000, 33, 15, 4),
  (32, 'Bryan Mbeumo', 11, 55000000, 35, 9, 6),
  (33, 'Mathias Jensen', 11, 40000000, 36, 5, 8),
  -- FULHAM (12)
  (34, 'Joao Palhinha', 12, 60000000, 37, 4, 3),
  (35, 'Andreas Pereira', 12, 45000000, 36, 7, 8),
  (36, 'Raul Jimenez', 12, 35000000, 32, 8, 2),
  -- CRYSTAL PALACE (13)
  (37, 'Eberechi Eze', 13, 65000000, 34, 11, 6),
  (38, 'Michael Olise', 13, 70000000, 32, 10, 7),
  (
    39,
    'Jean-Philippe Mateta',
    13,
    40000000,
    35,
    9,
    3
  ),
  -- WOLVES (14)
  (40, 'Pedro Neto', 14, 60000000, 30, 5, 9),
  (41, 'Matheus Cunha', 14, 55000000, 34, 8, 6),
  (42, 'Hwang Hee-Chan', 14, 50000000, 36, 12, 4),
  -- BOURNEMOUTH (15)
  (43, 'Dominic Solanke', 15, 60000000, 38, 19, 3),
  (44, 'Philip Billing', 15, 40000000, 36, 6, 4),
  (45, 'Marcus Tavernier', 15, 35000000, 34, 5, 7),
  -- EVERTON (16)
  (
    46,
    'Dominic Calvert-Lewin',
    16,
    45000000,
    32,
    8,
    3
  ),
  (47, 'Abdoulaye Doucoure', 16, 40000000, 36, 7, 5),
  (48, 'Dwight McNeil', 16, 35000000, 38, 4, 6),
  -- NOTTINGHAM FOREST (17)
  (49, 'Morgan Gibbs-White', 17, 55000000, 38, 6, 9),
  (50, 'Taiwo Awoniyi', 17, 50000000, 30, 10, 2),
  (51, 'Anthony Elanga', 17, 45000000, 35, 5, 6),
  -- SOUTHAMPTON (18)
  (52, 'Che Adams', 18, 35000000, 34, 8, 4),
  (53, 'James Ward-Prowse', 18, 60000000, 38, 7, 9),
  (54, 'Adam Armstrong', 18, 30000000, 36, 9, 3),
  -- LEICESTER CITY (19)
  (55, 'Jamie Vardy', 19, 40000000, 34, 10, 2),
  (56, 'James Maddison', 19, 70000000, 35, 9, 11),
  (57, 'Harvey Barnes', 19, 65000000, 33, 13, 4),
  -- IPSWICH TOWN (20)
  (58, 'Conor Chaplin', 20, 25000000, 38, 12, 6),
  (59, 'Nathan Broadhead', 20, 22000000, 36, 9, 5),
  (60, 'Leif Davis', 20, 20000000, 37, 4, 10);