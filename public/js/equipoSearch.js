/**
 * equipoSearch.js
 * Script para manejar la búsqueda de equipos en la tabla de clasificación o valor, enviando la consulta al servidor mediante AJAX y actualizando el contenido de la tabla sin recargar la página
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search");
  const searchBtn = document.getElementById("search-btn");
  const tablaBody = document.getElementById("tabla-equipos-body");
  const sourceSelector = document.getElementById("source-selector");

  if (!searchInput || !tablaBody) {
    return;
  }

  // Detectar tipo de tabla según la página
  function getActionParam() {
    const params = new URLSearchParams(window.location.search);
    return params.get("action");
  }

  let tipo = "clasificacion";
  if (getActionParam() === "valor-equipo") {
    tipo = "valor";
  }

  function getSource() {
    if (sourceSelector && sourceSelector.value) {
      return sourceSelector.value;
    }
    const params = new URLSearchParams(window.location.search);
    return params.get("source") || "bdd";
  }

  function renderError(message) {
    tablaBody.innerHTML =
      '<tr><td colspan="8" class="text-center text-danger">' +
      message +
      "</td></tr>";
  }

  async function parseErrorResponse(response) {
    const contentType = response.headers.get("content-type") || "";
    if (contentType.includes("application/json")) {
      try {
        const payload = await response.json();
        if (payload && payload.msg) {
          return payload.msg;
        }
      } catch (error) {
        return "Error de API no parseable";
      }
    }

    try {
      const text = await response.text();
      return text || "Error desconocido en la respuesta";
    } catch (error) {
      return "Error de red o timeout";
    }
  }

  async function buscarEquipos() {
    const query = searchInput.value;
    const source = getSource();

    const response = await fetch(
      "app/controlador/searchBarControllerEquipo.php?tipo=" +
        tipo +
        "&source=" +
        encodeURIComponent(source) +
        "&q=" +
        encodeURIComponent(query),
    );

    if (!response.ok) {
      const errorMessage = await parseErrorResponse(response);
      renderError(errorMessage);
      return;
    }

    const html = await response.text();
    tablaBody.innerHTML = html;
  }

  // Buscar al escribir
  searchInput.addEventListener("input", function () {
    buscarEquipos().catch(function (error) {
      renderError(error.message || "Error inesperado");
    });
  });

  if (sourceSelector) {
    sourceSelector.addEventListener("change", function () {
      buscarEquipos().catch(function (error) {
        renderError(error.message || "Error inesperado");
      });
    });
  }

  // Buscar al hacer click en la lupa
  if (searchBtn) {
    searchBtn.addEventListener("click", function () {
      buscarEquipos().catch(function (error) {
        renderError(error.message || "Error inesperado");
      });
    });
  }
});
