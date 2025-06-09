<?php
// Incluimos el archivo de conexión a la base de datos.
include 'includes/conexion.php';

// 1.obtenemos las categorías para los filtros
$cats = [];
//consultamos todas las categorías de productos para usarlas como filtros
$result = $conn->query("SELECT nombre_categoria FROM categorias");
while ($row = $result->fetch_assoc()) {
    // Guardamos el nombre de cada categoría en el arreglo $cats
    $cats[] = $row['nombre_categoria'];
}

// 2. Obtener los productos con la categoría asociada
// Creamos el SQL para traer todos los productos junto con su categoría
$sql = "SELECT p.codigo_producto, p.imagen_url, p.nombre_producto, p.inspiracion, p.casa, p.descripcion, c.nombre_categoria
        FROM productos p
        JOIN categorias c ON p.categoria_id = c.id";
$result = $conn->query($sql);
$productos = [];
while ($row = $result->fetch_assoc()) {
    // Guardamos cada producto en el arreglo $productos
    $productos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catalogo - Su Aroma</title>
    <!-- Vinculamos el CSS para las tablas -->
    <link rel="stylesheet" href="CSS/tablas.css" />
</head>
<body>
<div class="container">
    <!-- Barra lateral con el logo y el menú de navegación -->
    <aside class="logo-section">
        <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" />
        <nav class="menu-buttons">
            <ul>
                <!-- Menú de navegación -->
                <li><a href="vender.php" class="menu-buttons">VENDER</a></li>
                <li><a href="catalogo.php" class="menu-buttons active">CATÁLOGO</a></li>
                <li><a href="inventario.php" class="menu-buttons">INVENTARIO</a></li>
                <li><a href="crear.php" class="menu-buttons">CREAR</a></li>
                <li><a href="informe.php" class="menu-buttons">INFORMES</a></li>
            </ul>
        </nav>
    </aside>
    <main class="content">
        <h2 class="title">Catálogo de productos</h2>
        <!-- Botones para filtrar por categoría -->
        <div class="filter-buttons">
            <?php foreach ($cats as $cat): ?>
                <!-- Cada botón filtra por una categoría distinta -->
                <button onclick="filtrar('<?= htmlspecialchars($cat) ?>')">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
            <!-- Botón para mostrar todos los productos -->
            <button onclick="filtrar('todos')">Mostrar Todos</button>
        </div>

        <!-- Contenedor de la tabla de productos -->
        <div class="table-container">
            <table id="tablaPerfumes">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Inspiración</th>
                        <th>Casa</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <!-- Cada fila representa un producto. El atributo data-categoria nos ayuda a filtrar con JS -->
                        <tr data-categoria="<?= htmlspecialchars($prod['nombre_categoria']) ?>">
                            <td><?= htmlspecialchars($prod['codigo_producto']) ?></td>
                            <td>
                                <?php if ($prod['imagen_url']): ?>
                                    <!-- Mostramos la imagen si existe, con tamaño y cursor para indicar que se puede ampliar -->
                                    <img src="<?= htmlspecialchars($prod['imagen_url']) ?>" alt="img" style="max-width:60px;cursor:pointer;">
                                <?php else: ?>
                                    <!-- Si no hay imagen, mostramos un texto alternativo -->
                                    <span>Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                            <td><?= htmlspecialchars($prod['inspiracion']) ?></td>
                            <td><?= htmlspecialchars($prod['casa']) ?></td>
                            <td><?= htmlspecialchars($prod['descripcion']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Botón para cerrar sesión -->
        <a class="logout" href="logout.php">Cerrar sesión</a>
    </main>
</div>

<!-- Modal para mostrar la imagen ampliada -->
<div id="imageModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;">
    <span class="close" style="position:absolute;top:20px;right:30px;font-size:30px;cursor:pointer;color:white;">&times;</span>
    <img class="modal-content" id="imgAmpliada" style="max-width:90%;max-height:80%;">
</div>


<script>
// Función para filtrar productos por categoría usando JS
function filtrar(categoria) {
    // Selecciona todas las filas de la tabla de perfumes
    document.querySelectorAll('#tablaPerfumes tbody tr').forEach(tr => {
        // Si la categoría es 'todos' o coincide con la fila, la muestra; si no, la oculta
        if (categoria === 'todos' || tr.getAttribute('data-categoria') === categoria) {
            tr.style.display = '';
        } else {
            tr.style.display = 'none';
        }
    });
}

// Modal de imagen ampliada al hacer click en una imagen
document.querySelectorAll('#tablaPerfumes tbody tr td img').forEach(img => {
    img.addEventListener('click', () => {
        // Muestra el modal y coloca la imagen grande
        document.getElementById('imageModal').style.display = "flex";
        document.getElementById('imgAmpliada').src = img.src;
    });
});

// Cierra el modal al hacer click en la 'X'
document.querySelector('.close').onclick = function() {
    document.getElementById('imageModal').style.display = "none";
}

// Si se hace click fuera de la imagen (en el fondo del modal), también se cierra
window.onclick = function(event) {
    if (event.target == document.getElementById('imageModal')) {
        document.getElementById('imageModal').style.display = "none";
    }
}
</script>
</body>
</html>

