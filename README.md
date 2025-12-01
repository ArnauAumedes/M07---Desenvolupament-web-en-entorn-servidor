
# Pràctica: Gestió d'Articles amb Paginació, Autenticació i Recuperació de Contrasenya

Aquest projecte implementa una aplicació web de gestió d'articles amb funcionalitats completes de paginació, autenticació d'usuaris i recuperació de contrasenya. A continuació es detalla com s'ha implementat cada part i la justificació de les decisions tècniques preses.

---

## 1. Estructura de la Base de Dades

### Taules principals

- **articles**: Guarda els articles creats pels usuaris.
- **users**: Gestiona la informació d'autenticació i registre d'usuaris.
- **password_reset_temp**: Permet la gestió segura de la recuperació de contrasenya.

#### Scripts de creació
```sql
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `titol` varchar(255) NOT NULL,
  `cos` text NOT NULL,
  `data_creacio` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modificacio` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `trn_date` DATETIME DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `password_reset_temp` (
  `email` VARCHAR(255) NOT NULL,
  `key` VARCHAR(255) NOT NULL,
  `expDate` DATETIME NOT NULL,
  PRIMARY KEY (`email`, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Justificació:**
- Es garanteix la integritat i eficiència de les consultes amb claus primàries i tipus adequats.
- La taula `password_reset_temp` permet un flux segur de recuperació de contrasenya sense exposar dades sensibles.

---

## 2. Accés a Dades i Seguretat

- S'utilitza PDO amb el patró Singleton per garantir una única connexió segura i centralitzada a la base de dades (`app/model/database/database.php`).
- Totes les consultes es fan amb prepared statements per evitar SQL Injection.
- Els DAOs (`ArticleDAO`, `UserDAO`, `PasswordResetDAO`) encapsulen la lògica d'accés a dades, facilitant el manteniment i la reutilització.

**Justificació:**
- Separar l'accés a dades de la lògica de negoci millora la seguretat, la testabilitat i la claredat del codi.

---

## 3. Paginació d'Articles

### Backend
- El controlador `ArticleController` gestiona la lògica de paginació: valida paràmetres, calcula el nombre total de pàgines i obté només els articles necessaris amb LIMIT/OFFSET.
- Es defineix una whitelist de valors per `per_page` ([1, 5, 10, 20]) per evitar consultes ineficients o atacs.
- Si la pàgina o el nombre per pàgina no són vàlids, es redirigeix a valors per defecte per mantenir la coherència de la URL i la seguretat.
- S'utilitza try/catch per capturar errors PDO i evitar exposar informació sensible a l'usuari.

### Frontend
- La vista `menu.php` mostra un selector per triar quants articles veure per pàgina i controls de navegació (Inici, Anterior, números, Següent, Final).
- Només es mostren un màxim de 5 enllaços numèrics per facilitar la navegació i evitar sobrecàrrega visual.

**Justificació:**
- La paginació eficient millora el rendiment i l'experiència d'usuari, especialment amb grans volums de dades.
- Validar i controlar els paràmetres evita errors i possibles vectors d'atac.

---

## 4. Autenticació i Recuperació de Contrasenya

### Flux de recuperació
1. L'usuari sol·licita la recuperació de contrasenya des del formulari (`send-email.php`).
2. El controlador `ForgotPasswordController` valida l'email, genera una clau única i una data d'expiració, i fa un `insert` a `password_reset_temp`.
3. S'envia un correu amb un enllaç segur per restablir la contrasenya.
4. Quan l'usuari accedeix a l'enllaç, el controlador `ResetPasswordController` valida la clau i l'email, mostra el formulari de nova contrasenya i, si tot és correcte, actualitza la contrasenya i elimina el registre de `password_reset_temp`.

**Justificació:**
- El registre temporal i l'expiració de la clau impedeixen l'ús fraudulent dels enllaços de recuperació.
- Eliminar el registre després d'un canvi correcte de contrasenya garanteix que l'enllaç no es pugui reutilitzar.

---

## 5. Disseny de Vistes i Usabilitat

- S'utilitza Bootstrap 4 i una estructura coherent de targetes i contenidors per a totes les vistes (login, send-email, reset-password).
- Els missatges d'error i èxit es mostren de manera clara i accessible.
- Tots els formularis tenen validació bàsica i botons d'acció ben identificats.
- S'inclouen enllaços ràpids per tornar al menú principal o recuperar la contrasenya des del login.

**Justificació:**
- Un disseny coherent i modern facilita la navegació i millora l'experiència d'usuari.
- L'accessibilitat i la claredat dels missatges redueixen la frustració i els errors.

---

## 6. Altres Decisions Tècniques

- S'han definit valors per defecte i redireccions per garantir la robustesa davant paràmetres incorrectes.
- S'ha prioritzat la seguretat (hash de contrasenyes, prepared statements, validació d'inputs) en tot el projecte.
- El codi està modularitzat per facilitar el manteniment i l'escalabilitat.

---

## 7. Fitxers Clau

- `app/controlador/ArticleController.php` — Lògica de paginació i menú d'articles
- `app/model/dao/ArticleDAO.php` — Accés a dades d'articles
- `app/model/dao/UserDAO.php` — Accés a dades d'usuaris
- `app/model/dao/PasswordResetDAO.php` — Gestió de recuperació de contrasenya
- `app/vista/menu.php` — Vista de llistat d'articles i controls de paginació
- `app/vista/login.php` — Vista d'autenticació
- `app/vista/send-email.php` — Vista de sol·licitud de recuperació de contrasenya
- `app/vista/reset-password.php` — Vista de canvi de contrasenya
- `public/css/style.css` — Estils personalitzats

---

## 8. Conclusions

El projecte implementa una aplicació web segura, robusta i usable, amb una arquitectura clara i bones pràctiques tant a nivell de backend (seguretat, modularitat, validació) com de frontend (usabilitat, accessibilitat, disseny modern). Totes les funcionalitats han estat justificades per garantir una bona experiència d'usuari i la seguretat de les dades.



 