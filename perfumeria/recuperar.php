<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="recuperar.css">
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo">
        </div>
        <div class="login-section">
            <h2 class="title">RECUPERAR<br>CONTRASEÑA</h2>
            <form onsubmit="verificador(event)">
                <label for="codigo">CÓDIGO DE RECUPERACIÓN</label>
                <input type="text" id="codigo" placeholder="1SE55N4">
                
                <label for="password">NUEVA CONTRASEÑA</label>
                <input type="password" id="password" placeholder="******">
                
                <button type="submit">Guardar</button>
            </form>
        </div>
    </div>

    <script>
    function verificador(event) {
        event.preventDefault(); // Evita que se envíe el formulario

        const codigo = document.getElementById('codigo').value;
        const contraseña = document.getElementById('password').value;

        const encoder = new TextEncoder();
        const data = encoder.encode(codigo);

        crypto.subtle.digest("SHA-256", data).then(hashBuffer => {
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

            if (hashHex === "cc7980b11a556d770cc02bf632b7b8daac4ac1657d14ecfbc9fdc216994848d5") {
                if (contraseña !== "") {
                    fetch('includes/backend/cargar_n_contraseña.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'dato=' + encodeURIComponent(contraseña)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.mensaje === "Contraseña actualizada correctamente.") {
                            window.location.href = "login.php";
                        } else {
                            alert("Error del servidor: " + data.mensaje); 
                        }
                    })
                    .catch(error => {
                        console.error('Error en fetch:', error);
                        alert("Ocurrió un error al procesar la solicitud.");
                    });
                } else {
                    alert("Debe ingresar una contraseña.");
                }
            } else {
                alert("El código de recuperación es incorrecto.");
            }
        });
    }
    </script>
</body>
</html>