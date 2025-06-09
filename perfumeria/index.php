<?php
session_start(); //iniciamos la sesión para poder usar variables de sesión (como para mensajes de error)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Su Aroma - Login</title>
    <link rel="stylesheet" href="CSS/styles.css"> <!--ENLAZAMOS EL CSS-->
</head>
<body>
    <div class="container">
        <div class="logo-section"> <!--sesion donde aparece el logo -->
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo"> 
        </div>

        <div class="login-section">
            <h2 class="title">BIENVENIDA</h2>
            <p class="subtitle">Inicia sesión para continuar.</p>

            <?php if (!empty($_SESSION['login_error'])): ?><!--lo que hace la variable $_session es mostrar mensajes o
                                                            mantener el usuario logueado-->
                <!--si hay un mensaje de error de login guardado en la sesión, lo mostramos aquí -->
                <div class="error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
                <!--aqui eliminamos el mensaje para que no vuelva a salir al recargar -->
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <!--este es el formulario de inicio de sesión, envía los datos por POST a "login.php" -->
            <form action="login.php" method="post"><!--elo que hace el metodo post es que los datos del formulario "viajan"
                                                    ocultos, no se muestran en la url-->
                <label for="usuario">USUARIO</label>
                <input type="text" id="usuario" name="usuario" ><!--1)nombre del parámetro que se enviará al POST-->


                <label for="password">CONTRASEÑA</label><!--como sabrán los label son los campos-->
                <input type="password" id="password" name="password" ><!--1)lo mismo con este-->

                <!--este el el apartado para mostrar la contraseña-->
                <label>
                    <input type="checkbox" id="togglePassword"><!--esta id la pasamos al script-->
                    Mostrar contraseña
                </label>

                <!--boton para enviar el formulario -->
                <button type="submit" name="login">Entrar</button>

                <!--y este es el enlace para recuperar la contraseña-->
                <a href="recuperar.php">¿Has olvidado tu contraseña?</a>
            </form>
        </div>
    </div>

    <script>
        //este script loo que hace es mostrar u ocultar la contraseña cuando marcas el checkbox
        //esto decalra variables, el "const", se usa cuando quieres crear una variable cuyo valor NO va a cambiar
        const togglePassword = document.getElementById('togglePassword'); //busca en el html el elemento que tenga el atributo id="togglePassword".
        const passwordInput = document.getElementById('password');//lo mismo este pero con password
        //el "addEventListener" es una función en que sirve para decirle a un elemento del HTML:
        //"Oye, cuando pase esto (por ejemplo, un clic, un cambio, pasar el mouse, etc.), ejecuta esta función.”
        togglePassword.addEventListener('change', function () {
            //esto cambia el tipo del input para que se vea la contraseña o se oculte
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
</body>
</html>

