<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include __DIR__ . '/../conexiones.php';
header('Content-Type: application/json');

$conn = conectar();

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Recibe los datos
$codigo_producto = $_POST['Código'] ?? '';
$nombre_producto = $_POST['Nombre'] ?? '';
$inspiracion = $_POST['Inspiración'] ?? '';
$casa = $_POST['Casa'] ?? '';
$descripcion = $_POST['Descripción'] ?? '';
$cantidad = isset($_POST['Cantidad']) ? intval($_POST['Cantidad']) : 0;
$precio = isset($_POST['Precio']) ? floatval($_POST['Precio']) : 0;
$precio_30ml = isset($_POST['precio_30ml']) ? floatval($_POST['precio_30ml']) : 0;
$precio_60ml = isset($_POST['precio_60ml']) ? floatval($_POST['precio_60ml']) : 0;
$precio_100ml = isset($_POST['precio_100ml']) ? floatval($_POST['precio_100ml']) : 0;
$recarga_30ml = isset($_POST['recarga_30ml']) ? floatval($_POST['recarga_30ml']) : 0;
$recarga_60ml = isset($_POST['recarga_60ml']) ? floatval($_POST['recarga_60ml']) : 0;
$recarga_100ml = isset($_POST['recarga_100ml']) ? floatval($_POST['recarga_100ml']) : 0;
$categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;

$imagen_url = null;
if (!empty($_FILES['imagen']['name'])) {
    $imagen_nombre = basename($_FILES['imagen']['name']);
    $imagen_tmp = $_FILES['imagen']['tmp_name'];
    $directorio = 'uploads/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }
    $ruta_destino = $directorio . uniqid() . "_" . $imagen_nombre;
    if (move_uploaded_file($imagen_tmp, $ruta_destino)) {
        $imagen_url = $ruta_destino;
    }
} elseif (!empty($_POST['ImagenExistente'])) {
    $imagen_url = $_POST['ImagenExistente'];
}

// Verificar si el producto ya existe
$sql_verificar = "SELECT * FROM productos WHERE codigo_producto = ?";
$stmt = $conn->prepare($sql_verificar);
$stmt->bind_param("s", $codigo_producto);
$stmt->execute();
$resultado = $stmt->get_result(); 

if ($resultado->num_rows > 0) {
    // UPDATE
    $sql = "UPDATE productos SET 
        imagen_url = ?, nombre_producto = ?, inspiracion = ?, casa = ?, descripcion = ?, cantidad = ?, 
        precio = ?, 
        precio_30ml = ?, precio_60ml = ?, precio_100ml = ?, 
        recarga_30ml = ?, recarga_60ml = ?, recarga_100ml = ?, categoria_id = ?
        WHERE codigo_producto = ?";
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param(
        "sssssiddddddisi",
        $imagen_url, $nombre_producto, $inspiracion, $casa, $descripcion, $cantidad,
        $precio,
        $precio_30ml, $precio_60ml, $precio_100ml,
        $recarga_30ml, $recarga_60ml, $recarga_100ml,
        $categoria_id, $codigo_producto
    );
} else {
    // INSERT
    $sql = "INSERT INTO productos (
        codigo_producto, imagen_url, nombre_producto, inspiracion, casa, descripcion, cantidad,
        precio, precio_30ml, precio_60ml, precio_100ml, recarga_30ml, recarga_60ml, recarga_100ml, categoria_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssidddddddi",
        $codigo_producto, $imagen_url, $nombre_producto, $inspiracion, $casa, $descripcion, $cantidad,
        $precio, $precio_30ml, $precio_60ml, $precio_100ml,
        $recarga_30ml, $recarga_60ml, $recarga_100ml,
        $categoria_id
    );
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "imagen" => $imagen_url]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}
$stmt->close();
$conn->close();

file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);
?>
