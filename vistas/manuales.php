<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos de Amueblarq</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container {
            padding: 20px;
        }
        .document-link {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #008000; /* Color de fondo para el enlace */
        }
        .document-link i {
            margin-right: 5px;
        }
        .document-link:hover {
            background-color: #005700; /* Color de fondo al pasar el ratón */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enlaces a Documentos</h1>
        <!-- Enlace a un documento Word -->
        <a class="document-link" href="documentos/MANUAL%20TECNICO%20AMUEBLARQ.docx" download>
            <i class="fas fa-file-word"></i> Descargar Manual Técnico (Word)
        </a>
        <!-- Enlace a otro documento Word -->
        <a class="document-link" href="documentos/MANUAL%20DE%20USUARIO%20AMUEBLARQ.docx" download>
            <i class="fas fa-file-word"></i> Descargar Manual de Usuario (Word)
        </a>
    </div>
</body>
</html>