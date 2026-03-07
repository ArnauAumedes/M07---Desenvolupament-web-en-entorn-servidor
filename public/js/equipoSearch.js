/**
 * equipoSearch.js
 * Script para manejar la búsqueda de equipos en la tabla de clasificación o valor, enviando la consulta al servidor mediante AJAX y actualizando el contenido de la tabla sin recargar la página
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search");
  const searchBtn = document.getElementById("search-btn");
  const tablaBody = document.getElementById("tabla-equipos-body");

  // Detectar tipo de tabla según la página
  function getActionParam() {
    const params = new URLSearchParams(window.location.search);
    return params.get("action");
  }

  let tipo = "clasificacion";
  if (getActionParam() === "valor-equipo") {
    tipo = "valor";
  }

  function buscarEquipos() {
    const query = searchInput.value;
    fetch(
      "app/controlador/searchBarControllerEquipo.php?tipo=" +
        tipo +
        "&q=" +
        encodeURIComponent(query),
    )
      .then((response) => response.text())
      .then((html) => {
        tablaBody.innerHTML = html;
      });
  }
  // Buscar al escribir
  searchInput.addEventListener("input", buscarEquipos);
  // Buscar al hacer click en la lupa
  if (searchBtn) {
    searchBtn.addEventListener("click", buscarEquipos);
  }
});
