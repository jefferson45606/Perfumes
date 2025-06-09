<?php
include 'includes/conexion.php';

// 1. Obtener las categorías para los filtros
$cats = [];
$result = $conn->query("SELECT nombre_categoria FROM categorias");
while ($row = $result->fetch_assoc()) {
    $cats[] = $row['nombre_categoria'];
}

// 2. Obtener los productos con la categoría asociada
$sql = "SELECT p.codigo_producto, p.nombre_producto, p.cantidad, p.inspiracion, p.casa, p.precio, c.nombre_categoria
        FROM productos p
        JOIN categorias c ON p.categoria_id = c.id";
$result = $conn->query($sql);
$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
?>
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
    <aside class="logo-section">
        <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" />
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
        <h2 class="title">Inventario</h2>
        <div class="filter-buttons">
            <?php foreach ($cats as $cat): ?>
                <button onclick="filtrar('<?= htmlspecialchars($cat) ?>')">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
            <button onclick="filtrar('todos')">Mostrar Todo</button>
        </div>

        <div class="table-container">
            <table id="tablaPerfumes">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Inspiración</th>
                        <th>Casa</th>
                        <th>Precio base</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr data-categoria="<?= htmlspecialchars($prod['nombre_categoria']) ?>">
                            <td><?= htmlspecialchars($prod['codigo_producto']) ?></td>
                            <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                            <td><?= htmlspecialchars($prod['cantidad']) ?></td>
                            <td><?= htmlspecialchars($prod['inspiracion']) ?></td>
                            <td><?= htmlspecialchars($prod['casa']) ?></td>
                            <td>$<?= number_format($prod['precio'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a class="logout" href="logout.php">Cerrar sesión</a>
    </main>
</div>
<script>
// Filtrar por categoría
function filtrar(categoria) {
    document.querySelectorAll('#tablaPerfumes tbody tr').forEach(tr => {
        if (categoria === 'todos' || tr.getAttribute('data-categoria') === categoria) {
            tr.style.display = '';
        } else {
            tr.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
