<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

include 'includes/conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vender - Su Aroma</title>
    <link rel="stylesheet" href="CSS/vender.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo" />
            <nav class="menu-buttons">
                <ul>
                    <li><a href="vender.php" class="menu-buttons active">VENDER</a></li>
                    <li><a href="catalogo.php" class="menu-buttons">CATÁLOGO</a></li>
                    <li><a href="inventario.php" class="menu-buttons">INVENTARIO</a></li>
                    <li><a href="crear.php" class="menu-buttons">CREAR</a></li>
                    <li><a href="informe.php" class="menu-buttons">INFORMES</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <?php if (isset($_SESSION['vender_msg']) && $_SESSION['vender_msg'] !== ''): ?>
                <div class="message"><?php echo htmlspecialchars($_SESSION['vender_msg']); unset($_SESSION['vender_msg']); ?></div>
            <?php endif; ?>

            <form id="sellForm" action="vender_process.php" method="post" class="form-grid">
                <!-- Código del producto -->
                <div class="form-group">
                    <label for="codigo">CÓDIGO</label>
                    <input type="text" id="codigo" name="codigo" required />
                </div>

                <!-- Tipo de producto y tamaño -->
                <div class="form-group">
                    <label for="tipo">Tipo de producto</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">-- Selecciona --</option>
                        <option value="botella|10000">Precio.</option>
                        <option value="botella|20000">NORMAL 30 ml.</option>
                        <option value="botella|37000">NORMAL 60 ml.</option>
                        <option value="botella|58000">NORMAL 100 ml.</option>
                        <option value="recarga|13000">RECARGA 30 ml.</option>
                        <option value="recarga|23000">RECARGA 60 ml.</option>
                        <option value="recarga|48000">RECARGA 100 ml.</option>
                    </select>
                </div>

                <!-- Nombre del producto (se auto rellena segun el codigo del producto) -->
                <div class="form-group">
                    <label for="nombre_producto">Nombre del producto</label>
                    <input type="text" id="nombre_producto" name="nombre_producto" required />
                </div>

                <!-- Cantidad que se van a vender -->
                <div class="form-group">
                    <label for="cantidad">CANTIDAD</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required />
                </div>

                <!-- Botón para agregar más productos (pendiente de implementar) -->
                <div class="form-group full-width">
                    <button type="button" id="addProductBtn">Agregar otro producto</button>
                </div>

                <!-- Precio total auto calculado -->
                <div class="form-group">
                    <label for="precio_total">Precio total</label>
                    <input type="text" id="precio_total" name="precio_total" readonly />
                </div>

                <!-- Métodos de pago estáticos -->
                <div class="form-group">
                    <label for="pago">Método de pago</label>
                    <select id="pago" name="pago" required>
                        <option value="">-- Selecciona método --</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="nequi">Nequi</option>
                    </select>
                </div>

                <!-- Botón para registrar la venta-->
                <div class="form-group full-width">
                    <button type="submit" name="vender" value="1">Vender</button>
                </div>
            </form>

            <!-- Apartado de cierre de sesion -->
            <div class="logout-container">
                <a href="logout.php" class="logout-button">Cerrar sesión</a>
            </div>
        </main>
    </div>

    <script>
        // Auto completar nombre según código (placeholder para AJAX)
        const codigoInput = document.getElementById('codigo');
        const nombreInput = document.getElementById('nombre_producto');
        codigoInput.addEventListener('blur', () => {
            nombreInput.value = codigoInput.value ? 'Nombre de ' + codigoInput.value : '';
        });

        // Cálculo del precio total con formato COP
        const cantidadInput = document.getElementById('cantidad');
        const tipoSelect    = document.getElementById('tipo');
        const precioInput   = document.getElementById('precio_total');
        function updateTotal() {
            const cantidad = parseFloat(cantidadInput.value) || 0;
            const parts    = tipoSelect.value.split('|');
            const precio   = parseFloat(parts[1]) || 0;
            const total    = cantidad * precio;
            precioInput.value = total.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        cantidadInput.addEventListener('input', updateTotal);
        tipoSelect.addEventListener('change', updateTotal);

        // Feedback de botón Agregar producto
        document.getElementById('addProductBtn').addEventListener('click', () => {
            alert('Función de agregar producto aún no implementada');
        });
    </script>
</body>
</html>



