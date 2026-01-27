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
      "/practicas/app/controlador/searchBarController.php?tipo=" +
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
