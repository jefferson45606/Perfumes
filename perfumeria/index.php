<?php
session_start();
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
            <h2 class="title">BIENVENIDA</h2>
            <p class="subtitle">Inicia sesión para continuar.</p>

            <?php if (!empty($_SESSION['login_error'])): ?>
                <div class="error"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form action="login.php" method="post">
                <label for="usuario">USUARIO</label>
                <input type="text" id="usuario" name="usuario" required>

                <label for="password">CONTRASEÑA</label>
                <input type="password" id="password" name="password" required>

                <label>
                    <input type="checkbox" id="togglePassword">
                    Mostrar contraseña
                </label>

                <button type="submit" name="login">Entrar</button>

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

