<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2 class="m-0 fw-bold">ADMINISTRAR PROVEEDORES</h2>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Administración</li>
                    <li class="breadcrumb-item active">Proveedores</li>
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
                        <h3 class="card-title">Listado de Proveedores</h3>
                    </div>
                    <div class="card-body">
                        <table id="proveedoresTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Detalles</th>
                                    <th>Opciones</th>
                                    <th>ID</th>
                                    <th>Tipo Documento</th>
                                    <th>RUC</th>
                                    <th>Razón Social</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    require_once "../modelos/proveedores.modelo.php";
                                    $proveedores = ProveedoresModelo::mdlObtenerProveedores($_POST);
                                    foreach ($proveedores['data'] as $proveedor) {
                                        echo "<tr>";
                                        foreach ($proveedor as $valor) {
                                            echo "<td>$valor</td>";
                                        }
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
