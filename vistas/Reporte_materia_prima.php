<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gráficos de Productos y Categorías</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <style>
    /* Estilos para los contenedores de los gráficos */
    .grafico-container {
            width: calc(100% - 30px); /* Ocupa la mitad del ancho del contenedor principal con espacio entre ellos */
            height: 500px;
            border: 4px solid #ccc;
            padding: 10px;
            box-sizing: border-box;
            margin-right: 30px; /* Agrega un margen a la derecha para separar los gráficos */
        }
    
    /* Estilos para el botón de impresión */
    .boton-imprimir {
        background-color: green; /* Color de fondo verde */
        color: white; /* Color de texto blanco */
        border: none;
        padding: 20px 20px;
        cursor: pointer;
        margin-bottom: 40px; /* Agrega un margen inferior para separar el botón de los gráficos */
    }
    
    /* Estilos para ocultar los botones de impresión en la versión impresa */
    @media print {
        .boton-imprimir {
            display: none;
        }
    }
    /* Estilos para el contenedor principal */
    .container {
            display: flex; /* Utiliza flexbox para alinear los elementos en una fila */
            flex-wrap: wrap; /* Permite que los elementos se envuelvan en líneas adicionales si es necesario */
        }
</style>

</head>
<body>
    <!-- Botón de impresión -->
    <button class="boton-imprimir" onclick="imprimirGraficos()">Imprimir Gráficos</button>

    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    
    <!-- Contenedor principal para los gráficos -->
    <div class="container">
        <!-- Contenedor del gráfico de productos -->
        <div class="grafico-container">
            <canvas id="graficoProductos"></canvas>
        </div>

    <!-- Contenedor del gráfico de categorías -->
    <div class="grafico-container">
        <canvas id="graficoCategorias"></canvas>
    </div>

    <?php
    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "sistema-poss");

    // Consulta SQL para obtener los datos de descripción y precio_unitario_sin_igv de la tabla de productos
    $sqlProductos = "SELECT descripcion, precio_unitario_sin_igv FROM productos";

    // Ejecutar la consulta SQL y obtener los datos de productos
    $resultadoProductos = $conexion->query($sqlProductos);

    // Inicializar arrays para almacenar los datos de productos
    $descripciones = [];
    $preciosUnitarios = [];

    // Obtener los datos de la consulta y almacenarlos en arrays de productos
    if ($resultadoProductos->num_rows > 0) {
        while ($row = $resultadoProductos->fetch_assoc()) {
            $descripciones[] = $row['descripcion'];
            $preciosUnitarios[] = $row['precio_unitario_sin_igv'];
        }
    }

    // Consulta SQL para obtener los datos de descripción y estado de la tabla de categorías
    $sqlCategorias = "SELECT descripcion, estado FROM categorias";

    // Ejecutar la consulta SQL y obtener los datos de categorías
    $resultadoCategorias = $conexion->query($sqlCategorias);

    // Inicializar arrays para almacenar los datos de categorías
    $descripcionesCategorias = [];
    $estadosCategorias = [];

    // Contadores para categorías activas e inactivas
    $activas = 0;
    $inactivas = 0;

    // Obtener los datos de la consulta y almacenarlos en arrays de categorías
    if ($resultadoCategorias->num_rows > 0) {
        while ($row = $resultadoCategorias->fetch_assoc()) {
            $descripcionesCategorias[] = $row['descripcion'];
            $estadosCategorias[] = $row['estado'];
            // Contar categorías activas e inactivas
            if ($row['estado'] == 1) {
                $activas++;
            } else {
                $inactivas++;
            }
        }
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
    ?>

    <script>
       // Función para imprimir los gráficos
       function imprimirGraficos() {
            window.print();
        }

        // Datos para el gráfico de productos
        var datosProductos = {
            labels: <?php echo json_encode($descripciones); ?>,
            datasets: [{
                label: 'Materia prima por precio unitario',
                data: <?php echo json_encode($preciosUnitarios); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        // Datos para el gráfico de categorías
        var datosCategorias = {
            labels: ['Activas', 'Inactivas'],
            datasets: [{
                label: 'Estado de Categorías',
                data: [<?php echo $activas; ?>, <?php echo $inactivas; ?>],
                backgroundColor: ['#63f677', '#fc7575']
            }]
        };

        // Opciones para los gráficos
       // Opciones para los gráficos
       // Opciones para los gráficos
var opciones = {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
    },
    plugins: {
        datalabels: {
            anchor: 'end', // posición de la etiqueta (final de la barra)
            align: 'top', // alineación de la etiqueta (arriba de la barra)
            color: '#000', // color del texto
            font: {
                weight: 'bold' // peso de la fuente
            },
            formatter: function(value, context) {
                // Formato personalizado para mostrar el valor en porcentaje
                return (value * 100).toFixed(2) + '%';
            }
        }
    }
};


    
        // Obtener el contexto del lienzo para el gráfico de productos
        var contextoProductos = document.getElementById('graficoProductos').getContext('2d');

        // Crear el gráfico de barras para productos
        var graficoProductos = new Chart(contextoProductos, {
            type: 'bar',
            data: datosProductos,
            options: opciones
        });

        // Obtener el contexto del lienzo para el gráfico de categorías
        var contextoCategorias = document.getElementById('graficoCategorias').getContext('2d');

        // Crear el gráfico de barras para categorías
        var graficoCategorias = new Chart(contextoCategorias, {
            type: 'bar',
            data: datosCategorias,
            options: opciones
        });
        
    </script>
     <div class="encabezado-imprimir">
        <img src="vistas\logo.jpg" alt="Logo">
        <p>Tegucigalpa, Honduras C.A. Avenida Roble, Centro Comercial Plaza Marie, contiguo a Pricesmart.<br>
        Teléfonos: (504) 2235.9934, 2231.0519. e-mail: manuelamuebla@yahoo.es</p>
    </div>
    <br>
    <div class="encabezado-impresion">
    <p>Materia Prima / Fecha: <?php echo date('d/m/Y'); ?></p>
</div>
    <br>
    <br>
    <br>
    <br>
    </div>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Header Style</title>
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