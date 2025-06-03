<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informes - Su Aroma</title>
  <link rel="stylesheet" href="CSS/informe.css" />

  <!-- jsPDF y autoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>                                                                                                        
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo" />
      <nav class="menu-buttons">
        <ul>
          <li><a href="vender.php" class="menu-buttons">VENDER</a></li>
          <li><a href="catalogo.php" class="menu-buttons">CATÁLOGO</a></li>
          <li><a href="inventario.php" class="menu-buttons">INVENTARIO</a></li>
          <li><a href="crear.php" class="menu-buttons">CREAR</a></li>
          <li><a href="informe.php" class="menu-buttons active">INFORMES</a></li>
        </ul>
      </nav>
    </aside>

    <main class="content">
      <h2 class="title">Informes de Ventas</h2>

      <!-- Botones de periodo -->
      <div class="filter-buttons">
        <button onclick="mostrarCategorias('día')">Día</button>
        <button onclick="mostrarCategorias('semana')">Semana</button>
        <button onclick="mostrarCategorias('mes')">Mes</button>
        <button onclick="mostrarCategorias('año')">Año</button>
      </div>

      <!-- Categorías -->
      <div id="categoria-container" class="filter-buttons"></div>

      <!-- Resumen de ventas -->
      <div id="resumenDatos"></div>

      <!-- Tabla -->
      <div class="table-container">
        <table id="tablaPerfumes">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nombre</th>
              <th>Cantidad</th>
              <th>Inspiración</th>
              <th>Casa</th>
              <th>Precio</th>
              <th>N. 30ML</th>
              <th>N. 60ML</th>
              <th>N. 100ML</th>
              <th>R. 30ML</th>
              <th>R. 60ML</th>
              <th>R. 10ML</th>
            </tr>
          </thead>
          <tbody id="tablaBody">
            <tr data-genero="dama">
              <td>001</td>
              <td>Rosa Mística</td>
              <td>36</td>
              <td>No. 5</td>
              <td>Elegance Co.</td>
              <td>36000</td>
              <td>3</td><td>2</td><td>1</td><td>5</td><td>2</td><td>0</td>
            </tr>
            <tr data-genero="caballero">
              <td>002</td>
              <td>Brisa Nocturna</td>
              <td>62</td>
              <td>Notas cítricas</td>
              <td>Oceanic Fragrance</td>
              <td>62000</td>
              <td>4</td><td>5</td><td>2</td><td>3</td><td>6</td><td>1</td>
            </tr>
          </tbody>
        </table>
      </div>

      <a class="logout" href="logout.php">Cerrar sesión</a>
    </main>
  </div>

  <script>
    let periodoActual = "";

    function mostrarCategorias(periodo) {
      periodoActual = periodo;
      const contenedor = document.getElementById('categoria-container');
      contenedor.innerHTML = `
        <h3>Categorías - ${periodo.toUpperCase()}</h3>
        <button onclick="filtrar('${periodo}', 'dama')">Dama</button>
        <button onclick="filtrar('${periodo}', 'caballero')">Caballero</button>
        <button onclick="filtrar('${periodo}', 'todos')">Mostrar Todo</button>
        <button onclick="descargarPDF('${periodo}')">Descargar PDF</button>
      `;
      document.getElementById("resumenDatos").innerHTML = "";
    }

    function filtrar(periodo, genero) {
      const filas = document.querySelectorAll("#tablaBody tr");
      let totalVentas = 0;
      let totalProductos = 0;

      filas.forEach(fila => {
        const generoFila = fila.getAttribute("data-genero");
        const mostrar = genero === "todos" || genero === generoFila;
        fila.style.display = mostrar ? "" : "none";

        if (mostrar) {
          const cantidad = parseInt(fila.cells[2].textContent) || 0;
          const precio = parseInt(fila.cells[5].textContent) || 0;
          totalProductos += cantidad;
          totalVentas += cantidad * precio;
        }
      });

      document.getElementById("resumenDatos").innerHTML = `
        Total productos vendidos: ${totalProductos.toLocaleString()} <br>
        Total de ventas: $${totalVentas.toLocaleString()}
      `;
    }

    function descargarPDF(periodo) {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      const filas = document.querySelectorAll("#tablaBody tr");
      let totalVentas = 0;
      let totalProductos = 0;

      filas.forEach(fila => {
        if (fila.style.display !== "none") {
          const cantidad = parseInt(fila.cells[2].textContent) || 0;
          const precio = parseInt(fila.cells[5].textContent) || 0;
          totalProductos += cantidad;
          totalVentas += cantidad * precio;
        }
      });

      doc.setFontSize(14);
      doc.text(`Informe de Ventas - ${periodo.toUpperCase()}`, 10, 10);

      doc.setFontSize(12);
      doc.text(`Total productos vendidos: ${totalProductos.toLocaleString()}`, 10, 20);
      doc.text(`Total de ventas: $${totalVentas.toLocaleString()}`, 10, 30);

      doc.autoTable({
        html: '#tablaPerfumes',
        startY: 40
      });

      doc.save(`informe_${periodo}.pdf`);
    }
  </script>
</body>
</html>
