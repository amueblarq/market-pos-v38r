<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2 class="m-0 fw-bold">ADMINISTRAR CLIENTES</h2>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Administración</li>
                    <li class="breadcrumb-item active">Clientes</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.content-header -->

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Clientes</h3>
                    </div>
                    <div class="card-body">
                        <table id="clientesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>rtn</th>
                                    <th>Nombres / Apellidos / Razón Social</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Incluir el archivo de modelo de clientes
                                    require_once "../modelos/clientes.modelo.php";
                                    // Obtener los clientes desde el modelo
                                    $clientes = ClientesModelo::obtenerClientes();
                                    // Iterar sobre los clientes y mostrarlos en la tabla
                                    foreach ($clientes as $cliente) {
                                        echo "<tr>";
                                        echo "<td>" . $cliente['id'] . "</td>";
                                        echo "<td>" . $cliente['rtn'] . "</td>";
                                        echo "<td>" . $cliente['nombres_apellidos_razon_social'] . "</td>";
                                        echo "<td>" . $cliente['direccion'] . "</td>";
                                        echo "<td>" . $cliente['telefono'] . "</td>";
                                        echo "<td>" . ($cliente['estado'] == 1 ? 'Activo' : 'Inactivo') . "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
