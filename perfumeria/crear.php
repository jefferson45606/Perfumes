<!DOCTYPE html>
<html lang="es"> 
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title> Crear productos - Su Aroma</title>
  <link rel="stylesheet" href="CSS/crear.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="container">
    <aside class="logo-section">
      <img src="IMG/Su_Aroma.png" alt="Logo Su Aroma" />
      <nav class="menu-buttons"> 
        <ul>
          <li><a href="vender.php" class="menu-buttons">VENDER</a></li>
          <li><a href="catalogo.php" class="menu-buttons">CATÁLOGO</a></li>
          <li><a href="inventario.php" class="menu-buttons">INVENTARIO</a></li>
          <li><a href="crear.php" class="menu-buttons active">CREAR</a></li>
          <li><a href="informe.php" class="menu-buttons">INFORMES</a></li>
        </ul>
      </nav>
    </aside>

    <main class="login-section">
      <h2 class="title">Crear productos</h2>
      <p class="subtitle">Agrega categorías y productos personalizados</p>

      <div class="categorias-section">
        <label for="nuevaCategoria">Categorías</label>
        <div id="botonesCategorias" class="botones-categorias"></div>
        <div class="crear-categoria">
          <input type="text" id="nuevaCategoria" placeholder="Nueva categoría" />
          <button onclick="crearCategoria()">Crear categoría</button>
        </div>
      </div>

      <div class="buscar-producto" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" id="BuscarProducto" placeholder="Buscar producto" />
        <button onclick="buscarProducto()" class="boton-categoria">BUSCAR PRODUCTO</button>
      </div>

      <div id="tablasPorCategoria"></div>

      <a class="logout" href="logout.php">Cerrar sesión</a>
    </main>
  </div>

  <!-- Modal para ver imagen -->
  <div id="imageModal" class="modal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center;">
    <span class="close" style="position:absolute; top:20px; right:30px; font-size:30px; cursor:pointer; color:white;">&times;</span>
    <img class="modal-content" id="imgAmpliada" style="max-width: 90%; max-height: 80%;">
  </div>

  <!-- Aquí se insertará dinámicamente la tabla de creación -->
  <div id="modalProducto" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 1000; justify-content: center; align-items: center; padding: 20px; display: none;">
    <div id="modalContenido" style="background: #e6dbdb; padding: 30px; border-radius: 15px; max-height: 90vh; overflow-y: auto; width: 50%; max-width: 1600px; position: relative; box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.2);">
      <span id="cerrarModalProducto" style="position: absolute; top: 15px; right: 20px; cursor: pointer; font-size: 28px; font-weight: bold; color: #333;">&times;</span>
    </div>
  </div>

  

<script>
let categorias = [];
let categoriaSeleccionadaId = null;

// Cargar categorías desde el backend : manda todos los datos de las categorías
fetch('includes/backend/categorias_api.php')// <-- Aquí se obtiene la lista de categorías
  .then(r => r.json())// <-- Aquí se espera la respuesta del servidor
  .then(data => {// <-- Aquí se procesa la respuesta
    categorias = data;// <-- Aquí se guarda la lista de categorías
    mostrarCategorias();// <-- Aquí se muestra la lista de categorías en los botones

    // CREA los contenedores de cada categoría
    const tablasPorCategoria = document.getElementById('tablasPorCategoria');// <-- Aquí se obtiene el contenedor donde se mostrarán las categorías
    tablasPorCategoria.innerHTML = ''; // <-- Limpia todo antes de crear los contenedores

    categorias.forEach(categoria => {//esto es para crear los contenedores de cada categoría
      mostrarTablaCategoria(categoria.nombre_categoria);// <-- Aquí se crea el contenedor para cada categoría
    });

    // Mostrar solo la primera categoría al inicio
    if (categorias.length > 0) {// <-- Aquí se verifica si hay categorías
      filtrarCategoria(categorias[0].nombre_categoria);// <-- Aquí se filtra para mostrar solo la primera categoría
    }

    // CARGA los productos al inicio
    fetch('includes/backend/productos_api.php')// <-- Aquí se obtiene la lista de productos
      .then(r => r.json())// <-- Aquí se espera la respuesta del servidor
      .then(data => { // <-- aquí estaba el error
        if (data.success) {// <-- Aquí se verifica si la respuesta fue exitosa
          limpiarProductosDeCategorias(); // Esto solo elimina las tarjetas, no el botón
          data.productos.forEach(producto => {// <-- Aquí se itera sobre cada producto
            const categoria = categorias.find(cat => cat.id == producto.categoria_id);// <-- Aquí se busca la categoría del producto
            if (categoria) {// <-- Aquí se verifica si la categoría existe
              const contenedor = document.getElementById(`contenedor-${categoria.nombre_categoria}`);// <-- Aquí se obtiene el contenedor de la categoría
              if (contenedor) {// <-- Aquí se verifica si el contenedor existe
                crearTarjetaProducto({//  <-- Aquí se crea la tarjeta del producto
                  ...producto,
                  Imagen: producto.imagen_url,
                  Código: producto.codigo_producto,
                  Nombre: producto.nombre_producto,
                  Inspiración: producto.inspiracion,
                  Casa: producto.casa,
                  Descripción: producto.descripcion,
                  Cantidad: producto.cantidad,
                  Precio: producto.precio,
                  'Precio 30ml': producto.precio_30ml,
                  'Precio 60ml': producto.precio_60ml,
                  'Precio 100ml': producto.precio_100ml,
                  'Recarga 30ml': producto.recarga_30ml,
                  'Recarga 60ml': producto.recarga_60ml,
                  'Recarga 100ml': producto.recarga_100ml,
                }, contenedor);
              }
            }
          });
        }
      });
  });

function mostrarCategorias() {// Muestra los botones de categorías
  const botonesCategorias = document.getElementById('botonesCategorias');// Limpia los botones existentes
  botonesCategorias.innerHTML = '';// Limpia el contenedor de botones de categorías
  categorias.forEach((cat, idx) => {// Itera sobre cada categoría
    const btn = document.createElement('button');// Crea un botón para cada categoría
    btn.textContent = cat.nombre_categoria;// Asigna el nombre de la categoría al botón
    btn.classList.add('boton-categoria');// Añade la clase de estilo al botón
    btn.dataset.id = cat.id;// Asigna el ID de la categoría al botón
    btn.onclick = () => {// Al hacer clic en el botón, se activa la función de filtrado
      document.querySelectorAll('.boton-categoria').forEach(b => b.classList.remove('activo'));// Elimina la clase 'activo' de todos los botones
      btn.classList.add('activo');// Añade la clase 'activo' al botón clicado
      categoriaSeleccionadaId = cat.id;// Actualiza la categoría seleccionada
      filtrarCategoria(cat.nombre_categoria);//
    };
    botonesCategorias.appendChild(btn);

    // Selecciona la primera categoría por defecto
    if (idx === 0) {// Si es la primera categoría, la selecciona automáticamente
      btn.classList.add('activo');// Añade la clase 'activo' al primer botón
      categoriaSeleccionadaId = cat.id;// Actualiza la categoría seleccionada al ID de la primera categoría
    }
  });
}

function crearCategoria() {// Crea una nueva categoría
  const input = document.getElementById('nuevaCategoria');// Obtiene el valor del input de nueva categoría
  const nueva = input.value.trim();// Elimina espacios al inicio y al final del valor ingresado

  if (!nueva || categorias.includes(nueva)) {// Verifica si el valor es vacío o ya existe en la lista de categorías
    alert("Categoría inválida o ya existente");// Muestra un mensaje de alerta si la categoría es inválida o ya existe
    return;// Sale de la función si la categoría es inválida
  }

  fetch('includes/backend/categoria_save.php', {// Envía la nueva categoría al backend
    method: 'POST',// Usa el método POST para enviar los datos
    headers: {// Define los encabezados de la solicitud
      'Content-Type': 'application/x-www-form-urlencoded'// Especifica el tipo de contenido como URL codificada
    },
    body: 'dato=' + encodeURIComponent(nueva)// Codifica el valor de la nueva categoría para enviarlo en el cuerpo de la solicitud
  })
  .then(response => response.json())// Convierte la respuesta del servidor a JSON
  .then(data => {// Procesa la respuesta del servidor
    if (data.success && data.categoria) {// Verifica si la respuesta fue exitosa y contiene la nueva categoría
      categorias.push(data.categoria);// Añade la nueva categoría a la lista de categorías
      mostrarCategorias();// Muestra los botones de categorías actualizados
      input.value = '';// Limpia el input de nueva categoría
    } else {// Si la respuesta no fue exitosa
      alert(data.message || "Ocurrió un error al guardar la categoría.");// Muestra un mensaje de alerta con el error
    }
  })
  .catch(error => {
    console.error('Error en la solicitud:', error);// Muestra el error en la consola
    alert("Error de conexión al guardar la categoría.");// Muestra un mensaje de alerta si hay un error en la solicitud
  });
}

function filtrarCategoria(nombre) {// Filtra los productos por categoría
  categorias.forEach(cat => {// Itera sobre cada categoría
    const contenedor = document.getElementById(`contenedor-${cat.nombre_categoria}`);// Obtiene el contenedor de la categoría actual
    if (contenedor) {// Si el contenedor existe
      contenedor.style.display = (cat.nombre_categoria === nombre) ? 'block' : 'none';// Muestra el contenedor si es la categoría seleccionada, de lo contrario lo oculta
    }
  });
}

function mostrarTablaCategoria(nombre) {// Crea un contenedor para la categoría si no existe
  let contenedor = document.getElementById(`contenedor-${nombre}`);// Verifica si ya existe un contenedor para esta categoría
  if (!contenedor) {// Si no existe, lo crea
    contenedor = document.createElement('div');// Crea un nuevo contenedor
    contenedor.id = `contenedor-${nombre}`;// Asigna un ID único al contenedor
    contenedor.style.marginTop = '20px';// Estilo para el contenedor
    document.getElementById('tablasPorCategoria').appendChild(contenedor);// Agrega el contenedor al DOM
  } else {// Si ya existe, limpia su contenido antes de agregar el botón
    // Limpia el contenedor completamente antes de agregar el botón
    contenedor.innerHTML = '';// Esto elimina todo el contenido del contenedor, incluidas las tarjetas de productos
  }

  // Solo agrega el botón si no existe ya (esto ahora siempre será cierto)
  if (!contenedor.querySelector('.boton-crear-producto')) {// Verifica si ya existe el botón de crear producto
    const botonCrear = document.createElement('div');// Crea un nuevo botón para crear productos
    botonCrear.classList.add('boton-crear-producto');// Añade una clase para el estilo del botón
    botonCrear.textContent = '🛠️ Crea tu producto aquí';// Asigna el texto al botón
    botonCrear.style.cursor = 'pointer';// Cambia el cursor al pasar sobre el botón
    botonCrear.style.padding = '15px';// Añade padding al botón
    botonCrear.style.backgroundColor = '#e6486f';// Establece el color de fondo del botón
    botonCrear.style.color = '#fff';// Establece el color del texto del botón
    botonCrear.style.borderRadius = '10px';// Añade bordes redondeados al botón
    botonCrear.style.fontWeight = 'bold';// Establece el peso de la fuente del botón
    botonCrear.style.textAlign = 'center';// Alinea el texto al centro del botón
    botonCrear.onmouseenter = () => botonCrear.style.backgroundColor = '#c53557';// Cambia el color de fondo al pasar el mouse
    botonCrear.onmouseleave = () => botonCrear.style.backgroundColor = '#e6486f';// Restaura el color de fondo al quitar el mouse
    botonCrear.onclick = () => abrirFormulario(nombre, contenedor);// Al hacer clic, abre el formulario para crear un producto

    contenedor.appendChild(botonCrear);// Añade el botón al contenedor de la categoría
  }
}

function abrirFormulario(categoriaNombre, contenedorPadre, datos = null, card = null) {// Abre el formulario para crear o editar un producto
  // Corregido: Actualiza la categoría seleccionada al editar
  if (datos && datos['categoria_id']) {// Si estamos editando un producto, obtenemos la categoría del producto
    categoriaSeleccionadaId = datos['categoria_id'];// <-- Aquí se obtiene el ID de la categoría del producto
  }

  modalContenido.innerHTML = '';// Limpia el contenido del modal antes de agregar nuevos elementos
  const form = document.createElement('form');// Crea un nuevo formulario
  form.style.display = 'flex';// Establece el formulario como un contenedor flexible
  form.style.flexDirection = 'column';// Establece la dirección del formulario como columna
  form.style.gap = '10px';// Añade un espacio entre los elementos del formulario

  const campos = [
    'Código', 'Imagen', 'Nombre', 'Inspiración', 'Casa', 'Descripción', 'Cantidad',
    'Precio',
    { nombre: 'Precio 30ml' },
    { nombre: 'Precio 60ml' },
    { nombre: 'Precio 100ml' },
    { nombre: 'Recarga 30ml' },
    { nombre: 'Recarga 60ml' },
    { nombre: 'Recarga 100ml' }
  ];

  const inputs = {};

  campos.forEach(campo => {// Itera sobre cada campo para crear los inputs
    const campoNombre = typeof campo === 'string' ? campo : campo.nombre;// Verifica si el campo es un string o un objeto y obtiene el nombre del campo

    const label = document.createElement('label');// Crea un nuevo elemento de etiqueta para el campo
    label.textContent = campoNombre;// Asigna el nombre del campo al texto de la etiqueta
    if (campoNombre === 'Imagen') {// Si el campo es 'Imagen', añade una clase especial para el estilo
      label.classList.add('imagen-label');// Añade una clase para el estilo de la etiqueta de imagen
    }

    const input = document.createElement('input');// Crea un nuevo input para el campo
    input.name = campoNombre;// Asigna el nombre del campo al input
    input.required = true;// Establece el input como requerido por defecto

    if (campoNombre === 'Imagen') {// Si el campo es 'Imagen', configura el input para aceptar archivos de imagen
      input.type = 'file';// Establece el tipo de input como 'file' para cargar imágenes
      input.accept = 'image/*';// Acepta solo archivos de imagen
      input.required = !datos; // si estamos editando, la imagen no es obligatoria
    } else if (campoNombre === 'Cantidad') {// Si el campo es 'Cantidad', establece el tipo de input como número
      input.type = 'number';// Establece el tipo de input como 'number' para la cantidad
    } else {// Para otros campos, establece el tipo de input como texto
      input.type = 'text';// Establece el tipo de input como 'text' para los demás campos
    }

    if (datos && campoNombre !== 'Imagen') {// Si estamos editando un producto y el campo no es 'Imagen', establece el valor del input con los datos existentes
      input.value = datos[campoNombre] || '';// Asigna el valor del campo desde los datos existentes o un string vacío si no existe
    }

    if (campoNombre === 'Imagen' && datos && datos['Imagen']) {// Si estamos editando un producto y ya existe una imagen, muestra la imagen existente
      label.classList.add('imagen-label');// Añade una clase para el estilo de la etiqueta de imagen existente

      // Campo oculto para manter a imagem existente se não for carregada uma nova
      const hidden = document.createElement('input');// Crea un input oculto para mantener la imagen existente
      hidden.type = 'hidden';// Establece el tipo de input como 'hidden' para que no sea visible
      hidden.name = 'ImagenExistente';// Asigna el nombre del campo como 'ImagenExistente' para identificar la imagen existente
      hidden.value = datos['Imagen'];// Asigna el valor del campo oculto con la ruta de la imagen existente
      form.appendChild(hidden);// Añade el input oculto al formulario

      // Mostrar nome do arquivo existente DEPOIS do input
      input.style.display = 'inline-block';// Muestra el input de imagen como un bloque en línea
      const nombreArchivo = document.createElement('span');// Crea un nuevo elemento span para mostrar el nombre del archivo existente
      nombreArchivo.textContent = ' - ' + datos['Imagen'].split('/').pop();// Asigna el texto del span con el nombre del archivo existente
      nombreArchivo.style.fontSize = '12px';// Establece el tamaño de fuente del span como 12px
      nombreArchivo.style.color = '#555';// Establece el color del texto del span como gris oscuro

      label.appendChild(input);// Añade el input de imagen al label
      label.appendChild(nombreArchivo);// Añade el span con el nombre del archivo al label
      form.appendChild(label);// Añade el label al formulario

      inputs[campoNombre] = input;// Guarda el input de imagen en el objeto inputs para su posterior uso

      return;// Sale de la función si el campo es 'Imagen' y ya existe una imagen
    }

    inputs[campoNombre] = input;// Guarda el input en el objeto inputs para su posterior uso

    label.appendChild(input);// Añade el input al label
    form.appendChild(label);// Añade el label al formulario
  });

  const btnGuardar = document.createElement('button');// Crea un botón para guardar el producto
  btnGuardar.type = 'submit';// Establece el tipo del botón como 'submit' para enviar el formulario
  btnGuardar.textContent = 'Guardar';// Asigna el texto del botón como 'Guardar'
  btnGuardar.style.padding = '10px';//  Añade padding al botón
  btnGuardar.style.backgroundColor = '#d14662';// Establece el color de fondo del botón como un tono rosado
  btnGuardar.style.color = '#fff';//  Establece el color del texto del botón como blanco
  btnGuardar.style.border = 'none';// Elimina el borde del botón
  btnGuardar.style.borderRadius = '6px';// Añade bordes redondeados al botón
  btnGuardar.style.cursor = 'pointer';// Cambia el cursor al pasar sobre el botón

  form.appendChild(btnGuardar);// Añade el botón de guardar al formulario
  modalContenido.appendChild(form);// Añade el formulario al contenido del modal
  modalProducto.style.display = 'flex';// Muestra el modal con el formulario

  form.onsubmit = (e) => {// Al enviar el formulario, se ejecuta esta función
    e.preventDefault();// Previene el comportamiento por defecto del formulario (recargar la página)
    const nuevosDatos = {};// Crea un objeto para almacenar los nuevos datos del producto

    // Validar duplicado
    const nombreNuevo = inputs['Nombre'].value.trim().toLowerCase();// Obtiene el nombre del nuevo producto en minúsculas
    const productosExistentes = document.querySelectorAll('[data-producto]');// Obtiene todas las tarjetas de productos existentes
    const nombreDuplicado = Array.from(productosExistentes).some(card => {// Verifica si ya existe un producto con el mismo nombre
      const prod = JSON.parse(card.dataset.producto);// Convierte los datos del producto de la tarjeta a un objeto
      const mismoNombre = prod['Nombre'].trim().toLowerCase() === nombreNuevo;// Compara el nombre del producto existente con el nuevo nombre
      const esMismoProducto = card === card;// Verifica si la tarjeta actual es la misma que la que se está editando
      return mismoNombre && (!datos || !esMismoProducto);// Retorna true si hay un producto con el mismo nombre y no es el mismo producto que se está editando
    });

    if (nombreDuplicado) {// Si ya existe un producto con el mismo nombre
      alert("Ya existe un producto con ese nombre.");// Muestra un mensaje de alerta indicando que el nombre ya está en uso
      return;// Sale de la función si hay un nombre duplicado
    }

    campos.forEach(c => {// Itera sobre cada campo para obtener los valores ingresados
      const campoNombre = typeof c === 'string' ? c : c.nombre;// Obtiene el nombre del campo, ya sea un string o un objeto
      if (campoNombre === 'Imagen') return;// Si el campo es 'Imagen', no lo agrega a nuevosDatos aún, ya que se maneja por separado
      const val = inputs[campoNombre].value.trim();// Obtiene el valor del input del campo y elimina espacios al inicio y al final
      nuevosDatos[campoNombre] = inputs[campoNombre].type === 'number' ? parseFloat(val) || 0 : val;// Convierte el valor a número si el tipo del input es 'number', o lo deja como string si es otro tipo
    });

    const fileInput = inputs['Imagen'];// Obtiene el input de imagen del formulario
    const file = fileInput.files[0];// Obtiene el primer archivo seleccionado en el input de imagen
    const formData = new FormData();//  Crea un nuevo objeto FormData para enviar los datos del formulario

    for (const key in nuevosDatos) {// Itera sobre cada campo de nuevosDatos
      formData.append(key, nuevosDatos[key]);// Añade el campo y su valor al objeto FormData
    }
    // Corregido: Siempre envía el ID de la categoría seleccionada
    formData.append('categoria_id', categoriaSeleccionadaId);// Añade el ID de la categoría seleccionada al objeto FormData

    if (file) {// Si se seleccionó un archivo de imagen
      formData.append('imagen', file);//  Añade el archivo de imagen al objeto FormData con la clave 'imagen'
    } else if (datos?.['Imagen']) {// Si no se seleccionó un nuevo archivo pero ya existe una imagen
      formData.append('ImagenExistente', datos['Imagen']);// Añade la imagen existente al objeto FormData con la clave 'ImagenExistente'
    }

    // Corregido: Envía correctamente los valores de recarga_100ml
    formData.append('precio_30ml', inputs['Precio 30ml'].value.trim());// Añade el precio de 30ml al objeto FormData
    formData.append('precio_60ml', inputs['Precio 60ml'].value.trim());// Añade el precio de 60ml al objeto FormData
    formData.append('precio_100ml', inputs['Precio 100ml'].value.trim());// Añade el precio de 100ml al objeto FormData
    formData.append('recarga_30ml', inputs['Recarga 30ml'].value.trim());// Añade el valor de recarga de 30ml al objeto FormData
    formData.append('recarga_60ml', inputs['Recarga 60ml'].value.trim());// Añade el valor de recarga de 60ml al objeto FormData
    formData.append('recarga_100ml', inputs['Recarga 100ml'].value.trim());// Añade el valor de recarga de 100ml al objeto FormData

    fetch('includes/backend/producto_save.php', {// Envía los datos del producto al backend
      method: 'POST',// Usa el método POST para enviar los datos
      body: formData// Envía el objeto FormData con los datos del producto
    })
    .then(r => r.json())//  Convierte la respuesta del servidor a JSON
    .then(resultado => {// Procesa la respuesta del servidor
      if (resultado.success) {// Si la respuesta fue exitosa
        if (card) {// Si estamos editando un producto existente
          actualizarProducto({ ...nuevosDatos, Imagen: resultado.imagen }, card);// Actualiza la tarjeta del producto existente con los nuevos datos
        } else {// Si estamos creando un nuevo producto
          crearTarjetaProducto({ ...nuevosDatos, Imagen: resultado.imagen }, contenedorPadre);// Crea una nueva tarjeta de producto con los nuevos datos
        }
        modalProducto.style.display = 'none';// Oculta el modal después de guardar el producto
      } else {//  Si la respuesta no fue exitosa
        alert(resultado.message || 'Error al guardar el producto.');// Muestra un mensaje de alerta con el error
      }
    })
    .catch(error => {// Si ocurre un error en la solicitud
      alert("Error de red o del servidor.");//  Muestra un mensaje de alerta indicando un error de red o del servidor
      console.error(error);// Muestra el error en la consola para depuración
    });
  }
}
function terminar() {// Esta función se llama al hacer clic en el botón "Guardar" del formulario
  nuevosDatos['Categoría'] = categoria; // Guardamos categoría
  if (card) {// Si estamos editando un producto existente
    actualizarProducto(nuevosDatos, card);// Actualiza la tarjeta del producto existente con los nuevos datos
  } else {//  Si estamos creando un nuevo producto
    crearTarjetaProducto(nuevosDatos, contenedorPadre);// Crea una nueva tarjeta de producto con los nuevos datos
  }
  guardar_producto(nuevosDatos);//  Guarda los datos del producto
  modalProducto.style.display = 'none';// Oculta el modal después de guardar el producto
}

function guardarProducto(nuevosDatos, file, callback) {// Esta función guarda los datos del producto, incluyendo la imagen si se proporciona
  if (file) {// Si se proporciona un archivo de imagen
    const reader = new FileReader();// Crea un nuevo FileReader para leer el archivo de imagen
    reader.onload = () => {// Cuando el archivo se haya leído correctamente
      nuevosDatos['Imagen'] = reader.result;//  Asigna el resultado de la lectura del archivo (la imagen en formato base64) a nuevosDatos['Imagen']
      callback(nuevosDatos);// Llama al callback con los nuevos datos del producto, incluyendo la imagen
    };
    reader.readAsDataURL(file);// Lee el archivo de imagen como una URL de datos (base64)
  } else {// Si no se proporciona un archivo de imagen
    callback(nuevosDatos);// Llama al callback con los nuevos datos del producto sin imagen
  }
}

function crearTarjetaProducto(datos, contenedor) {//  Crea una tarjeta de producto y la añade al contenedor especificado
  const card = document.createElement('div');// Crea un nuevo elemento div para la tarjeta del producto
  card.style.display = 'inline-block';// Establece el estilo de la tarjeta como un bloque en línea para que se muestre en línea con otros elementos
  card.style.margin = '10px';// Añade un margen de 10px alrededor de la tarjeta
  card.style.textAlign = 'center';// Establece el texto dentro de la tarjeta como centrado
  card.style.position = 'relative';// Establece la posición de la tarjeta como relativa para poder posicionar elementos dentro de ella
  card.style.width = '120px';// Establece el ancho de la tarjeta como 120px
  card.style.borderRadius = '12px';// Añade bordes redondeados a la tarjeta con un radio de 12px
  card.style.overflow = 'hidden';// Asegura que el contenido que sobresalga de la tarjeta se oculte
  card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';// Añade una transición suave para transformaciones y sombras
  card.style.cursor = 'pointer';// Cambia el cursor al pasar sobre la tarjeta para indicar que es interactiva

  card.onmouseenter = () => {// Al pasar el mouse sobre la tarjeta, se aplica un efecto de escala y sombra
    card.style.transform = 'scale(1.05)';// Aumenta el tamaño de la tarjeta al 105% de su tamaño original
    card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';// Añade una sombra suave alrededor de la tarjeta para darle un efecto de profundidad
  };
  card.onmouseleave = () => {// Al quitar el mouse de la tarjeta, se restaura su tamaño y sombra originales
    card.style.transform = 'scale(1)';// Restaura el tamaño de la tarjeta al 100% de su tamaño original
    card.style.boxShadow = 'none';// Elimina la sombra de la tarjeta
  };

  const img = document.createElement('img');// Crea un nuevo elemento de imagen para mostrar la imagen del producto
  let rutaImagen = datos['Imagen'];// Obtiene la ruta de la imagen del producto desde los datos
  if (rutaImagen && !rutaImagen.startsWith('http')) {// Si la ruta de la imagen no comienza con 'http', se asume que es una ruta relativa
    rutaImagen = 'uploads/' + rutaImagen.replace(/^uploads[\/\\]/, '');// Esto asegura que la ruta sea correcta para acceder a la imagen en el servidor
  }
  img.src = rutaImagen || 'https://via.placeholder.com/100';// Si no hay una ruta de imagen válida, se usa una imagen de marcador de posición
  img.style.width = '100%';// Establece el ancho de la imagen al 100% del contenedor de la tarjeta
  img.style.height = '120px';// Establece la altura de la imagen a 120px para que se ajuste al diseño de la tarjeta
  img.style.objectFit = 'contain';// Asegura que la imagen se ajuste al contenedor sin distorsionarse
  img.style.background = '#fff';// Establece el fondo de la imagen como blanco para que se vea bien en la tarjeta

  const info = document.createElement('div');// Crea un nuevo elemento div para mostrar la información del producto
  info.style.background = '#f4b6c2';// Establece el fondo del div de información como un tono rosado claro
  info.style.padding = '8px';// Añade un padding de 8px alrededor del texto de información
  info.style.fontSize = '12px';// Establece el tamaño de fuente del texto de información a 12px
  info.style.borderBottomLeftRadius = '12px';// Añade un borde redondeado en la esquina inferior izquierda del div de información
  info.style.borderBottomRightRadius = '12px';// Añade un borde redondeado en la esquina inferior derecha del div de información
  info.innerHTML = `<strong>${datos['Código'] || ''}</strong><br>${datos['Nombre'] || ''}`;// Asigna el contenido del div de información con el código y el nombre del producto, usando etiquetas HTML para dar formato

  const eliminarBtn = document.createElement('button');// Crea un nuevo botón para eliminar el producto
  eliminarBtn.innerHTML = '🗑️';// Asigna el contenido del botón como un ícono de papelera
  eliminarBtn.style.position = 'absolute';// Establece la posición del botón como absoluta para que se pueda posicionar dentro de la tarjeta
  eliminarBtn.style.top = '5px';// Establece la posición del botón a 5px desde la parte superior de la tarjeta
  eliminarBtn.style.right = '5px';// Establece la posición del botón a 5px desde el lado derecho de la tarjeta
  eliminarBtn.style.background = '#ff4d4d';// Establece el fondo del botón como un tono rojo claro
  eliminarBtn.style.border = 'none';// Elimina el borde del botón para que se vea más limpio
  eliminarBtn.style.color = '#fff';// Establece el color del texto del botón como blanco para que contraste con el fondo
  eliminarBtn.style.borderRadius = '50%';// Añade bordes redondeados al botón para que tenga forma circular
  eliminarBtn.style.width = '28px';// Establece el ancho del botón a 28px para que sea un botón pequeño y redondo
  eliminarBtn.style.height = '28px';// Establece la altura del botón a 28px para que sea un botón pequeño y redondo
  eliminarBtn.style.cursor = 'pointer';// Cambia el cursor al pasar sobre el botón para indicar que es interactivo
  eliminarBtn.onclick = (e) => {// Al hacer clic en el botón de eliminar, se ejecuta esta función
    e.stopPropagation();// Previene que el clic se propague a la tarjeta del producto, evitando que se abra el formulario al hacer clic en el botón de eliminar
    if (confirm("¿Eliminar este producto?")) card.remove();// Si el usuario confirma la eliminación, se elimina la tarjeta del producto del DOM
  };

  card.dataset.producto = JSON.stringify(datos);// Guarda los datos del producto en un atributo personalizado de la tarjeta para poder acceder a ellos más tarde

  card.onclick = () => {// Al hacer clic en la tarjeta del producto, se abre el formulario para editar el producto
    const datosActuales = JSON.parse(card.dataset.producto);// Convierte los datos del producto guardados en el atributo personalizado de la tarjeta a un objeto
    abrirFormulario('', contenedor, datosActuales, card);// Llama a la función abrirFormulario para mostrar el formulario de edición con los datos actuales del producto y la tarjeta actual
  };

  card.appendChild(img);// Añade la imagen del producto a la tarjeta
  card.appendChild(info);// Añade el div de información del producto a la tarjeta
  card.appendChild(eliminarBtn);// Añade el botón de eliminar a la tarjeta
  contenedor.appendChild(card);// Añade la tarjeta del producto al contenedor especificado
}

function actualizarProducto(datos, card) {// Actualiza los datos de un producto existente en la tarjeta
  const img = card.querySelector('img');// Obtiene el elemento de imagen dentro de la tarjeta
  let rutaImagen = datos['Imagen'];// Obtiene la ruta de la imagen del producto desde los datos
  if (rutaImagen && !rutaImagen.startsWith('http')) {// Si la ruta de la imagen no comienza con 'http', se asume que es una ruta relativa
    rutaImagen = 'includes/backend/' + rutaImagen;// Esto asegura que la ruta sea correcta para acceder a la imagen en el servidor
  }
  img.src = rutaImagen || 'https://via.placeholder.com/100';// Si no hay una ruta de imagen válida, se usa una imagen de marcador de posición

  const info = card.querySelector('div');// Obtiene el div de información dentro de la tarjeta
  info.innerHTML = `<strong>${datos['Código'] || ''}</strong><br>${datos['Nombre'] || ''}`;// Actualiza el contenido del div de información con el código y el nombre del producto, usando etiquetas HTML para dar formato

  card.dataset.producto = JSON.stringify(datos); // ✅ esto es lo que garantiza que se use el dato actualizado
}

cerrarModalProducto.onclick = () => {// Al hacer clic en el botón de cerrar del modal, se oculta el modal
  modalProducto.style.display = 'none';// Oculta el modal de producto
};

window.onclick = (e) => {// Al hacer clic en cualquier parte de la ventana
  if (e.target == modalProducto) {// Si el clic fue en el fondo del modal
    modalProducto.style.display = "none";// Oculta el modal de producto
  }
};

function limpiarProductosDeCategorias() {// Limpia los productos de todas las categorías, dejando solo el botón de crear producto
  categorias.forEach(cat => {// Itera sobre cada categoría
    const contenedor = document.getElementById(`contenedor-${cat.nombre_categoria}`);// Obtiene el contenedor de la categoría actual
    if (contenedor) {// Si el contenedor existe
      // Elimina todo menos el botón de crear producto
      Array.from(contenedor.children).forEach(child => {// Itera sobre los hijos del contenedor
        if (!child.classList.contains('boton-crear-producto')) {// Si el hijo no es el botón de crear producto
          contenedor.removeChild(child);// Elimina el hijo del contenedor
        }
      });
    }
  });
}

function buscarProducto() {// Busca productos por código o nombre
  const valor = document.getElementById('BuscarProducto').value.trim();// Obtiene el valor del input de búsqueda y elimina espacios al inicio y al final
  if (valor === '') {// Si el valor de búsqueda está vacío
    // Si está vacío, recarga todos los productos normalmente y muestra solo la primera categoría
    fetch('includes/backend/productos_api.php')// Envía una solicitud al backend para obtener todos los productos
      .then(r => r.json())// Convierte la respuesta del servidor a JSON
      .then(data => {// Procesa la respuesta del servidor
        if (data.success) {// Si la respuesta fue exitosa
          limpiarProductosDeCategorias();// Limpia los productos de todas las categorías
          data.productos.forEach(producto => {// Itera sobre cada producto recibido
            const categoria = categorias.find(cat => cat.id == producto.categoria_id);// Busca la categoría del producto actual
            if (categoria) {// Si se encuentra la categoría del producto
              const contenedor = document.getElementById(`contenedor-${categoria.nombre_categoria}`);// Obtiene el contenedor de la categoría del producto
              if (contenedor) {// Si el contenedor existe
                crearTarjetaProducto({// Crea una tarjeta de producto con los datos del producto
                  ...producto,
                  Imagen: producto.imagen_url,
                  Código: producto.codigo_producto,
                  Nombre: producto.nombre_producto,
                  Inspiración: producto.inspiracion,
                  Casa: producto.casa,
                  Descripción: producto.descripcion,
                  Cantidad: producto.cantidad,
                  Precio: producto.precio,
                  'Precio 30ml': producto.precio_30ml,
                  'Precio 60ml': producto.precio_60ml,
                  'Precio 100ml': producto.precio_100ml,
                  'Recarga 30ml': producto.recarga_30ml,
                  'Recarga 60ml': producto.recarga_60ml,
                  'Recarga 100ml': producto.recarga_100ml,
                }, contenedor);
              }
            }
          });
          // Mostrar solo la primera categoría
          if (categorias.length > 0) {// Si hay categorías disponibles
            filtrarCategoria(categorias[0].nombre_categoria);// Muestra solo la primera categoría
          }
        }
      });
    return;// Sale de la función si el valor de búsqueda está vacío
  }

  // Si es número, busca por código. Si es texto, busca por nombre (parcial)
  const esNumero = !isNaN(valor);// Verifica si el valor de búsqueda es un número
  fetch('includes/backend/productos_api.php')// Envía una solicitud al backend para obtener todos los productos
    .then(r => r.json())// Convierte la respuesta del servidor a JSON
    .then(data => {// Procesa la respuesta del servidor
      if (data.success) {// Si la respuesta fue exitosa
        limpiarProductosDeCategorias();// Limpia los productos de todas las categorías

        let resultados = [];// Inicializa un array para almacenar los resultados de la búsqueda
        if (esNumero) {// Si el valor de búsqueda es un número
          resultados = data.productos.filter(p => String(p.codigo_producto) === valor);// Filtra los productos por código exacto
        } else {// Si el valor de búsqueda es texto
          resultados = data.productos.filter(p =>// Busca productos que contengan el texto en el nombre (ignorando mayúsculas y minúsculas)
            p.nombre_producto.toLowerCase().includes(valor.toLowerCase())// Filtra los productos por nombre (parcial)
          );
        }

        // Oculta todos los contenedores de categoría
        categorias.forEach(cat => {// Itera sobre cada categoría
          const contenedor = document.getElementById(`contenedor-${cat.nombre_categoria}`);// Obtiene el contenedor de la categoría actual
          if (contenedor) contenedor.style.display = 'none';// Oculta el contenedor de la categoría actual
        });

        // Elimina el contenedor de resultados anteriores si existe
        let contenedorResultados = document.getElementById('contenedor-resultados-busqueda');// Obtiene el contenedor de resultados de búsqueda si existe
        if (contenedorResultados) contenedorResultados.remove();// Si existe, lo elimina para evitar duplicados

        // Crea un nuevo contenedor para los resultados
        contenedorResultados = document.createElement('div');// Crea un nuevo div para mostrar los resultados de la búsqueda
        contenedorResultados.id = 'contenedor-resultados-busqueda';// Asigna un ID único al contenedor de resultados
        contenedorResultados.style.marginTop = '20px';// Añade un margen superior al contenedor de resultados
        document.getElementById('tablasPorCategoria').appendChild(contenedorResultados);// Añade el contenedor de resultados al DOM

        if (resultados.length === 0) {// Si no se encontraron productos
          contenedorResultados.innerHTML = '<div style="padding:20px; color:#d14662; font-weight:bold;">No se encontraron productos.</div>';// Muestra un mensaje indicando que no se encontraron productos
          return;// Sale de la función si no se encontraron productos
        }

        // Muestra solo los productos encontrados
        resultados.forEach(producto => {// Itera sobre cada producto encontrado
          crearTarjetaProducto({// Crea una tarjeta de producto con los datos del producto
            ...producto,
            Imagen: producto.imagen_url,
            Código: producto.codigo_producto,
            Nombre: producto.nombre_producto,
            Inspiración: producto.inspiracion,
            Casa: producto.casa,
            Descripción: producto.descripcion,
            Cantidad: producto.cantidad,
            Precio: producto.precio,
            'Precio 30ml': producto.precio_30ml,
            'Precio 60ml': producto.precio_60ml,
            'Precio 100ml': producto.precio_100ml,
            'Recarga 30ml': producto.recarga_30ml,
            'Recarga 60ml': producto.recarga_60ml,
            'Recarga 100ml': producto.recarga_100ml,
          }, contenedorResultados);// Crea una tarjeta de producto y la añade al contenedor de resultados
        });
      }
    });
}
</script>
</body>
</html>