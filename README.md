# Pràctica 03 — Paginació 

Aquest README descriu de manera específica què s'ha implementat a la pràctica per la funcionalitat de paginació i per què s'han pres les decisions tècniques.

## Fitxers implicats
- app/controlador/ArticleController.php (mètode private function showMenu())
- app/model/dao/ArticleDAO.php (mètodes countAll() i findPage())
- app/vista/menu.php (dropdown per_page, renderitzat de la taula i controls de paginació)
- public/css/style.css (estils per `.paginacion` i `#per_page`)

---

## Resum funcional
- L'usuari pot triar quants articles veure per pàgina (1, 5, 10, 20) via un select.
- El controlador valida els paràmetres, calcula total de pàgines i offset, i obté només els articles necessaris amb LIMIT/OFFSET.
- La vista mostra controls: Inici, Anterior, enllaços numèrics (finestra màx. 5), "..." si cal, Següent i Final.
- Si la pàgina demanada és invàlida, el sistema redirigeix a una URL amb `page=1` per mantenir coherència de la query string.

---

## Explicació detallada — backend (ArticleController::showMenu)

Codi rellevant:
```php
// filepath: c:\xampp\htdocs\practicas\Pràctica 03 - Paginació\app\controlador\ArticleController.php
private function showMenu()
{
    // Obtenir els parametres de paginació
    $allowed = [1,5,10,20];
    // Default a 5 si no és vàlid
    $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 5;
    if (!in_array($perPage, $allowed)) {
        $perPage = 5;
        if (isset($_GET['per_page'])) {
            header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?per_page=5");
        }
    }

    // Pàgina actual
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

    try {
        $totalItems = $this->articleDAO->countAll(); 
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        // Ajustar la pàgina actual si excedeix el total de pàgines
        if ($currentPage > $totalPages || $currentPage < 1) {
            if ($currentPage !== 1) {
                header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?per_page=" . $perPage . "&page=1");
            }
            $currentPage = 1; 
        }
        // Calcular offset
        $offset = ($currentPage - 1) * $perPage;
        $articles = $this->articleDAO->findPage($perPage, $offset);
    } catch (Exception $e) {
        $articles = [];
        $totalItems = 0;
        $totalPages = 1;
        $message = "Error obtenint articles: " . $e->getMessage();        
    }

    include __DIR__ . '/../vista/menu.php';
}
```

Explicació pas a pas i justificacions:
- `$allowed = [1,5,10,20];`  
  Definició d'una whitelist per `per_page`. Justificació: evitar valors inesperats o maliciosos que podrien produir consultes ineficients o errors.

- `$perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 5;`  
  Casteig a int i valor per defecte 5. Justificació: sempre treballar amb enter per LIMIT i fer un valor raonable per defecte.

- `if (!in_array($perPage, $allowed)) { ... header(...per_page=5) }`  
  Si l'usuari passa un `per_page` no permès, es força a 5 i es pot redirigir per actualitzar la URL. Justificació: coherència de la query string i seguretat.

- `$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;`  
  Assegura que la pàgina actual és un enter ≥ 1. Justificació: prevenir offsets negatius.

- `try { $totalItems = $this->articleDAO->countAll(); ... } catch (Exception $e) { ... }`  
  S'usa try/catch per capturar errors PDO i evitar mostrar excepcions crítiques a l'usuari. Justificació: robustesa i UX controlada.

- ` $totalPages = max(1, (int)ceil($totalItems / $perPage));`  
  Càlcul del nombre de pàgines; `max(1, ...)` garanteix com a mínim 1 pàgina encara amb 0 articles. Justificació: evita dividir per zero i simplifica la lògica de la vista.

- `if ($currentPage > $totalPages || $currentPage < 1) { ... header(...&page=1) ... }`  
  Comprovació de rang. Si la pàgina sol·licitada és fora de rang, redirigeix a `page=1` i assigna `currentPage = 1`. Justificació: manté la URL i la vista coherents. (Alternativa raonable: redirigir a l'última pàgina vàlida — es podria canviar si es preferís.)

- `$offset = ($currentPage - 1) * $perPage;`  
  Càlcul de l'offset per LIMIT/OFFSET. Justificació: és la fórmula estàndard per recuperar la finestra de registres correcte.

- `$articles = $this->articleDAO->findPage($perPage, $offset);`  
  Recupera només els registres necessaris. Justificació: eficiència en la consulta i menor càrrega de memòria.

---

## Explicació detallada — frontend (menu.php)

Codi rellevant (resum):
```html
<form id="perPageForm" method="GET" class="form-inline" style="display:inline-block;">
    <input type="hidden" name="action" value="menu">
    <div class="hint-text">Mostrant <?php echo isset($perPage) ? $perPage : 5; ?> per pàgina</div>
    <select name="per_page" id="per_page" onchange="document.getElementById('perPageForm').submit()">
        <?php
        $options = [1, 5, 10, 20];
        $currentPerPage = $perPage ?? 5;
        foreach ($options as $option) {
            $selected = ($option == $currentPerPage) ? 'selected' : '';
            echo "<option value=\"$option\" $selected>$option</option>";
        }
        ?>
    </select>
    <input type="hidden" name="page" value="1">
</form>

<!-- Llistat d'articles (renderitzat amb $articles) -->

<div class="paginacion">
    <!-- controls: Inici, Anterior, números (finestra màx. 5), ..., Següent, Final -->
</div>
```

Explicació i justificacions:
- El `form` envia per GET `per_page` i fixa `action=menu` i `page=1`. Això reinicia la navegació a la primera pàgina quan canvia el nombre per pàgina (UX previsible).
- `select` té onchange que submit immediat: interacció senzilla i ràpida sense JavaScript complex.
- La vista rep `$articles` (proporcionats pel controlador) i renderitza només els registres d'aquesta pàgina.
- El bloc `.paginacion` implementa: enllaços Inici/Anterior, finestra numèrica centrada (màx. 5), "..." quan cal, i Enllaços Següent/Final. Justificació: usabilitat (no mostrar centenars de números) i facilitar saltar a principi/final.



 