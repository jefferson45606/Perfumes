<?php 
session_start();
require_once ("includes/conexion.php");

// Recibir datos del formulario
$usuario = $_POST['usuario'] ?? '';
$contraseña = $_POST['contraseña'] ?? '';

if (empty($usuario) || empty($contraseña)) {
    header("Location: index.php?error=campos");
    exit();
}

if (strlen($usuario) > 50 || strlen($contraseña) > 50) {
    header("Location: index.php?error=campos");
    exit();
}


// Buscar usuario 
$sql = "SELECT * FROM administrador WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows == 1) {
    $fila = $resultado->fetch_assoc();
    
    // Verificar contraseña (usa password_hash al insertar)
    if (password_verify($contraseña, $fila['contraseña'])) {
        $_SESSION['usuario'] = $fila['usuario'];
        header("Location: panel.php");
        exit();
    }
}

// si se ejecuta esta linea de codigo es porque hubo un problema con el usuario o la contraseña
header("Location: index.php?error=credenciales");
exit();
?>
