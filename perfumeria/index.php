<?php
include("includes/conexion.php");

$msg= '';
if(isset($_POST['registrar'])){
    $usuario=$_POST['usuario'];
    $contraseña=$_POST['password'];

    $select1="SELECT * FROM `administrador` WHERE usuario='$usuario' AND contraseña='$password'";
    $select_user=mysqli_query($conn, $select1);

    if(mysqli_num_rows($select_user) > 0){
        $row1=mysqli_fetch_assoc($select_user);
        if($row1['usuario']=='administrador'){
            $_SESSION['administrador']=$row1['usuario'];
            $_SESSION['contraseña']=$row1['contraseña'];
            header("location:vender.html");
        }
        
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Su Aroma - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>


<body>
    <div class="container">
        <div class="logo-section">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo">
        </div>

        <div class="login-section">
            <form action="" method="post"></form> <!-- metodo post -->
                <h2 class="title">BIENVENIDA</h2>
                <p class="subtitle">Inicia sesión para continuar.</p>
                
                <label for="usuario">USUARIO</label>
                <input type="text" id="usuario" name="usuario" placeholder="yamile bruno" required>
                
                <label for="password">CONTRASEÑA</label>
                <input type="password" id="password" name="password" placeholder="12345" required>

                <div class="show-password">
                    <input type="checkbox" id="togglePassword">
                    <label for="togglePassword">Mostrar contraseña</label>
                </div>
                
                <button class="btn font-weight-bold" name="registrar">INGRESAR</button>
                
                <a href="recuperar.html">¿Has olvidado tu contraseña?</a>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>
