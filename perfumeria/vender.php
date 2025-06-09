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
                    <button onclick="window.location.href='vender.php';" class="menu-buttons active">VENDER</button>
                    <button onclick="window.location.href='catalogo.php';" class="menu-buttons">CATÁLOGO</button>
                    <button onclick="window.location.href='inventario.php';" class="menu-buttons">INVENTARIO</button>
                    <button onclick="window.location.href='crear.php';" class="menu-buttons">CREAR</a></li>
                    <button onclick="window.location.href='informe.php';" class="menu-buttons">INFORMES</button>
                </ul>
            </nav>
        </aside>

        <!--aqui esta la secion principal del contenido del formulario de venta -->
        <main class="content">
            <?php if (!empty($_SESSION['vender_msg'])): //Si hay mensaje de venta guardado en $_SESSION muestra si los campos están vacios?>  
                <!--mostramos el mensaje en un div con clase 'message' -->
                <div class="message">
                    <?= htmlspecialchars($_SESSION['vender_msg']); //el htmlspecialchars evita ataques "XSS" convirtiendo caracteres especiales en entidades HTML ?>
                    <?php unset($_SESSION['vender_msg']); //esta linea impia el mensaje para no mostrarlo otra vez ?>
                </div>
            <?php endif; ?>

            <!--titulo de la sesion de venta-->
            <h2 class="title">VENDER PRODUCTO</h2>

            <!--este formulario con la id "sellForm", envía datos a "vender_process.php" con en metodo POST -->
            <form id="sellForm" action="vender_process.php" method="post" class="form-grid">

                <!--campo para el código, para que sea obligatorio usamos "required" -->
                <div class="form-group">
                    <label for="codigo">CÓDIGO</label>
                    <input type="text" id="codigo" name="codigo"/>
                </div>

                <!--campo desplegable para poder seleccionar el tipo de producto -->
                <div class="form-group">
                    <label for="tipo">Tipo de producto</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">-- Selecciona --</option>
                        <!--cada "value" une tipo y mililitros separados por "|" con la linea pues-->
                        <option value="botella|30">NORMAL 30 ml.</option>
                        <option value="botella|60">NORMAL 60 ml.</option>
                        <option value="botella|100">NORMAL 100 ml.</option>
                        <option value="recarga|30">RECARGA 30 ml.</option>
                        <option value="recarga|60">RECARGA 60 ml.</option>
                        <option value="recarga|100">RECARGA 100 ml.</option>
                    </select>
                </div>

                <!--este input muestra el nombre del producto agregado automáticamente -->
                <div class="form-group">
                    <label for="nombre_producto">NOMBRE producto</label>
                    <input type="text" id="nombre_producto" name="nombre_producto" readonly />
                </div>

                <!--en este campo se pone la cantidad de productos a vender, minimo 1 -->
                <div class="form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required />
                </div>

                <!--boton para agregar el producto a la lista de "espera"; inicialmente desactivado -->
                <div class="form-group full-width">
                    <button type="button" id="addProductBtn" disabled>Agregar producto</button>
                </div>

                <!--esto muestra el precio total calculado, no se puede editar manualmente -->
                <div class="form-group">
                    <label for="precio_total">PRECIO TOTAL</label>
                    <input type="text" id="precio_total" name="precio_total" readonly /><!--por esto -->
                </div>

                <!--campo desplegable con métodos de pago sacados de la base de datos -->
                <div class="form-group">
                    <label for="pago">MÉTODO DE PAGO</label>
                    <select id="pago" name="pago" required>
                        <option value="">-- Selecciona --</option><!--en este caso efectivo o nequi-->
                        <?php 
                        //esta linea hace una consulta para obtener los métodos de pago
                        $rs = $conn->query("SELECT nombre_metodo FROM metodo_pago");
                        while ($row = $rs->fetch_assoc()): //iteramos cada fila ?>
                            <option value="<?= htmlspecialchars($row['nombre_metodo']) ?>"><?= htmlspecialchars($row['nombre_metodo']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!--este es el apartado de la tabla donde se listan los productos que se van agregando -->
                <div class="form-group full-width">
                    <table id="itemsTable">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Cant.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody> <!--con esto se insertan las filas dinámicamente -->
                    </table>
                </div>

                <!--este es un campo oculto que guardara un JSON con todos los ítems -->
                <input type="hidden" name="items" id="itemsInput" />

                <!--boton para enviar definitivamente la venta -->
                <div class="form-group full-width">
                    <button type="submit" name="vender" value="1">Vender todo</button>
                </div>

            </form>

            <!--enlace para cerrar la sesion y limpiar la sesión de usuario -->
            <div class="logout-container">
                <a href="logout.php" class="logout-button">Cerrar sesión</a>
            </div>
        </main>
    </div>

    <script>
        //agarramos los elementos del DOM para usarlos más adelante
        const codigo = document.getElementById('codigo');              //input de código
        const nombreInput = document.getElementById('nombre_producto'); //muestra nombre del producto
        const tipoSelect = document.getElementById('tipo');            //el select de "tipo|ml"
        const cantidadInput = document.getElementById('cantidad');      //input cantidad
        const precioTotal = document.getElementById('precio_total');    //input precio total
        const addBtn = document.getElementById('addProductBtn');        //boton agregar
        const itemsTableBody = document.querySelector('#itemsTable tbody');
        const itemsInput = document.getElementById('itemsInput');      //este es el hidden del JSON
        let productData = null; //aquí guardaremos datos del producto traidos de la API
        let items = [];         //array donde acumulamos los productos agregados

        //esta funcionreinicia campos cuando cambias de producto
        function resetForm() {
            tipoSelect.value = '';         //selecciona el primer option vacío
            cantidadInput.value = '';      //limpia la cantidad
            precioTotal.value = '';        //limpia el precio
            tipoSelect.disabled = true;    //deshabilita el select 
            cantidadInput.disabled = true; //deshabilita la cantidad
            addBtn.disabled = true;        //deshabilita el botón
        }

        //cuando el campo "código" pierde el foco (blur), traemos datos del servidor
        codigo.addEventListener('blur', async () => {
            resetForm(); //limpiamos todo antes
            if (!codigo.value) return; //si está vacío, no hacemos nada
            
            //esto es para que cuando acceda a get.php muestre el nombre que se le asigno al codigo del producto
            const res = await fetch(`get_productos.php?codigo=${encodeURIComponent(codigo.value)}`);
            if (!res.ok) return;       //si falla la petición, salimos
            
            //parseamos la respuesta como JSON
            productData = await res.json();
            //mostramos el "nombre" del producto en el campo correspondiente
            nombreInput.value = productData.nombre_producto;
            //habilitamos el "select" de "tipo" para que el usuario elija el tipo y la cantidad
            tipoSelect.disabled = false;
        });

        //cuando se cambia el tipo/cantidad, sacamos su precio unitario
        //y habilitamos el campo de "cantidad"
        tipoSelect.addEventListener('change', () => {
            //la constaante de abajo separa 'botella|30' en ['botella','30']
            const [t, ml] = tipoSelect.value.split('|');
            //esta obtiene el precio unitario según el tipo y ml desde "productData"
            const unitPrice = productData.precios[t][ml] || 0;
            //guardamos el "unitPrice" en un atributo data para usarlo luego
            tipoSelect.dataset.unitPrice = unitPrice;
            //aqui es donde se habilita el campo de cantidad
            cantidadInput.disabled = false;
        });

        //lo que hace esto es que cada vez que el usuario establezca una cantidad, recalculamos el total
        cantidadInput.addEventListener('input', () => {
            const qty = parseInt(cantidadInput.value) || 0;               //la cantida ingresada por el usuario
            const unit = parseFloat(tipoSelect.dataset.unitPrice) || 0;    //precio unitario del producto seleccionado
            //multiplicamos y formateamos a moneda CO
            precioTotal.value = (qty * unit).toLocaleString('es-CO', {minimumFractionDigits:2});
            //solo se activa el botón de agregar si qty >= 1
            addBtn.disabled = qty < 1;
        });

        //al hacer clic en "agregar producto" almacenamos el ítem
        addBtn.addEventListener('click', () => {
            //repetimos "split" para obtener tipo y ml
            const [t, ml] = tipoSelect.value.split('|');
            const qty = parseInt(cantidadInput.value);
            const unit = parseFloat(tipoSelect.dataset.unitPrice);
            //creamos un objeto con los datos del ítem
            const item = { codigo: codigo.value, nombre: nombreInput.value, tipo: t, ml: ml, cantidad: qty, total: qty * unit };
            items.push(item); //lo agregamos al array

            //creamos una fila nueva en la tabla para mostrarlo
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.codigo}</td>
                <td>${item.nombre}</td>
                <td>${item.tipo} ${item.ml}ml</td>
                <td>${item.cantidad}</td>
                <td>${item.total.toLocaleString('es-CO', {minimumFractionDigits:2})}</td>
            `;
            itemsTableBody.appendChild(tr);

            //actualizamos el hidden input con todo el JSON de items
            itemsInput.value = JSON.stringify(items);

            //reiniciamos formulario listo para el próximo producto
            resetForm();
            codigo.value = '';
            nombreInput.value = '';
        });
    </script>
</body>
</html>