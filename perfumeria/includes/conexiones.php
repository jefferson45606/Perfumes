<?php
function conectar(){ // Función para conectar a la base de datos
    // conexion a la base de datos
    $server="localhost";// Servidor de la base de datos
    $user="proyecto"; // Usuario de la base de datos
    $pass="FICHA_2826320";// Contraseña del usuario de la base de datos
    $bd="perfumeria";// Nombre de la base de datos

    $conexion= new mysqli($server,$user,$pass,$bd);// Crear una nueva conexión a la base de datos MySQLi

    if($conexion->connect_error){// Verificar si hay un error de conexión
        die("error de conexion ".$conexion->connect_error);// Terminar la ejecución del script y mostrar el error de conexión
    }
    return $conexion;// Retornar la conexión a la base de datos
}
?>