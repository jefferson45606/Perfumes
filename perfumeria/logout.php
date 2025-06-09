<?php
session_start(); // Iniciamos la sesión para poder usar variables de sesión
session_unset(); // Limpiamos todas las variables de sesión
session_destroy(); // Destruimos la sesión para cerrar la sesión del usuario
header('Location: index.php'); //este es el codigo de cierre de sesion
exit;

