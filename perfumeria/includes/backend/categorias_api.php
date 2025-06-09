<?php
require_once '../conexiones.php';// Incluir el archivo de conexión a la base de datos
header('Content-Type: application/json');// Establecer el tipo de contenido a JSON

$conexion = conectar();// Conectar a la base de datos
$sql = "SELECT id, nombre_categoria FROM categorias";// Consulta SQL para obtener todas las categorías
$result = $conexion->query($sql); // Ejecutar la consulta SQL

$categorias = [];// Inicializar un array para almacenar las categorías
if ($result && $result->num_rows > 0) {// Verificar si hay resultados
    while($row = $result->fetch_assoc()){// Recorrer los resultados y almacenar cada fila en el array
        $categorias[] = $row;// Agregar cada categoría al array
    }
}

echo json_encode($categorias);// Convertir el array de categorías a JSON y enviarlo como respuesta
$conexion->close();// Cerrar la conexión a la base de datos
?>