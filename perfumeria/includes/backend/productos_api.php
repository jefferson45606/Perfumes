<?php
include __DIR__ . '/../conexiones.php';// Incluir el archivo de conexión a la base de datos
header('Content-Type: application/json'); // Establecer el tipo de contenido a JSON

$conn = conectar();// Conectar a la base de datos
if ($conn->connect_error) {// Verificar si hay un error de conexión
    echo json_encode(["success" => false, "message" => "Error de conexión"]);// Enviar mensaje de error en formato JSON
    exit;// Terminar la ejecución del script
}

$sql = "SELECT * FROM productos";// Consulta SQL para obtener todos los productos
$result = $conn->query($sql);// Ejecutar la consulta SQL

$productos = [];// Inicializar un array para almacenar los productos
while ($row = $result->fetch_assoc()) { // Recorrer los resultados y almacenar cada fila en el array
    $productos[] = $row;// Agregar cada producto al array
}

echo json_encode(["success" => true, "productos" => $productos]);// Convertir el array de productos a JSON y enviarlo como respuesta
$conn->close();// Cerrar la conexión a la base de datos
?>