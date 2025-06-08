<?php
require_once '../conexiones.php'; // ajusta si está en otra ubicación

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dato'])) {
    $categoria = trim($_POST['dato']);

    if ($categoria !== '') {
        $conn = conectar(); 

        $stmt = $conn->prepare("INSERT INTO categorias (nombre_categoria) VALUES (?)");
        $stmt->bind_param("s", $categoria);
        if ($stmt->execute()) {
            $id = $conn->insert_id; 
            echo json_encode([
                'success' => true,
                'categoria' => [
                    'id' => $id,
                    'nombre_categoria' => $categoria
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar la categoría.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Nombre de categoría vacío.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida.']);
}
?>