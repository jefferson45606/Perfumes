<?php
ini_set('display_errors', 1);// Mostrar errores en pantalla
ini_set('display_startup_errors', 1);// Mostrar errores de inicio
error_reporting(E_ALL);// Reportar todos los errores
include __DIR__ . '/../conexiones.php';// Incluir el archivo de conexión a la base de datos
header('Content-Type: application/json');// Establecer el tipo de contenido a JSON

$conn = conectar();// Conectar a la base de datos

if ($conn->connect_error) {// Verificar si hay un error de conexión
    echo json_encode(["success" => false, "message" => "Error de conexión: " . $conn->connect_error]);// Enviar mensaje de error en formato JSON
    exit;// Terminar la ejecución del script
}

// Recibe los datos
$codigo_producto = $_POST['Código'] ?? '';// Obtener el código del producto del POST, si no existe, asignar una cadena vacía
$nombre_producto = $_POST['Nombre'] ?? '';// Obtener el nombre del producto del POST, si no existe, asignar una cadena vacía
$inspiracion = $_POST['Inspiración'] ?? '';// Obtener la inspiración del producto del POST, si no existe, asignar una cadena vacía
$casa = $_POST['Casa'] ?? '';// Obtener la casa del producto del POST, si no existe, asignar una cadena vacía
$descripcion = $_POST['Descripción'] ?? '';// Obtener la descripción del producto del POST, si no existe, asignar una cadena vacía
$cantidad = isset($_POST['Cantidad']) ? intval($_POST['Cantidad']) : 0;// Obtener la cantidad del producto del POST, si no existe, asignar 0
$precio = isset($_POST['Precio']) ? floatval($_POST['Precio']) : 0;// Obtener el precio del producto del POST, si no existe, asignar 0
$precio_30ml = isset($_POST['precio_30ml']) ? floatval($_POST['precio_30ml']) : 0;// Obtener el precio de 30ml del POST, si no existe, asignar 0
$precio_60ml = isset($_POST['precio_60ml']) ? floatval($_POST['precio_60ml']) : 0;//    Obtener el precio de 60ml del POST, si no existe, asignar 0
$precio_100ml = isset($_POST['precio_100ml']) ? floatval($_POST['precio_100ml']) : 0;// Obtener el precio de 100ml del POST, si no existe, asignar 0
$recarga_30ml = isset($_POST['recarga_30ml']) ? floatval($_POST['recarga_30ml']) : 0;// Obtener el precio de recarga de 30ml del POST, si no existe, asignar 0
$recarga_60ml = isset($_POST['recarga_60ml']) ? floatval($_POST['recarga_60ml']) : 0;// Obtener el precio de recarga de 60ml del POST, si no existe, asignar 0
$recarga_100ml = isset($_POST['recarga_100ml']) ? floatval($_POST['recarga_100ml']) : 0;// Obtener el precio de recarga de 100ml del POST, si no existe, asignar 0
$categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;// Obtener el ID de la categoría del POST, si no existe, asignar null

$imagen_url = null;// Inicializar la variable de la URL de la imagen
if (!empty($_FILES['imagen']['name'])) {// Verificar si se ha subido una imagen
    $imagen_nombre = basename($_FILES['imagen']['name']);// Obtener el nombre del archivo de la imagen
    $imagen_tmp = $_FILES['imagen']['tmp_name'];// Obtener la ruta temporal del archivo de la imagen
    $directorio = 'uploads/';// Definir el directorio donde se guardarán las imágenes
    if (!is_dir($directorio)) {// Verificar si el directorio existe
        mkdir($directorio, 0777, true);// Si no existe, crear el directorio con permisos 0777
    }
    $ruta_destino = $directorio . uniqid() . "_" . $imagen_nombre;// Definir la ruta de destino para la imagen, usando uniqid para evitar colisiones de nombres
    if (move_uploaded_file($imagen_tmp, $ruta_destino)) {// Mover el archivo subido a la ruta de destino
        $imagen_url = $ruta_destino;// Asignar la ruta de destino a la variable de imagen
    }
} elseif (!empty($_POST['ImagenExistente'])) {// Verificar si se ha enviado una imagen existente
    $imagen_url = $_POST['ImagenExistente'];// Si se ha enviado una imagen existente, asignarla a la variable de imagen
}

// Verificar si el producto ya existe
$sql_verificar = "SELECT * FROM productos WHERE codigo_producto = ?";// Consulta SQL para verificar si el producto ya existe
$stmt = $conn->prepare($sql_verificar);// Preparar la sentencia SQL para evitar inyecciones SQL
$stmt->bind_param("s", $codigo_producto);// Vincular el parámetro de la sentencia preparada
$stmt->execute();// Ejecutar la sentencia preparada
$resultado = $stmt->get_result(); // Obtener el resultado de la ejecución de la sentencia

if ($resultado->num_rows > 0) {// Si el producto ya existe
    // UPDATE
    $sql = "UPDATE productos SET 
        imagen_url = ?, nombre_producto = ?, inspiracion = ?, casa = ?, descripcion = ?, cantidad = ?, 
        precio = ?, 
        precio_30ml = ?, precio_60ml = ?, precio_100ml = ?, 
        recarga_30ml = ?, recarga_60ml = ?, recarga_100ml = ?, categoria_id = ?
        WHERE codigo_producto = ?";
    $stmt = $conn->prepare($sql); // Preparar la sentencia SQL para actualizar el producto
    $stmt->bind_param(
        "sssssiddddddisi",
        $imagen_url, $nombre_producto, $inspiracion, $casa, $descripcion, $cantidad,
        $precio,
        $precio_30ml, $precio_60ml, $precio_100ml,
        $recarga_30ml, $recarga_60ml, $recarga_100ml,
        $categoria_id, $codigo_producto
    );
} else {// Si el producto no existe, se procede a insertarlo
    // INSERT
    $sql = "INSERT INTO productos (
        codigo_producto, imagen_url, nombre_producto, inspiracion, casa, descripcion, cantidad,
        precio, precio_30ml, precio_60ml, precio_100ml, recarga_30ml, recarga_60ml, recarga_100ml, categoria_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);// Preparar la sentencia SQL para insertar un nuevo producto
    $stmt->bind_param(
        "ssssssidddddddi",
        $codigo_producto, $imagen_url, $nombre_producto, $inspiracion, $casa, $descripcion, $cantidad,
        $precio, $precio_30ml, $precio_60ml, $precio_100ml,
        $recarga_30ml, $recarga_60ml, $recarga_100ml,
        $categoria_id
    );
}

if ($stmt->execute()) {// Ejecutar la sentencia preparada (ya sea UPDATE o INSERT)
    echo json_encode(["success" => true, "imagen" => $imagen_url]);// Enviar respuesta JSON indicando éxito y la URL de la imagen
} else {// Si hay un error al ejecutar la sentencia
    echo json_encode(["success" => false, "message" => $stmt->error]);// Enviar mensaje de error en formato JSON
}
$stmt->close();// Cerrar la sentencia preparada
$conn->close();// Cerrar la conexión a la base de datos

file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);// Guardar los datos recibidos en un archivo de depuración para análisis posterior
?>
