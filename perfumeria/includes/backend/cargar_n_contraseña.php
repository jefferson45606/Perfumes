<?php
require_once '../conexiones.php';// Incluir el archivo de conexión a la base de datos
header('Content-Type: application/json');// Establecer el tipo de contenido a JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dato'])) {// Verificar que la solicitud es POST y que se ha enviado el dato
    $contrasena = trim($_POST['dato']);// Obtener la contraseña del POST y eliminar espacios en blanco

    if ($contrasena !== '') { // Verificar que la contraseña no esté vacía
        $conn = conectar();// Conectar a la base de datos

        // Preparar sentencia segura
        $sql = "UPDATE administrador SET contraseña = ? WHERE usuario = ?";// Actualizar la contraseña del usuario específico
        $stmt = $conn->prepare($sql);// Preparar la sentencia SQL para evitar inyecciones SQL
        $usuario = 'yamile bruno';// Usuario específico para actualizar la contraseña
        $stmt->bind_param("ss", $contrasena, $usuario);// Vincular los parámetros a la sentencia preparada

        if ($stmt->execute()) {// Ejecutar la sentencia preparada
            echo json_encode(["mensaje" => "Contraseña actualizada correctamente."]);// Si la ejecución es exitosa, enviar mensaje de éxito
        } else {// Si hay un error al ejecutar la sentencia
            echo json_encode(["mensaje" => "Error al actualizar."]);// Enviar mensaje de error
        }

        $stmt->close();// Cerrar la sentencia preparada
        $conn->close();// Cerrar la conexión a la base de datos
    }
}
?>