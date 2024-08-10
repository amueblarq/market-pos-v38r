<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <!-- Agregar la referencia a Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #808080; /* Cambiar a color gris */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .grafico-container {
            margin-top: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            animation: fadeIn 1s ease;
        }

        h2 {
            text-align: center;
            color: #006400;
        }

        /* Estilos para el botón de impresión */
        .boton-imprimir {
            background-color: green;
            /* Color de fondo verde */
            color: white;
            /* Color de texto blanco */
            border: none;
            padding: 10px 10px;
            cursor: pointer;
            margin-bottom: 40px;
            /* Agrega un margen inferior para separar el botón de los gráficos */
        }

        /* Cambiar el color de fondo cuando se pasa el cursor sobre el botón */
         .boton-imprimir:hover {
           background-color: orange; /* Color de fondo naranja */
         }
    
        /* Estilos para ocultar los botones de impresión en la versión impresa */
        @media print {
            .boton-imprimir {
                display: none;
            }

            .grafico-container {
                page-break-before: always;
                /* Agregar salto de página antes de cada gráfico */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        
        <!-- Botón de impresión global -->
        <button class="boton-imprimir" onclick="imprimirTodos()">Imprimir Todos los Gráficos</button>
        
        <!-- Gráfico de Pedidos por Categoría -->
        <div id="graficoPedidosCategoria" class="grafico-container">
            <br>
            <br>
            <br>
            <h2>Cantidad de Pedidos por Categoría</h2>
            <button class="boton-imprimir" onclick="imprimirGrafico('graficoPedidosCategoriaCanvas', 'Pedidos_por_Categoria')">Descargar como imagen</button>
            <canvas id="graficoPedidosCategoriaCanvas" width="800" height="400"></canvas>
        </div>

        <!-- Gráfico de Pedidos por Descripción -->
        <div id="graficoPedidosDescripcion" class="grafico-container">
        <br>
            <br>
            <br>
            <h2>Cantidad de Pedidos por Descripción</h2>
            <button class="boton-imprimir" onclick="imprimirGrafico('graficoPedidosDescripcionCanvas', 'Pedidos_por_Descripcion')">Descargar como imagen</button>
            <canvas id="graficoPedidosDescripcionCanvas" width="800" height="400"></canvas>
        </div>

        <!-- Gráfico de Pedidos por Cantidad de Materiales -->
        <div id="graficoPedidosCantidad" class="grafico-container">
        <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <h2>Cantidad de Pedidos por Cantidad de Materiales</h2>
            <button class="boton-imprimir" onclick="imprimirGrafico('graficoPedidosCantidadCanvas', 'Cantidad_por_Materiales')">Descargar como imagen</button>
            <canvas id="graficoPedidosCantidadCanvas" width="800" height="400"></canvas>
        </div>
    </div>

    <script>
        // Función para mostrar todos los gráficos al cargar la página
        document.addEventListener("DOMContentLoaded", function () {
            mostrarGrafico("graficoPedidosCategoria");
            mostrarGrafico("graficoPedidosDescripcion");
            mostrarGrafico("graficoPedidosCantidad");
        });

        // Función para mostrar un gráfico específico
        function mostrarGrafico(graficoId) {
            var ctx = document.getElementById(graficoId + "Canvas").getContext("2d");

            // Definir una paleta de colores pasteles suaves
            var coloresPasteles = ['#FFD2C5', '#FFECBB', '#E1F7D5', '#C7CEEA', '#FFDDDF'];

            // Configuración y datos específicos para cada gráfico
            var etiquetas, datos, colores;

            <?php
                // Conexión a la base de datos
                $conexion = new mysqli("localhost", "root", "", "sistema-poss");

                // Verificar la conexión
                if ($conexion->connect_error) {
                    die("Error de conexión: " . $conexion->connect_error);
                }

                // Consultas SQL para obtener los datos necesarios para los gráficos
                $sql_categorias = "SELECT categoria, COUNT(*) AS cantidad FROM pedidos GROUP BY categoria";
                $result_categorias = $conexion->query($sql_categorias);

                $sql_descripciones = "SELECT descripcion, COUNT(*) AS cantidad FROM pedidos GROUP BY descripcion";
                $result_descripciones = $conexion->query($sql_descripciones);

                $sql_cantidades = "SELECT cantidad_materiales, COUNT(*) AS cantidad FROM pedidos GROUP BY cantidad_materiales";
                $result_cantidades = $conexion->query($sql_cantidades);

                // Procesar resultados y generar los datos en formato JSON
                $categorias = [];
                $cantidadesCategoria = [];
                while ($row = $result_categorias->fetch_assoc()) {
                    $categorias[] = $row['categoria'];
                    $cantidadesCategoria[] = $row['cantidad'];
                }

                $descripciones = [];
                $cantidadesDescripcion = [];
                while ($row = $result_descripciones->fetch_assoc()) {
                    $descripciones[] = $row['descripcion'];
                    $cantidadesDescripcion[] = $row['cantidad'];
                }

                $cantidadesMateriales = [];
                $cantidadesCantidad = [];
                while ($row = $result_cantidades->fetch_assoc()) {
                    $cantidadesMateriales[] = $row['cantidad_materiales'];
                    $cantidadesCantidad[] = $row['cantidad'];
                }

                // Cerrar la conexión a la base de datos
                $conexion->close();
            ?>

            if (graficoId === "graficoPedidosCategoria") {
                etiquetas = <?php echo json_encode($categorias); ?>;
                datos = <?php echo json_encode($cantidadesCategoria); ?>;
                colores = coloresPasteles.slice(0, datos.length); // Usar solo los colores necesarios
            } else if (graficoId === "graficoPedidosDescripcion") {
                etiquetas = <?php echo json_encode($descripciones); ?>;
                datos = <?php echo json_encode($cantidadesDescripcion); ?>;
                colores = coloresPasteles.slice(0, datos.length); // Usar solo los colores necesarios
            } else if (graficoId === "graficoPedidosCantidad") {
                etiquetas = <?php echo json_encode($cantidadesMateriales); ?>;
                datos = <?php echo json_encode($cantidadesCantidad); ?>;
                colores = coloresPasteles.slice(0, datos.length); // Usar solo los colores necesarios
            }

            var grafico = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: 'Cantidad de Pedidos',
                        data: datos,
                        backgroundColor: colores,
                        borderColor: colores,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

    // Función para imprimir un gráfico específico
function imprimirGrafico(graficoCanvasId, nombreArchivo) {
    var canvas = document.getElementById(graficoCanvasId);
    var imgData = canvas.toDataURL('image/png');

    // Crear un elemento 'a' para descargar la imagen como archivo
    var link = document.createElement('a');
    link.href = imgData;
    link.download = nombreArchivo + '.png'; // Utilizar el nombre del archivo proporcionado

    // Simular un clic en el enlace para iniciar la descarga
    link.click();
}
        // Función para imprimir todos los gráficos
        function imprimirTodos() {
            window.print();
        }


    </script>


     <div class="encabezado-imprimir">
        <img src="vistas\logo.jpg" alt="Logo">
        <p>Tegucigalpa, Honduras C.A. Avenida Roble, Centro Comercial Plaza Marie, contiguo a Pricesmart.<br>
        Teléfonos: (504) 2235.9934, 2231.0519. e-mail: manuelamuebla@yahoo.es</p>
    </div>
    <br>
</div>
    <br>
    <br>
    <br>
    <br>
    <br> 
    <br>
    </div>
<head>
    <style>
        /* Estilos para el h3 en el Content Header */
        .content-header h3 {
            margin: 0; /* Elimina el margen por defecto */
            padding: 10px 0; /* Añade espaciado interno */
            font-weight: bold; /* Texto en negrita */
            font-size: 26px; /* Tamaño de fuente */
            color: #333; /* Color del texto */
        }
    </style>
    <style>
.encabezado-impresion {
            display: none; /* Ocultar en la visualización normal */
        }

        @media print {
            /* Mostrar el contenedor solo en la impresión */
            .encabezado-impresion {
                display: block;
                position: fixed;
                top: 0;
                right: 0;
                left: auto;
                text-align: right;
                padding: 10px;
                background-color: #fff; /* Color de fondo */
                font-size: 14px; /* Tamaño de fuente */
                font-weight: bold; /* Negrita */
                z-index: 1000; /* Asegura que esté encima de otros elementos */
            }
        }
        @media print {
            .btn-imprimir {
                display: none; /* Oculta el botón de impresión al imprimir */
            }
        }
        .encabezado-imprimir {
            display: none;
        }
        @media print {
            .encabezado-imprimir {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                text-align: center;
                background-color: #ffffff; /* Fondo blanco para la impresión */
                padding: 10px;
                margin-bottom: 120px;
            }

            .encabezado-imprimir img {
                max-width: 200px; /* Ancho máximo del logo */
                height: auto;
                margin-bottom: 10px;
            }

            .encabezado-imprimir p {
                margin: 0;
                font-size: 12px;
            }
        }
        /* Estilos para el contenedor principal */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
    </style>
        
</body>

</html>
