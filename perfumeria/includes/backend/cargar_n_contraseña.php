<?php
require_once '../conexiones.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dato'])) {
    $contrasena = trim($_POST['dato']);

    if ($contrasena !== '') {
        $conn = conectar();

        // Preparar sentencia segura
        $sql = "UPDATE administrador SET contraseña = ? WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $usuario = 'yamile bruno';
        $stmt->bind_param("ss", $contrasena, $usuario);

        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Contraseña actualizada correctamente."]);
        } else {
            echo json_encode(["mensaje" => "Error al actualizar."]);
        }

        $stmt->close();
        $conn->close();
    }
}
?>