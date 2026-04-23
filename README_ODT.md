# README_ODT - Estat de millores (apartats 2-22)

Data: 2026-04-22  
Projecte: practicas

Document d'entrega per a revisió docent. Cada apartat inclou, dins del mateix bloc, estat, explicació i evidència tècnica (Abans/Després a tots els apartats).

## Índex

- [Resum executiu](#resum-executiu)
- [2. Justificar l'ús de Singleton](#2-justificar-lús-de-singleton)
- [3. Peticions simultànies i connexions independents](#3-peticions-simultànies-i-connexions-independents)
- [4. Token de reset amb md5 predictible](#4-token-de-reset-amb-md5-predictible)
- [5. Exposició de missatges interns](#5-exposició-de-missatges-interns)
- [6. display_errors visible per als usuaris](#6-display_errors-visible-per-als-usuaris)
- [7. Paginació amb findAll + array_slice](#7-paginació-amb-findall--array_slice)
- [8. Ús de GET sense protecció a deleteUser](#8-ús-de-get-sense-protecció-a-deleteuser)
- [9. Exposició d'errors interns per URL](#9-exposició-derrors-interns-per-url)
- [10. El DAO no ha d'heretar d'entitat](#10-el-dao-no-ha-dheretar-dentitat)
- [11. Token en text pla a BDD](#11-token-en-text-pla-a-bdd)
- [12. getOrderPreference accepta qualsevol valor](#12-getorderpreference-accepta-qualsevol-valor)
- [13. Accés directe a /config o .env](#13-accés-directe-a-config-o-env)
- [14. Emmagatzematge de contrasenyes](#14-emmagatzematge-de-contrasenyes)
- [15. UserTokenDAO (remember me) vs token API](#15-usertokendao-remember-me-vs-token-api)
- [16. google.php no valida state CSRF](#16-googlephp-no-valida-state-csrf)
- [17. session_start massa tard a google.php](#17-session_start-massa-tard-a-googlephp)
- [18. session_start al final a hybridauth.php](#18-session_start-al-final-a-hybridauthphp)
- [19. Duplicació de carpeta config](#19-duplicació-de-carpeta-config)
- [20. Lògica de vista al DAO](#20-lògica-de-vista-al-dao)
- [21. ordenarPorValor duplicat al DAO](#21-ordenarporvalor-duplicat-al-dao)
- [22. Casuístiques Sign in (BDD <-> Social Auth)](#22-casuístiques-sign-in-bdd---social-auth)
- [Canvis tècnics addicionals aplicats](#canvis-tècnics-addicionals-aplicats)

## Resum executiu

- Millorats: 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21.
- Parcials: 22.
- Pendents: 15.

## 2. Justificar l'ús de Singleton

Estat: Millorat

Què s'ha implementat:
- La connexió a config/db-connection.php ara aplica un Singleton real.
- S'ha definit constructor privat, instància estàtica i mètode getInstance().
- S'ha bloquejat la clonació i la deserialització.

Motiu tècnic:
- El disseny anterior deia que feia servir Singleton però permetia múltiples instàncies.

Evidència (Abans vs Després):

~~~php
// Antes
class Database
{
  public function __construct()
  {
    $this->connect();
  }
}
~~~

~~~php
// Despues
class Database
{
  private static $instance = null;

  private function __construct()
  {
    $this->connect();
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}
~~~

## 3. Peticions simultànies i connexions independents

Estat: Millorat

Què s'ha implementat:
- S'ha unificat l'ús de Database::getInstance() a controladors, API, OAuth i CRUD.
- S'ha documentat l'estratègia de concurrència a README.md (secció "Concurrencia i connexions a base de dades").

Motiu tècnic:
- Es defineix explícitament el comportament de connexió per request/procés a PHP, evitant ambigüitat sobre la reutilització de PDO en escenaris concurrents.

Nota d'arquitectura aplicada:
1. Instància única de Database per request/procés.
2. Aïllament natural entre requests concurrents a Apache/PHP-FPM.
3. Punt únic de canvi futur: config/db-connection.php.

Evidència (Abans vs Després):

~~~php
// Antes
$database = new Database();
$pdo = $database->getConnection();
~~~

~~~php
// Despues
$database = Database::getInstance();
$pdo = $database->getConnection();
~~~

## 4. Token de reset amb md5 predictible

Estat: Millorat

Què s'ha implementat:
- Token de recuperació generat amb random_bytes(32).
- Token emmagatzemat hashejat amb password_hash a PasswordResetDAO.

Motiu tècnic:
- S'evita la predictibilitat i l'emmagatzematge en text pla.

Evidència (Abans vs Després):

~~~php
// Antes
$token = md5(uniqid((string) mt_rand(), true));
$passwordResetDAO->saveToken($userId, $token); // texto plano
~~~

~~~php
// Despues
$token = bin2hex(random_bytes(32));
$tokenHash = password_hash($token, PASSWORD_DEFAULT);
$passwordResetDAO->saveToken($userId, $tokenHash); // hasheado
~~~

## 5. Exposició de missatges interns

Estat: Millorat

Què s'ha implementat:
- Als controladors de canvi/registre es mostren missatges genèrics a l'usuari.
- El detall tècnic es manté als logs amb error_log.

Motiu tècnic:
- Evita la fuga de missatges de base de dades o stack.

Evidència (Abans vs Després):

~~~php
// Antes
catch (PDOException $e) {
  die('Error de connexió: ' . $e->getMessage());
}
~~~

~~~php
// Despues
catch (PDOException $e) {
  error_log('Error de connexio a BD: ' . $e->getMessage());
  throw new Exception('Error de connexio a la base de dades');
}
~~~

## 6. display_errors visible per als usuaris

Estat: Millorat

Què s'ha implementat:
- Eliminades directives display_errors/display_startup_errors/error_reporting(E_ALL) a vistes OSM:
  - app/vista/osm/asistencias.php
  - app/vista/osm/lista-entrenador.php
  - app/vista/osm/mejores-valorados.php
  - app/vista/osm/pichichis.php
  - app/vista/osm/tabla-clasificacion.php
  - app/vista/osm/valor-equipo.php

Motiu tècnic:
- No exposar errors interns als usuaris finals.

Evidència (Abans vs Després):

~~~php
// Antes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
~~~

~~~php
// Despues
// Bloque eliminado. Se respeta configuración global de logging.
~~~

## 7. Paginació amb findAll + array_slice

Estat: Millorat

Què s'ha implementat:
- S'ha substituït càrrega completa + retall en memòria per SQL paginada i ordenada.
- Nous mètodes DAO:
  - EquipoDAO::getClasificacionPaginada()
  - EquipoDAO::getValorEquipoPaginado()
  - JugadorDAO::getMejoresValoradosPaginados()
  - JugadorDAO::getPichichisPaginados()
  - JugadorDAO::getAsistenciasPaginados()

Motiu tècnic:
- Menor consum de memòria i millor rendiment amb volums alts.

Evidència (Abans vs Després):

~~~php
// Antes (controlador)
$equipos = $this->equipoDataService->getAll($source);
if ($ordenCallback !== null) {
  $equipos = $this->equipoDataService->sortByValue($equipos, $ordenCallback, $order);
}
$equipos = array_slice($equipos, $offset, $limit);
~~~

~~~php
// Despues (controlador, fuente bdd)
if ($vista === 'tabla-clasificacion') {
  $equipos = $this->equipoDAO->getClasificacionPaginada($limit, $offset, $order);
} elseif ($vista === 'valor-equipo') {
  $equipos = $this->equipoDAO->getValorEquipoPaginado($limit, $offset, $order);
} else {
  $equipos = $this->equipoDAO->getEquiposPaginados($limit, $offset);
}
~~~

## 8. Ús de GET sense protecció a deleteUser

Estat: Millorat

Què s'ha implementat:
- L'esborrat d'usuaris requereix POST, sessió vàlida, rol admin i token CSRF vàlid.
- S'ha substituït l'enllaç GET per formulari POST a la vista.

Motiu tècnic:
- Redueix el risc CSRF i abusos per enllaços manipulats.

Evidència (Abans vs Després):

~~~php
// Antes (vista)
<a href="index.php?action=deleteUser&id=<?= urlencode($user->getId()) ?>"
   onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">
   <i class="fa fa-trash text-danger" aria-hidden="true"></i>
</a>
~~~

~~~php
// Despues (vista)
<form method="post" action="index.php" style="display:inline;"
    onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
  <input type="hidden" name="action" value="deleteUser">
  <input type="hidden" name="id" value="<?= htmlspecialchars((string) $user->getId(), ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <button type="submit" style="background:none;border:none;padding:0;">
    <i class="fa fa-trash text-danger" aria-hidden="true"></i>
  </button>
</form>
~~~

~~~php
// Despues (controlador)
private function deleteUser()
{
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }

  if (empty($_SESSION['user']['user_id']) || empty($_SESSION['user']['isAdmin'])) {
    header("Location: index.php?deletedUser=error");
    exit();
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
    if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? null)) {
      header("Location: index.php?deletedUser=error");
      exit();
    }
    // ... borrado
  }
}
~~~

## 9. Exposició d'errors interns per URL

Estat: Millorat

Què s'ha implementat:
- Es manté el patró de resposta genèrica al client amb detall tècnic als logs.

Motiu tècnic:
- Mitiga la fuga de traces i missatges d'excepció a URL o pantalla.

Evidència (Abans vs Després):

~~~php
// Antes
header('Location: index.php?error=' . urlencode($e->getMessage()));
~~~

~~~php
// Despues
error_log('Operacion fallida: ' . $e->getMessage());
header('Location: index.php?error=operacion_no_disponible');
~~~

## 10. El DAO no ha d'heretar d'entitat

Estat: Millorat

Què s'ha implementat:
- EquipoDAO, JugadorDAO, UserDAO i UserTokenDAO han deixat d'estendre entitats.
- Es manté la implementació d'interfície DAO sense herència de model.

Motiu tècnic:
- Corregeix el disseny orientat a objectes i separa responsabilitats.

Evidència (Abans vs Després):

~~~php
// Antes
class UserDAO extends User implements DAO
~~~

~~~php
// Despues
class UserDAO implements DAO
~~~

## 11. Token en text pla a BDD

Estat: Millorat

Què s'ha implementat:
- UserTokenDAO emmagatzema remember-me amb password_hash.

Motiu tècnic:
- Si hi ha fuga de base de dades, el token no és reutilitzable directament.

Evidència (Abans vs Després):

~~~php
// Antes
$token = bin2hex(random_bytes(32));
$userTokenDAO->save($userId, $token); // texto plano
~~~

~~~php
// Despues
$token = bin2hex(random_bytes(32));
$tokenHash = password_hash($token, PASSWORD_DEFAULT);
$userTokenDAO->save($userId, $tokenHash); // hasheado
~~~

## 12. getOrderPreference accepta qualsevol valor

Estat: Millorat

Què s'ha implementat:
- CookieHelper::getOrderPreference() valida whitelist asc/desc.

Motiu tècnic:
- Evita l'ús d'entrades arbitràries que puguin acabar en SQL dinàmic insegur.

Evidència (Abans vs Després):

~~~php
// Antes
public static function getOrderPreference(): string
{
  return $_COOKIE['order'] ?? 'desc';
}
~~~

~~~php
// Despues
public static function getOrderPreference(): string
{
  $value = strtolower((string) ($_COOKIE['order'] ?? 'desc'));
  return in_array($value, ['asc', 'desc'], true) ? $value : 'desc';
}
~~~

## 13. Accés directe a /config o .env

Estat: Millorat

Què s'ha implementat:
- .htaccess bloqueja accés a .env, config/ i app/config/.
- Eliminat app/config/env.php duplicat.

Motiu tècnic:
- Redueix exposició accidental de secrets i configuracions sensibles.

Evidència (Abans vs Després):

~~~text
Antes
config/env.php
app/config/env.php
~~~

~~~text
Ahora
config/env.php
app/config/env.php -> eliminado
~~~

## 14. Emmagatzematge de contrasenyes

Estat: Millorat

Què s'ha implementat:
- Es manté password_hash(PASSWORD_DEFAULT) i password_verify().

Motiu tècnic:
- Pràctica actual recomanada per al hashing de contrasenyes.

Evidència (Abans vs Després):

~~~php
// Antes
$user->setPassword($plainPassword); // texto plano o hash no estandar
~~~

~~~php
// Despues
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
$isValid = password_verify($loginPassword, $hash);
~~~

## 15. UserTokenDAO (remember me) vs token API

Estat: No millorat

Què falta:
- Definir i aplicar política única de tokens de sessió i API.

Passos proposats:
1. Afegir camp type a user_tokens (remember_me, api).
2. Implementar validació/revocació/expiració per a tokens API a BDD.
3. Limitar claus estàtiques a entorn local/desenvolupament.

Evidència (Abans vs Després (proposat)):

~~~php
// Antes (actual)
// Unico repositorio de tokens sin tipado explicito
$userTokenDAO->save($userId, $tokenHash);
~~~

~~~php
// Despues (propuesto)
$userTokenDAO->save($userId, $tokenHash, [
  'type' => 'api',
  'expires_at' => $expiresAt,
  'revoked' => 0,
]);
~~~

## 16. google.php no valida state CSRF

Estat: Millorat

Què s'ha implementat:
- google.php genera state aleatori, el desa en sessió i el valida amb hash_equals.

Motiu tècnic:
- Prevé OAuth login CSRF.

Evidència (Abans vs Després):

~~~php
// Antes
$googleClient->authenticate($_GET['code']);
~~~

~~~php
// Despues
if (!hash_equals($_SESSION['oauth_state'] ?? '', $_GET['state'] ?? '')) {
  throw new RuntimeException('Invalid OAuth state');
}
$googleClient->authenticate($_GET['code']);
~~~

## 17. session_start massa tard a google.php

Estat: Millorat

Què s'ha implementat:
- session_start() s'executa a l'inici del flux.

Motiu tècnic:
- Assegura persistència de state i variables de sessió.

Evidència (Abans vs Després):

~~~php
// Antes
// ... salida HTML/includes
session_start();
~~~

~~~php
// Despues
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// ... resto del flujo OAuth
~~~

## 18. session_start al final a hybridauth.php

Estat: Millorat

Què s'ha implementat:
- session_start() mogut a l'inici de l'script.

Motiu tècnic:
- Evita pèrdua d'estat d'autenticació.

Evidència (Abans vs Després):

~~~php
// Antes
// configuracion hybridauth
// ...
session_start();
~~~

~~~php
// Despues
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// configuracion hybridauth
~~~

## 19. Duplicació de carpeta config

Estat: Millorat

Què s'ha implementat:
- Eliminat app/config/env.php.
- Configuració consolidada a config/.

Motiu tècnic:
- Menys deute tècnic i menor risc d'inconsistències.

Evidència (Abans vs Després):

~~~php
// Antes
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../config/env.php';
~~~

~~~php
// Despues
require_once __DIR__ . '/../../config/env.php';
// fuente unica de configuracion
~~~

## 20. Lògica de vista al DAO

Estat: Millorat

Què s'ha implementat:
- S'ha eliminat getDiferenciaObjetivoPosicion() d'EquipoDAO.
- La lògica de diferència objectiu/posició queda centralitzada a EquipoDataService.
- La vista lista-entrenador manté la crida a equipoDAO, però en aquest context equipoDAO apunta al servei (no al DAO de persistència).

Motiu tècnic:
- El DAO queda exclusivament per a accés a dades, evitant lògica de presentació a la capa de persistència.

Evidència (Abans vs Després):

~~~php
// Antes (en EquipoDAO)
public function getDiferenciaObjetivoPosicion($objetivo, $posicionActual)
{
  // logica de presentacion en DAO
}
~~~

~~~php
// Despues (en servicio)
public function getDiferenciaObjetivoPosicion(int $objetivo, int $posicionActual): array
{
  // logica encapsulada en EquipoDataService
}
~~~

## 21. ordenarPorValor duplicat al DAO

Estat: Millorat

Què s'ha implementat:
- S'ha eliminat ordenarPorValor() d'EquipoDAO, JugadorDAO i UserDAO.
- Es manté l'ordenació centralitzada en serveis mitjançant sortByValue().

Motiu tècnic:
- S'elimina duplicació de lògica i es redueix deute tècnic en usar una única estratègia d'ordenació per servei.

Evidència (Abans vs Després):

~~~php
// Antes (duplicado en DAO)
public function ordenarPorValor($items, $value, $order = 'desc')
{
  usort($items, function ($a, $b) use ($value, $order) {
    $valorA = $value($a);
    $valorB = $value($b);
    return $order === 'desc' ? ($valorB <=> $valorA) : ($valorA <=> $valorB);
  });
  return $items;
}
~~~

~~~php
// Despues (servicio)
$items = $this->equipoDataService->sortByValue($items, fn ($i) => $i['valor'], $order);
~~~

## 22. Casuístiques Sign in (BDD <-> Social Auth)

Estat: Parcial

Què falta:
- Formalitzar política de vinculació entre comptes OAuth i comptes amb contrasenya.
- Persistir proveïdor OAuth vinculat per usuari per traçabilitat (camp o taula dedicada).
- Definir flux de vinculació/verificació per a comptes BDD ja existents.

Què s'ha implementat en aquesta iteració:
- En login tradicional s'ha afegit un missatge específic per a comptes sense contrasenya local (OAuth-first), indicant accés per Google/GitHub o configuració de contrasenya des del perfil.

Passos proposats:
1. Definir regla única per email (auto-link o verificació addicional).
2. Gestionar cas OAuth-first sense password local (alta opcional de contrasenya).
3. Documentar migració i missatges UX per a comptes existents.

Evidència (Abans vs Després (proposat)):

~~~php
// Antes (actual)
$user = $userDAO->findByEmail($oauthEmail);
if ($user === null) {
  $user = $userDAO->createFromOAuth($oauthProfile);
}
~~~

~~~php
// Despues (propuesto)
$user = $userDAO->findByEmail($oauthEmail);
if ($user !== null && !$user->hasOAuthProvider('google')) {
  $authService->linkOAuthAccountWithVerification($user, 'google', $oauthProfile);
}
~~~

## Canvis tècnics addicionals aplicats

- A config/db-connection.php ja no s'exposa l'error de connexió amb die(...); ara es registra a log i es llença una excepció genèrica.
- S'han migrat usos de new Database() a Database::getInstance() en controladors, OAuth, API i vistes CRUD.
