<?php

$menuUsuario = [];

// Verificar si el índice "usuario" está definido en $_SESSION
if (isset($_SESSION["usuario"])) {
    // El índice "usuario" está definido, entonces podemos acceder a sus propiedades
    $menuUsuario = UsuarioModelo::mdlObtenerMenuUsuario($_SESSION["usuario"]->id_usuario);
}

?>



<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
         <img src="vistas/assets/dist/img/Adminlogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
         <span class="brand-text font-weight-light">AMUEBLARQ</span>
     </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="vistas/assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <?php if (isset($_SESSION["usuario"])) : ?>
                    <h6 class="text-warning"><?php echo $_SESSION["usuario"]->nombre_usuario . ' ' . $_SESSION["usuario"]->apellido_usuario ?></h6>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <!-- Colores de la barra y demas... -->
            <style>
                [class*="sidebar-dark-"] {
                    background-color: #28a745 !important;
                }

                [class*=sidebar-dark-] .sidebar a {
                    color: #fff;
                }

                .card-gray:not(.card-outline)>.card-header {
                    background-color: #28a745 !important;
                }

                .bg-main {
                    background-color: #28a745 !important;
                    color: white !important;
                }

                .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
                .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
                    background-color: #f39c12;
                    color: #fff;
                }

                .card-primary.card-outline-tabs>.card-header a.active {
                    border-top: 3px solid #fd7e14;
                }

                .card-primary.card-outline-tabs>.card-header a.active {
                    background: #28a745 !important;
                    color: white !important;
                }

                table.dataTable thead tr>.dtfc-fixed-left,
                table.dataTable thead tr>.dtfc-fixed-right,
                table.dataTable tfoot tr>.dtfc-fixed-left,
                table.dataTable tfoot tr>.dtfc-fixed-right {
                    background-color: #28a745;
                    color: yellow;
                }

                .bg-success {
                    background-color: #3498db!important;
                }
            </style>


            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">

                <?php foreach ($menuUsuario as $menu) : ?>
                    <li class="nav-item <?php if ($menu->abrir_arbol == 1) : ?> <?php echo ' menu-is-opening menu-open'; ?> <?php endif; ?>">
                        <a style="cursor: pointer;" class="nav-link <?php if ($menu->vista_inicio == 1) : ?>
                                                <?php echo 'active'; ?>
                                            <?php endif; ?>" <?php if (!empty($menu->vista)) : ?> onclick="CargarContenido('vistas/<?php echo $menu->vista; ?>','content-wrapper')" <?php endif; ?>>
                            <i class="nav-icon <?php echo $menu->icon_menu; ?>"></i>
                            <p>
                                <?php echo $menu->modulo ?>
                                <?php if (empty($menu->vista)) : ?>
                                    <i class="right fas fa-angle-left"></i>
                                <?php endif; ?>
                            </p>
                        </a>

                        <?php if (empty($menu->vista)) : ?>

                            <?php
                            $subMenuUsuario = UsuarioModelo::mdlObtenerSubMenuUsuario($menu->id, $_SESSION["usuario"]->id_usuario);
                            ?>

                            <ul class="nav nav-treeview">

                                <?php foreach ($subMenuUsuario as $subMenu) : ?>

                                    <li class="nav-item">
                                        <a style="cursor: pointer;" class="nav-link <?php if ($subMenu->vista_inicio == 1) : ?>
                                                <?php echo 'active '; ?>
                                            <?php endif; ?>" onclick="CargarContenido('vistas/<?php echo $subMenu->vista ?>','content-wrapper')">
                                            <i class="<?php echo $subMenu->icon_menu; ?> nav-icon"></i>
                                            <p><?php echo $subMenu->modulo; ?></p>
                                        </a>
                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>

                <!--</li>
                <li class="nav-item">
                     <a href="#" class="nav-link">
                         <i class="nav-icon fas fa-store-alt"></i>
                         <p>Ordenes<i class="right fas fa-angle-left"></i></p>
                     </a>
                     <ul class="nav nav-treeview">
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('vistas/ventas.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Orden</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('vistas/administrar_ventas.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Administrar Ordenes</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('vistas/obtener_ordenes.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Ordenes finalizadas</p>
                             </a>
                         </li>
                         <li class="nav-item">
                             <a href="#" class="nav-link" style="cursor:pointer;" onclick="CargarContenido('vistas/kardex_ordenes.php','content-wrapper')">
                                 <i class="far fa-circle nav-icon"></i>
                                 <p>Kardex-Ordenes</p>
                             </a>
                     </ul>
                 </li>-->
                 <!-- Nuevo botón: Manuales -->
        <li class="nav-item">
            <a style="cursor: pointer;" class="nav-link" href="vistas/manuales.php">
                <i class="nav-icon fas fa-book"></i>
                <p>Manuales</p>
            </a>
        </li>

                <li class="nav-item">
                    <a style="cursor: pointer;" class="nav-link" href="http://localhost/market-pos-v38/?cerrar_sesion=1">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Cerrar Sesion
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

 <!--<script>
    var idleTime = 0;
    $(document).ready(function () {
        // Increment the idle time counter every second.
        var idleInterval = setInterval(timerIncrement, 1000); // 1 second

        // Zero the idle timer on mouse movement.
        $(this).mousemove(function (e) {
            idleTime = 0;
        });
        $(this).keypress(function (e) {
            idleTime = 0;
        });
    });

    function timerIncrement() {
        idleTime++;
        if (idleTime > 9) { // 10 seconds
            window.location.href = 'http://localhost/market-pos-v38/?cerrar_sesion=1';
        }
    }
</script>-->
