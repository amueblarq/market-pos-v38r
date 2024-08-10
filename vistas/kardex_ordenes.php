<?php
    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "sistema-poss");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Consulta para obtener la cantidad de cada producto en órdenes en proceso
    $sqlProductosProceso = "SELECT op.producto, COUNT(*) AS cantidad 
                            FROM ordenes o
                            LEFT JOIN orden_producto op ON o.id = op.orden_id
                            WHERE o.estado = 'En proceso'
                            GROUP BY op.producto";
    $resultadoProductosProceso = $conexion->query($sqlProductosProceso);

    $productosProceso = [];
    $cantidadesProceso = [];
    $coloresProceso = []; // Array para almacenar colores

    // Definir colores de manera dinámica
    $coloresDisponibles = [
        "#FFA8C6", "#6EB8E5", "#FFD78C", "#FFB886", "#7FFF7F", "#A386FF", "#FFA8D1", "#7FFF9F", "#66FF66", "#FFA8D8",
        "#FFB3C2", "#7FCCE5", "#FFDF99", "#FFC6A8", "#8FFF8F", "#B3A8FF", "#FFB3CC", "#8FFF9F", "#74FF74", "#FFB3D6",
        "#FFB8C4", "#85CCE8", "#FFE3B3", "#FFCCAF", "#9CFF9C", "#C8AEFF", "#FFB8D1", "#9CFFAF", "#7AFF7A", "#FFB8DC",
        "#FFC2CC", "#96D4EB", "#FFE8BF", "#FFD3C2", "#A9FFA9", "#D0B3FF", "#FFC2DB", "#A9FFC0", "#8CFF8C", "#FFC2E2",
        "#FFCCCF", "#ABDBF0", "#FFF1D2", "#FFDBD5", "#B7FFB7", "#D9C1FF", "#FFCCDD", "#B7FFC0", "#A0FFA0", "#FFCCED"
    ];
    
    // Obtener los datos de la consulta
    if ($resultadoProductosProceso->num_rows > 0) {
        $contadorColores = 0;
        while ($row = $resultadoProductosProceso->fetch_assoc()) {
            $productosProceso[] = $row['producto'];
            $cantidadesProceso[] = $row['cantidad'];
            // Asignar un color a cada producto
            $coloresProceso[] = $coloresDisponibles[$contadorColores % count($coloresDisponibles)];
            $contadorColores++;
        }
    }

    // Consulta para obtener la cantidad de cada producto en órdenes finalizadas
    $sqlProductosFinalizados = "SELECT op.producto, COUNT(*) AS cantidad 
                                FROM ordenes o
                                LEFT JOIN orden_producto op ON o.id = op.orden_id
                                WHERE o.estado = 'Finalizada'
                                GROUP BY op.producto";
    $resultadoProductosFinalizados = $conexion->query($sqlProductosFinalizados);

    $productosFinalizados = [];
    $cantidadesFinalizados = [];
    $coloresFinalizados = []; // Array para almacenar colores

    // Obtener los datos de la consulta
    if ($resultadoProductosFinalizados->num_rows > 0) {
        $contadorColores = 0;
        while ($row = $resultadoProductosFinalizados->fetch_assoc()) {
            $productosFinalizados[] = $row['producto'];
            $cantidadesFinalizados[] = $row['cantidad'];
            // Asignar un color a cada producto
            $coloresFinalizados[] = $coloresDisponibles[$contadorColores % count($coloresDisponibles)];
            $contadorColores++;
        }
    }

    // Consulta para obtener las ventas por categoría
    $sqlVentasCategoria = "SELECT categoria, COUNT(*) AS total_ventas FROM ordenes GROUP BY categoria";
    $resultadoVentasCategoria = $conexion->query($sqlVentasCategoria);

    $categorias = [];
    $ventasPorCategoria = [];

    // Obtener los datos de la consulta
    if ($resultadoVentasCategoria->num_rows > 0) {
        while ($row = $resultadoVentasCategoria->fetch_assoc()) {
            $categorias[] = $row['categoria'];
            $ventasPorCategoria[] = $row['total_ventas'];
        }
    }

    // Consulta para obtener las ventas por mes de órdenes finalizadas
    $sqlVentasPorMes = "SELECT MONTH(fecha_pedido) AS mes, COUNT(*) AS total_ventas
                        FROM ordenes
                        WHERE estado = 'Finalizada' AND YEAR(fecha_pedido) = YEAR(CURRENT_DATE())
                        GROUP BY MONTH(fecha_pedido)";
    $resultadoVentasPorMes = $conexion->query($sqlVentasPorMes);

    $meses = [];
    $ventasPorMes = [];

    // Obtener los datos de la consulta
    if ($resultadoVentasPorMes->num_rows > 0) {
        while ($row = $resultadoVentasPorMes->fetch_assoc()) {
            $meses[] = "Mes " . $row['mes']; // Agregar "Mes" para visualización
            $ventasPorMes[] = $row['total_ventas'];
        }
    }

    // Consulta para obtener los 5 productos más vendidos
    $sqlTopProductos = "SELECT producto, COUNT(*) AS total_ventas
                        FROM orden_producto
                        GROUP BY producto
                        ORDER BY total_ventas DESC
                        LIMIT 5";
    $resultadoTopProductos = $conexion->query($sqlTopProductos);

    $productosTop = [];
    $cantidadesTop = [];
    $coloresTop = []; // Array para almacenar colores

    // Definir colores de manera dinámica
    $coloresDisponibles = [
        "#FFA8C6", "#6EB8E5", "#FFD78C", "#FFB886", "#7FFF7F", "#A386FF", "#FFA8D1", "#7FFF9F", "#66FF66", "#FFA8D8",
        "#FFB3C2", "#7FCCE5", "#FFDF99", "#FFC6A8", "#8FFF8F", "#B3A8FF", "#FFB3CC", "#8FFF9F", "#74FF74", "#FFB3D6",
        "#FFB8C4", "#85CCE8", "#FFE3B3", "#FFCCAF", "#9CFF9C", "#C8AEFF", "#FFB8D1", "#9CFFAF", "#7AFF7A", "#FFB8DC",
        "#FFC2CC", "#96D4EB", "#FFE8BF", "#FFD3C2", "#A9FFA9", "#D0B3FF", "#FFC2DB", "#A9FFC0", "#8CFF8C", "#FFC2E2",
        "#FFCCCF", "#ABDBF0", "#FFF1D2", "#FFDBD5", "#B7FFB7", "#D9C1FF", "#FFCCDD", "#B7FFC0", "#A0FFA0", "#FFCCED"
    ];
    

    // Obtener los datos de la consulta
    if ($resultadoTopProductos->num_rows > 0) {
        $contadorColores = 0;
        while ($row = $resultadoTopProductos->fetch_assoc()) {
            $productosTop[] = $row['producto'];
            $cantidadesTop[] = $row['total_ventas'];
            // Asignar un color a cada producto
            $coloresTop[] = $coloresDisponibles[$contadorColores % count($coloresDisponibles)];
            $contadorColores++;
        }
    }

    // Consulta para obtener los datos
  // Consulta para obtener el total de clientes
$sqlClientes = "SELECT COUNT(DISTINCT cliente_id) AS total_clientes FROM ordenes";
$resultadoClientes = $conexion->query($sqlClientes);
$totalClientes = $resultadoClientes->fetch_assoc()['total_clientes'];

// Consulta para obtener el total de órdenes
$sqlOrdenes = "SELECT COUNT(*) AS total_ordenes FROM ordenes";
$resultadoOrdenes = $conexion->query($sqlOrdenes);
$totalOrdenes = $resultadoOrdenes->fetch_assoc()['total_ordenes'];

// Consulta para obtener el total de órdenes en proceso
$sqlOrdenesProceso = "SELECT COUNT(*) AS total_ordenes_proceso FROM ordenes WHERE estado = 'En proceso'";
$resultadoOrdenesProceso = $conexion->query($sqlOrdenesProceso);
$totalOrdenesProceso = $resultadoOrdenesProceso->fetch_assoc()['total_ordenes_proceso'];

// Consulta para obtener el total de órdenes finalizadas
$sqlOrdenesFinalizadas = "SELECT COUNT(*) AS total_ordenes_finalizadas FROM ordenes WHERE estado = 'Finalizada'";
$resultadoOrdenesFinalizadas = $conexion->query($sqlOrdenesFinalizadas);
$totalOrdenesFinalizadas = $resultadoOrdenesFinalizadas->fetch_assoc()['total_ordenes_finalizadas'];

// Consulta para obtener el ingreso total solo de órdenes finalizadas
$sqlIngresoTotalFinalizadas = "SELECT SUM(costo_total) AS total_ingreso_finalizadas FROM ordenes WHERE estado = 'Finalizada'";
$resultadoIngresoTotalFinalizadas = $conexion->query($sqlIngresoTotalFinalizadas);
$totalIngresoFinalizadas = $resultadoIngresoTotalFinalizadas->fetch_assoc()['total_ingreso_finalizadas'];

    // Cerrar la conexión a la base de datos
    $conexion->close();
    ?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            .btn-imprimir-completo {
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
        /* Estilos globales */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Estilos globales y estilos de los contenedores de gráficos */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-top: 20px;
        }
        /* Estilos para las cajas de indicadores */
        .indicador {
            width: calc(20% - 20px);
            min-width: 150px; /* Reducción del tamaño mínimo */
            height: 120px; /* Reducción del alto */
            border-radius: 10px;
            padding: 10px; /* Reducción del padding */
            box-sizing: border-box;
            text-align: center;
            margin-bottom: 10px; /* Reducción de la separación */
            background-color: #f5f5f5;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1); /* Reducción de la sombra */
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .indicador i {
            font-size: 36px; /* Reducción del tamaño del icono */
            margin-bottom: 5px; /* Reducción del espacio del icono */
            color: #333;
        }
        .indicador h4 {
            font-weight: bold;
            font-size: 14px; /* Reducción del tamaño del título */
            margin-bottom: 3px; /* Reducción del espacio del título */
            color: #333;
        }
        .indicador p {
            font-size: 18px; /* Reducción del tamaño del número */
            font-weight: bold;
            color: #333;
        }
        /* Estilos para los contenedores de gráficos */
        .chart-container {
            width: calc(50% - 20px);
            min-width: 300px;
            height: 400px;
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative; /* Agregamos posición relativa para posicionar el botón */
        }
        /* Estilos para el botón dentro del contenedor de gráficos */
        .chart-container .btn-imprimir {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 100;
            padding: 8px 16px;
            font-size: 14px;
            background-color: #fd7e14;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Estilos para el botón al pasar el cursor */
        .chart-container .btn-imprimir:hover {
            background-color: #45a049;
        }
        /* Estilos para el botón */
        .btn-imprimir-completo {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #fd7e14;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Estilos para el botón al pasar el cursor */
        .btn-imprimir:hover {
            background-color: #45a049;
        }
        /* Estilos para el contenedor del gráfico */
        .chart-container-ventas {
            position: relative;
            width: 50%;
            min-width: 300px;
            height: 400px;
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
        }

        /* Estilos para el botón de imprimir dentro del contenedor */
        .chart-container-ventas .btn-imprimir-ventas {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 8px; /* Reducir el padding del botón */
            font-size: 12px; /* Reducir el tamaño de fuente del botón */
            background-color: #fd7e14;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Estilos para el botón al pasar el cursor */
        .chart-container-ventas .btn-imprimir-ventas:hover {
            background-color: #45a049;
        }
        /* Estilos para los botones de impresión solo en la impresión */
    @media print {
        .btn-imprimir,
        .btn-imprimir-ventas,
        .btn-imprimir-completo {
            display: none; /* Ocultar los botones de imprimir al imprimir */
        }
    }
    </style>
</head>
<body>
<div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- Aplicación de estilos al h3 -->
                    <h3 class="m-0 fw-bold">KARDEX-ORDENES</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Ordenes</li>
                        <li class="breadcrumb-item"> Kardex-Ordenes</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div><!-- /.content-header -->
    <div>
    <!-- Contenedor del encabezado solo para impresión -->
    <div class="encabezado-imprimir">
        <img src="vistas\logo.jpg" alt="Logo">
        <p>Tegucigalpa, Honduras C.A. Avenida Roble, Centro Comercial Plaza Marie, contiguo a Pricesmart.<br>
        Teléfonos: (504) 2235.9934, 2231.0519. e-mail: manuelamuebla@yahoo.es</p>
    </div>
    <br>
    <div class="encabezado-impresion">
    <p>KARDEX-ORDENES / Fecha: <?php echo date('d/m/Y'); ?></p>
</div>
    <br>
    <br>
    <br>
    <br>
    </div>
    <div class="container">
        <!-- Indicadores -->
        <div class="indicador" style="background-color: #79DFFD;">
            <i class="fas fa-users"></i>
            <h4>Total Clientes</h4>
            <p><?php echo $totalClientes; ?></p>
        </div>
        <div class="indicador" style="background-color: #7197FE;">
            <i class="fas fa-clipboard-list"></i>
            <h4>Total Órdenes</h4>
            <p><?php echo $totalOrdenes; ?></p>
        </div>
        <div class="indicador" style="background-color: #F9F34E;">
            <i class="fas fa-cog"></i>
            <h4>Órdenes en Proceso</h4>
            <p><?php echo $totalOrdenesProceso; ?></p>
        </div>
        <div class="indicador" style="background-color: #FC7575;">
            <i class="fas fa-check-circle"></i>
            <h4>Órdenes Finalizadas</h4>
            <p><?php echo $totalOrdenesFinalizadas; ?></p>
        </div>
        <div class="indicador" style="background-color: #63F677;">
            <i class="fas fa-money-bill-wave-alt"></i>
            <h4>Ingreso Total Órdenes Finalizadas</h4>
            <p>L <?php echo number_format($totalIngresoFinalizadas, 2); ?></p>
        </div>
        <!-- Gráficos -->
        <div class="chart-container">
            <h3 style="text-align: center;">Órdenes en proceso</h3>
            <canvas id="graficoProductosProceso"></canvas>
            <button class="btn-imprimir" onclick="imprimirGrafico('graficoProductosProceso')">Imprimir</button>
        </div>
        <div class="chart-container">
            <h3 style="text-align: center;">Órdenes finalizadas</h3>
            <canvas id="graficoProductosFinalizados"></canvas>
            <button class="btn-imprimir" onclick="imprimirGrafico('graficoProductosFinalizados')">Imprimir</button>
        </div>
        <div class="chart-container">
            <h3 style="text-align: center;">Los 5 Productos más vendidos</h3>
            <canvas id="graficoTopProductos"></canvas>
            <button class="btn-imprimir" onclick="imprimirGrafico('graficoTopProductos')">Imprimir</button>
        </div>
        <div class="chart-container">
            <h3 style="text-align: center;">Ventas por mes</h3>
            <canvas id="graficoVentasPorMes"></canvas>
            <button class="btn-imprimir" onclick="imprimirGrafico('graficoVentasPorMes')">Imprimir</button>
        </div>
        <div class="chart-container-ventas" id="ventasPorCategoriaContainer">
        <h3 style="text-align: center;">Ventas por categoría</h3>
        <canvas id="graficoVentasCategoria"></canvas>
        <!-- Botón de imprimir dentro del contenedor -->
        <button class="btn-imprimir-ventas" onclick="imprimirGrafico('graficoVentasCategoria')">Imprimir Gráfico</button>
    </div>
    </div>
    <!-- Botón de impresión -->
    <button class="btn-imprimir-completo" id="btnImprimir-completo">Imprimir</button>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Función para imprimir el gráfico correspondiente
        function imprimirGrafico(canvasId) {
            var canvas = document.getElementById(canvasId); // Obtener el canvas del gráfico correspondiente
            var imgData = canvas.toDataURL('image/png'); // Obtener los datos de la imagen en formato PNG

            // Crear un objeto de imagen
            var img = new Image();
            img.src = imgData;

            // Abrir una nueva ventana y agregar la imagen
            var ventana = window.open('', '_blank');
            ventana.document.write('<html><head><title>Gráfico</title></head><body>');
            ventana.document.write('<img src="' + img.src + '" style="max-width: 100%;">');
            ventana.document.write('</body></html>');

            // Esperar a que la imagen se cargue antes de imprimir
            img.onload = function() {
                ventana.print(); // Imprimir la imagen
                ventana.close(); // Cerrar la ventana después de imprimir
            };
        }
    </script>
</body>

<script>
        var productosProceso = <?php echo json_encode($productosProceso); ?>;
        var cantidadesProceso = <?php echo json_encode($cantidadesProceso); ?>;
        var coloresProceso = <?php echo json_encode($coloresProceso); ?>;

        var productosFinalizados = <?php echo json_encode($productosFinalizados); ?>;
        var cantidadesFinalizados = <?php echo json_encode($cantidadesFinalizados); ?>;
        var coloresFinalizados = <?php echo json_encode($coloresFinalizados); ?>;

        var productosTop = <?php echo json_encode($productosTop); ?>;
        var cantidadesTop = <?php echo json_encode($cantidadesTop); ?>;
        var coloresTop = <?php echo json_encode($coloresTop); ?>;

        var categorias = <?php echo json_encode($categorias); ?>;
        var ventasPorCategoria = <?php echo json_encode($ventasPorCategoria); ?>;

        var meses = <?php echo json_encode($meses); ?>;
        var ventasPorMes = <?php echo json_encode($ventasPorMes); ?>;
         // Paleta de colores para cada mes
         var coloresMeses = ["#FFA8C6", "#6EB8E5", "#FFD78C", "#FFB886", "#7FFF7F", "#A386FF", "#FFA8D1", "#7FFF9F", "#66FF66", "#FFA8D8", "#FFB3C2", "#7FCCE5"];

         var coloresPorCategoria = [
    "#FFA8C6", "#6EB8E5", "#FFD78C", "#FFB886", "#7FFF7F", 
    "#A386FF", "#FFA8D1", "#7FFF9F", "#66FF66", "#FFA8D8"
    // Puedes agregar más colores según la cantidad de categorías
];

         var ctxProceso = document.getElementById('graficoProductosProceso').getContext('2d');
var ctxFinalizados = document.getElementById('graficoProductosFinalizados').getContext('2d');
var ctxTopProductos = document.getElementById('graficoTopProductos').getContext('2d');
var ctxVentasPorMes = document.getElementById('graficoVentasPorMes').getContext('2d');
var ctxVentasCategoria = document.getElementById('graficoVentasCategoria').getContext('2d');


        

var graficoProductosProceso = new Chart(ctxProceso, {
    type: 'pie',
    data: {
        labels: productosProceso.map((producto, index) => `${producto} (${cantidadesProceso[index]})`), // Agregar cantidades a las etiquetas
        datasets: [{
            label: 'Cantidad',
            data: cantidadesProceso,
            backgroundColor: coloresProceso,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});


var graficoProductosFinalizados = new Chart(ctxFinalizados, {
    type: 'pie',
    data: {
        labels: productosFinalizados.map((producto, index) => `${producto} (${cantidadesFinalizados[index]})`), // Agregar cantidades a las etiquetas
        datasets: [{
            label: 'Cantidad',
            data: cantidadesFinalizados,
            backgroundColor: coloresFinalizados,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});


var graficoTopProductos = new Chart(ctxTopProductos, {
    type: 'pie',
    data: {
        labels: productosTop.map((producto, index) => `${producto} (${cantidadesTop[index]})`), // Agregar cantidades a las etiquetas
        datasets: [{
            label: 'Cantidad',
            data: cantidadesTop,
            backgroundColor: coloresTop,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});


var graficoVentasCategoria = new Chart(ctxVentasCategoria, {
    type: 'bar',
    data: {
        labels: categorias.map((categoria, index) => `${categoria} (${ventasPorCategoria[index]})`), // Agregar cantidades a las etiquetas
        datasets: [{
            label: 'Ventas por Categoría',
            data: ventasPorCategoria,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});

        var graficoVentasPorMes = new Chart(ctxVentasPorMes, {
    type: 'pie',
    data: {
        labels: meses.map((mes, index) => `${mes} (${ventasPorMes[index]})`), // Agregar cantidades a las etiquetas
        datasets: [{
            label: 'Ventas por Mes',
            data: ventasPorMes,
            backgroundColor: coloresMeses.slice(0, meses.length),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

        
        // Obtener el botón por su clase
      var btnImprimir = document.querySelector('.btn-imprimir-completo');

        // Agregar un evento de clic al botón
       btnImprimir.addEventListener('click', function () {
            // Utilizar la función de impresión al hacer clic en el botón
          window.print();
       });
    </script>
</html>
