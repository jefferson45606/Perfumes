<?php
header('Content-Type: application/json'); //elimina la cabecera de tipo de contenido JSON, osea el recuadro del producto
require_once '../conexiones.php';
$conn = conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_producto'])) {
    $codigo = $_POST['codigo_producto'];
    // Primero puedes borrar de detalle_venta si tienes FK
    // $conn->query("DELETE FROM detalle_venta WHERE producto_id = (SELECT id FROM productos WHERE codigo_producto=?)");
    $stmt = $conn->prepare("DELETE FROM productos WHERE codigo_producto = ?");
    $stmt->bind_param('s', $codigo);
    $ok = $stmt->execute();
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo eliminar']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
}
$conn->close();
?>
