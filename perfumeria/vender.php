<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vender - Su Aroma</title>
    <link rel="stylesheet" href="vender.css" />
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo" />
            <nav class="menu-buttons">
                <ul>
                    <li><a href="vender.html" class="menu-buttons active">VENDER</a></li>
                    <li><a href="catalogo.html" class="menu-buttons">CATÁLOGO</a></li>
                    <li><a href="inventario.html" class="menu-buttons">INVENTARIO</a></li>
                    <li><a href="crear.html" class="menu-buttons">CREAR</a></li>
                    <li><a href="informe.html" class="menu-buttons">INFORMES</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <h2 class="title">VENDER PRODUCTO</h2>
            <form class="form-grid">
                <div class="form-group">
                    <label for="codigo">CÓDIGO</label>
                    <input type="text" id="codigo" />
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de producto</label>
                    <select id="tipo">
                        <option>NORMAL</option>
                        <option>NORMAL 30 ml.</option>
                        <option>NORMAL 60 ml.</option>
                        <option>NORMAL 100 ml.</option>
                        <option>RECARGA 30 ml.</option>
                        <option>RECARGA 60 ml.</option>
                        <option>RECARGA 100 ml.</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="producto">Nombre del producto</label>
                    <input type="text" id="producto" />
                </div>

                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" />
                </div>

                <div class="form-group full-width">
                    <button type="button">Agregar otro producto</button>
                </div>

                <div class="form-group">
                    <label for="precio">Precio total</label>
                    <input type="text" id="precio" />
                </div>

                <div class="form-group">
                    <label for="pago">Método de pago</label>
                    <select id="pago">
                        <option>EFECTIVO</option>
                        <option>NEQUI</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <button type="submit">Vender</button>
                </div>
            </form>
            <a class="logout" href="index.php">Cerrar sesión</>
        </main>
    </div>
</body>
</html>
