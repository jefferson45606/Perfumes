<?php
//niciamos sesion y conectamos a la base de datos
session_start();
include 'includes/conexion.php';

//con esto se ve que los datos vengan del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { //el metodo post sirve para evitar que alguien entre directamente al script sin haber enviado el formulario de login
    header('Location: index.php');
    exit;
}

//aqui se obtienen los datos enviados por el formulario
$usuario  = trim($_POST['usuario'] ?? ''); //el "trim" es para que no se incluya espacios en blanco
$password = trim($_POST['password'] ?? ''); //el "??" es para que se asigne una cadena vacia
$msg = ''; //es para almacenar mensajes de error o exito


// si hace falta alguno de los campos, devolvemos un error
if ($usuario === '' || $password === '') { //por si se deja un campo vacio
    $msg = 'Por favor completa todos los campos.';
} else {
    // preparamos la consulta para buscar la contraseña del usuario en la BD
    $sql = "SELECT contraseña FROM administrador WHERE usuario = ?"; //<- parametro de consulta, el "?" se reemplazará por el valor de $usuario
    if ($stmt = $conn->prepare($sql)) { //si el "prepare()" falla, dejamos el mensaje de error
        $stmt->bind_param('s', $usuario); //la "s" indica que el valor es un string
        $stmt->execute(); //ejecuta la consulta en la base de datos
        $stmt->bind_result($hash); //la variable "$hash" contendrá la contraseña de la base de datos
        
        if ($stmt->fetch()) { //si "fetch" devuelve true, significa que se encontró el usuario, en ese caso en "$hash" estará la contraseña guardada
            if ($password === $hash) { //NOTA: aquí se compara en texto plano, osea que la contraseña puesta por el usuario debe coincidir con la que está en la BD
                // autenticación exitosa
                $_SESSION['usuario'] = $usuario; //se guarda en la sesion para recordar que ese usuario ya se logueo
                $stmt->close();
                header('Location: vender.php'); //una vez autenticado se redirige a la página de vender
                exit;
            } else {
                //aqui si el usuario no existe en la tabla o la contraseña no coincide se muestra el mismo mensaje
                $msg = 'Usuario o contraseña incorrectos.'; 
            }
        } else {
            $msg = 'Usuario o contraseña incorrectos.';
        }

        $stmt->close();
    } else {
        // esta linea es por si fallo la consulta a la BD
        error_log('Error preparando login: ' . $conn->error);
        $msg = 'Ocurrió un error al intentar iniciar sesión. Intenta nuevamente.';
    }
}

// si esto se ejecuta es porque algo salió mal, guardamos el mensaje y redirigimos al login
$_SESSION['login_error'] = $msg;
header('Location: index.php');
exit; //detener el script