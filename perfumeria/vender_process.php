<?php
session_start();
include 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo   = trim($_POST['codigo']);
    $tipoData = explode('|', $_POST['tipo']);
    $cantidad = (int)$_POST['cantidad'];
    $pago     = $_POST['pago'];  // ahora puede ser 'efectivo' o 'nequi'

    if (empty($codigo) || count($tipoData) !== 2 || $cantidad < 1 || empty($pago)) {
        $_SESSION['vender_msg'] = 'Datos de venta incompletos o inválidos.';
        header('Location: vender.php'); exit;
    }
    list($tipo, $mililitros) = $tipoData;

    // Obtener producto y nombre
    $stmt = $conn->prepare("SELECT id, nombre_producto FROM productos WHERE codigo_producto = ?");
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $stmt->bind_result($prodId, $prodName);
    if (!$stmt->fetch()) {
        $_SESSION['vender_msg'] = 'Código de producto no encontrado.';
        header('Location: vender.php'); exit;
    }
    $stmt->close();

    // Obtener precio unitario
    $stmt = $conn->prepare("SELECT precio FROM productos WHERE tipo = ? AND mililitros = ?");
    $stmt->bind_param('iss', $prodId, $tipo, $mililitros);
    $stmt->execute();
    $stmt->bind_result($unitPrice);
    if (!$stmt->fetch()) {
        $_SESSION['vender_msg'] = 'Precio no encontrado para ese tipo o tamaño.';
        header('Location: vender.php'); exit;
    }
    $stmt->close();

    $total = round($unitPrice * $cantidad, 2);

    // Registrar venta
    $stmt = $conn->prepare("INSERT INTO ventas (metodo_pago, fecha) VALUES (?, NOW())");
    $stmt->bind_param('s', $pago); // almacenar el método como texto
    $stmt->execute();
    $ventaId = $stmt->insert_id;
    $stmt->close();

    // Registrar detalle
    $stmt = $conn->prepare(
        "INSERT INTO detalle_venta (venta_id, producto_id, tipo, mililitros, cantidad, precio_unitario, precio_total, nombre_producto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('iissidds', $ventaId, $prodId, $tipo, $mililitros, $cantidad, $unitPrice, $total, $prodName);
    $stmt->execute();
    $stmt->close();

    // Actualizar inventario
    $stmt = $conn->prepare("UPDATE inventario SET cantidad = cantidad - ? WHERE producto_id = ?");
    $stmt->bind_param('ii', $cantidad, $prodId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['vender_msg'] = 'Venta registrada con éxito. Total: ' . number_format($total, 2);
    header('Location: vender.php');
    exit;
}
header('Location: vender.php');
exit;
?>