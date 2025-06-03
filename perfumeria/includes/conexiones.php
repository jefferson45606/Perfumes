<?php
function conectar(){
    // conexion a la base de datos
    $server="localhost";
    $user="proyecto";
    $pass="FICHA_2826320";
    $bd="perfumeria";

    $conexion= new mysqli($server,$user,$pass,$bd);

    if($conexion->connect_error){
        die("error de conexion ".$conexion->connect_error);
    }
    return $conexion;
}
?>