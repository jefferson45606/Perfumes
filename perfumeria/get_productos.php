<?php
// Endpoint para obtener nombre y precios de un producto según su código
header('Content-Type: application/json; charset=utf-8');
include 'includes/conexion.php';

if (!isset($_GET['codigo']) || empty($_GET['codigo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta código']);
    exit;
}

$codigo = $_GET['codigo'];
$stmt = $conn->prepare(
    "SELECT nombre_producto, precio_30ml, precio_60ml, precio_100ml, recarga_30ml, recarga_60ml, recarga_100ml FROM productos WHERE codigo_producto = ?" );
$stmt->bind_param('s', $codigo);
$stmt->execute();
$stmt->bind_result($nombre,$p30, $p60, $p100, $r30, $r60, $r100);

if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'No encontrado']);
    exit;
}
$stmt->close();

echo json_encode([
    'nombre_producto' => $nombre,
    'precios' => [
        'botella' => [30 => (float)$p30, 60 => (float)$p60, 100 => (float)$p100],
        'recarga' => [30 => (float)$r30, 60 => (float)$r60, 100 => (float)$r100]
    ]
]);