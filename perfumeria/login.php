<?php
session_start();
include 'includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $msg = '';

    if ($usuario === '' || $password === '') {
        $msg = 'Por favor completa todos los campos.';
    } else {
        $sql = "SELECT contraseña FROM administrador WHERE usuario = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $usuario);
            $stmt->execute();
            $stmt->bind_result($hash);
            if ($stmt->fetch()) {
                // Si tus contraseñas están en texto plano:
                if ($password === $hash) {
                    $_SESSION['usuario'] = $usuario;
                    header('Location: vender.html');
                    exit;
                }
                // Si más adelante migras a hashing, usa:
                // if (password_verify($password, $hash)) { ... }
                $msg = 'Usuario o contraseña incorrectos.';
            } else {
                $msg = 'Usuario o contraseña incorrectos.';
            }
            $stmt->close();
        } else {
            error_log('Error preparando login: ' . $conn->error);
            $msg = 'Ocurrió un error. Intenta nuevamente.';
        }
    }
} else {
    // Acceso directo: redirigir al login
    header('Location: index.php');
    exit;
}

// Si hay mensaje de error, lo guardamos en sesión y volvemos
$_SESSION['login_error'] = $msg;
header('Location: index.php');
exit;
