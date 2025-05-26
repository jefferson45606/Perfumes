<?php
// conexion a la base de datos
$server="localhost";
$user="proyecto";
$pass="FICHA_2826320";
$bd="perfumeria";

$conexion= new mysqli($server,$user,$pass,$bd);

if($conexion->connect_error){
    die("error de conexion ".$conexion->connect_error);
}else {
    echo "Conexión exitosa <br>";
}

// de aqui se validan los datos de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    

    // varifica los datos del usuario
    if (empty($usuario) || empty($contraseña)) {
        die(" Usuario y contraseña son requeridos.");
    }

    $stmt = $conexion->prepare("SELECT * FROM administrador WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows === 1) {
        $row = $resultado->fetch_assoc();
        
        if (password_verify($contraseña, $row['contraseña'])) {
            header("Location: login.html");
            exit;
        } else {
            echo "Contraseña incorrecta";
        }

    } else {
        echo "Usuario no encontrado";
    }

    $stmt->close();
}
?>