<?php
session_start();
session_unset(); 
session_destroy();
header('Location: index.php'); //este es el codigo de cierre de sesion
exit;

