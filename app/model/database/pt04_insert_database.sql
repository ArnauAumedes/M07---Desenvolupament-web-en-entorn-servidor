-- Datos iniciales para la tabla equipos
INSERT INTO `equipos` (
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
  `trofeo`
)
VALUES
(
  1,
  'ARSENAL',
  100,
  'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  2,
  'MANCHESTER CITY',
  100,
  'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  3,
  'TOTTENHAM',
  100,
  'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur.svg',
  10, 9, 0, 1,
  NULL, NULL, 0
),
(
  4,
  'LIVERPOOL',
  100,
  'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  5,
  'CHELSEA',
  100,
  'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  6,
  'MANCHESTER UNITED',
  100,
  'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  7,
  'NEWCASTLE UNITED',
  100,
  'https://upload.wikimedia.org/wikipedia/en/5/56/Newcastle_United_Logo.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  8,
  'ASTON VILLA',
  100,
  'https://upload.wikimedia.org/wikipedia/en/f/f9/Aston_Villa_FC_crest_%282016%29.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  9,
  'WEST HAM',
  100,
  'https://upload.wikimedia.org/wikipedia/en/c/c2/West_Ham_United_FC_logo.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  10,
  'BRIGHTON',
  100,
  'https://upload.wikimedia.org/wikipedia/en/f/fd/Brighton_%26_Hove_Albion_logo.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  11,
  'BRENTFORD',
  100,
  'https://upload.wikimedia.org/wikipedia/en/2/2a/Brentford_FC_crest.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  12,
  'FULHAM',
  100,
  'https://upload.wikimedia.org/wikipedia/en/e/eb/Fulham_FC_%28shield%29.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  13,
  'CRYSTAL PALACE',
  100,
  'https://upload.wikimedia.org/wikipedia/en/0/0c/Crystal_Palace_FC_logo.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  14,
  'WOLVES',
  100,
  'https://upload.wikimedia.org/wikipedia/en/f/fc/Wolverhampton_Wanderers.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  15,
  'BOURNEMOUTH',
  100,
  'https://upload.wikimedia.org/wikipedia/en/e/e5/AFC_Bournemouth.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  16,
  'EVERTON',
  100,
  'https://upload.wikimedia.org/wikipedia/en/7/7c/Everton_FC_logo.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  17,
  'NOTTINGHAM FOREST',
  100,
  'https://upload.wikimedia.org/wikipedia/en/6/6b/Nottingham_Forest_FC_logo.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  18,
  'SOUTHAMPTON',
  100,
  'https://upload.wikimedia.org/wikipedia/en/c/c9/FC_Southampton.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  19,
  'LEICESTER CITY',
  100,
  'https://upload.wikimedia.org/wikipedia/en/6/6d/Leicester_City_crest.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
),
(
  20,
  'IPSWICH TOWN',
  100,
  'https://upload.wikimedia.org/wikipedia/en/4/43/Ipswich_Town.svg',
  0, 0, 0, 0,
  NULL, NULL, 0
);


-- Datos iniciales para la tabla users
INSERT INTO
  `users` (
    `user_id`,
    `username`,
    `email`,
    `password`,
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
    NOW ()
  ),
  (
    '101',
    'usuario1',
    'usuario1@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    1,
    NOW ()
  ),
  (
    '102',
    'usuario2',
    'usuario2@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    1,
    NOW ()
  ),
  (
    '103',
    'usuario3',
    'usuario3@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    1,
    NOW ()
  ),
  (
    '104',
    'usuario4',
    'usuario4@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    1,
    NOW ()
  ),
  (
    '105',
    'usuario5',
    'usuario5@example.com',
    '$2y$10$joWmJvece7Q.tg018cEbfOa2rDdqjCPX/W0pP3bGl4WqZrTdN1Ehe',
    1,
    NOW ()
  );