<?php
require_once '../conexiones.php'; // ajusta si está en otra ubicación

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dato'])) {
    $categoria = trim($_POST['dato']);

    if ($categoria !== '') {
        $conn = conectar();

        // Verifica si ya existe
        $stmt = $conn->prepare("SELECT id FROM categorias WHERE nombre_categoria = ?");
        $stmt->bind_param("s", $categoria);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'La categoría ya existe.']);
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO categorias (nombre_categoria) VALUES (?)");
            $stmt->bind_param("s", $categoria);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Categoría guardada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al insertar en la base de datos.']);
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'La categoría no puede estar vacía.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida.']);
}
?>