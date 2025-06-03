<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventario - Su Aroma</title>  
    <link rel="stylesheet" href="CSS/tablas.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo" />
            <nav class="menu-buttons">
                <ul>
                    <li><a href="vender.php" class="menu-buttons">VENDER</a></li>
                    <li><a href="catalogo.php" class="menu-buttons">CATÁLOGO</a></li>
                    <li><a href="inventario.php" class="menu-buttons active">INVENTARIO</a></li>
                    <li><a href="crear.php" class="menu-buttons">CREAR</a></li>
                    <li><a href="informe.php" class="menu-buttons">INFORMES</a></li>
                </ul>
            </nav>
        </aside>
        <main class="content">
            <h2 class="title">Inventario </h2>
            <div class="filter-buttons">
                <button onclick="filtrar('dama')">Perfume Dama</button>
                <button onclick="filtrar('caballero')">Perfume Caballero</button>
                <button onclick="filtrar('todos')">Mostrar Todo</button>
            </div>

            <div class="table-container">
                <table id="tablaPerfumes">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>cantidad</th>
                            <th>Inspiración</th>
                            <th>Casa</th>
                            <th>precio</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                    </tbody>
                </table>
            </div>

            <a class="logout" href="logout.php">Cerrar sesión</a>
        </main>
    </div>
    
    <script>//este script es para cargar la informacion de tabla
const perfumes = [
  {
    codigo: "003",
    nombre: "Luz de Luna",
    cantidad: 50,
    inspiracion: "Light Blue",
    casa: "Luna Scent",
    precio: 45000,
  },
  {
    codigo: "004",
    nombre: "Amanecer Salvaje",
    cantidad: 40,
    inspiracion: "Sauvage",
    casa: "Wild Co.",
    precio: 58000,
  }
];
const tablaBody = document.getElementById("tablaBody");

perfumes.forEach(perfume => {
  const fila = document.createElement("tr");
  fila.setAttribute("data-genero", perfume.genero);

  fila.innerHTML = `
    <td>${perfume.codigo}</td>
    <td>${perfume.nombre}</td>
    <td>${perfume.cantidad}</td>
    <td>${perfume.inspiracion}</td>
    <td>${perfume.casa}</td>
    <td>${perfume.precio}</td>
  `;

  tablaBody.appendChild(fila);
});
</script>

</body>
</html>
