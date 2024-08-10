<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2 class="m-0 fw-bold">ADMINISTRAR PERFILES</h2>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Seguridad</li>
                    <li class="breadcrumb-item active">Perfiles</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.content-header -->

<div class="content">
    <div class="row">
        <div class="col-12 ">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <!-- TAB LISTADO DE TIPOS DE DOCUMENTO -->
                        <li class="nav-item">
                            <a class="nav-link active my-0" id="listado-perfiles-tab" data-toggle="pill" href="#listado-perfiles" role="tab" aria-controls="listado-perfiles" aria-selected="true"><i class="fas fa-list"></i> Listado de Perfiles</a>
                        </li>
                        <!-- TAB REGISTRO DE TIPO DE DOCUMENTO -->
                        <li class="nav-item">
                            <a class="nav-link my-0" id="registrar-perfiles-tab" data-toggle="pill" href="#registrar-perfiles" role="tab" aria-controls="registrar-perfiles" aria-selected="false"><i class="fas fa-file-signature"></i> Registro de Perfil</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <!-- TAB CONTENT LISTADO DE SERIES -->
                        <div class="tab-pane fade active show" id="listado-perfiles" role="tabpanel" aria-labelledby="listado-perfiles-tab">
                            <div class="row">
                                <!--LISTADO DE CATEGORIAS -->
                                <div class="col-md-12">
                                    <table id="tbl_perfiles" class="table table-striped w-100 shadow border border-secondary">
                                        <thead class="bg-main text-left">
                                            <th>ID</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                        </thead>
                                        <tbody class="small text left">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- TAB CONTENT REGISTRO DE SERIES -->
                        <div class="tab-pane fade" id="registrar-perfiles" role="tabpanel" aria-labelledby="registrar-perfiles-tab">
                            <form id="frm-datos-perfiles" class="needs-validation-perfiles" novalidate>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-floating mb-2">
                                            <input type="text" id="descripcion" class="form-control text-uppercase" name="descripcion" required>
                                            <label for="descripcion">Descripción</label>
                                            <div class="invalid-feedback">Ingrese la descripción</div>
                                        </div>
                                    </div>
                                    <!-- ESTADO -->
                                    <div class="col-4">
                                        <div class="form-floating mb-2">
                                            <select class="form-select select2" id="estado" name="estado" aria-label="Floating label select example" required>
                                                <option value="" selected disabled>--Seleccione un estado--</option>
                                                <option value="1">ACTIVO</option>
                                                <option value="0">INACTIVO</option>
                                            </select>
                                            <div class="invalid-feedback">Seleccione un estado</div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="float-right">
                                            <a class="btn btn-outline-danger mx-1" id="btnCancelarPerfil">CANCELAR</a>
                                            <a class="btn btn-outline-success mx-1" id="btnRegistrarPerfil">GUARDAR TIPO DE DOCUMENTO</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        CargarSelects();
        fnc_CargarDatatablePerfil();

        $("#btnRegistrarPerfil").on('click', function() {
            fnc_GuardarPerfil();
        });

        $("#btnCancelarPerfil").on('click', function() {
            fnc_LimpiarFomulario();
        });

        $("#registrar-perfiles-tab").on('click', function() {
            fnc_LimpiarFomulario();
        })

        $("#listado-perfiles-tab").on('click', function() {
            fnc_LimpiarFomulario();
        })

        // Validación para permitir solo letras y números en la descripción
        $("#descripcion").on('input', function() {
            var inputVal = $(this).val();
            var regex = /^[a-zA-Z0-9\s]*$/; // Expresión regular para letras, números y espacios
            if (!regex.test(inputVal)) {
                $(this).val(inputVal.replace(/[^a-zA-Z0-9\s]/g, ''));
            }
        });
    })

    function CargarSelects() {     
        $('.select2').select2()
    }

    function fnc_CargarDatatablePerfil() {

        if ($.fn.DataTable.isDataTable('#tbl_perfiles')) {
            $('#tbl_perfiles').DataTable().destroy();
            $('#tbl_perfiles tbody').empty();
        }

        $("#tbl_perfiles").DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                title: function() {
                    var printTitle = 'LISTADO DE PERFILES';
                    return printTitle
                }
            }, 'pageLength'],
            pageLength: 10,
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: 'ajax/perfiles.ajax.php',
                data: {
                    'accion': 'obtener_perfiles'
                },
                type: 'POST'
            },
            columnDefs: [{
                    "className": "dt-center",
                    "targets": "_all"
                },
                {
                    targets: 0,
                    orderable: false,
                    className: 'control'
                },
                {
                    targets: 2,
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (rowData[2] != 'ACTIVO') {
                            $(td).parent().css('background', '#F2D7D5')
                            $(td).parent().css('color', 'black')
                        }
                    }
                },

            ],
            order: [
                [0, 'ASC']
            ],
            // scrollX: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        })

        

        $("#tbl_perfiles").on('draw.dt', function() {

            $("#tbl_perfiles").Tabledit({
                url: 'ajax/actions_editable/actions_perfiles.php',
                dataType: 'json',
                columns: {
                    identifier: [0, 'id_perfil'],
                    editable: [
                        [1, 'descripcion'],
                        [2, 'estado', '{ "1" : "ACTIVO", "0" : "INACTIVO"}'],
                    ]
                },
                restoreButton: true,
                buttons: {
                    edit: {
                        class: 'btn btn-sm m-0 p-0 data-bs-toggle="tooltip" data-bs-placement="top" title="Activar/Editar Categoría"',
                        html: '<i class="fas fa-edit text-primary fs-5"></i>',
                        action: 'edit'
                    },
                    delete: {
                        class: 'd-none',
                        html: '',
                        action: 'delete'
                    },
                    save: {
                        class: 'btn btn-sm btn-success p-0 px-2 rounded-pill',
                        html: '<i class="fas fa-check "></i>'
                    },
                    restore: {
                        class: 'btn btn-sm btn-warning',
                        html: 'Deshacer',
                        action: 'restore'
                    },
                    confirm: {
                        class: 'btn btn-sm btn-danger p-0 px-1 rounded-pill',
                        html: '<i class="fas fa-check "></i>'
                    }
                },
                onSuccess: function(data, textStatus, jqXHR) {
    if (data.action == "edit") {
        Swal.fire({
            position: 'top-center',
            icon: data.tipo_msj,
            title: data.msj,
            showConfirmButton: true,
            timer: 2000
        });
        Swal.fire("Se actualizó el perfil correctamente");
        $("#tbl_perfiles").DataTable().ajax.reload();
    }
}
            })
        })
    }

    function fnc_GuardarPerfil() {

        form_perfiles_validate = validarFormulario('needs-validation-perfiles');

        //INICIO DE LAS VALIDACIONES
        if (!form_perfiles_validate) {
            mensajeToast("error", "complete los datos obligatorios");
            return;
        }

        Swal.fire({
            title: 'Está seguro(a) de registrar el Perfil?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, deseo registrarlo!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {

            if (result.isConfirmed) {

                var formData = new FormData();
                formData.append('accion', 'registrar_perfil');
                formData.append('datos_perfil', $("#frm-datos-perfiles").serialize());

                response = SolicitudAjax('ajax/perfiles.ajax.php', 'POST', formData);

                Swal.fire({
                    position: 'top-center',
                    icon: response['tipo_msj'],
                    title: response['msj'],
                    showConfirmButton: true,
                    timer: 2000
                })

                fnc_LimpiarFomulario();

            }

        })
    }

    function fnc_LimpiarFomulario() {
        $("#descripcion").val('')
        $("#estado").val('1');

        $(".needs-validation-perfiles").removeClass("was-validated");

        $("#listado-perfiles-tab").addClass('active')
        $("#listado-perfiles-tab").attr('aria-selected', true)
        $("#listado-perfiles").addClass('active show')

        //DESACTIVAR PANE LISTADO DE TIPO DE DOCUMENTO
        $("#registrar-perfiles-tab").removeClass('active')
        $("#registrar-perfiles-tab").attr('aria-selected', false)
        $("#registrar-perfiles").removeClass('active show')



        $("#tbl_perfiles").DataTable().ajax.reload();
    }
</script>