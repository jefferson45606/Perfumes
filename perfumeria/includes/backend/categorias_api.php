<?php
function cargar_categoria(){
    $nombre = [];
    require_once '../conexiones.php';
    $conexion = conectar();
    $sql = "SELECT nombre_categoria FROM categorias";
    $result = $conexion->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $nombre[] = $row;
        }
    }
    return $nombre;
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
echo json_encode(cargar_categoria());
?>