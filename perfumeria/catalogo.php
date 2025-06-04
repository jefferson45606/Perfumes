<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catalogo - Su Aroma</title>  
    <link rel="stylesheet" href="CSS/tablas.css" />
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" class="logo" />
            <nav class="menu-buttons">
                <ul>
                    <li><a href="vender.php" class="menu-buttons">VENDER</a></li>
                    <li><a href="catalogo.php" class="menu-buttons active">CATÁLOGO</a></li>
                    <li><a href="inventario.php" class="menu-buttons">INVENTARIO</a></li>
                    <li><a href="crear.php" class="menu-buttons">CREAR</a></li>
                    <li><a href="informe.php" class="menu-buttons">INFORMES</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <h2 class="title">Catalogo de productos </h2>
            <div class="filter-buttons">
                <button onclick="filtrar('dama')">Perfume Dama</button>
                <button onclick="filtrar('caballero')">Perfume Caballero</button>
                <button onclick="filtrar('todos')">Mostrar Todos</button>
            </div>

            <div class="table-container">
                <table id="tablaPerfumes">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Inspiración</th>
                            <th>Casa</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-genero="dama">
                            <td>001</td>
                            <td><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:A
                                Nd9GcSxkwkWHlhkMxSguDWUv56gp7FmiVoztoMXOmcMx3rqPGoH3PhRYjwpV
                                lv_kHIjGULuugU&usqp=CAU" alt="1"></td>
                            <td>Rosa Mística</td>
                            <td>Chanel No. 5</td>
                            <td>Elegance Co.</td>
                            <td>Aroma floral sofisticado</td>
                        </tr>
                        <tr data-genero="caballero">
                            <td>002</td>
                            <td><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:A
                                Nd9GcSxkwkWHlhkMxSguDWUv56gp7FmiVoztoMXOmcMx3rqPGoH3PhRYjwpV
                                lv_kHIjGULuugU&usqp=CAU" alt="2"></td>
                            <td>Brisa Nocturna</td>
                            <td>Acqua di Gio</td>
                            <td>Oceanic Fragrance</td>
                            <td>Notas cítricas frescas</td>
                        </tr>
                        <!-- Modal para mostrar imagen ampliada -->
                        <div id="imageModal" class="modal">
                            <span class="close">&times;</span>
                            <img class="modal-content" id="imgAmpliada">
                        </div>   


                        
                    </tbody>
                </table>
            </div>

            <a class="logout" href="logout.php">Cerrar sesión</a>
        </main>
    </div>
    <script>
        // Obtener elementos
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('imgAmpliada');
        const closeBtn = document.getElementsByClassName('close')[0];
    
        // Escuchar clicks en las imágenes de la tabla
        document.querySelectorAll('table td img').forEach(img => {
            img.addEventListener('click', () => {
                modal.style.display = "block";
                modalImg.src = img.src;
            });
        });
    
        // Cerrar el modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
    
        // Cerrar el modal al hacer clic fuera de la imagen
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
    
</body>
</html>
