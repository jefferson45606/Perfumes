<?php
session_start();
include 'includes/conexion.php';

// Cargar categorías para los filtros
$categorias = [];
$res = $conn->query("SELECT nombre_categoria FROM categorias");
while ($row = $res->fetch_assoc()) $categorias[] = $row['nombre_categoria'];

// Leer filtros desde GET
$categoria = $_GET['categoria'] ?? 'todos';
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';

// Construir la consulta SQL según los filtros
$where = "WHERE 1";
$params = [];
$types = "";

if ($categoria !== 'todos') {
    $where .= " AND c.nombre_categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}
if ($desde && $hasta) {
    $where .= " AND v.fecha BETWEEN ? AND ?";
    $params[] = $desde . " 00:00:00";
    $params[] = $hasta . " 23:59:59";
    $types .= "ss";
}

$sql = "SELECT 
    v.fecha, mp.nombre_metodo AS metodo_pago, 
    c.nombre_categoria, p.codigo_producto, p.nombre_producto, 
    dv.cantidad, dv.tipo, dv.mililitros, dv.precio_unitario, dv.precio_total
FROM ventas v
JOIN metodo_pago mp ON v.metodo_pago_id = mp.id
JOIN detalle_venta dv ON v.id = dv.venta_id
JOIN productos p ON dv.producto_id = p.codigo_producto
JOIN categorias c ON p.categoria_id = c.id
$where
ORDER BY v.fecha DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$ventas = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Informe de Ventas</title>
  <link rel="stylesheet" href="CSS/informe.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <style>
    /* Ajusta el diseño aquí si quieres cambios extra */
    .table-container {
        max-width: 100%;
        overflow-x: auto;
        margin-bottom: 16px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        background: #fff;
    }
    th, td {
        border: 1px solid #eee;
        padding: 7px 9px;
        text-align: center;
    }
    th {
        background: #f4b6c2;
        color: #333;
        font-weight: bold;
    }
    tr:nth-child(even) { background: #f7f7f7;}
    .resumen {
        background: #e6dbdb;
        padding: 8px 16px;
        margin-bottom: 15px;
        border-radius: 8px;
        font-size: 16px;
    }
    label { font-size: 15px;}
    select, input[type="date"] {
        padding: 4px 8px;
        margin-right: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    button { padding: 6px 15px; margin-right: 5px;}
  </style>
</head>
<body>
<div class="container">
    <aside class="logo-section">
      <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" />
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
        <h2 class="title">Informe de Ventas</h2>
        <!-- Filtros -->
        <form method="get" style="margin-bottom: 20px;">
            <label for="categoria">Categoría:</label>
            <select name="categoria" id="categoria">
                <option value="todos">Mostrar Todo</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= htmlspecialchars($cat) ?>" <?= $cat==$categoria?'selected':'' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
            <label for="desde">Desde:</label>
            <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>">
            <label for="hasta">Hasta:</label>
            <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
            <button type="submit">Filtrar</button>
            <button type="button" onclick="descargarPDF()">Descargar PDF</button>
        </form>
        <!-- Resumen -->
        <?php 
        $totalProductos = 0; $totalVentas = 0;
        foreach ($ventas as $v) {
            $totalProductos += $v['cantidad'];
            $totalVentas += $v['precio_total'];
        }
        ?>
        <div class="resumen">
            <strong>Total productos vendidos:</strong> <?= $totalProductos ?> &nbsp; | &nbsp;
            <strong>Total de ventas:</strong> $<?= number_format($totalVentas, 0, ',', '.') ?>
        </div>
        <!-- Tabla de ventas -->
        <div class="table-container">
            <table id="tablaInforme">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Tamaño (ml)</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Total</th>
                        <th>Método de pago</th>
                        <th>Categoría</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($v['fecha']))) ?></td>
                        <td><?= htmlspecialchars($v['codigo_producto']) ?></td>
                        <td><?= htmlspecialchars($v['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars(strtoupper($v['tipo'])) ?></td>
                        <td><?= htmlspecialchars($v['mililitros']) ?></td>
                        <td><?= intval($v['cantidad']) ?></td>
                        <td>$<?= number_format($v['precio_unitario'], 0, ',', '.') ?></td>
                        <td>$<?= number_format($v['precio_total'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($v['metodo_pago']) ?></td>
                        <td><?= htmlspecialchars($v['nombre_categoria']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a class="logout" href="logout.php">Cerrar sesión</a>
    </main>
</div>
<script>
function descargarPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({orientation: "landscape"});
  doc.text("Informe de Ventas", 10, 10);
  doc.autoTable({ html: '#tablaInforme', startY: 20 });
  doc.save("informe_ventas.pdf");
}
</script>
</body>
</html>
