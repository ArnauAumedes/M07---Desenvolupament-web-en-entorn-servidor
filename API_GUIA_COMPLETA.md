# PROMPT PARA MODO PLAN - Implementar modo dual de datos (BDD o API)

## Rol del agente
Actua como arquitecto backend senior y ejecutor tecnico en este proyecto PHP MVC.
Tu objetivo es implementar, de forma incremental y verificable, un sistema donde el usuario pueda escoger la fuente de datos:

1. Fuente BDD: datos desde DAOs actuales (proyecto existente).
2. Fuente API: datos desde la nueva API REST interna (/api).

No rompas el comportamiento actual. Mantener compatibilidad hacia atras es obligatorio.

---

## Contexto del repositorio
Usa esta estructura real:

- Entrada MVC: index.php
- Controladores web: app/controlador/
- DAO/modelo: app/model/dao/
- Entidades: app/model/entities/
- Vistas: app/vista/
- JS cliente: public/js/
- API entrypoint: api/index.php
- Capa API: app/api/
- Config: config/

---

## Objetivo funcional principal
Implementar un selector de origen de datos para listados y consultas (equipos, jugadores, usuarios):

- modo=bdd: obtiene datos como hasta ahora (DAO directamente en controlador MVC).
- modo=api: obtiene datos consumiendo endpoints REST internos.

El usuario debe poder cambiar entre modos sin recargar arquitectura ni duplicar logica de negocio.

---

## Arquitectura objetivo (obligatoria)

1. Routing API separado de routing web.
2. Controladores MVC no deben contener logica HTTP de API.
3. Introducir una capa de servicio/fachada para resolver fuente de datos.
4. JSON de la API con estructura uniforme.
5. Diferenciar status HTTP y status funcional JSON.

---

## Contrato de seleccion de fuente

Definir una unica forma de seleccionar fuente (prioridad recomendada):

1. Query param: ?source=bdd o ?source=api
2. Cookie persistente: data_source_preference
3. Fallback por defecto: bdd

Reglas:

- Si source invalido -> usar bdd y registrar warning.
- Persistir preferencia del usuario (cookie) para siguientes pantallas.

---

## Plan de implementacion por fases

### Fase 1 - Fundacion API interna

1. Consolidar api/index.php como front controller REST.
2. Consolidar app/api/ApiResponse.php para respuestas estandar:
   - status, msg, data, errors, meta
3. Ajustar .htaccess para enrutar /api/... a api/index.php antes del fallback 404 web.
4. Implementar endpoints de lectura minima:
   - GET /api/equipos
   - GET /api/jugadores
   - GET /api/usuarios

Criterio de exito:

- Responden JSON consistente.
- 404/405 correctos.

### Fase 2 - Capa dual de datos

Crear una capa de aplicacion para desacoplar origen:

- app/services/DataSourceResolver.php
- app/services/EquipoDataService.php
- app/services/JugadorDataService.php
- app/services/UserDataService.php

Comportamiento:

- Si source=bdd -> usa DAO existente.
- Si source=api -> consume /api/... via cliente HTTP interno.

Nota:

- No duplicar validaciones de negocio en dos sitios.
- Centralizar transformacion de datos si hay diferencias de formato.

Criterio de exito:

- Mismo formato de salida para vista, independientemente del origen.

### Fase 3 - Integracion en controladores MVC

Actualizar controladores actuales:

- app/controlador/EquipoController.php
- app/controlador/JugadorController.php
- app/controlador/UserController.php

Cambio:

- Sustituir llamadas directas de listado por DataService correspondiente.
- Mantener acciones CRUD existentes tal como estan, salvo ajustes minimos.

Criterio de exito:

- Las vistas actuales siguen funcionando.
- Cambiar source altera el origen real de datos sin romper UI.

### Fase 4 - Selector en interfaz

Implementar selector visible para usuario (por ejemplo en cabecera o filtros):

- Opcion "Base de datos"
- Opcion "API"

Archivos probables:

- app/vista/globals/...
- public/js/equipoSearch.js
- public/js/jugadorSearch.js
- public/js/userSearch.js

Comportamiento UI:

1. Al cambiar selector, actualizar query param source y cookie.
2. Rehacer fetch/listado con origen elegido.
3. Mostrar indicador visual del modo activo.

Criterio de exito:

- El usuario controla la fuente de datos de forma explicita.

### Fase 5 - Seguridad y errores

1. API key o JWT en rutas API protegidas (segun alcance de entrega).
2. Manejo de errores de consumo API:
   - Si no llega JSON, fallback a texto
   - timeouts y errores de red controlados
3. Logs para:
   - source invalido
   - fallo de API
   - discrepancias de esquema

Criterio de exito:

- Sin errores fatales en UI al caer API.
- Mensajes claros al usuario.

---

## Requisitos tecnicos de salida JSON API

Formato base:

```json
{
  "status": true,
  "msg": "Operacion correcta",
  "data": [],
  "errors": [],
  "meta": {}
}
```

Reglas HTTP:

- 200 exito lectura
- 201 creacion
- 400 request invalida
- 401 no autenticado
- 403 sin permisos
- 404 no encontrado
- 405 metodo no permitido
- 422 validacion funcional
- 500 error interno controlado

---

## Requisitos de consumo de API externa de futbol

Agregar servicio dedicado:

- app/services/FootballApiService.php
- app/services/FootballMapper.php

Reglas:

1. Validar JSON antes de procesar.
2. Validar claves minimas esperadas.
3. Mapear payload externo a formato interno estable.
4. Si proveedor cambia estructura, solo tocar mapper.

Ejemplo minimo de validacion:

```php
$json = json_decode($responseBody, true);

if (!is_array($json)) {
    throw new Exception('Provider response is not valid JSON');
}

if (!isset($json['data'])) {
    throw new Exception("Provider JSON missing 'data' key");
}
```

---

## Pruebas obligatorias (definicion de completado)

### A. Pruebas de modo dual

1. source=bdd lista datos correctamente.
2. source=api lista datos correctamente.
3. cambio de source mantiene misma vista y estructura.
4. source invalido cae a bdd + log.

### B. Pruebas API interna

1. GET lista recursos (200)
2. GET item inexistente (404)
3. metodo no permitido (405)
4. errores de validacion (422)

### C. Pruebas UX/AJAX

1. Si error JSON parseable -> mostrar msg API.
2. Si error no JSON -> usar response.text().
3. Si API no disponible en source=api -> mensaje de degradacion + opcion volver a bdd.

---

## Entregables esperados por el modo plan

1. Cambios de codigo por fases, con resumen por archivo modificado.
2. Evidencia de pruebas ejecutadas (HTTP y UI).
3. Lista de riesgos residuales.
4. Siguiente paso recomendado.

---

## Restricciones

1. No romper rutas MVC actuales.
2. No eliminar funcionalidades existentes.
3. No acoplar vistas al formato crudo del provider externo.
4. Mantener coherencia de nombres y estilo del proyecto.

---

## Definition of Done

Se considera terminado cuando:

1. Usuario puede elegir BDD o API en UI.
2. Equipos, jugadores y usuarios funcionan en ambos modos.
3. API propia responde con contrato JSON uniforme.
4. Errores y codigos HTTP estan bien aplicados.
5. Proyecto demostrable en vivo con cambio de fuente sin romper navegacion.

---

## Sprint backlog ejecutable (prioridad por archivo)

Usar este bloque como plan operativo directo.

### Sprint 1 - Base API + selector de fuente

Objetivo del sprint:

- Tener API interna minima operativa.
- Tener resuelto source=bdd/source=api a nivel de resolucion.

Tareas (en orden):

1. Alta prioridad - api/index.php
   - Implementar dispatch REST base por recurso/metodo.
   - Responder 404 y 405 correctamente.
2. Alta prioridad - app/api/ApiResponse.php
   - Estandarizar salida JSON (status/msg/data/errors/meta).
3. Alta prioridad - .htaccess
   - Enrutar /api/... a api/index.php antes del fallback web.
4. Alta prioridad - app/services/DataSourceResolver.php
   - Resolver source desde query/cookie/fallback.
5. Media prioridad - app/api/EquipoApiController.php
   - GET /api/equipos y GET /api/equipos/{id}.
6. Media prioridad - app/api/JugadorApiController.php
   - GET /api/jugadores y GET /api/jugadores/{id}.
7. Media prioridad - app/api/UserApiController.php
   - GET /api/usuarios y GET /api/usuarios/{id}.

Estimacion:

- 1 a 2 dias.

Salida esperada:

- Fuente seleccionable ya resuelta en backend.
- Endpoints de lectura funcionando en /api.

### Sprint 2 - Integracion en MVC y UI

Objetivo del sprint:

- Conectar listados actuales a capa dual sin romper pantallas.

Tareas (en orden):

1. Alta prioridad - app/services/EquipoDataService.php
   - Implementar getAll/getById usando DAO o API segun source.
2. Alta prioridad - app/services/JugadorDataService.php
   - Misma estrategia dual.
3. Alta prioridad - app/services/UserDataService.php
   - Misma estrategia dual.
4. Alta prioridad - app/controlador/EquipoController.php
   - Sustituir lectura directa por EquipoDataService.
5. Alta prioridad - app/controlador/JugadorController.php
   - Sustituir lectura directa por JugadorDataService.
6. Alta prioridad - app/controlador/UserController.php
   - Sustituir lectura directa por UserDataService.
7. Media prioridad - app/vista/globals/ (componente selector)
   - Agregar selector visible Base de datos/API.
8. Alta prioridad - public/js/equipoSearch.js
   - Propagar source en fetch y manejar fallback de error no JSON.
9. Alta prioridad - public/js/jugadorSearch.js
   - Propagar source en fetch y manejo de errores.
10. Alta prioridad - public/js/userSearch.js
   - Propagar source en fetch y manejo de errores.

Estimacion:

- 2 a 3 dias.

Salida esperada:

- Usuario cambia fuente en UI y ve datos sin romper flujo actual.

### Sprint 3 - Seguridad, provider externo y pruebas

Objetivo del sprint:

- Endurecer seguridad y cerrar calidad de entrega.

Tareas (en orden):

1. Alta prioridad - app/api/ApiKeyHelper.php o middleware equivalente
   - Validar api-key (401/403) para rutas protegidas.
2. Media prioridad - app/services/FootballApiService.php
   - Consumir API externa con timeout y control de errores.
3. Media prioridad - app/services/FootballMapper.php
   - Mapear payload externo a formato interno estable.
4. Alta prioridad - app/api/*.http
   - Casos 200/401/403/404/405/422.
5. Alta prioridad - pruebas manuales UI
   - source=bdd, source=api, source invalido.
6. Media prioridad - logs
   - Registrar fallos de API externa y source invalido.

Estimacion:

- 1 a 2 dias.

Salida esperada:

- Proyecto defendible en demo con evidencias de robustez.

---

## Criterios de aceptacion por historia

### Historia A - Selector de fuente

Aceptada cuando:

1. Cambiar source altera el origen real de datos.
2. source persiste por cookie.
3. source invalido cae a bdd sin romper pantalla.

### Historia B - API interna uniforme

Aceptada cuando:

1. Todos los endpoints retornan la misma estructura JSON.
2. Se respetan codigos HTTP definidos.

### Historia C - Integracion AJAX robusta

Aceptada cuando:

1. Si error es JSON, se muestra msg de API.
2. Si error no es JSON, se muestra response.text().
3. Si source=api falla, usuario puede volver a bdd.

---

## Comando operativo para modo plan

Instruccion sugerida para lanzar en modo plan:

"Implementa este backlog en 3 sprints sobre este repositorio, aplicando cambios por fases, validando al final de cada sprint, y entregando resumen por archivo modificado + evidencia de pruebas."
