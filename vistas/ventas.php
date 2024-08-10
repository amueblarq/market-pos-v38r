<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Orden de Producto</title>
    <style>
        /* Estilos para el cuadro ancho */
        .cuadro-ancho {
            width: 85%;
            margin: 0 auto;
            padding: 20px;
            background-color: #D4D4D4;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }

        h2 {
            font-weight: bold; /* Negrita */
            text-align: center;
            margin-bottom: 20px;
        }
        /* Estilo para h3 */
        h3 {
            font-size: 22px; /* Tamaño de letra más pequeño */
            margin-top: 20px; /* Espacio arriba */
            margin-bottom: 10px; /* Espacio abajo */
            color: #F18F0A; /* Color de texto naranja */
        }

        /* Estilos para botones */
        button {
            background-color: #fca311;
            color: white;
            padding: 10px 60px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            margin-left: 45%;
            transform: translateX(-50%);
        }

        button:hover {
            background-color: #ffba6b;
        }

        /* Estilos para los campos y etiquetas */
        label,
        input,
        select,
        textarea {
            display: inline-block;
            margin-bottom: 10px;
            width: 48%;
            font-weight: normal; /* Cambiar a normal */
        }

        input,
        select,
        textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Estilos para la notificación personalizada */
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #DD511D;
            color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            display: none;
        }
    </style>
</head>

<body>
    <!-- Contenedor del cuadro ancho -->
    <div class="cuadro-ancho">
        <h2>Orden de Producto</h2>
        <form id="formulario">
            <!-- Datos del Cliente -->
            <h3>Datos del Cliente</h3>
            <label for="nombre_cliente">Nombre del Cliente:</label>
            <input type="text" id="nombre_cliente">
            <label for="rtn">RTN o Identidad:</label>
            <input type="text" id="rtn" maxlength="15">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono">
            
            <!-- Datos de la Orden -->
            <h3>Datos de la Orden</h3>
            <label for="codigo">Código:</label>
            <input type="text" id="codigo" readonly>
            <label for="categoria">Categoría:</label>
            <select id="categoria" onchange="mostrarProductos()">
                <option value="" selected disabled>Seleccione una categoría</option>
                <option value="dormitorio">Dormitorio</option>
                <option value="oficina">Oficina</option>
                <option value="comedor">Comedor</option>
                <option value="cocina">Cocina</option>
                <option value="baño">Baño</option>
            </select>

            <!-- Lista de Productos -->
            <h3>Lista de Productos</h3>
            <div id="listaProductos"></div>

            <!-- Detalles de la Orden -->
            <h3>Detalles de la Orden</h3>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" rows="4"></textarea>
            <label for="fecha_pedido">Fecha de Pedido:</label>
            <input type="date" id="fecha_pedido">
            <label for="fecha_finalizacion">Fecha de Finalización:</label>
            <input type="date" id="fecha_finalizacion">

            <!-- Detalles de Pago -->
            <h3>Detalles de Pago</h3>
            <label for="precio_unitario">Precio Unitario:</label>
            <input type="number" id="precio_unitario" min="0" step="0.01" onchange="calcularCostoTotal()">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" min="1" onchange="calcularCostoTotal()">
            <label for="costo_total">Costo Total:</label>
            <input type="number" id="costo_total" readonly>

            <label for="tipo_pago">Tipo de Pago:</label>
            <select id="tipo_pago" onchange="calcularMontoAPagar()">
                <option value="" selected disabled>Seleccione un tipo de pago</option>
                <option value="efectivo">Efectivo(Abono)</option>
                <option value="tarjeta">Tarjeta(Abono)</option>
                <option value="transferencia">Transferencia(Abono)</option>
                <option value="efec completo">Efectivo(Pago Completo)</option>
                <option value="tar completa">Tarjeta(Pago Completo)</option>
                <option value="trans completa">Transferencia(Pago Completo)</option>
            </select>

            <label for="porcentaje_pago">Porcentaje a Pagar:</label>
            <input type="text" id="porcentaje_pago" readonly>

            <label for="monto_pago">Monto a Pagar:</label>
            <input type="text" id="monto_pago" readonly>

            <button type="button" onclick="guardarOrden()">Guardar Orden</button>
        </form>
    </div>

    <!-- Notificación personalizada -->
    <div id="custom-notification" class="custom-notification"></div>

    <script>
        // Función para mostrar los productos correspondientes a la categoría seleccionada
        function mostrarProductos() {
            const categoriaSeleccionada = document.getElementById("categoria").value;
            const listaProductos = document.getElementById("listaProductos");

            // Limpiamos la lista de productos
            listaProductos.innerHTML = '';

            // Agregamos los productos correspondientes a la categoría seleccionada
            const productos = {
                dormitorio: ['Puertas', 'Llamadores', 'Escritorio', 'muebles Entre', 'Librero'],
                oficina: ['Gaveta', 'llamadores', 'Repisas',
                    'Mueble Entre', 'Escritorio', 'Puertas'
                ],
                comedor: ['Bar Gaveta', 'Llamadores'],
                cocina: ['Closet principal', 'Closet 1', 'Closet 3', 'Closet De Blanco',
                    'Closet Empleado', 'Walk In Closet Principal',
                    'Walk In Closet 1', 'Walk In Closet 2',
                    'Walk In Closet 3', 'Alacena'
                ],
                baño: ['Repisa', 'Gavetero', 'Llamadores',
                    'Particiones'
                ]
            } [categoriaSeleccionada];

            if (productos) {
                // Ordenamos los productos según el orden en el formulario de administrar ventas
                const ordenProductos = {
                    dormitorio: ['Puertas', 'Llamadores', 'Escritorio', 'muebles Entre', 'Librero'],
                    oficina: ['Gaveta', 'Llamadores', 'Repisas',
                        'Mueble Entre', 'Escritorio', 'Puertas'
                    ],
                    comedor: ['Bar Gaveta', 'Llamadores'],
                    cocina: ['Closet principal', 'Closet 1', 'Closet 3', 'Closet De Blanco',
                        'Closet Empleado', 'Walk In Closet Principal',
                        'Walk In Closet 1', 'Walk In Closet 2',
                        'Walk In Closet 3', 'Alacena'
                    ],
                    baño: ['Repisa', 'Gavetero', 'Llamadores',
                        'Particiones'
                    ]
                } [categoriaSeleccionada];

                ordenProductos.forEach(producto => {
                    if (productos.includes(producto)) {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = producto;
                        checkbox.name = 'producto';
                        const label = document.createElement('label');
                        label.appendChild(checkbox);
                        label.appendChild(document.createTextNode(producto));
                        listaProductos.appendChild(label);
                        listaProductos.appendChild(document.createElement('br'));
                    }
                });
            }
        }

        function calcularCostoTotal() {
            const precioUnitario = parseFloat(document.getElementById("precio_unitario").value);
            const cantidad = parseFloat(document.getElementById("cantidad").value);
            const costoTotal = precioUnitario * cantidad;
            document.getElementById("costo_total").value = costoTotal;
        }

        function calcularMontoAPagar() {
    const tipoPago = document.getElementById("tipo_pago").value;
    let porcentajePago = 0;

    switch (tipoPago) {
        case 'efectivo':
        case 'tarjeta':
        case 'transferencia':
            porcentajePago = 0.6; // Paga el 60%
            break;
        case 'efec completo':
        case 'tar completa':
        case 'trans completa':
            porcentajePago = 1; // Pago completo
            break;
        default:
            porcentajePago = 0; // Porcentaje por defecto
            break;
    }

    document.getElementById("porcentaje_pago").value = (porcentajePago * 100) + '%';

    // Calculamos el monto a pagar
    const costoTotal = parseFloat(document.getElementById("costo_total").value);
    const montoPago = (costoTotal * porcentajePago).toFixed(2);
    document.getElementById("monto_pago").value = montoPago;
        }

        function guardarOrden() {
            const categoria = document.getElementById("categoria").value;
            const productosSeleccionados = [...document.querySelectorAll('input[name="producto"]:checked')].map(checkbox =>
                checkbox.value);
            const descripcion = document.getElementById("descripcion").value;
            const fechaPedido = document.getElementById("fecha_pedido").value;
            const fechaFinalizacion = document.getElementById("fecha_finalizacion").value;
            const costoTotal = parseFloat(document.getElementById("costo_total").value);
            const tipoPago = document.getElementById("tipo_pago").value;
            const porcentajePago = parseFloat(document.getElementById("porcentaje_pago").value) / 100;
            const montoPago = parseFloat(document.getElementById("monto_pago").value);

            // Datos del cliente
            const nombreCliente = document.getElementById("nombre_cliente").value;
            const rtn = document.getElementById("rtn").value; // Corregir aquí
            const direccion = document.getElementById("direccion").value;
            const telefono = document.getElementById("telefono").value;
            const datos = {
                categoria: categoria,
                productos: productosSeleccionados,
                descripcion: descripcion,
                fechaPedido: fechaPedido,
                fechaFinalizacion: fechaFinalizacion,
                costoTotal: costoTotal,
                tipoPago: tipoPago,
                porcentajePago: porcentajePago,
                montoPago: montoPago,
                // Agregar datos del cliente
                cliente: {
                    nombre: nombreCliente,
                    rtn: rtn,
                    direccion: direccion,
                    telefono: telefono
                }
            };

            // Enviar los datos al servidor mediante AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "guardar_orden.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Mostrar notificación
                    const notification = document.getElementById("custom-notification");
                    notification.textContent = "¡Orden guardada exitosamente y el mensaje fue enviado a su correo!";
                    notification.style.display = "block";

                    // Ocultar la notificación después de unos segundos
                    setTimeout(function() {
                        notification.style.display = "none";
                    }, 3000); // Mostrar durante 3 segundos

                    // Limpiar el formulario después de guardar la orden
                    document.getElementById("formulario").reset();
                    // Mostrar nuevamente los productos después de limpiar el formulario
                    mostrarProductos();
                }
            };
            xhr.send(JSON.stringify(datos));
        }

        // Validación para permitir solo texto con espacios y máximo 35 caracteres en el nombre del cliente
        document.getElementById("nombre_cliente").addEventListener("input", function () {
    let nombre = this.value;
    // Reemplazar cualquier caracter que no sea letra, espacio o ñ por una cadena vacía
    nombre = nombre.replace(/[^a-zA-Z\sñÑ]/g, '');
    // Limitar a 35 caracteres
    if (nombre.length > 35) {
        nombre = nombre.slice(0, 35);
    }
    this.value = nombre;
});

// Validación para que el guion sea automatico y aparezca despues del cuarto y octavo digito en el camnpo identidad
document.getElementById("rtn").addEventListener("input", function () {
    let rtn = this.value;
    // Eliminar caracteres que no sean números
    rtn = rtn.replace(/[^\d]/g, '');
    // Agregar guiones automáticamente después del cuarto y octavo dígito
    if (rtn.length > 4) {
        rtn = rtn.substring(0, 4) + '-' + rtn.substring(4);
    }
    if (rtn.length > 9) {
        rtn = rtn.substring(0, 9) + '-' + rtn.substring(9);
    }
    // Limitar a 15 caracteres
    if (rtn.length > 15) {
        rtn = rtn.substring(0, 15);
    }
    this.value = rtn;
});
// Validación para que el guion sea automatico y aparezca despues del cuarto digito en el camnpo telefono
document.getElementById("telefono").addEventListener("input", function () {
    let telefono = this.value;
    // Eliminar caracteres que no sean números
    telefono = telefono.replace(/[^\d]/g, '');
    // Agregar guion automáticamente después del cuarto dígito
    if (telefono.length > 4) {
        telefono = telefono.substring(0, 4) + '-' + telefono.substring(4);
    }
    // Limitar a 9 caracteres
    if (telefono.length > 9) {
        telefono = telefono.substring(0, 9);
    }
    this.value = telefono;
});
        function validarCantidad() {
            const cantidadInput = document.getElementById("cantidad");
            const cantidad = parseInt(cantidadInput.value);

            if (isNaN(cantidad) || cantidad < 0) {
                alert("La cantidad debe ser un número positivo.");
                cantidadInput.value = ""; // Limpiar el campo si el valor es inválido
            }
        }
        // Llama a la función validarCantidad() cuando cambie el valor del campo de cantidad
        document.getElementById("cantidad").addEventListener("change", validarCantidad);


        function validarPrecioUnitario() {
            const precioUnitarioInput = document.getElementById("precio_unitario");
            const precioUnitario = parseFloat(precioUnitarioInput.value);

            if (isNaN(precioUnitario) || precioUnitario < 0) {
                alert("El precio unitario debe ser un número positivo.");
                precioUnitarioInput.value = ""; // Limpiar el campo si el valor es inválido
            }
        }

        // Llama a la función validarPrecioUnitario() cuando cambie el valor del campo de precio unitario
        document.getElementById("precio_unitario").addEventListener("change", validarPrecioUnitario);

        // Validación para impedir seleccionar fechas anteriores a la fecha actual y días después del año actual
        const fechaPedidoInput = document.getElementById("fecha_pedido");
        const fechaFinalizacionInput = document.getElementById("fecha_finalizacion");

        const hoy = new Date();
        const dd = String(hoy.getDate()).padStart(2, '0');
        const mm = String(hoy.getMonth() + 1).padStart(2, '0'); // Enero es 0!
        const yyyy = hoy.getFullYear();

        const fechaActual = yyyy + '-' + mm + '-' + dd;

        fechaPedidoInput.setAttribute("min", fechaActual);
        fechaFinalizacionInput.setAttribute("min", fechaActual);

        const ultimoDiaAnioActual = new Date(yyyy, 11, 31).toISOString().split('T')[0];
        fechaPedidoInput.setAttribute("max", ultimoDiaAnioActual);
        fechaFinalizacionInput.setAttribute("max", ultimoDiaAnioActual);

        // Validación para impedir seleccionar una fecha de finalización anterior a la fecha de pedido
fechaPedidoInput.addEventListener("change", function () {
    fechaFinalizacionInput.value = ""; // Limpiar el campo de fecha de finalización si se cambia la fecha de pedido
    fechaFinalizacionInput.setAttribute("min", this.value); // Actualizar el valor mínimo permitido
});

fechaFinalizacionInput.addEventListener("change", function () {
    if (this.value <= fechaPedidoInput.value) {
        alert("La fecha de finalización debe ser mayor a la fecha de pedido.");
        this.value = ""; // Limpiar el campo si la fecha de finalización no es válida
    }
});
    </script>
</body>
</html>