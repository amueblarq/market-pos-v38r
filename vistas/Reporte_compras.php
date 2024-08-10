<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gráfico de Compras por Producto</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {
            packages: ["corechart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            <?php
            // Conexión a la base de datos
            $conexion = new mysqli("localhost", "root", "", "sistema-poss");

            // Consulta SQL para obtener los datos de descripción y cantidad de la tabla detalle_compra, uniendo con la tabla productos
            $sqlDetalleCompra = "SELECT p.descripcion, SUM(dc.cantidad) AS cantidad 
                                 FROM detalle_compra dc
                                 JOIN productos p ON dc.codigo_producto = p.codigo_producto
                                 GROUP BY dc.codigo_producto";

            // Ejecutar la consulta SQL y obtener los datos
            $resultadoDetalleCompra = $conexion->query($sqlDetalleCompra);

            // Inicializar arrays para almacenar los datos
            $datos = "['Producto', 'Cantidad'],";
            // Obtener los datos de la consulta y almacenarlos en arrays
            if ($resultadoDetalleCompra->num_rows > 0) {
                while ($row = $resultadoDetalleCompra->fetch_assoc()) {
                    $datos .= "['" . $row['descripcion'] . "', " . $row['cantidad'] . "],";
                }
            }

            // Cerrar la conexión a la base de datos
            $conexion->close();
            ?>

            var data = google.visualization.arrayToDataTable([
                <?php echo $datos; ?>
            ]);

            var options = {
              title: 'GRAFICO DE COMPRAS POR PRODUCTO',
              is3D: true,
              titleTextStyle: {
              fontSize: 20, // Tamaño de fuente deseado
              },
              colors: ['#F6ADC6', '#A4C2F4', '#B6E2D5', '#FFF0B5', '#D8B4F7', '#FFD8B1', '#CED1D9', '#9FD6D2', '#FFDDC1', '#E0BBE4'], // Colores pasteles
              pieSliceTextStyle: {
              color: 'black' // Cambia el color de los porcentajes a negro
             }
     }; 

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }

       
    </script>
    <style>
        /* Estilos para centrar el gráfico en un cuadro */
        #contenedorGrafico {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh; /* Ajusta el alto según sea necesario */
        }

        /* Estilos para el cuadro del gráfico */
        #cuadroGrafico {
            width: 50%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="encabezado-imprimir">
        <img src="vistas\logo.jpg" alt="Logo">
        <p>Tegucigalpa, Honduras C.A. Avenida Roble, Centro Comercial Plaza Marie, contiguo a Pricesmart.<br>
        Teléfonos: (504) 2235.9934, 2231.0519. e-mail: manuelamuebla@yahoo.es</p>
    </div>
    <br>
    <div class="encabezado-impresion">
    <p>Compras / Fecha: <?php echo date('d/m/Y'); ?></p>
</div>
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
            font-size: 24px; /* Tamaño de fuente */
            color: #333; /* Color del texto */
        }
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
            #botonImprimir {
                display: none;
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
        /* Estilos para el contenedor principal */
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
    </style>

    <div id="contenedorGrafico">
        <div id="cuadroGrafico">
            <div id="piechart_3d" style="width: 100%; height: 500px;"></div>
        </div>
    </div>

    <!-- Botón para imprimir -->
    <button id="botonImprimir" style="position: absolute; top: 70px; left: 20%; transform: translateX(-20%); background-color: green; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;" onclick="window.print()">Imprimir Gráfico</button>
</body>
</html>