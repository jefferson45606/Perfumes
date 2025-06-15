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

  
<!--comienza el script-->
<script>
let categorias = [];
let categoriaSeleccionadaId = null;

// Cargar categorías desde el backend
fetch('includes/backend/categorias_api.php')
  .then(r => r.json())
  .then(data => {
    categorias = data;
    mostrarCategorias();

    // CREA los contenedores de cada categoría
    const tablasPorCategoria = document.getElementById('tablasPorCategoria');
    tablasPorCategoria.innerHTML = ''; // <-- Limpia todo antes de crear los contenedores

    categorias.forEach(categoria => {
      mostrarTablaCategoria(categoria.nombre_categoria);
    });

    // Mostrar solo la primera categoría al inicio
    if (categorias.length > 0) {
      filtrarCategoria(categorias[0].nombre_categoria);
    }

    // CARGA los productos al inicio
    fetch('includes/backend/productos_api.php')
      .then(r => r.json())
      .then(data => { // <-- aquí estaba el error
        if (data.success) {
          limpiarProductosDeCategorias(); // Esto solo elimina las tarjetas, no el botón
          data.productos.forEach(producto => {
            const categoria = categorias.find(cat => cat.id == producto.categoria_id);
            if (categoria) {
              const contenedor = document.getElementById(`contenedor-${categoria.nombre_categoria}`);
              if (contenedor) {
                crearTarjetaProducto({
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

function mostrarCategorias() {
  const botonesCategorias = document.getElementById('botonesCategorias');
  botonesCategorias.innerHTML = '';
  categorias.forEach((cat, idx) => {
    const btn = document.createElement('button');
    btn.textContent = cat.nombre_categoria;
    btn.classList.add('boton-categoria');
    btn.dataset.id = cat.id;
    btn.onclick = () => {
      document.querySelectorAll('.boton-categoria').forEach(b => b.classList.remove('activo'));
      btn.classList.add('activo');
      categoriaSeleccionadaId = cat.id;
      filtrarCategoria(cat.nombre_categoria);
    };
    botonesCategorias.appendChild(btn);

    // Selecciona la primera categoría por defecto
    if (idx === 0) {
      btn.classList.add('activo');
      categoriaSeleccionadaId = cat.id;
    }
  });
}

function crearCategoria() {
  const input = document.getElementById('nuevaCategoria');
  const nueva = input.value.trim();

  if (!nueva || categorias.includes(nueva)) {
    alert("Categoría inválida o ya existente");
    return;
  }

  fetch('includes/backend/categoria_save.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'dato=' + encodeURIComponent(nueva)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.categoria) {
      categorias.push(data.categoria);
      mostrarCategorias();
      input.value = '';
    } else {
      alert(data.message || "Ocurrió un error al guardar la categoría.");
    }
  })
  .catch(error => {
    console.error('Error en la solicitud:', error);
    alert("Error de conexión al guardar la categoría.");
  });
}

function filtrarCategoria(nombre) {
  categorias.forEach(cat => {
    const contenedor = document.getElementById(`contenedor-${cat.nombre_categoria}`);
    if (contenedor) {
      contenedor.style.display = (cat.nombre_categoria === nombre) ? 'block' : 'none';
    }
  });
}

function mostrarTablaCategoria(nombre) {
  let contenedor = document.getElementById(`contenedor-${nombre}`);
  if (!contenedor) {
    contenedor = document.createElement('div');
    contenedor.id = `contenedor-${nombre}`;
    contenedor.style.marginTop = '20px';
    document.getElementById('tablasPorCategoria').appendChild(contenedor);
  } else {
    // Limpia el contenedor completamente antes de agregar el botón
    contenedor.innerHTML = '';
  }

  // Solo agrega el botón si no existe ya (esto ahora siempre será cierto)
  if (!contenedor.querySelector('.boton-crear-producto')) {
    const botonCrear = document.createElement('div');
    botonCrear.classList.add('boton-crear-producto');
    botonCrear.textContent = '🛠️ Crea tu producto aquí';
    botonCrear.style.cursor = 'pointer';
    botonCrear.style.padding = '15px';
    botonCrear.style.backgroundColor = '#e6486f';
    botonCrear.style.color = '#fff';
    botonCrear.style.borderRadius = '10px';
    botonCrear.style.fontWeight = 'bold';
    botonCrear.style.textAlign = 'center';
    botonCrear.onmouseenter = () => botonCrear.style.backgroundColor = '#c53557';
    botonCrear.onmouseleave = () => botonCrear.style.backgroundColor = '#e6486f';
    botonCrear.onclick = () => abrirFormulario(nombre, contenedor);

    contenedor.appendChild(botonCrear);
  }
}

function abrirFormulario(categoriaNombre, contenedorPadre, datos = null, card = null) {
  // Corregido: Actualiza la categoría seleccionada al editar
  if (datos && datos['categoria_id']) {
    categoriaSeleccionadaId = datos['categoria_id'];
  }

  modalContenido.innerHTML = '';
  const form = document.createElement('form');
  form.style.display = 'flex';
  form.style.flexDirection = 'column';
  form.style.gap = '10px';

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

  campos.forEach(campo => {
    const campoNombre = typeof campo === 'string' ? campo : campo.nombre;
    const label = document.createElement('label');
    label.textContent = campoNombre;
    if (campoNombre === 'Imagen') {
      label.classList.add('imagen-label');
    }

    const input = document.createElement('input');
    input.name = campoNombre;
    input.required = true;

    if (campoNombre === 'Imagen') {
      input.type = 'file';
      input.accept = 'image/*';
      input.required = !datos; // si estamos editando, la imagen no es obligatoria
    } else if (
      campoNombre === 'Cantidad' ||
      campoNombre === 'Precio' ||
      campoNombre === 'Precio 30ml' ||
      campoNombre === 'Precio 60ml' ||
      campoNombre === 'Precio 100ml' ||
      campoNombre === 'Recarga 30ml' ||
      campoNombre === 'Recarga 60ml' ||
      campoNombre === 'Recarga 100ml'
    ) {
      input.type = 'number';
      input.min = 0; // <-- No permite valores negativos
      input.step = "any";
    } else {
      input.type = 'text';
    }

    // Hacer el campo Código solo lectura si estamos editando
    if (campoNombre === 'Código' && datos) {
      input.readOnly = true;
      input.style.background = '#eee';
      input.style.cursor = 'not-allowed';
    }

    if (datos && campoNombre !== 'Imagen') {
      input.value = datos[campoNombre] || '';
    }

    if (campoNombre === 'Imagen' && datos && datos['Imagen']) {
      label.classList.add('imagen-label');

      // Campo oculto para manter a imagem existente se não for carregada uma nova
      const hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'ImagenExistente';
      hidden.value = datos['Imagen'];
      form.appendChild(hidden);

      // Mostrar el nombre del archivo existente despues de la entrda
      input.style.display = 'inline-block';
      const nombreArchivo = document.createElement('span');
      nombreArchivo.textContent = ' - ' + datos['Imagen'].split('/').pop();
      nombreArchivo.style.fontSize = '12px';
      nombreArchivo.style.color = '#555';

      label.appendChild(input);
      label.appendChild(nombreArchivo);
      form.appendChild(label);

      inputs[campoNombre] = input;

      return;
    }

    inputs[campoNombre] = input;

    label.appendChild(input);
    form.appendChild(label);
  });

  const btnGuardar = document.createElement('button');
  btnGuardar.type = 'submit';
  btnGuardar.textContent = 'Guardar';
  btnGuardar.style.padding = '10px';
  btnGuardar.style.backgroundColor = '#d14662';
  btnGuardar.style.color = '#fff';
  btnGuardar.style.border = 'none';
  btnGuardar.style.borderRadius = '6px';
  btnGuardar.style.cursor = 'pointer';

  form.appendChild(btnGuardar);
  modalContenido.appendChild(form);
  modalProducto.style.display = 'flex';

  form.onsubmit = (e) => {
    e.preventDefault();
    const nuevosDatos = {};

    const nombreNuevo = inputs['Nombre'].value.trim().toLowerCase();
    const codigoNuevo = inputs['Código'].value.trim();
    let nombreDuplicado = false;
    let codigoDuplicado = false;

    const productosExistentes = document.querySelectorAll('[data-producto]');
    if (!datos) { // Solo al crear, no al editar
      productosExistentes.forEach(card => {
        const prod = JSON.parse(card.dataset.producto);
        if ((prod['Nombre'] || '').trim().toLowerCase() === nombreNuevo) {
          nombreDuplicado = true;
        }
        if ((prod['Código'] || prod['codigo_producto']) === codigoNuevo) {
          codigoDuplicado = true;
        }
      });
      if (nombreDuplicado) {
        alert("Ya existe un producto con ese nombre.");
        return;
      }
      if (codigoDuplicado) {
        alert("Ya existe un producto con ese código.");
        return;
      }
    } else {
      // Validación al editar (ya la tienes)
      const codigoActual = datos['Código'] || datos['codigo_producto'];
      productosExistentes.forEach(card => {
        const prod = JSON.parse(card.dataset.producto);
        const mismoNombre = (prod['Nombre'] || '').trim().toLowerCase() === nombreNuevo;
        const esOtroProducto = (prod['Código'] || prod['codigo_producto']) !== codigoActual;
        if (mismoNombre && esOtroProducto) {
          nombreDuplicado = true;
        }
      });
      if (nombreDuplicado) {
        alert("Ya existe otro producto con ese nombre.");
        return;
      }
    }

    campos.forEach(c => {
      const campoNombre = typeof c === 'string' ? c : c.nombre;
      if (campoNombre === 'Imagen') return;
      const val = inputs[campoNombre].value.trim();
      nuevosDatos[campoNombre] = inputs[campoNombre].type === 'number' ? parseFloat(val) || 0 : val;
    });

    const fileInput = inputs['Imagen'];
    const file = fileInput.files[0];
    const formData = new FormData();

    for (const key in nuevosDatos) {
      formData.append(key, nuevosDatos[key]);
    }
    formData.append('categoria_id', categoriaSeleccionadaId);

    if (file) {
      formData.append('imagen', file);
    } else if (datos?.['Imagen']) {
      formData.append('ImagenExistente', datos['Imagen']);
    }

    formData.append('precio_30ml', inputs['Precio 30ml'].value.trim());
    formData.append('precio_60ml', inputs['Precio 60ml'].value.trim());
    formData.append('precio_100ml', inputs['Precio 100ml'].value.trim());
    formData.append('recarga_30ml', inputs['Recarga 30ml'].value.trim());
    formData.append('recarga_60ml', inputs['Recarga 60ml'].value.trim());
    formData.append('recarga_100ml', inputs['Recarga 100ml'].value.trim());

    fetch('includes/backend/producto_save.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(resultado => {
      if (resultado.success) {
        if (card) {
          actualizarProducto({ ...nuevosDatos, Imagen: resultado.imagen }, card);
        } else {
          crearTarjetaProducto({ ...nuevosDatos, Imagen: resultado.imagen }, contenedorPadre);
        }
        modalProducto.style.display = 'none';
      } else {
        alert(resultado.message || 'Error al guardar el producto.');
      }
    })
    .catch(error => {
      alert("Error de red o del servidor.");
      console.error(error);
    });
  }
}
function terminar() {
  nuevosDatos['Categoría'] = categoria; // Guardamos categoría
  if (card) {
    actualizarProducto(nuevosDatos, card);
  } else {
    crearTarjetaProducto(nuevosDatos, contenedorPadre);
  }
  guardar_producto(nuevosDatos);
  modalProducto.style.display = 'none';
}

function guardarProducto(nuevosDatos, file, callback) {
  if (file) {
    const reader = new FileReader();
    reader.onload = () => {
      nuevosDatos['Imagen'] = reader.result;
      callback(nuevosDatos);
    };
    reader.readAsDataURL(file);
  } else {
    callback(nuevosDatos);
  }
}

function crearTarjetaProducto(datos, contenedor) {
  const card = document.createElement('div');
  card.style.display = 'inline-block';
  card.style.margin = '10px';
  card.style.textAlign = 'center';
  card.style.position = 'relative';
  card.style.width = '120px';
  card.style.borderRadius = '12px';
  card.style.overflow = 'hidden';
  card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
  card.style.cursor = 'pointer';

  card.onmouseenter = () => {
    card.style.transform = 'scale(1.05)';
    card.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
  };
  card.onmouseleave = () => {
    card.style.transform = 'scale(1)';
    card.style.boxShadow = 'none';
  };

  const img = document.createElement('img');
  let rutaImagen = datos['Imagen'];
  if (rutaImagen && !rutaImagen.startsWith('http')) {
    rutaImagen = '..' + rutaImagen;
  }
  img.src = rutaImagen || 'https://via.placeholder.com/100';
  img.style.width = '100%';
  img.style.height = '120px';
  img.style.objectFit = 'contain';
  img.style.background = '#fff';

  const info = document.createElement('div');
  info.style.background = '#f4b6c2';
  info.style.padding = '8px';
  info.style.fontSize = '12px';
  info.style.borderBottomLeftRadius = '12px';
  info.style.borderBottomRightRadius = '12px';
  info.innerHTML = `<strong>${datos['Código'] || ''}</strong><br>${datos['Nombre'] || ''}`;

  const eliminarBtn = document.createElement('button');
  eliminarBtn.innerHTML = '🗑️';
  eliminarBtn.style.position = 'absolute';
  eliminarBtn.style.top = '5px';
  eliminarBtn.style.right = '5px';
  eliminarBtn.style.background = '#ff4d4d';
  eliminarBtn.style.border = 'none';
  eliminarBtn.style.color = '#fff';
  eliminarBtn.style.borderRadius = '50%';
  eliminarBtn.style.width = '28px';
  eliminarBtn.style.height = '28px';
  eliminarBtn.style.cursor = 'pointer';
  eliminarBtn.onclick = (e) => {
    e.stopPropagation();
    if (confirm("¿Eliminar este producto?")) {
      const codigo=datos['Código'] || datos['codigo_producto'];
      //elimina del servidor y luego de lavista
      fetch("includes/backend/producto_delete.php",{
        method:"POST",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'codigo_producto=' + encodeURIComponent(codigo)
      })
      .then(r => r.json())
      .then(json => {
        if (json.success){
          card.remove();

        } else {
          alert("Error al eliminar el producto: " + (json.message || ''));
        }
      })
      .catch(() => alert('Error de conexión al intentar borrar'));
    }
  };

  card.dataset.producto = JSON.stringify(datos);

  card.onclick = () => {
    const datosActuales = JSON.parse(card.dataset.producto);
    abrirFormulario('', contenedor, datosActuales, card);
  };

  card.appendChild(img);
  card.appendChild(info);
  card.appendChild(eliminarBtn);
  contenedor.appendChild(card);
}

function actualizarProducto(datos, card) {
  const img = card.querySelector('img');
  let rutaImagen = datos['Imagen'];
  if (rutaImagen && !rutaImagen.startsWith('http')) {
    rutaImagen = 'includes/backend/' + rutaImagen;
  }
  img.src = rutaImagen || 'https://via.placeholder.com/100';

  const info = card.querySelector('div');
  info.innerHTML = `<strong>${datos['Código'] || ''}</strong><br>${datos['Nombre'] || ''}`;

  card.dataset.producto = JSON.stringify(datos); // ✅ esto es lo que garantiza que se use el dato actualizado
}

cerrarModalProducto.onclick = () => {
  modalProducto.style.display = 'none';
};

window.onclick = (e) => {
  if (e.target == modalProducto) {
    modalProducto.style.display = "none";
  }
};

function limpiarProductosDeCategorias() {
  categorias.forEach(cat => {
    const contenedor = document.getElementById(`contenedor-${cat.nombre_categoria}`);
    if (contenedor) {
      // Elimina todo menos el botón de crear producto
      Array.from(contenedor.children).forEach(child => {
        if (!child.classList.contains('boton-crear-producto')) {
          contenedor.removeChild(child);
        }
      });
    }
  });
}

function buscarProducto() {
  const valor = document.getElementById('BuscarProducto').value.trim();
  if (valor === '') {
    // Si está vacío, recarga todos los productos normalmente y muestra solo la primera categoría
    fetch('includes/backend/productos_api.php')
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          limpiarProductosDeCategorias();
          data.productos.forEach(producto => {
            const categoria = categorias.find(cat => cat.id == producto.categoria_id);
            if (categoria) {
              const contenedor = document.getElementById(`contenedor-${categoria.nombre_categoria}`);
              if (contenedor) {
                crearTarjetaProducto({
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
          if (categorias.length > 0) {
            filtrarCategoria(categorias[0].nombre_categoria);
          }
        }
      });
    return;
  }

  // Si es número, busca por código. Si es texto, busca por nombre (parcial)
  const esNumero = !isNaN(valor);
  fetch('includes/backend/productos_api.php')
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        limpiarProductosDeCategorias();

        let resultados = [];
        if (esNumero) {
          resultados = data.productos.filter(p => String(p.codigo_producto) === valor);
        } else {
          resultados = data.productos.filter(p =>
            p.nombre_producto.toLowerCase().includes(valor.toLowerCase())
          );
        }

        // Oculta todos los contenedores de categoría
        categorias.forEach(cat => {
          const contenedor = document.getElementById(`contenedor-${cat.nombre_categoria}`);
          if (contenedor) contenedor.style.display = 'none';
        });

        // Elimina el contenedor de resultados anteriores si existe
        let contenedorResultados = document.getElementById('contenedor-resultados-busqueda');
        if (contenedorResultados) contenedorResultados.remove();

        // Crea un nuevo contenedor para los resultados
        contenedorResultados = document.createElement('div');
        contenedorResultados.id = 'contenedor-resultados-busqueda';
        contenedorResultados.style.marginTop = '20px';
        document.getElementById('tablasPorCategoria').appendChild(contenedorResultados);

        if (resultados.length === 0) {
          contenedorResultados.innerHTML = '<div style="padding:20px; color:#d14662; font-weight:bold;">No se encontraron productos.</div>';
          return;
        }

        // Muestra solo los productos encontrados
        resultados.forEach(producto => {
          crearTarjetaProducto({
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
          }, contenedorResultados);
        });
      }
    });
}
</script>
</body>
</html>