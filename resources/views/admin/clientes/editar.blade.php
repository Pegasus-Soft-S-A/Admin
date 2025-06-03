@php
    $rol = Auth::user()->tipo;
    // Definir constantes de roles
    define('ROL_ADMIN', 1);
    define('ROL_DISTRIBUIDOR', 2);
    define('ROL_SOPORTE_DISTRIBUIDOR', 3);
    define('ROL_SOPORTE_MATRIZ', 7);
    define('ROL_VISOR', 6);
    define('ROL_COMERCIAL', 8);
    define('ROL_VENTAS', 4);
@endphp
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
                                        <h3 class="card-label">Cliente</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                            <div class="btn-group" role="group" aria-label="First group">

                                                <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-icon" data-toggle="tooltip"
                                                    title="Volver"><i class="la la-long-arrow-left"></i></a>

                                                @if (Auth::user()->tipo == ROL_ADMIN)
                                                    <a class="btn btn-danger btn-icon confirm-delete" href="javascript:void(0)"
                                                        data-href="{{ route('clientes.eliminar', $cliente->sis_clientesid) }}" data-toggle="tooltip"
                                                        title="Eliminar"> <i class="la la-trash"></i>
                                                    </a>
                                                @endif
                                                @if (puede('clientes', 'guardar_clientes'))
                                                    <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip" title="Guardar"><i
                                                            class="la la-save"></i></button>
                                                @endif
                                                @if (puede('clientes', 'crear_clientes'))
                                                    <a href="{{ route('clientes.crear') }}" class="btn btn-warning btn-icon" data-toggle="tooltip"
                                                        title="Nuevo"><i class="la la-user-plus"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('admin.clientes._form')

                                </div>

                                <div class="card-footer pt-2 pb-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="font-size-sm font-weight-bolder text-dark ml-2">Auditoría</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="font-size-sm font-weight-bolder text-dark ml-2">Creación</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $cliente->usuariocreacion }}</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $cliente->fechacreacion }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="font-size-sm font-weight-bolder text-dark ml-2">Modificación</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $cliente->usuariomodificacion }}</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $cliente->fechamodificacion }}</span>
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
                            <div class="card-header flex-wrap py-5">
                                <div class="card-title">
                                    <h3 class="card-label">Licencias </h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                        <div class="btn-group" role="group" aria-label="First group">
                                            @if (puede('web', 'crear_web'))
                                                <a href="{{ route('licencias.Web.crear', $cliente->sis_clientesid) }}" class="btn btn-primary btn-icon"
                                                    data-toggle="tooltip" title="Nuevo Web"><i class="la la-cloud"></i></a>
                                            @endif
                                            @if (puede('pc', 'crear_pc'))
                                                <a href="{{ route('licencias.Pc.crear', $cliente->sis_clientesid) }}" class="btn btn-warning btn-icon"
                                                    data-toggle="tooltip" title="Nuevo PC"><i class="la la-tv"></i></a>
                                            @endif
                                            @if (puede('vps', 'crear_vps'))
                                                <a href="{{ route('licencias.Vps.crear', $cliente->sis_clientesid) }}" class="btn btn-secondary btn-icon"
                                                    data-toggle="tooltip" title="Nuevo VPS"><i class="la la-cloud"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!--begin: Datatable-->
                                <table class="table table-sm table-bordered table-head-custom table-hover" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="no-exportar">#</th>
                                            <th class="no-exportar">Servidor</th>
                                            <th data-priority="1">Contrato</th>
                                            <th data-priority="2">Tipo</th>
                                            <th>Fecha Caduca</th>
                                            <th class="no-exportar">Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                                <!--end: Datatable-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
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

@section('script')
    <script>
        $('#formulario').submit(function(event) {
            $("#provinciasid").prop("disabled", false);
            $("#distribuidor").prop("disabled", false);
            $("#vendedor").prop("disabled", false);
            $("#revendedor").prop("disabled", false);
            $("#red_origen").prop("disabled", false);
        });

        $(document).ready(function() {
            var seleccionado = '';
            $.ajax({
                url: '{{ route('registro.recuperarciudades') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: '{{ $cliente->provinciasid }}'
                },
                success: function(data) {
                    $('#ciudadesid').empty();
                    data.map(function(ciudades) {
                        if (ciudades.ciudadesid == '{{ $cliente->ciudadesid }}') {
                            seleccionado = 'selected';

                        } else {
                            seleccionado = 'noselected';
                        }

                        $('#ciudadesid').append('<option value="' + ciudades.ciudadesid +
                            '" ' + seleccionado + ' >' + ciudades
                            .ciudad + '</option>')
                    });


                }
            })


            var distribuidor = '{{ $cliente->sis_distribuidoresid }}';
            $('#vendedor').empty();
            $('#vendedor').append('<option value="">Seleccione un Vendedor</option>');
            $('#revendedor').empty();
            $('#revendedor').append('<option value="">Seleccione un Revendedor</option>');

            $.ajax({
                type: "GET",
                url: '/admin/revendedoresdistribuidor/' + distribuidor + '/2',
                success: function(data) {
                    $.each(data, function(fetch, vendedor) {
                        for (i = 0; i < vendedor.length; i++) {
                            vendedorid = '{{ $cliente->sis_vendedoresid }}'
                            select = vendedor[i].sis_revendedoresid == vendedorid ? 'Selected' :
                                ""
                            $('#vendedor').append('<option ' + select + ' value="' + vendedor[i]
                                .sis_revendedoresid + '">' + vendedor[i].razonsocial +
                                '</option>');
                        }
                    })
                }
            });
            $.ajax({
                type: "GET",
                url: '/admin/revendedoresdistribuidor/' + distribuidor + '/1',
                success: function(data) {
                    $.each(data, function(fetch, vendedor) {
                        for (i = 0; i < vendedor.length; i++) {
                            revendedorid = '{{ $cliente->sis_revendedoresid }}'
                            select = vendedor[i].sis_revendedoresid == revendedorid ?
                                'Selected' : ""
                            $('#revendedor').append('<option ' + select + ' value="' + vendedor[
                                    i].sis_revendedoresid + '">' + vendedor[i].razonsocial +
                                '</option>');
                        }
                    })
                }
            });

            //inicializar datatable
            var table = $('#kt_datatable').DataTable({
                //Posicion de los elementos de la datatable f:filtering l:length t:table r:processing i:info p:pagination
                // dom:"<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
                //     "<'row'<'col-sm-12'tr>>" +
                //     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                responsive: true,
                processing: true,
                searching: false,
                paging: false,
                //Combo cantidad de registros a mostrar por pantalla
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, 'Todos']
                ],
                //Registros por pagina
                pageLength: 15,
                //Orden inicial
                order: [
                    [0, 'asc']
                ],
                //Guardar pagina, busqueda, etc
                stateSave: false,
                //Trabajar del lado del server
                serverSide: true,
                //Peticion ajax que devuelve los registros
                ajax: "{{ route('licencias.index', $cliente->sis_clientesid) }}",
                //Columnas de la tabla (Debe contener misma cantidad que thead)
                columns: [{
                        data: 'sis_licenciasid',
                        name: 'sis_licenciasid',
                        visible: false
                    },
                    {
                        data: 'sis_servidoresid',
                        name: 'sis_servidoresid',
                        visible: false
                    },
                    {
                        data: 'numerocontrato',
                        name: 'numerocontrato'
                    },
                    {
                        data: 'tipo_licencia',
                        name: 'tipo_licencia'
                    },
                    {
                        data: 'fechacaduca',
                        name: 'fechacaduca'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                ],
            });
        });

        //Cargar select dependiendes del distribuidor
        $('#distribuidor').on('change', function(e) {

            var distribuidor = e.target.value;
            $('#vendedor').empty();
            $('#vendedor').append('<option value="">Seleccione un Vendedor</option>');
            $('#revendedor').empty();
            $('#revendedor').append('<option value="">Seleccione un Revendedor</option>');

            $.ajax({
                type: "GET",
                url: '/admin/revendedoresdistribuidor/' + distribuidor + '/2',
                success: function(data) {
                    $.each(data, function(fetch, vendedor) {
                        for (i = 0; i < vendedor.length; i++) {
                            $('#vendedor').append('<option value="' + vendedor[i]
                                .sis_revendedoresid + '">' + vendedor[i].razonsocial +
                                '</option>');
                        }
                    })
                }
            });
            $.ajax({
                type: "GET",
                url: '/admin/revendedoresdistribuidor/' + distribuidor + '/1',
                success: function(data) {
                    $.each(data, function(fetch, vendedor) {
                        for (i = 0; i < vendedor.length; i++) {
                            $('#revendedor').append('<option value="' + vendedor[i]
                                .sis_revendedoresid + '">' + vendedor[i].razonsocial +
                                '</option>');
                        }
                    })
                }
            });
        });

        function recuperarInformacion() {

            var cad = document.getElementById('identificacion').value;
            $.ajax({
                url: '{{ route('identificaciones.index') }}',
                headers: {
                    'usuario': 'perseo',
                    'clave': 'Perseo1232*'
                },
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    identificacion: cad
                },
                success: function(data) {
                    $("#spinner").removeClass("spinner spinner-success spinner-right");
                    data = JSON.parse(data);
                    if (data.identificacion) {
                        $("#nombres").val(data.razon_social);
                        $("#direccion").val(data.direccion);
                        $("#correo").val(data.correo);
                    }
                }
            });
        }


        function cambiarCiudad(id) {


            $.ajax({
                url: '{{ route('registro.recuperarciudades') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id.value
                },
                success: function(data) {
                    $('#ciudadesid').empty();

                    data.map(ciudades => {

                            $('#ciudadesid').append('<option value="' + ciudades.ciudadesid + '">' +
                                ciudades
                                .ciudad + '</option>')
                        }

                    );
                }
            })
        }
    </script>
@endsection
