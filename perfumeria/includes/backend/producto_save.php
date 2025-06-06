<?php
// producto_save.php

header('Content-Type: application/json');

// Obtener el cuerpo JSON
$data = json_decode(file_get_contents('php://input'), true);

// Puedes guardar en base de datos, archivo, etc.
file_put_contents('productos_guardados.json', json_encode($data, JSON_PRETTY_PRINT));

// Respuesta de éxito
echo json_encode(['status' => 'ok', 'mensaje' => 'Producto guardado correctamente']);

echo json_decode($data);
