<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inventario - Su Aroma</title>  
    <link rel="stylesheet" href="CSS/tablas.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo" />
            <nav class="menu-buttons">
                <ul>
                    <li><a href="vender.php" class="menu-buttons">VENDER</a></li>
                    <li><a href="catalogo.php" class="menu-buttons">CATÁLOGO</a></li>
                    <li><a href="inventario.php" class="menu-buttons active">INVENTARIO</a></li>
                    <li><a href="crear.php" class="menu-buttons">CREAR</a></li>
                    <li><a href="informe.php" class="menu-buttons">INFORMES</a></li>
                </ul>
            </nav>
        </aside>
        <main class="content">
            <h2 class="title">Inventario </h2>
            <div class="filter-buttons">
                <button onclick="filtrar('dama')">Perfume Dama</button>
                <button onclick="filtrar('caballero')">Perfume Caballero</button>
                <button onclick="filtrar('todos')">Mostrar Todo</button>
            </div>

            <div class="table-container">
                <table id="tablaPerfumes">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>cantidad</th>
                            <th>Inspiración</th>
                            <th>Casa</th>
                            <th>precio</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-genero="dama">
                            <td>001</td>  
                            <td>Rosa Mística</td>
                            <td>36</td>
                            <td>dfdfgdghtdfh No. 5</td>
                            <td>Elegance Co.</td>
                            <td>36000</td>
                        </tr>
                        <tr data-genero="caballero">
                            <td>002</td>
                            <td>Brisa Nocturna</td>
                            <td>62</td>
                            <td>Oceanic Fragrance</td>
                            <td>Notas cítricas frescas</td>
                            <td>62000</td>

                        </tr>
                    </tbody>
                </table>
            </div>

            <a class="logout" href="logout.php">Cerrar sesión</a>
        </main>
    </div>
    
</body>
</html>
