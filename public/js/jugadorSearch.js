/**
 * jugadorSearch.js
 * Script para manejar la búsqueda de jugadores en la tabla de mejores valorados, asistencias o pichichis, enviando la consulta al servidor mediante AJAX y actualizando el contenido de la tabla sin recargar la página
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search");
  const searchBtn = document.getElementById("search-btn");
  const tablaBody = document.getElementById("tabla-jugadores-body");

  // Detectar tipo de tabla según la página
  function getActionParam() {
    const params = new URLSearchParams(window.location.search);
    return params.get("action");
  }

  let tipo = "mejores-valorados";
  if (getActionParam() === "asistencias") {
    tipo = "asistencias";
  } else if (getActionParam() === "pichichis") {
    tipo = "pichichis";
  }

  function buscarJugadores() {
    const query = searchInput.value;
    fetch(
      "app/controlador/searchBarControllerJugador.php?tipo=" +
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
  searchInput.addEventListener("input", buscarJugadores);
  // Buscar al hacer click en la lupa
  if (searchBtn) {
    searchBtn.addEventListener("click", buscarJugadores);
  }
});
