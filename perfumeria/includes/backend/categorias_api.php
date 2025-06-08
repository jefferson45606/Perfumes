<?php
require_once '../conexiones.php';
header('Content-Type: application/json');

$conexion = conectar();
$sql = "SELECT id, nombre_categoria FROM categorias";
$result = $conexion->query($sql);

$categorias = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        $categorias[] = $row;
    }
}

echo json_encode($categorias);
$conexion->close();
?>