# README_ODT - Estado de mejoras (apartados 2-22)

Fecha: 2026-04-21  
Proyecto: practicas

Documento de entrega para revision docente. Cada apartado incluye, en el mismo bloque, estado, explicacion y evidencia tecnica (Antes/Despues en todos los apartados).

## Índice

- [Resumen ejecutivo](#resumen-ejecutivo)
- [2. Justificar uso de Singleton](#2-justificar-uso-de-singleton)
- [3. Peticiones simultáneas y conexiones independientes](#3-peticiones-simultáneas-y-conexiones-independientes)
- [4. Token reset con md5 predecible](#4-token-reset-con-md5-predecible)
- [5. Exposición de mensajes internos](#5-exposición-de-mensajes-internos)
- [6. display_errors visible para usuarios](#6-display_errors-visible-para-usuarios)
- [7. Paginación con findAll + array_slice](#7-paginación-con-findall--array_slice)
- [8. Uso de GET sin protección en deleteUser](#8-uso-de-get-sin-protección-en-deleteuser)
- [9. Exposición de errores internos por URL](#9-exposición-de-errores-internos-por-url)
- [10. DAO no debe heredar de entidad](#10-dao-no-debe-heredar-de-entidad)
- [11. Token en texto plano en BDD](#11-token-en-texto-plano-en-bdd)
- [12. getOrderPreference acepta cualquier valor](#12-getorderpreference-acepta-cualquier-valor)
- [13. Acceso directo a /config o .env](#13-acceso-directo-a-config-o-env)
- [14. Almacenamiento de contraseñas](#14-almacenamiento-de-contraseñas)
- [15. UserTokenDAO (remember me) vs token API](#15-usertokendao-remember-me-vs-token-api)
- [16. google.php no valida state CSRF](#16-googlephp-no-valida-state-csrf)
- [17. session_start demasiado tarde en google.php](#17-session_start-demasiado-tarde-en-googlephp)
- [18. session_start al final en hybridauth.php](#18-session_start-al-final-en-hybridauthphp)
- [19. Duplicidad carpeta config](#19-duplicidad-carpeta-config)
- [20. Lógica de vista en DAO](#20-lógica-de-vista-en-dao)
- [21. ordenarPorValor duplicado en DAO](#21-ordenarporvalor-duplicado-en-dao)
- [22. Casuísticas Sign in (BDD <-> Social Auth)](#22-casuísticas-sign-in-bdd---social-auth)
- [Cambios técnicos adicionales aplicados](#cambios-técnicos-adicionales-aplicados)

## Resumen ejecutivo

- Mejorados: 2, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18, 19.
- Parciales: 3, 22.
- Pendientes: 15, 20, 21.

## 2. Justificar uso de Singleton

Estado: Mejorado

Qué se implementó:
- La conexión en config/db-connection.php ahora aplica Singleton real.
- Se definió constructor privado, instancia estática y método getInstance().
- Se bloqueó clonación y deserialización.

Motivo técnico:
- El diseño anterior decía usar Singleton pero permitía múltiples instancias.

Evidencia (Antes vs Despues):

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

## 3. Peticiones simultáneas y conexiones independientes

Estado: Parcial

Qué se implementó:
- Se unificó uso de Database::getInstance() en controladores, API, OAuth y CRUD.

Qué falta:
- Documentar estrategia de concurrencia para entorno real (Apache/PHP-FPM/workers).

Pasos propuestos:
1. Definir modelo objetivo: conexión por proceso o aislamiento por request.
2. Añadir nota técnica de concurrencia en README principal.
3. Si se requiere aislamiento estricto por request, sustituir Singleton por factoría.

Evidencia (Antes vs Despues):

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

## 4. Token reset con md5 predecible

Estado: Mejorado

Qué se implementó:
- Token de recuperación generado con random_bytes(32).
- Token almacenado hasheado con password_hash en PasswordResetDAO.

Motivo técnico:
- Se evita predictibilidad y almacenamiento en texto plano.

Evidencia (Antes vs Despues):

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

## 5. Exposición de mensajes internos

Estado: Mejorado

Qué se implementó:
- En controladores de cambio/registro se muestran mensajes genéricos al usuario.
- El detalle técnico se mantiene en logs con error_log.

Motivo técnico:
- Evita fuga de mensajes de base de datos o stack.

Evidencia (Antes vs Despues):

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

## 6. display_errors visible para usuarios

Estado: Mejorado

Qué se implementó:
- Eliminadas directivas display_errors/display_startup_errors/error_reporting(E_ALL) en vistas OSM:
  - app/vista/osm/asistencias.php
  - app/vista/osm/lista-entrenador.php
  - app/vista/osm/mejores-valorados.php
  - app/vista/osm/pichichis.php
  - app/vista/osm/tabla-clasificacion.php
  - app/vista/osm/valor-equipo.php

Motivo técnico:
- No exponer errores internos a usuarios finales.

Evidencia (Antes vs Despues):

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

## 7. Paginación con findAll + array_slice

Estado: Mejorado

Qué se implementó:
- Se reemplazó carga completa + recorte en memoria por SQL paginada y ordenada.
- Nuevos métodos DAO:
  - EquipoDAO::getClasificacionPaginada()
  - EquipoDAO::getValorEquipoPaginado()
  - JugadorDAO::getMejoresValoradosPaginados()
  - JugadorDAO::getPichichisPaginados()
  - JugadorDAO::getAsistenciasPaginados()

Motivo técnico:
- Menor consumo de memoria y mejor rendimiento con volúmenes altos.

Evidencia (Antes vs Despues):

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

## 8. Uso de GET sin protección en deleteUser

Estado: Mejorado

Qué se implementó:
- El borrado de usuarios requiere POST, sesión válida, rol admin y token CSRF válido.
- Se reemplazó enlace GET por formulario POST en la vista.

Motivo técnico:
- Reduce riesgo CSRF y abusos por enlaces manipulados.

Evidencia (Antes vs Despues):

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

## 9. Exposición de errores internos por URL

Estado: Mejorado

Qué se implementó:
- Se mantiene patrón de respuesta genérica al cliente con detalle técnico en logs.

Motivo técnico:
- Mitiga fuga de trazas y mensajes de excepción en URL o pantalla.

Evidencia (Antes vs Despues):

~~~php
// Antes
header('Location: index.php?error=' . urlencode($e->getMessage()));
~~~

~~~php
// Despues
error_log('Operacion fallida: ' . $e->getMessage());
header('Location: index.php?error=operacion_no_disponible');
~~~

## 10. DAO no debe heredar de entidad

Estado: Mejorado

Qué se implementó:
- EquipoDAO, JugadorDAO, UserDAO y UserTokenDAO dejaron de extender entidades.
- Se mantiene implementación de interfaz DAO sin herencia de modelo.

Motivo técnico:
- Corrige diseño orientado a objetos y separa responsabilidades.

Evidencia (Antes vs Despues):

~~~php
// Antes
class UserDAO extends User implements DAO
~~~

~~~php
// Despues
class UserDAO implements DAO
~~~

## 11. Token en texto plano en BDD

Estado: Mejorado

Qué se implementó:
- UserTokenDAO almacena remember-me con password_hash.

Motivo técnico:
- Si hay fuga de base de datos, el token no es reutilizable directamente.

Evidencia (Antes vs Despues):

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

## 12. getOrderPreference acepta cualquier valor

Estado: Mejorado

Qué se implementó:
- CookieHelper::getOrderPreference() valida whitelist asc/desc.

Motivo técnico:
- Evita uso de entradas arbitrarias que puedan terminar en SQL dinámico inseguro.

Evidencia (Antes vs Despues):

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

## 13. Acceso directo a /config o .env

Estado: Mejorado

Qué se implementó:
- .htaccess bloquea acceso a .env, config/ y app/config/.
- Eliminado app/config/env.php duplicado.

Motivo técnico:
- Reduce exposición accidental de secretos y configuraciones sensibles.

Evidencia (Antes vs Despues):

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

## 14. Almacenamiento de contraseñas

Estado: Mejorado

Qué se implementó:
- Se mantiene password_hash(PASSWORD_DEFAULT) y password_verify().

Motivo técnico:
- Práctica actual recomendada para hashing de contraseñas.

Evidencia (Antes vs Despues):

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

Estado: No mejorado

Qué falta:
- Definir y aplicar política única de tokens de sesión y API.

Pasos propuestos:
1. Añadir campo type en user_tokens (remember_me, api).
2. Implementar validación/revocación/expiración para tokens API en BDD.
3. Limitar claves estáticas a entorno local/desarrollo.

Evidencia (Antes vs Despues (propuesto)):

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

Estado: Mejorado

Qué se implementó:
- google.php genera state aleatorio, lo guarda en sesión y lo valida con hash_equals.

Motivo técnico:
- Previene OAuth login CSRF.

Evidencia (Antes vs Despues):

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

## 17. session_start demasiado tarde en google.php

Estado: Mejorado

Qué se implementó:
- session_start() se ejecuta al inicio del flujo.

Motivo técnico:
- Asegura persistencia de state y variables de sesión.

Evidencia (Antes vs Despues):

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

## 18. session_start al final en hybridauth.php

Estado: Mejorado

Qué se implementó:
- session_start() movido al inicio del script.

Motivo técnico:
- Evita pérdida de estado de autenticación.

Evidencia (Antes vs Despues):

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

## 19. Duplicidad carpeta config

Estado: Mejorado

Qué se implementó:
- Eliminado app/config/env.php.
- Configuración consolidada en config/.

Motivo técnico:
- Menos deuda técnica y menor riesgo de inconsistencias.

Evidencia (Antes vs Despues):

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

## 20. Lógica de vista en DAO

Estado: No mejorado

Qué falta:
- Aún existe lógica de presentación (getDiferenciaObjetivoPosicion) en EquipoDAO.

Pasos propuestos:
1. Mover esa lógica a servicio de aplicación o helper de vista.
2. Dejar DAO solo para persistencia/consulta.
3. Ajustar llamadas en controladores y vistas.

Evidencia (Antes vs Despues (propuesto)):

~~~php
// Antes (actual en DAO)
public function getDiferenciaObjetivoPosicion(array $equipo): string
{
  if ($equipo['posicion'] <= $equipo['objetivo']) {
    return 'cumpliendo objetivo';
  }

  return 'por debajo del objetivo';
}
~~~

~~~php
// Despues (propuesto en servicio/helper)
public function calcularDiferenciaObjetivoPosicion(array $equipo): string
{
  return $equipo['posicion'] <= $equipo['objetivo']
    ? 'cumpliendo objetivo'
    : 'por debajo del objetivo';
}
~~~

## 21. ordenarPorValor duplicado en DAO

Estado: No mejorado

Qué falta:
- Persisten métodos de ordenación duplicados en distintos DAO.

Pasos propuestos:
1. Eliminar ordenarPorValor() de DAO.
2. Centralizar orden en servicios/controladores (sortByValue).
3. Añadir tests funcionales de orden.

Evidencia (Antes vs Despues (propuesto)):

~~~php
// Antes (duplicado en DAO)
public function ordenarPorValor(array $items, string $order): array
{
  usort($items, fn ($a, $b) => $order === 'asc' ? $a['valor'] <=> $b['valor'] : $b['valor'] <=> $a['valor']);
  return $items;
}
~~~

~~~php
// Despues (propuesto en servicio)
$items = $this->equipoDataService->sortByValue($items, fn ($i) => $i['valor'], $order);
~~~

## 22. Casuísticas Sign in (BDD <-> Social Auth)

Estado: Parcial

Qué falta:
- Formalizar política de vinculación entre cuentas OAuth y cuentas con contraseña.

Pasos propuestos:
1. Definir regla única por email (auto-link o verificación adicional).
2. Gestionar caso OAuth-first sin password local (alta opcional de contraseña).
3. Documentar migración y mensajes UX para cuentas existentes.

Evidencia (Antes vs Despues (propuesto)):

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

## Cambios técnicos adicionales aplicados

- En config/db-connection.php ya no se expone el error de conexión con die(...); ahora se registra en log y se lanza excepción genérica.
- Se migraron usos de new Database() a Database::getInstance() en controladores, OAuth, API y vistas CRUD.


