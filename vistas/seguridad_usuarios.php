<!-- Content Header (Page header) -->
<div class="content-header">
    <style>
        body {
            overflow-y: hidden; /* Evita que aparezca la barra de desplazamiento vertical */
        }

        #togglePassword {
            background-color: transparent !important;
            border: none !important;
        }

        #toggleIcon {
            color: black;
            position: absolute;
            top: 44%;
            right: 35px;
            transform: translateY(-50%);
        }
    </style>
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h2 class="m-0 fw-bold">ADMINISTRAR USUARIOS</h2>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Seguridad</li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div><!-- /.content-header -->

<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <!-- TAB LISTADO DE USUARIOS -->
                        <li class="nav-item">
                            <a class="nav-link active my-0" id="listado-usuarios-tab" data-toggle="pill" href="#listado-usuarios" role="tab" aria-controls="listado-usuarios" aria-selected="true"><i class="fas fa-list"></i> Listado de Usuarios</a>
                        </li>
                        <!-- TAB REGISTRO DE USUARIO -->
                        <li class="nav-item">
                            <a class="nav-link my-0" id="registrar-usuario-tab" data-toggle="pill" href="#registrar-usuario" role="tab" aria-controls="registrar-usuario" aria-selected="false"><i class="fas fa-file-signature"></i> Registro de Usuario</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <!-- TAB CONTENT LISTADO DE USUARIOS -->
                        <div class="tab-pane fade active show" id="listado-usuarios" role="tabpanel" aria-labelledby="listado-usuarios-tab">
                            <div class="row">
                                <!--LISTADO DE USUARIOS -->
                                <div class="col-md-12">
                                    <table id="tbl_usuarios" class="table table-striped w-100 shadow border border-secondary">
                                        <thead class="bg-main text-left">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Usuario</th>
                                            <th>Clave</th>
                                            <th>Correo</th>
                                            <th>Perfil</th>
                                            <th>Bloqueado</th>
                                            <th>Estado</th>
                                        </thead>
                                        <tbody class="small text left"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
<!-- TAB CONTENT REGISTRO DE USUARIO -->
<div class="tab-pane fade" id="registrar-usuario" role="tabpanel" aria-labelledby="registrar-usuario-tab">
    <form id="frm-datos-usuario" class="needs-validation-usuarios" novalidate>
        <div class="row">
            <div class="col-6">
                <div class="form-floating mb-2">
                    <input type="text" id="nombre_usuario" class="form-control" name="nombre_usuario" required>
                    <label for="nombre_usuario">Nombre</label>
                    <div class="invalid-feedback">Ingrese el nombre</div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-floating mb-2">
                    <input type="text" id="apellido_usuario" class="form-control" name="apellido_usuario" required>
                    <label for="apellido_usuario">Apellido</label>
                    <div class="invalid-feedback">Ingrese el apellido</div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-floating mb-2">
                    <input type="text" id="usuario" class="form-control" name="usuario" required>
                    <label for="usuario">Usuario</label>
                    <div class="invalid-feedback">Ingrese el usuario</div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-floating mb-2">
                    <input type="email" id="correo" class="form-control" name="correo" required>
                    <label for="correo">Correo</label>
                    <div class="invalid-feedback">Ingrese el correo</div>
                </div>
            </div>
            <div class="col-6">
            <div class="form-floating mb-2 position-relative">
        <input type="password" id="clave" class="form-control" name="clave" required>
        <label for="clave">Contraseña</label>
        <div class="input-group-append">
            <span class="input-group-text bg-transparent border-0" id="togglePassword">
                <i class="fas fa-eye text-black" id="toggleIcon"></i>
            </span>
        </div>
        <div class="invalid-feedback">Ingrese la contraseña</div>
    </div>
</div>
            <div class="col-6">
                <div class="form-floating mb-2">
                    <input type="number" id="id_perfil_usuario" class="form-control" name="id_perfil_usuario" value="1" required>
                    <label for="id_perfil_usuario">Perfil Usuario</label>
                    <div class="invalid-feedback">Ingrese el ID del perfil de usuario</div>
                </div>
            </div>
            <!-- ESTADO -->
            <div class="col-4">
                <div class="form-floating mb-2">
                    <select class="form-select" id="estado" name="estado" aria-label="Estado" required>
                        <option value="" selected disabled>--Seleccione un estado--</option>
                        <option value="1">ACTIVO</option>
                        <option value="0">INACTIVO</option>
                    </select>
                    <label for="estado">Estado</label>
                    <div class="invalid-feedback">Seleccione un estado</div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <div class="float-right">
                    <a class="btn btn-outline-danger mx-1" id="btnCancelarUsuario">CANCELAR</a>
                    <a class="btn btn-outline-success mx-1" id="btnRegistrarUsuario">GUARDAR USUARIO</a>
                </div>
            </div>
        </div>
    </form>
</div>

                <!-- /.card -->
            </div>
        </div>
    </div>
</div>

<script>
    // Obtener el elemento select por su ID
    const selectEstado = document.getElementById('estado');

    // Agregar evento change al select
    selectEstado.addEventListener('change', function() {
        // Obtener el valor seleccionado
        const selectedValue = this.value;
        
        // Obtener el elemento label del estado
        const estadoLabel = document.querySelector('label[for="estado"]');
        
        // Actualizar el texto del label según la selección
        estadoLabel.textContent = (selectedValue === '1') ? 'Estado: ACTIVO' : 'Estado: INACTIVO';
    });
</script>

<script>
    document.getElementById("nombre_usuario").addEventListener("input", function () {
        this.value = this.value.replace(/[0-9]/g, ''); // Remover números
    });

    document.getElementById("apellido_usuario").addEventListener("input", function () {
        this.value = this.value.replace(/[0-9]/g, ''); // Remover números
    });
</script>

<script>

$(document).ready(function() {
    $('#togglePassword').on('click', function() {
        var passwordField = $('#clave');
        var icon = $('#toggleIcon');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});


$(document).ready(function() {
    $('#clave').on('input', function() {
        var password = this.value;
        var hasNumbers = password.replace(/[^0-9]/g, '').length;
        var hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        // Verificar si la contraseña está vacía
        if (password.trim() === '') {
            $(this).removeClass('is-valid');
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').addClass('d-none');
            return;
        }

        // Verificar si la contraseña cumple con los requisitos
        if (password.length >= 8 && hasNumbers >= 4 && hasSpecialChars) {
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
            $(this).siblings('.invalid-feedback').addClass('d-none');
        } else {
            $(this).removeClass('is-valid');
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').removeClass('d-none');
            $(this).siblings('.invalid-feedback').text('La contraseña debe tener al menos 8 caracteres, 4 números y 2 caracteres especiales');
        }
    });

    $('#btnRegistrarUsuario').on('click', function() {
        var password = $('#clave').val();
        
        // Verificar si la contraseña está vacía
        if (password.trim() === '') {
            $('#clave').removeClass('is-valid');
            $('#clave').addClass('is-invalid');
            $('#clave').siblings('.invalid-feedback').removeClass('d-none');
            $('#clave').siblings('.invalid-feedback').text('Ingrese la contraseña');
            return;
        }
        
        // Verificar si la contraseña cumple con los requisitos
        var hasNumbers = password.replace(/[^0-9]/g, '').length;
        var hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        if (!(password.length >= 8 && hasNumbers >= 4 && hasSpecialChars)) {
            $('#clave').removeClass('is-valid');
            $('#clave').addClass('is-invalid');
            $('#clave').siblings('.invalid-feedback').removeClass('d-none');
            $('#clave').siblings('.invalid-feedback').text('La contraseña debe tener al menos 8 caracteres, 4 números y 2 caracteres especiales');
            return;
        }
    });
});


$(document).ready(function() {
        // Validación para el campo de nombre: evitar caracteres especiales y tres letras iguales consecutivas
        $('#nombre_usuario').on('input', function() {
            var userInput = this.value.replace(/[^a-zA-Z0-9]/g, ''); // Elimina caracteres especiales
            var lastChar = '';
            var count = 1;
            
            for (var i = 0; i < userInput.length; i++) {
                if (userInput.charAt(i) === lastChar) {
                    count++;
                    if (count === 3) {
                        // Reemplazar el último carácter por una cadena vacía
                        userInput = userInput.substring(0, i) + userInput.substring(i + 1);
                        count--;
                    }
                } else {
                    lastChar = userInput.charAt(i);
                    count = 1;
                }
            }
            
            // Asignar el valor modificado al campo de nombre
            this.value = userInput;
        });

        // Validación para el campo de apellido: evitar caracteres especiales y tres letras iguales consecutivas
        $('#apellido_usuario').on('input', function() {
            var userInput = this.value.replace(/[^a-zA-Z0-9]/g, ''); // Elimina caracteres especiales
            var lastChar = '';
            var count = 1;
            
            for (var i = 0; i < userInput.length; i++) {
                if (userInput.charAt(i) === lastChar) {
                    count++;
                    if (count === 3) {
                        // Reemplazar el último carácter por una cadena vacía
                        userInput = userInput.substring(0, i) + userInput.substring(i + 1);
                        count--;
                    }
                } else {
                    lastChar = userInput.charAt(i);
                    count = 1;
                }
            }
            
            // Asignar el valor modificado al campo de apellido
            this.value = userInput;
        });

        // Validación para el campo de usuario: evitar caracteres especiales y tres letras iguales consecutivas
        $('#usuario').on('input', function() {
            var userInput = this.value.replace(/[^a-zA-Z0-9]/g, ''); // Elimina caracteres especiales
            var lastChar = '';
            var count = 1;
            
            for (var i = 0; i < userInput.length; i++) {
                if (userInput.charAt(i) === lastChar) {
                    count++;
                    if (count === 3) {
                        // Reemplazar el último carácter por una cadena vacía
                        userInput = userInput.substring(0, i) + userInput.substring(i + 1);
                        count--;
                    }
                } else {
                    lastChar = userInput.charAt(i);
                    count = 1;
                }
            }
            
            // Asignar el valor modificado al campo de usuario
            this.value = userInput;
        });
    });


    $(document).ready(function() {
        CargarSelects();
        fnc_CargarDatatableUsuario();

        $("#btnRegistrarUsuario").on('click', function() {
            fnc_GuardarUsuario();
        });

        $("#btnCancelarUsuario").on('click', function() {
            fnc_LimpiarFomulario();
        });

        $("#registrar-usuarios-tab").on('click', function() {
            fnc_LimpiarFomulario();
        })

        $("#listado-usuarios-tab").on('click', function() {
            fnc_LimpiarFomulario();
        })

    })

    function CargarSelects() {
        $('.select2').select2()
    }

    function fnc_CargarDatatableUsuario() {

        if ($.fn.DataTable.isDataTable('#tbl_usuarios')) {
            $('#tbl_usuarios').DataTable().destroy();
            $('#tbl_usuarios tbody').empty();
        }

        $("#tbl_usuarios").DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                title: function() {
                    var printTitle = 'LISTADO DE USUARIOS';
                    return printTitle
                }
            }, 'pageLength'],
            pageLength: 10,
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: 'ajax/usuarios.ajax.php',
                data: {
                    'accion': 'obtener_usuarios'
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
                    targets: 7,
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (rowData[7] != 'ACTIVO') {
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

        $("#tbl_usuarios").on('draw.dt', function() {

            $("#tbl_usuarios").Tabledit({
                url: 'ajax/actions_editable/actions_usuarios.php',
                dataType: 'json',
                columns: {
                    identifier: [0, 'id_usuario'], 
                    editable: [
                        [1, 'nombre_usuario'],
                        [2, 'apellido_usuario'],
                        
                        
                        [6, 'id_perfil_usuario'],

                        [7, 'bloqueado'],
                        [8, 'estado', '{ "1" : "ACTIVO", "0" : "INACTIVO"}'],
                    ]
                },
                restoreButton: true,
                buttons: {
                    edit: {
                        class: 'btn btn-sm m-0 p-0 data-bs-toggle="tooltip" data-bs-placement="top" title="Editar Usuario"',
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
        position: "top-end",
        icon: "success",
        title: "Se actualizó el usuario correctamente",
        showConfirmButton: false,
        timer: 1500
    });
    $("#tbl_usuarios").DataTable().ajax.reload();
}
}

            })
        })
    }

    function fnc_GuardarUsuario() {

        form_usuarios_validate = validarFormulario('needs-validation-usuarios');

        //INICIO DE LAS VALIDACIONES
        if (!form_usuarios_validate) {
            mensajeToast("error", "complete los datos obligatorios");
            return;
        }

        Swal.fire({
            title: 'Está seguro(a) de registrar el Usuario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, deseo registrarlo!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {

            if (result.isConfirmed) {

                var formData = new FormData();
                formData.append('accion', 'registrar_usuario');
                formData.append('datos_usuario', $("#frm-datos-usuario").serialize());

                response = SolicitudAjax('ajax/usuarios.ajax.php', 'POST', formData);

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
        $("#nombre").val('');
        $("#apellido").val('');
        $("#usuario").val('');
        $("#clave").val('');
        $("#correo").val('');
        $("#estado").val('1');

        $(".needs-validation-usuarios").removeClass("was-validated");

        $("#listado-usuarios-tab").addClass('active')
        $("#listado-usuarios-tab").attr('aria-selected', true)
        $("#listado-usuarios").addClass('active show')

        //DESACTIVAR PANE LISTADO DE TIPO DE DOCUMENTO
        $("#registrar-usuario-tab").removeClass('active')
        $("#registrar-usuario-tab").attr('aria-selected', false)
        $("#registrar-usuario").removeClass('active show')

        $("#tbl_usuarios").DataTable().ajax.reload();
    }
</script>

