@extends('admin.layouts.app')
@section('contenido')
    <style>
        #kt_datatable td {
            padding: 3px;
        }
    </style>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form id="formulario" class="form" action="{{ route('clientes.actualizar', $cliente->sis_clientesid) }}" method="POST">
                            @method('PUT')
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bold text-dark">
                                            <i class="fas fa-user-edit text-primary mr-3"></i>Editar Cliente
                                        </h3>
                                    </div>

                                    <div class="card-toolbar">
                                        <!-- Grupo principal de botones -->
                                        <div class="btn-group" role="group">
                                            <!-- Botón Volver -->
                                            <a href="{{ route('clientes.index') }}"
                                               class="btn btn-secondary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="bottom"
                                               title="Volver al listado de clientes">
                                                <i class="fas fa-arrow-left mr-2"></i>
                                                <span class="d-none d-sm-inline">Volver</span>
                                            </a>
                                        </div>

                                        <!-- Grupo de acciones principales -->
                                        <div class="btn-group ml-2" role="group">
                                            @if (puede('clientes', 'guardar_clientes'))
                                                <!-- Botón Guardar -->
                                                <button type="submit"
                                                        class="btn btn-success font-weight-bold"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="Guardar los cambios realizados"
                                                        id="btn-guardar">
                                                    <i class="fas fa-save mr-2"></i>
                                                    <span class="d-none d-sm-inline">Guardar Cambios</span>
                                                </button>
                                            @endif

                                            @if (puede('clientes', 'crear_clientes'))
                                                <!-- Botón Nuevo Cliente -->
                                                <a href="{{ route('clientes.crear') }}"
                                                   class="btn btn-primary font-weight-bold"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom"
                                                   title="Crear un nuevo cliente">
                                                    <i class="fas fa-user-plus mr-2"></i>
                                                    <span class="d-none d-lg-inline">Nuevo</span>
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Grupo de acciones adicionales -->
                                        <div class="btn-group ml-2" role="group">
                                            <!-- Dropdown de Acciones -->
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                        class="btn btn-light-primary font-weight-bold dropdown-toggle"
                                                        data-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="Más opciones">
                                                    <i class="fas fa-ellipsis-v mr-2"></i>
                                                    <span class="d-none d-lg-inline">Acciones</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right shadow-lg">
                                                    <!-- Sección: Licencias -->
                                                    <h6 class="dropdown-header">
                                                        <i class="fas fa-key mr-2 text-primary"></i>Crear Licencias
                                                    </h6>

                                                    @if (puede('web', 'crear_web'))
                                                        <a class="dropdown-item d-flex align-items-center"
                                                           href="{{ route('licencias.Web.crear', $cliente->sis_clientesid) }}">
                                                            <i class="fas fa-cloud text-info mr-3"></i>Nueva Licencia Web
                                                        </a>
                                                    @endif

                                                    @if (puede('pc', 'crear_pc'))
                                                        <a class="dropdown-item d-flex align-items-center"
                                                           href="{{ route('licencias.Pc.crear', $cliente->sis_clientesid) }}">
                                                            <i class="fas fa-desktop text-warning mr-3"></i>Nueva Licencia PC
                                                        </a>
                                                    @endif

                                                    @if (puede('vps', 'crear_vps'))
                                                        <a class="dropdown-item d-flex align-items-center"
                                                           href="{{ route('licencias.Vps.crear', $cliente->sis_clientesid) }}">
                                                            <i class="fas fa-server text-secondary mr-3"></i>Nueva Licencia VPS
                                                        </a>
                                                    @endif

                                                    @if (puede('clientes', 'eliminar_clientes'))
                                                        <div class="dropdown-divider"></div>
                                                        <div class="dropdown-divider"></div>

                                                        <!-- Sección: Zona Peligrosa -->
                                                        <h6 class="dropdown-header text-danger">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i>Zona Peligrosa
                                                        </h6>

                                                        <a class="dropdown-item  d-flex align-items-center btn-eliminar-cliente"
                                                           href="javascript:void(0)"
                                                           data-href="{{ route('clientes.eliminar', $cliente->sis_clientesid) }}"
                                                           data-cliente-nombre="{{ $cliente->nombres }}"
                                                           data-cliente-identificacion="{{ $cliente->identificacion }}">
                                                            <i class="fas fa-trash mr-3 "></i>Eliminar Cliente
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('admin.clientes._form')

                                </div>

                                <!-- Footer con información de auditoría -->
                                <div class="card-footer bg-light py-2">
                                    <div class="row align-items-center mb-0">
                                        <div class="col-md-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-history text-primary mr-2"></i>
                                                <small class="font-weight-bold text-dark mb-0">Auditoría</small>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-plus text-success mr-2"></i>
                                                <div>
                                                    <small class="font-weight-bold text-dark mb-0">Creado:</small>
                                                    <small class="text-muted ml-1">
                                                        {{ $cliente->usuariocreacion }}
                                                        - {{ date('d/m/Y H:i', strtotime($cliente->fechacreacion)) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-edit text-warning mr-2"></i>
                                                <div>
                                                    <small class="font-weight-bold text-dark mb-0">Modificado:</small>
                                                    <small class="text-muted ml-1">
                                                        {{ $cliente->usuariomodificacion ?: 'Sin modificaciones' }}
                                                        @if($cliente->fechamodificacion)
                                                            - {{ date('d/m/Y H:i', strtotime($cliente->fechamodificacion)) }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--end::Card-->
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-custom">
                            <!-- Header mejorado -->
                            <div class="card-header border-0 py-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bold text-dark">
                                        <i class="fas fa-key text-primary mr-3"></i>Licencias del Cliente
                                    </span>
                                </h3>
                                <div class="card-toolbar">
                                    <!-- Grupo de botones mejorado -->
                                    <div class="btn-group" role="group" aria-label="Crear licencias">
                                        @if (puede('web', 'crear_web'))
                                            <a href="{{ route('licencias.Web.crear', $cliente->sis_clientesid) }}"
                                               class="btn btn-primary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="Crear nueva licencia Web">
                                                <i class="fas fa-cloud mr-2"></i>
                                                <span class="d-none d-sm-inline">Web</span>
                                            </a>
                                        @endif

                                        @if (puede('pc', 'crear_pc'))
                                            <a href="{{ route('licencias.Pc.crear', $cliente->sis_clientesid) }}"
                                               class="btn btn-warning font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="Crear nueva licencia PC">
                                                <i class="fas fa-desktop mr-2"></i>
                                                <span class="d-none d-sm-inline">PC</span>
                                            </a>
                                        @endif

                                        @if (puede('vps', 'crear_vps'))
                                            <a href="{{ route('licencias.Vps.crear', $cliente->sis_clientesid) }}"
                                               class="btn btn-secondary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="Crear nueva licencia VPS">
                                                <i class="fas fa-server mr-2"></i>
                                                <span class="d-none d-sm-inline">VPS</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <!-- Tabla responsive mejorada -->
                                <div class="table-responsive">
                                    <table class="table table-head-custom table-head-bg table-borderless table-vertical-center" id="kt_datatable">
                                        <thead>
                                        <tr class="text-uppercase">
                                            <th class="pl-7 min-w-100px">
                                                <span class="text-dark-75 font-weight-bolder">Licencia</span>
                                            </th>
                                            <th class="min-w-100px">
                                                <span class="text-dark-75 font-weight-bolder">Tipo</span>
                                            </th>
                                            <th class="min-w-120px">
                                                <span class="text-dark-75 font-weight-bolder">Estado</span>
                                            </th>
                                            <th class="min-w-100px text-right">
                                                <span class="text-dark-75 font-weight-bolder">Vencimiento</span>
                                            </th>
                                            <th class="min-w-70px text-right">
                                                <span class="text-dark-75 font-weight-bolder">Acciones</span>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Se llena dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Estado sin licencias -->
                                <div id="sin-licencias" class="text-center py-10" style="display: none;">
                                    <div class="symbol symbol-100 symbol-light-primary mx-auto mb-7">
                                        <span class="symbol-label">
                                            <i class="fas fa-key text-primary" style="font-size: 3rem;"></i>
                                        </span>
                                    </div>
                                    <h3 class="font-weight-bold text-dark mb-4">No hay licencias creadas</h3>
                                    <p class="text-muted font-weight-bold font-size-lg mb-7">
                                        Este cliente aún no tiene licencias asociadas.<br>
                                        Puedes crear una nueva licencia usando los botones de arriba.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<div id="actividad-modal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">Actividad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr class="text-center">
                        <td>Módulo</td>
                        <td>Accion</td>
                        <td>Fecha</td>
                        <td>Usuario</td>
                        <td>Empresa</td>
                    </tr>
                    </thead>
                    <tbody class="text-center">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // ====================================
        // SISTEMA GENÉRICO DE ELIMINACIÓN REUTILIZABLE
        // ====================================

        $(document).ready(function () {
            // Inicializar DataTable de licencias
            inicializarTablaLicencias();

            // Configurar evento de eliminación GENÉRICO
            configurarEliminacion();
        });

        // Configurar eventos de eliminación (GENÉRICO para clientes y licencias)
        function configurarEliminacion() {
            // Capturar tanto clientes como licencias
            $(document).on('click', '.btn-eliminar-cliente, .btn-eliminar-licencia', function (e) {
                e.preventDefault();

                const url = $(this).data('href');
                const tipo = $(this).hasClass('btn-eliminar-cliente') ? 'cliente' : 'licencia';

                // Datos específicos según el tipo
                let datosEliminacion = {};

                if (tipo === 'cliente') {
                    datosEliminacion = {
                        tipo: 'cliente',
                        nombre: $(this).data('cliente-nombre'),
                        identificacion: $(this).data('cliente-identificacion'),
                        titulo: '⚠️ Eliminar Cliente',
                        descripcion: 'Está a punto de eliminar el cliente:',
                        elemento: `<strong>${$(this).data('cliente-nombre')}</strong><br><small class="text-muted">${$(this).data('cliente-identificacion')}</small>`,
                        redirect: "{{ route('clientes.index') }}"
                    };
                } else {
                    datosEliminacion = {
                        tipo: 'licencia',
                        nombre: $(this).data('licencia-contrato'),
                        identificacion: $(this).data('licencia-tipo'),
                        titulo: '⚠️ Eliminar Licencia',
                        descripcion: 'Está a punto de eliminar la licencia:',
                        elemento: `<strong>Contrato: ${$(this).data('licencia-contrato')}</strong><br><small class="text-muted">Tipo: ${$(this).data('licencia-tipo')}</small>`,
                        redirect: null // No redirigir, solo recargar tabla
                    };
                }

                confirmarEliminacion(url, datosEliminacion);
            });
        }

        // Función de confirmación genérica
        function confirmarEliminacion(url, datos) {
            Swal.fire({
                title: datos.titulo,
                html: `
                    <div class="text-left">
                        <p>${datos.descripcion}</p>
                        <div class="alert alert-light-warning p-3 mt-3">
                            ${datos.elemento}
                        </div>
                        <p class="text-danger mt-3"><strong>Esta acción no se puede deshacer.</strong></p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#F64E60',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Segundo nivel de confirmación (IGUAL PARA AMBOS)
                    Swal.fire({
                        title: 'Confirmación final',
                        text: 'Escriba "ELIMINAR" para confirmar',
                        input: 'text',
                        inputAttributes: {
                            autocapitalize: 'off',
                            placeholder: 'Escriba ELIMINAR aquí'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar definitivamente',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#F64E60',
                        cancelButtonColor: '#6c757d',
                        preConfirm: (texto) => {
                            if (texto !== 'ELIMINAR') {
                                Swal.showValidationMessage('Debe escribir exactamente "ELIMINAR"');
                                return false;
                            }
                            return true;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Ejecutar eliminación por AJAX
                            ejecutarEliminacion(url, datos);
                        }
                    });
                }
            });
        }

        // Ejecutar eliminación por AJAX (SIMPLIFICADO)
        function ejecutarEliminacion(url, datos) {
            // Mostrar indicador de carga
            Swal.fire({
                title: 'Eliminando...',
                text: `Por favor espere mientras se elimina ${datos.tipo === 'cliente' ? 'el cliente' : 'la licencia'}`,
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Realizar petición AJAX
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.close();

                    // ✅ Simplificado: Solo verificar success
                    if (response.success || response.respuesta) {
                        // Mensaje de éxito genérico (manejado en frontend)
                        const mensaje = `${datos.tipo === 'cliente' ? 'Cliente' : 'Licencia'} eliminado correctamente.`;

                        Swal.fire({
                            title: '¡Eliminado!',
                            text: mensaje,
                            icon: 'success',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#1BC5BD',
                            timer: 3000, // Auto cerrar en 3 segundos
                            timerProgressBar: true
                        }).then(() => {
                            if (datos.tipo === 'cliente' && datos.redirect) {
                                // Redirigir para clientes
                                window.location.href = datos.redirect;
                            } else {
                                // Recargar tabla de licencias
                                $('#kt_datatable').DataTable().ajax.reload(null, false);
                            }
                        });
                    } else {
                        // Error desde el backend
                        Swal.fire({
                            title: 'Error',
                            text: response.message || `No se pudo eliminar ${datos.tipo === 'cliente' ? 'el cliente' : 'la licencia'}.`,
                            icon: 'error',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#F64E60'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();

                    let errorMessage = `Ocurrió un error inesperado al eliminar ${datos.tipo === 'cliente' ? 'el cliente' : 'la licencia'}.`;

                    // Intentar obtener mensaje específico del backend
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error de Eliminación',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#F64E60'
                    });
                }
            });
        }

        // ====================================
        // TABLA DE LICENCIAS
        // ====================================

        // Inicializar tabla de licencias
        function inicializarTablaLicencias() {
            var table = $('#kt_datatable').DataTable({
                responsive: true,
                processing: true,
                searching: false,
                paging: false,
                info: false,
                lengthChange: false,
                orderable: false,
                // order: [[3, 'asc']], // Ordenar por fecha de vencimiento
                serverSide: true,
                ajax: {
                    url: "{{ route('licencias.index', $cliente->sis_clientesid) }}",
                },
                columns: [
                    {
                        data: 'numerocontrato',
                        name: 'numerocontrato',
                        orderable: false,
                        render: function (data, type, row) {
                            let icono = '';

                            switch (row.tipo_licencia) {
                                case 'Perseo Web':
                                case 'Facturito':
                                    icono = '<i class="fas fa-cloud text-primary"></i>';
                                    break;
                                case 'Perseo PC':
                                    icono = '<i class="fas fa-desktop text-warning"></i>';
                                    break;
                                case 'Perseo VPS':
                                    icono = '<i class="fas fa-server text-secondary"></i>';
                                    break;
                            }

                            return `
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 symbol-light-secondary text-center">
                                        <span class="symbol-label font-size-h6">${icono}</span>
                                    </div>
                                    <div class="d-flex flex-column ml-2">
                                        <span class="text-dark-75 font-weight-bolder font-size-lg mb-1">${data}</span>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'tipo_licencia',
                        name: 'tipo_licencia',
                        orderable: false,
                        render: function (data, type, row) {
                            let badgeClass = '';
                            let icono = '';

                            switch (data) {
                                case 'Perseo Web':
                                    badgeClass = 'badge-light-primary';
                                    icono = 'fas fa-cloud';
                                    break;
                                case 'Facturito':
                                    badgeClass = 'badge-light-info';
                                    icono = 'fas fa-receipt';
                                    break;
                                case 'Perseo PC':
                                    badgeClass = 'badge-light-warning';
                                    icono = 'fas fa-desktop';
                                    break;
                                case 'Perseo VPS':
                                    badgeClass = 'badge-light-secondary';
                                    icono = 'fas fa-server';
                                    break;
                                default:
                                    badgeClass = 'badge-light-dark';
                                    icono = 'fas fa-question';
                            }

                            return `<span class="badge ${badgeClass} font-weight-bold px-4 py-3">
                                <i class="${icono} mr-2"></i>${data}
                            </span>`;
                        }
                    },
                    {
                        data: 'fechacaduca',
                        name: 'fechacaduca',
                        orderable: false,
                        render: function (data, type, row) {
                            if (!data) return '<span class="text-muted">Sin fecha</span>';

                            // Calcular días hasta vencer
                            const fechaVencimiento = moment(data, 'DD-MM-YYYY');
                            const hoy = moment();
                            const diasRestantes = fechaVencimiento.diff(hoy, 'days');

                            let badgeClass = '';
                            let estadoTexto = '';
                            let icono = '';

                            if (diasRestantes < 0) {
                                badgeClass = 'badge-danger';
                                estadoTexto = 'Vencida';
                                icono = 'fas fa-times-circle';
                            } else if (diasRestantes <= 7) {
                                badgeClass = 'badge-warning';
                                estadoTexto = 'Por vencer';
                                icono = 'fas fa-exclamation-triangle';
                            } else {
                                badgeClass = 'badge-success';
                                estadoTexto = 'Vigente';
                                icono = 'fas fa-check-circle';
                            }

                            return `
                                <div class="d-flex flex-column">
                                    <span class="badge ${badgeClass} font-weight-bold text-white">
                                        <i class="${icono} mr-1 text-white"></i>${estadoTexto}
                                    </span>
                                    <span class="text-muted font-weight-bold font-size-sm">
                                        ${diasRestantes >= 0 ? `${diasRestantes} días restantes` : `Vencida hace ${Math.abs(diasRestantes)} días`}
                                    </span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'fechacaduca',
                        name: 'fechacaduca',
                        orderable: false,
                        render: function (data, type, row) {
                            if (!data) return '<span class="text-muted">Sin fecha</span>';

                            return `
                                <div class="d-flex flex-column align-items-end">
                                    <span class="text-dark-75 font-weight-bolder">${data}</span>
                                    <span class="text-muted font-weight-bold font-size-sm">Fecha límite</span>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-right pr-7",
                        render: function (data, type, row) {
                            return `
                                <div class="align-items-end">
                                    ${data}
                                </div>
                            `;
                        }
                    }
                ],
                drawCallback: function (settings) {
                    // Mostrar/ocultar estado sin licencias
                    const api = this.api();
                    const data = api.rows().data();

                    if (data.length === 0) {
                        $('#sin-licencias').show();
                        $('#licencias-stats').hide();
                    } else {
                        $('#sin-licencias').hide();
                        $('#licencias-stats').show();
                    }
                },
            });

            return table;
        }
    </script>
@endpush
