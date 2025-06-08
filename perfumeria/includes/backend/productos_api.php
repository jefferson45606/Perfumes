<?php
include __DIR__ . '/../conexiones.php';
header('Content-Type: application/json');

$conn = conectar();
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit;
}

$sql = "SELECT * FROM productos";
$result = $conn->query($sql);

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode(["success" => true, "productos" => $productos]);
$conn->close();
?>