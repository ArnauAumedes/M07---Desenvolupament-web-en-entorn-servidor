/**
 * userSearch.js
 * Script para manejar la búsqueda de usuarios en la tabla de clasificación, enviando la consulta al servidor mediante AJAX y actualizando el contenido de la tabla sin recargar la página
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("search");
  const searchBtn = document.getElementById("search-btn");
  const tablaBody = document.getElementById("tabla-users-body");

  function buscarUsers() {
    const query = searchInput.value;
    fetch(
      "app/controlador/searchBarControllerUser.php?" + "&q=" +
        encodeURIComponent(query),
    )
      .then((response) => response.text())
      .then((html) => {
        tablaBody.innerHTML = html;
      });
  }
  // Buscar al escribir
  searchInput.addEventListener("input", buscarUsers);
  // Buscar al hacer click en la lupa
  if (searchBtn) {
    searchBtn.addEventListener("click", buscarUsers);
  }
});
