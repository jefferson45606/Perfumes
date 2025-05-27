<?php
// conexion a la base de datos
$server="localhost";
$user="proyecto";
$pass="FICHA_2826320";
$bd="perfumeria";

$conn= new mysqli($server,$user,$pass,$bd);

if($conn->connect_error){
    die("error de conexion ".$conn->connect_error);
}
?> 