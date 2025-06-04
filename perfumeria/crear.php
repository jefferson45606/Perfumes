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
    const categorias = ['Dama', 'Caballero'];
    fetch('includes/backend/categorias_api.php')
    .then(response => response.json())
    .then(data => {
      categorias.push(...data.map(item => item.nombre_categoria));
      mostrarCategorias();
    })
    .catch(error => {
      console.error('Error al cargar datos:', error);
    });
    const botonesCategorias = document.getElementById('botonesCategorias');
    const tablasPorCategoria = document.getElementById('tablasPorCategoria');
    const modalProducto = document.getElementById('modalProducto');
    const modalContenido = document.getElementById('modalContenido');
    const cerrarModalProducto = document.getElementById('cerrarModalProducto');
    
    function mostrarCategorias() {
      botonesCategorias.innerHTML = '';
      categorias.forEach(cat => {
        const btn = document.createElement('button');
        btn.textContent = cat;
        btn.classList.add('boton-categoria');
        btn.onclick = () => {
          document.querySelectorAll('.boton-categoria').forEach(b => b.classList.remove('activo'));
          btn.classList.add('activo');
          mostrarTablaCategoria(cat);
          filtrarCategoria(cat);
        };
        botonesCategorias.appendChild(btn);
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
        if (data.success) {
          categorias.push(nueva);
          mostrarCategorias();
          mostrarTablaCategoria(nueva);
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
        const contenedor = document.getElementById(`contenedor-${cat}`);
        if (contenedor) {
          contenedor.style.display = (cat === nombre) ? 'block' : 'none';
        }
      });
    }
    
    function mostrarTablaCategoria(nombre) {
      let contenedor = document.getElementById(`contenedor-${nombre}`);
      if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = `contenedor-${nombre}`;
        contenedor.style.marginTop = '20px';
    
        const tarjeta = document.createElement('div');
        tarjeta.textContent = '🛠️ Crea tu producto aquí';
        tarjeta.style.cursor = 'pointer';
        tarjeta.style.padding = '15px';
        tarjeta.style.backgroundColor = '#e6486f';
        tarjeta.style.color = '#fff';
        tarjeta.style.borderRadius = '10px';
        tarjeta.style.fontWeight = 'bold';
        tarjeta.style.textAlign = 'center';
        tarjeta.onmouseenter = () => tarjeta.style.backgroundColor = '#c53557';
        tarjeta.onmouseleave = () => tarjeta.style.backgroundColor = '#e6486f';
        tarjeta.onclick = () => abrirFormulario('', contenedor);
    
        contenedor.appendChild(tarjeta);
        tablasPorCategoria.appendChild(contenedor);
      }
    
      filtrarCategoria(nombre);
    }
    
    function abrirFormulario(categoria, contenedorPadre, datos = null, card = null) {
      modalContenido.innerHTML = '';
      const form = document.createElement('form');
      form.style.display = 'flex';
      form.style.flexDirection = 'column';
      form.style.gap = '10px';
    
      const campos = [
        'Código', 'Imagen', 'Nombre', 'Inspiración', 'Casa', 'Descripción',
        'Cantidad', 'Precio30', 'Precio60', 'Precio100',
        'Recarga30', 'Recarga60', 'Recarga100'
      ];
    
      const inputs = {};
    
      campos.forEach(campo => {
        const label = document.createElement('label');
        label.textContent = campo;
        const input = document.createElement('input');
        input.type = (campo === 'Cantidad' || campo.includes('Precio') || campo.includes('Recarga')) ? 'number' : 'text';
        input.name = campo;
    
        if (campo === 'Imagen') {
          input.type = 'file';
          input.accept = 'image/*';
        } else if (datos) {
          input.value = datos[campo] || '';
        }
    
        if (datos && campo !== 'Descripción' && !['Cantidad', 'Precio30', 'Precio60', 'Precio100', 'Recarga30', 'Recarga60', 'Recarga100'].includes(campo)) {
          input.disabled = true;
        }
    
        label.appendChild(input);
        form.appendChild(label);
        inputs[campo] = input;
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
    
        campos.forEach(c => {
          if (c === 'Imagen') return;
          const val = inputs[c].value;
          nuevosDatos[c] = inputs[c].type === 'number' ? parseFloat(val) || 0 : val;
        });
    
        const file = inputs['Imagen'].files[0];
        const terminar = () => {
          if (card) {
            actualizarProducto(nuevosDatos, card);
          } else {
            crearTarjetaProducto(nuevosDatos, contenedorPadre);
          }
          modalProducto.style.display = 'none';
        };
    
        if (file) {
          const reader = new FileReader();
          reader.onload = () => {
            nuevosDatos['Imagen'] = reader.result;
            terminar();
          };
          reader.readAsDataURL(file);
        } else {
          nuevosDatos['Imagen'] = datos?.['Imagen'] || '';
          terminar();
        }
      };
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
      img.src = datos['Imagen'] || 'https://via.placeholder.com/100';
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
        if (confirm("¿Eliminar este producto?")) card.remove();
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
      img.src = datos['Imagen'];
    
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
    
    mostrarCategorias();
    </script>
    
  
  
  
</body>
</html>
