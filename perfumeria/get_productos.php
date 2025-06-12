<?php
// get_productos.php
header('Content-Type: application/json');
require_once 'includes/conexion.php';

if (empty($_GET['codigo'])) {
    echo json_encode(['error' => 'No se proporcionó código']);
    exit;
}
$codigo = $_GET['codigo'];

$stmt = $conn->prepare(
    "SELECT nombre_producto, precio_30ml, precio_60ml, precio_100ml, 
            recarga_30ml, recarga_60ml, recarga_100ml 
        FROM productos WHERE codigo_producto = ?"
);
$stmt->bind_param('s', $codigo);
$stmt->execute();
$stmt->bind_result($nombre, $p30, $p60, $p100, $r30, $r60, $r100);

if ($stmt->fetch()) {
    echo json_encode([
        'nombre_producto' => $nombre,
        'precios' => [
            'botella' => [
                '30' => floatval($p30),
                '60' => floatval($p60),
                '100' => floatval($p100)
            ],
            'recarga' => [
                '30' => floatval($r30),
                '60' => floatval($r60),
                '100' => floatval($r100)
            ]
        ]
    ]);
} else {
    echo json_encode(['error' => 'Producto no encontrado']);
}
$stmt->close();
$conn->close();
?>
