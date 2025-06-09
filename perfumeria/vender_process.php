<?php
session_start();
include 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['items'])) {
    $usuario = $_SESSION['usuario'] ?? '';
    $pago = $_POST['pago'] ?? '';
    $items = json_decode($_POST['items'], true);

    if (!$pago || !$items || !is_array($items) || count($items) == 0) {
        $_SESSION['vender_msg'] = 'Debes agregar al menos un producto y un método de pago.';
        header('Location: vender.php');
        exit;
    }

    // Buscar ID del método de pago
    $stmt = $conn->prepare("SELECT id FROM metodo_pago WHERE nombre_metodo = ?");
    $stmt->bind_param("s", $pago);
    $stmt->execute();
    $stmt->bind_result($metodo_pago_id);
    if (!$stmt->fetch()) {
        $_SESSION['vender_msg'] = 'Método de pago no válido.';
        header('Location: vender.php'); exit;
    }
    $stmt->close();

    // Registrar venta (cabecera)
    $stmt = $conn->prepare("INSERT INTO ventas (fecha, metodo_pago_id) VALUES (NOW(), ?)");
    $stmt->bind_param("i", $metodo_pago_id);
    $stmt->execute();
    $venta_id = $stmt->insert_id;
    $stmt->close();

    // Procesar productos
    foreach ($items as $item) {
        // 1. Buscar el producto por código
        $stmt = $conn->prepare("SELECT codigo_producto, nombre_producto, precio_30ml, precio_60ml, precio_100ml, recarga_30ml, recarga_60ml, recarga_100ml FROM productos WHERE codigo_producto = ?");
        $stmt->bind_param("s", $item['codigo']);
        $stmt->execute();
        $stmt->bind_result($codigo_producto, $nombre_producto, $p30, $p60, $p100, $r30, $r60, $r100);
        if (!$stmt->fetch()) continue; // Si no existe, saltar
        $stmt->close();

        // 2. Determinar precio unitario
        $tipo = $item['tipo'];
        $ml = (int)$item['ml'];
        if ($tipo === 'botella') {
            $unit = $ml == 30 ? $p30 : ($ml == 60 ? $p60 : ($ml == 100 ? $p100 : 0));
        } else {
            $unit = $ml == 30 ? $r30 : ($ml == 60 ? $r60 : ($ml == 100 ? $r100 : 0));
        }
        $cantidad = intval($item['cantidad']);
        $total = round($unit * $cantidad, 2);

        // 3. Guardar detalle
        $stmt = $conn->prepare("INSERT INTO detalle_venta (venta_id, producto_id, tipo, mililitros, cantidad, precio_unitario, precio_total, nombre_producto)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiidds", $venta_id, $codigo_producto, $tipo, $ml, $cantidad, $unit, $total, $nombre_producto);
        $stmt->execute();
        $stmt->close();

        // 4. Actualizar inventario
        $stmt = $conn->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE codigo_producto = ?");
        $stmt->bind_param("is", $cantidad, $codigo_producto);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['vender_msg'] = 'Venta registrada con éxito.';
    header('Location: vender.php');
    exit;
} else {
    $_SESSION['vender_msg'] = 'No se enviaron productos a vender.';
    header('Location: vender.php');
    exit;
}
?>