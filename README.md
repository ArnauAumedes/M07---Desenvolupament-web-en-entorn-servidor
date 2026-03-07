## Instal·lació de la base de dades

Abans d’utilitzar l’aplicació, has d’importar la base de dades al teu servidor MySQL (per exemple, utilitzant phpMyAdmin). És important seguir aquest ordre:

1. Primer importa el fitxer `config/pt04_arnau_aumedes.sql`.
2. Després importa el fitxer `config/pt04_insert_database.sql`.

Això assegurarà que l’estructura i les dades inicials es creïn correctament.

### Usuari administrador per defecte

Després d’importar la base de dades, pots iniciar sessió com a administrador amb les credencials següents:

* **Usuari:** [admin@admin.com](mailto:admin@admin.com)
* **Contrasenya:** 123

## Connexió PDO

La connexió a la base de dades es gestiona mitjançant la classe `Database` ubicada a `config/db-connection.php`. Aquesta classe implementa el **patró Singleton**, el que garanteix que només existeixi una única instància de la connexió PDO durant tota l'execució de l'aplicació. Això millora el rendiment i la gestió de recursos.

La configuració de la connexió (host, usuari, contrasenya, nom de la base de dades, charset) s'obté de variables d'entorn definides al fitxer `.env`, el qual **no es puja a GitHub** per motius de seguretat (està inclòs al `.gitignore`). Això permet separar la configuració sensible del codi font i facilita el desplegament en diferents entorns.

La classe `Database` utilitza les següents bones pràctiques de seguretat i robustesa:
- Ús de **prepared statements** per evitar injeccions SQL.
- Configuració de PDO per llençar excepcions en cas d'error (`PDO::ERRMODE_EXCEPTION`).
- Desactivació de l'emulació de prepared statements (`PDO::ATTR_EMULATE_PREPARES = false`).
- Recuperació de resultats com a arrays associatius per defecte (`PDO::FETCH_ASSOC`).

Això assegura una connexió segura, eficient i fàcilment configurable per a tota l'aplicació.

## Paginació

La paginació està implementada a totes les vistes d'OSM (`/app/vista/osm`) mitjançant un component reutilitzable ubicat a `app/vista/globals/pagination.php`. Aquest component mostra els controls de navegació de pàgines i permet a l'usuari seleccionar quants elements vol veure per pàgina.

La lògica de la paginació es gestiona des dels controladors corresponents (`EquipoController.php` i `JugadorController.php`). Aquests controladors calculen la pàgina actual, el límit d'elements per pàgina i el nombre total de pàgines a partir dels paràmetres rebuts per GET (`page` i `limit`).

El component de paginació rep aquestes variables i genera els enllaços per navegar entre pàgines, mantenint també altres paràmetres de la consulta (com l'acció o l'ordre). Això permet una experiència d'usuari fluida i escalable en llistats llargs.

La implementació garanteix que només es mostren els elements corresponents a la pàgina seleccionada, millorant el rendiment i la usabilitat de l'aplicació.

## Validació d'usuaris

La validació d'usuaris es realitza principalment als controladors `loginController.php` (inici de sessió), `logoutController.php` (tancament de sessió) i `UserController.php` (gestió i llistat d'usuaris).

Durant el procés de login, es comprova que els camps no estiguin buits, que l'email tingui un format vàlid i que les credencials siguin correctes mitjançant la verificació de la contrasenya encriptada. També es valida que el compte estigui actiu abans de permetre l'accés.

Si la validació és correcta, es crea una sessió segura per a l'usuari i es pot activar l'opció "remember me" per mantenir la sessió iniciada durant més temps. En cas de logout, es destrueix la sessió, s'eliminen les cookies i es netegen els tokens de la base de dades per garantir la seguretat.

El controlador `UserController.php` permet gestionar i llistar els usuaris, aplicant també la paginació i l'ordenació quan sigui necessari.

Aquesta lògica garanteix una gestió d'usuaris segura, robusta i adaptada a les bones pràctiques de desenvolupament web.

### Remember Me
Si l'usuari marca aquesta opció en iniciar sessió, es genera un token únic i segur que es guarda a la base de dades i a una cookie. Això permet recordar la sessió de l'usuari durant 30 dies sense guardar la contrasenya. El token es gestiona i elimina en logout per seguretat. Aquesta solució millora la comoditat de l'usuari i evita riscos de seguretat associats a guardar contrasenyes.

### reCAPTCHA
Després de 3 intents fallits de login, es mostra un reCAPTCHA (tipus checkbox) per evitar intents automatitzats. La validació es fa amb l'API oficial de Google i només permet continuar si el reCAPTCHA és correcte. Això protegeix el sistema contra atacs de força bruta i bots.

### Editar perfil personal
L'usuari pot modificar el seu nickname, email i altres dades des del formulari d'edició de perfil, on es mostren les dades actuals. També pot canviar la contrasenya i la imatge/avatar si està implementat. Això dona flexibilitat i control a l'usuari sobre la seva informació.

### Usuari Admin
Existeix un usuari administrador que pot eliminar altres usuaris. Quan un usuari és esborrat, els seus articles associats també s'esborren (ON DELETE CASCADE), garantint la coherència de la base de dades i evitant dades orfes. Aquesta decisió assegura integritat i neteja de dades.

### Barra de cerca
Permet cercar per nom (o altres camps) en llistats d'usuaris, jugadors i equips. Les cerques es guarden mitjançant cookies per recordar preferències de l'usuari. La cerca es pot fer per enter o en temps real amb JavaScript/AJAX. Això millora l'experiència d'usuari i la rapidesa de consulta.

### Configuracions de seguretat
S'apliquen diverses mesures: fitxer `.htaccess` per gestionar errors i evitar accés a fitxers sensibles, variables d'entorn per separar informació confidencial, connexió PDO amb prepared statements, i eliminació de tokens/cookies en logout. Això protegeix l'aplicació contra vulnerabilitats comunes i garanteix la confidencialitat.

### Canvi de contrasenya
Si l'usuari està logat, pot accedir al formulari de canvi de contrasenya. Es demana la contrasenya antiga i la nova dues vegades. Es valida que la contrasenya antiga sigui correcta i que la nova compleixi requisits de seguretat. Aquesta metodologia evita canvis accidentals i reforça la seguretat.

### Recuperació de contrasenya
Si l'usuari no recorda la contrasenya, pot demanar la recuperació introduint el seu email. Es genera un token amb validesa limitada i s'envia per correu. Amb aquest token pot establir una nova contrasenya. Això permet recuperar l'accés de forma segura i controlada.

### Autenticació social OAuth
Permet iniciar sessió amb Google. S'utilitza la llibreria oficial, es recupera el perfil i es crea o recupera l'usuari a la base de dades. Aquesta opció facilita el registre i login, i aprofita la seguretat dels proveïdors externs.

### Autenticació social HybridAuth
Permet iniciar sessió amb GitHub mitjançant HybridAuth. Es recupera el perfil i es crea o recupera l'usuari a la base de dades. Això amplia les opcions d'autenticació i millora l'accessibilitat per a usuaris amb comptes socials.
