<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pedido de Materiales</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #f0f8f7;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #006400;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        font-weight: bold;
        color: #006400;
    }

    input[type="text"],
    input[type="number"],
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    button {
        padding: 10px 20px;
        background-color: #006400;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, border 0.3s ease;
    }

    button:hover {
        background-color: #228b22;
    }

    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .btn-container input[type="submit"],
    .btn-container button {
        color: #fff;
        border: 1px solid #006400;
    }

    .btn-container input[type="submit"] {
        background-color: #228b22;
        transition: background-color 0.3s ease, border 0.3s ease;
    }

    .btn-container input[type="submit"]:hover {
        background-color: #006400;
    }

    .btn-container a {
        text-decoration: none;
        color: #fff;
        padding: 10px 20px;
        background-color: #006400;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn-container a:hover {
        background-color: #228b22;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Formulario de Pedido de Materiales</h2>


        <?php
// Verificar si se han enviado datos mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos
    $conexion = new mysqli("localhost", "root", "", "sistema-poss");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos del formulario
    $codigo_orden = $_POST['codigo_orden'];

    // Verificar si ya existe un pedido asociado al código de orden
    $sql_verificar_pedido = "SELECT COUNT(*) as total_pedidos FROM pedidosb WHERE codigo_orden = '$codigo_orden'";
    $resultado_verificar = $conexion->query($sql_verificar_pedido);

    if ($resultado_verificar && $fila_verificar = $resultado_verificar->fetch_assoc()) {
        $total_pedidos = $fila_verificar['total_pedidos'];
        if ($total_pedidos > 0) {
            echo "<script>alert('Ya existe un pedido asociado al código de orden seleccionado. No se puede registrar otro pedido.');</script>";
        } else {
            // No existe un pedido previo, continuar con el registro del pedido de materiales
            $categoria = $_POST['categoria'];
            $descripcion = $_POST['descripcion'];
            $cantidad_materiales = $_POST['cantidad_materiales'];

            // Insertar datos en la tabla 'pedidos'
            $sql_pedidos = "INSERT INTO pedidosb(codigo_orden, categoria, descripcion, cantidad_materiales) 
                            VALUES ('$codigo_orden', '$categoria', '$descripcion', $cantidad_materiales)";

            if ($conexion->query($sql_pedidosb) === TRUE) {
                // Obtener el ID del pedido recién insertado
                $pedido_id = $conexion->insert_id;

                // Insertar datos en la tabla 'materiales'
                for ($i = 1; $i <= $cantidad_materiales; $i++) {
                    $nombre_material = $_POST['material_' . $i];
                    $cantidad_material = $_POST['cantidad_' . $i];

                    $sql_materiales = "INSERT INTO materiales (pedido_id, nombre, cantidad) 
                                       VALUES ($pedido_id, '$nombre_material', $cantidad_material)";

                    if ($conexion->query($sql_materiales) !== TRUE) {
                        echo "<script>alert('Error al insertar material: " . $conexion->error . "');</script>";
                        // En caso de error, puedes decidir qué hacer aquí, por ejemplo, deshacer la inserción del pedido.
                        break;
                    }
                }

                echo "<script>alert('¡Pedido procesado exitosamente!');</script>";
            } else {
                echo "<script>alert('Error al procesar el pedido: " . $conexion->error . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Error al verificar el pedido: " . $conexion->error . "');</script>";
    }
    // Cerrar la conexión a la base de datos
    $conexion->close();
} 
?>
        <form id="pedidoForm" action="procesar_pedidob.php" method="POST" onsubmit="return validarCampos()">
            <label for="codigo_orden">Código de Orden:</label>
            <select id="codigo_orden" name="codigo_orden" required onchange="mostrarDescripcion()">
                <option value="" selected disabled>Selecciona un código de orden</option>
                <?php
            // Conectar a la base de datos
            $conexion = new mysqli("localhost", "root", "", "sistema-poss");

            // Verificar la conexión
            if ($conexion->connect_error) {
                die("Error de conexión: " . $conexion->connect_error);
            }

            // Consulta SQL para obtener los datos de la tabla 'ordenes'
            $sql = "SELECT codigo, categoria, descripcion FROM ordenes";
            $resultado = $conexion->query($sql);

            // Generar opciones para cada código de orden
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    echo "<option value='" . $row['codigo'] . "' data-categoria='" . $row['categoria'] . "' data-descripcion='" . $row['descripcion'] . "'>" . $row['codigo'] . "</option>";
                }
            } else {
                echo "<option value=''>No hay órdenes disponibles</option>";
            }

            // Cerrar la conexión a la base de datos
            $conexion->close();
        ?>
            </select>

            <label for="categoria">Categoría:</label>
            <input type="text" id="categoria" name="categoria" readonly>

            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" readonly>

            <label for="cantidad_materiales">Cantidad de Materiales Requeridos:</label>
            <input type="number" id="cantidad_materiales" name="cantidad_materiales" min="1" required pattern="\d+"
                title="Ingrese solo números enteros">

            <div id="materiales_container">
                <!-- Aquí se agregarán dinámicamente los campos de materiales -->
            </div>

            <div class="btn-container">
                <button type="button" onclick="agregarMaterial()">Agregar Material</button>
                <input type="submit" value="Enviar Pedido">
            </div>
        </form>

        <div class="btn-container">
            <a href="http://localhost/market-pos-v38/#">Regresar</a>
        </div>
    </div>

    <script>
     function mostrarDescripcion() {
        var select = document.getElementById("codigo_orden");
        var categoriaInput = document.getElementById("categoria");
        var descripcionInput = document.getElementById("descripcion");
        var cantidadMaterialesInput = document.getElementById("cantidad_materiales");
        var categoria = select.options[select.selectedIndex].getAttribute("data-categoria");
        var descripcion = select.options[select.selectedIndex].getAttribute("data-descripcion");
        categoriaInput.value = categoria;
        descripcionInput.value = descripcion;

        // Restablecer la cantidad de materiales a 1 cada vez que se cambia el código de orden
        cantidadMaterialesInput.value = 1;

        // Llamar a la función para agregar los campos de materiales
        agregarMaterial();
    }

    function agregarMaterial() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "obtener_productos.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var productos = JSON.parse(xhr.responseText);
                var cantidadMateriales = parseInt(document.getElementById('cantidad_materiales').value);
                var container = document.getElementById('materiales_container');
                container.innerHTML = '';
                if (productos.length > 0) {
                    for (var i = 1; i <= cantidadMateriales; i++) {
                        var label = document.createElement('label');
                        label.textContent = 'Material (' + i + '):';
                        var select = document.createElement('select');
                        select.name = 'material_' + i;
                        for (var j = 0; j < productos.length; j++) {
                            var option = document.createElement('option');
                            option.text = productos[j].descripcion;
                            select.add(option);
                        }
                        var br = document.createElement('br');
                        container.appendChild(label);
                        container.appendChild(select);

                        // Agregar input de cantidad
                        var cantidadInput = document.createElement('input');
                        cantidadInput.type = 'number';
                        cantidadInput.name = 'cantidad_' + i;
                        cantidadInput.placeholder = 'Cantidad';
                        cantidadInput.min = '1';
                        container.appendChild(cantidadInput);
                        container.appendChild(br);
                    }
                } else {
                    var label = document.createElement('label');
                    label.textContent = 'No hay productos disponibles';
                    container.appendChild(label);
                }
            }
        };
        xhr.send();
    }

    function mostrarError(mensaje) {
        var mensajeError = document.getElementById('mensajeError');
        mensajeError.textContent = mensaje;
    }

    // Función para enviar el pedido y validar stock
    function enviarPedido() {
        var materiales = document.querySelectorAll('[name^="material_"]');
        var cantidades = document.querySelectorAll('[name^="cantidad_"]');
        var productosSeleccionados = [];
        var cantidadesSeleccionadas = [];

        materiales.forEach(function(material, index) {
            productosSeleccionados.push(material.value);
            // Obtener la cantidad específica para este material usando el mismo índice
            var cantidadInput = document.querySelector('[name="cantidad_' + (index + 1) + '"]');
            if (cantidadInput) {
                cantidadesSeleccionadas.push(cantidadInput.value);
            }
        });

        // Objeto JSON con los productos seleccionados y cantidades
        var data = {
            productos: productosSeleccionados,
            cantidades: cantidadesSeleccionadas
        };

        // Realizar solicitud AJAX para enviar el pedido y disminuir el stock de los productos seleccionados
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "enviar_pedidob.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    // Pedido procesado correctamente
                    console.log(xhr.responseText);
                    alert('¡Pedido procesado exitosamente!');
                } else {
                    // Error al procesar el pedido, mostrar mensaje de error
                    mostrarError(xhr.responseText);
                }
            }
        };

        xhr.send(JSON.stringify(data));
    }


    function validarCampos() {
        var cantidadMateriales = parseInt(document.getElementById('cantidad_materiales').value);
        var materialesInputs = document.querySelectorAll('[name^="material_"]');
        var camposVacios = 0;

        materialesInputs.forEach(input => {
            if (input.value.trim() === '') {
                camposVacios++;
            }
        });

        if (camposVacios > 0) {
            alert('Por favor, completa todos los campos de material requeridos.');
            return false;
        }
    }
    </script>
</body>

</html>