# Practicas - Gestio de Futbol MVC amb mode dual BDD/API

Aplicacio web PHP amb arquitectura MVC per gestionar equips, jugadors i usuaris, amb autenticacio tradicional i social, llistats amb AJAX, i un mode dual de dades (`bdd` o `api`) per a consultes.

## Taula de continguts
- [Visio general](#visio-general)
- [Stack tecnologic](#stack-tecnologic)
- [Estructura del projecte](#estructura-del-projecte)
- [Arquitectura i flux](#arquitectura-i-flux)
- [API interna](#api-interna)
- [Integracio API externa de futbol](#integracio-api-externa-de-futbol)
- [Instal-lacio i arrencada](#instal-lacio-i-arrencada)
- [Configuracio d-entorn](#configuracio-d-entorn)
- [Funcionalitats d-usuaris i seguretat](#funcionalitats-dusuaris-i-seguretat)
- [Paginacio i cerca AJAX](#paginacio-i-cerca-ajax)
- [Exemples d-us](#exemples-dus)
- [Troubleshooting](#troubleshooting)

## Visio general
El projecte permet administrar dades de futbol amb una interfície web clàssica i incorpora una capa API pròpia per desacoblar el consum de dades.

Flux principal de lectura:
1. L'usuari navega a una vista de llistat (equips, jugadors o usuaris).
2. El sistema resol la font de dades: query `source` -> cookie -> `bdd` per defecte.
3. El controlador MVC delega en un DataService.
4. El DataService retorna dades homogènies per a la vista, vinguin de BDD o API.
5. A les cerques en temps real, JavaScript/AJAX actualitza les files de la taula.

## Stack tecnologic
| Capa | Tecnologia | Us principal |
| --- | --- | --- |
| Backend | PHP (MVC custom) | Lògica de negoci i controladors |
| Persistència | MySQL + PDO | Emmagatzematge i consultes SQL |
| Frontend | HTML, CSS, JavaScript vanilla | Interfície i interaccions AJAX |
| API interna | Front controller REST a `api/index.php` | Exposició de recursos JSON |
| API externa | Football-Data.org v4 | Dades de competició per equips |
| Auth social | Google OAuth + HybridAuth (GitHub) | Login social |
| Seguretat addicional | Google reCAPTCHA | Protecció anti-força bruta |
| Correu | PHPMailer | Recuperació/canvi de contrasenya |

Dependències principals (segons `composer.json`):
- `google/recaptcha`
- `google/apiclient`
- `hybridauth/hybridauth`

## Estructura del projecte
```text
practicas/
|- api/
|  |- index.php                 # Entrypoint de la API interna
|- app/
|  |- api/                      # Controladors i helpers API
|  |- controlador/              # Controladors MVC web
|  |- model/dao/                # Capa DAO
|  |- model/entities/           # Entitats de domini
|  |- services/                 # DataServices + clients API
|  |- vista/                    # Vistes PHP
|- config/
|  |- db-connection.php         # Connexio PDO
|  |- env.php                   # Loader de variables d'entorn
|  |- pt04_arnau_aumedes.sql
|  |- pt04_insert_database.sql
|- public/
|  |- js/                       # JS servit al client
|- resources/
|  |- js/                       # Font JS editable
|- README.md
```

## Arquitectura i flux
### Modo dual de dades
La font de dades es resol a `app/services/DataSourceResolver.php`:
- Prioritat 1: query param `?source=bdd|api`
- Prioritat 2: cookie `data_source_preference`
- Fallback: `bdd`

### Cobertura actual del mode API
- `equips` en `source=api`: consumeix Football-Data (competició configurable) i mapatge intern.
- `jugadors` i `usuaris` en `source=api`: es mantenen sobre BDD (degradació controlada en l'abast actual).

### Robustesa
- Cache TTL server-side en consum de Football-Data.
- Fallback a BDD si el provider extern falla.
- Gestió d'errors AJAX amb suport per resposta JSON o text pla.

### Concurrencia i connexions a base de dades
- `Database::getInstance()` aplica Singleton dins del proces/request actual de PHP.
- En entorns Apache mod_php o PHP-FPM, cada request s'executa en el seu propi context, aixi que no es comparteix la instancia PDO entre usuaris.
- Consequencia practica: hi ha una sola connexio reutilitzada per request, pero a nivell global hi ha connexions independents per cada worker concurrent.
- Si en un futur es necessita un model diferent (pooling o factoria per operacio), el punt d'entrada a canviar es `config/db-connection.php`.

## API interna
L'API interna està centralitzada a `api/index.php` i respon JSON amb contracte uniforme:

```json
{
	"status": true,
	"msg": "Operacio correcta",
	"data": [],
	"errors": [],
	"meta": {}
}
```

### Autenticacio
Rutes protegides amb API key (`X-API-Key`) via `app/api/ApiKeyHelper.php`.

### Endpoints principals
| Metode | Path | Descripcio |
| --- | --- | --- |
| GET | `/api/equipos` | Llista d'equips |
| GET | `/api/equipos/{id}` | Detall d'equip |
| GET | `/api/jugadores` | Llista de jugadors |
| GET | `/api/jugadores/{id}` | Detall de jugador |
| GET | `/api/usuarios` | Llista d'usuaris |
| GET | `/api/usuarios/{id}` | Detall d'usuari |

Paràmetres de llistat suportats en recursos API:
- `limit` (1..100)
- `order` (`asc` o `desc`)

### Codis HTTP utilitzats
| Codi | Escenari |
| --- | --- |
| 200 | Consulta correcta |
| 400 | Request invàlida (ex. id no numèric) |
| 401 | API key absent |
| 403 | API key invàlida |
| 404 | Recurs o element no trobat |
| 405 | Mètode no permès |
| 422 | Error de validació funcional |
| 500 | Error intern controlat |

## Integracio API externa de futbol
El provider triat és **Football-Data.org (v4)**.

Implementació principal:
- `app/services/FootballApiService.php`: consum HTTP, validació, cache i obtenció de classificació.
- `app/services/FootballMapper.php`: normalització de payload extern a format intern estable.

Configuració de provider:
- Header d'autenticació: `X-Auth-Token`
- Competició per defecte configurable (`FOOTBALL_DEFAULT_COMPETITION`, p. ex. `PL`)
- Cache TTL configurable (`FOOTBALL_API_CACHE_TTL`)

Important:
- `INTERNAL_API_KEY` (API interna) i `FOOTBALL_API_KEY` (provider extern) són claus diferents i independents.

## Instal-lacio i arrencada
### Requisits previs
- XAMPP (Apache + MySQL)
- PHP 8.x recomanat
- Composer

### 1) Instal-lar dependències
```bash
composer install
```

### 2) Importar la base de dades (ordre obligatori)
1. `config/pt04_arnau_aumedes.sql`
2. `config/pt04_insert_database.sql`

### 3) Configurar `.env`
Defineix les variables mínimes (veure secció següent).

### 4) Servir el projecte
Executa Apache/MySQL des de XAMPP i obre:
- `http://localhost/practicas`

### Usuari administrador per defecte
- Usuari: `admin@admin.com`
- Contrasenya: `123`

## Configuracio d-entorn
Exemple orientatiu (no usar claus reals en repositori):

```env
DB_HOST=127.0.0.1
DB_DATABASE=pt04_arnau_aumedes
DB_USERNAME=root
DB_PASSWORD=

INTERNAL_API_KEY=canvia_aquesta_clau

FOOTBALL_API_BASE_URL=https://api.football-data.org/v4
FOOTBALL_API_KEY=la_teva_clau_football_data
FOOTBALL_DEFAULT_COMPETITION=PL
FOOTBALL_API_CACHE_TTL=120
```

Altres variables ja usades pel projecte:
- SMTP (`SMTP_HOST`, `SMTP_USERNAME`, ...)
- reCAPTCHA (`RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`)
- OAuth (`CLIENT_ID`, `CLIENT_SECRET`, `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`)

## Funcionalitats d'usuaris i seguretat
### Validacio d'usuaris
Control principal a `loginController.php`, `logoutController.php` i `UserController.php`:
- Validació de camps i format d'email.
- Verificació de contrasenya encriptada.
- Comprovació d'usuari actiu.

### Remember Me
Si l'usuari activa l'opció, es genera token segur a BDD + cookie (fins a 30 dies). Es neteja al logout.

### reCAPTCHA
Després de 3 intents fallits de login, es força validació reCAPTCHA (checkbox) amb Google.

### Canvi i recuperacio de contrasenya
- Canvi: validació de contrasenya antiga i nova.
- Recuperació: token temporal enviat per correu.

### OAuth i login social
- Google OAuth (llibreria oficial)
- GitHub via HybridAuth

### Configuracions de seguretat
- `.htaccess` per routing i errors.
- Ús de variables d'entorn per secrets.
- PDO amb prepared statements i `ERRMODE_EXCEPTION`.
- Eliminació de tokens/cookies a logout.

## Paginacio i cerca AJAX
### Paginacio
Component reutilitzable a `app/vista/globals/pagination.php` i càlcul de pàgines als controladors.

### Barra de cerca
Cerca per nom en usuaris, jugadors i equips:
- Persistència de preferències via cookies.
- Execució en temps real amb JavaScript/AJAX.
- Compatible amb `source=bdd` i `source=api` segons pantalla.

## Exemples d'us
### 1) Consultar API interna (PowerShell)
```powershell
$k='dev-internal-key'
Invoke-WebRequest -Uri 'http://localhost/practicas/api/equipos' -Headers @{'X-API-Key'=$k} -Method GET
```

### 2) Error esperat sense API key
```powershell
Invoke-WebRequest -Uri 'http://localhost/practicas/api/equipos' -Method GET
```

### 3) Forcar font API des de URL
```text
http://localhost/practicas/index.php?action=tabla-clasificacion&source=api
```

## Troubleshooting
### 403 a la API interna
- Revisa que `X-API-Key` coincideixi amb `INTERNAL_API_KEY`.

### Dades buides en source=api (equips)
- Comprova `FOOTBALL_API_KEY` i la competició.
- Revisa TTL/cache i logs del servidor.
- El sistema pot degradar a BDD si el provider falla.

### Warning de headers/cookies
- Evita sortida HTML o espais abans de crides que fan `setcookie`/`header`.

### API externa no respon
- Verifica quota/estat de Football-Data.
- Comprova connectivitat i timeout.
