<?php
#session_start(); 
// en pocas palabras, este script conecta a la base de datos de la perfumería   
$server="localhost";
$user="proyecto";
$pass="FICHA_2826320";
$bd="perfumeria";

$conn= new mysqli($server,$user,$pass,$bd);

if($conn->connect_error){
    error_log("MySQL connection error: " . $conn->connect_error);
    die("Lo sentimos, no se pudo conectar al servidor.");
} 
?>