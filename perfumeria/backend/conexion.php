<?php

// conexion a la base de datos
$server="localhost";
$user="proyecto";
$pass="FICHA_2826320";
$bd="perfumeria";


$conexion= new mysqli($server,$user,$pass,$bd);

if($conexion->connect_error){
    die("No se pudo acceder a la base de datos: ".$conexion->connect_error);

}else {
    echo "Conexión exitosa";
}

// de aqui se validan los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    
} 

//$usuario=$_POST['usuario'] ?? '';
//$contraseña=$_POST['contraseña'] ?? '';


// varifica los datos del usuario
$usuario = $conexion->real_escape_string($usuario);
$contraseña = $conexion->real_escape_string($contraseña);

$sql="SELECT * FROM administrador WHERE usuario='$usuario' AND contraseña='$contraseña'";
$resultado=$conexion->query($sql);

if ($resultado && $resultado->num_rows === 1) {
    $row = $resultado->fetch_assoc();
    
    if (password_verify($password, $row['password'])) {
        header("Location: bienvenida.php");
        exit;
    } else {
        echo "Contraseña incorrecta";
    }
}

?>