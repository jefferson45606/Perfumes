<?php
require_once '../conexiones.php'; // ajusta si está en otra ubicación

header('Content-Type: application/json');// Establecer el tipo de contenido a JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dato'])) {// Verificar que la solicitud es POST y que se ha enviado el dato
    $categoria = trim($_POST['dato']);// Obtener la categoría del POST y eliminar espacios en blanco

    if ($categoria !== '') {// Verificar que la categoría no esté vacía
        $conn = conectar(); // Conectar a la base de datos

        $stmt = $conn->prepare("INSERT INTO categorias (nombre_categoria) VALUES (?)");// Preparar la sentencia SQL para evitar inyecciones SQL
        $stmt->bind_param("s", $categoria);// Vincular los parámetros a la sentencia preparada
        if ($stmt->execute()) {// Ejecutar la sentencia preparada
            $id = $conn->insert_id; // Obtener el ID de la última inserción
            echo json_encode([//    Enviar respuesta JSON con éxito
                'success' => true,// Indicar que la operación fue exitosa
                'categoria' => [// Incluir los datos de la categoría recién creada
                    'id' => $id,// ID de la categoría recién creada
                    'nombre_categoria' => $categoria// Nombre de la categoría recién creada
                ]
            ]);
        } else {// Si hay un error al ejecutar la sentencia
            echo json_encode(['success' => false, 'message' => 'Error al guardar la categoría.']);// Enviar mensaje de error
        }

        $stmt->close();// Cerrar la sentencia preparada
        $conn->close();//   Cerrar la conexión a la base de datos
    } else {
        echo json_encode(['success' => false, 'message' => 'Nombre de categoría vacío.']);// Enviar mensaje de error si la categoría está vacía
    }
} else {// Si la solicitud no es POST o no se envió el dato
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida.']);// Enviar mensaje de error indicando que la solicitud no es válida
}
?>