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
                    <form class="form" action="{{ route('clientes.actualizar', $cliente->sis_clientesid) }}"
                        method="POST">
                        @method('PUT')
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header flex-wrap py-5">
                                <div class="card-title">
                                    <h3 class="card-label">Cliente</h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                        <div class="btn-group" role="group" aria-label="First group">

                                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-icon"
                                                data-toggle="tooltip" title="Volver"><i
                                                    class="la la-long-arrow-left"></i></a>

                                            <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                title="Guardar"><i class="la la-save"></i></button>

                                            <a href="{{ route('clientes.crear') }}" class="btn btn-warning btn-icon"
                                                data-toggle="tooltip" title="Nuevo"><i class="la la-user-plus"></i></a>
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
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$cliente->usuariocreacion}}</span>
                                        <span class="font-size-sm text-primary ml-2">{{$cliente->fechacreacion}}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="font-size-sm font-weight-bolder text-dark ml-2">Modificación</span>
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$cliente->usuariomodificacion}}</span>
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$cliente->fechamodificacion}}</span>
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
                                        <a href="{{ route('licencias.web.crear',$cliente->sis_clientesid) }}"
                                            class="btn btn-primary btn-icon" data-toggle="tooltip" title="Nuevo Web"><i
                                                class="la la-cloud"></i></a>
                                        <a href="{{ route('licencias.pc.crear',$cliente->sis_clientesid) }}"
                                            class="btn btn-warning btn-icon" data-toggle="tooltip" title="Nuevo PC"><i
                                                class="la la-tv"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!--begin: Datatable-->
                            <table class="table table-sm table-bordered table-head-custom table-hover"
                                id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="no-exportar">#</th>
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

@section('script')
<script>
    $(document).ready(function(){
        var distribuidor = '{{$cliente->sis_distribuidoresid}}';
        $('#vendedor').empty();
        $('#vendedor').append('<option value="">Seleccione un Vendedor</option>');
        $('#revendedor').empty();
        $('#revendedor').append('<option value="">Seleccione un Revendedor</option>');

        $.ajax({
            type:"GET",
            url: '/revendedoresDistribuidor/' + distribuidor + '/2',
            success: function(data){
                $.each(data, function(fetch, vendedor){
                    for(i = 0; i < vendedor.length; i++){
                        vendedorid='{{$cliente->sis_vendedoresid}}'
                        select = vendedor[i].sis_revendedoresid == vendedorid ? 'Selected' : ""
                        $('#vendedor').append('<option '+ select +' value="'+ vendedor[i].sis_revendedoresid +'">'+ vendedor[i].razonsocial +'</option>');
                    }
                })
            }
        });
        $.ajax({
            type:"GET",
            url: '/revendedoresDistribuidor/' + distribuidor + '/1',
            success: function(data){
                $.each(data, function(fetch, vendedor){
                    for(i = 0; i < vendedor.length; i++){
                    revendedorid='{{$cliente->sis_revendedoresid}}'
                    select = vendedor[i].sis_revendedoresid ==revendedorid ? 'Selected' : ""
                    $('#revendedor').append('<option '+ select +' value="'+ vendedor[i].sis_revendedoresid +'">'+ vendedor[i].razonsocial +'</option>');
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
            lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'Todos']],
            //Registros por pagina
            pageLength: 15,
            //Orden inicial
            order: [[ 0, 'asc' ]],
            //Guardar pagina, busqueda, etc
            stateSave: true,
            //Trabajar del lado del server
            serverSide: true,
            //Peticion ajax que devuelve los registros
            ajax: "{{ route('licencias.index',$cliente->sis_clientesid) }}",
            //Columnas de la tabla (Debe contener misma cantidad que thead)
            columns: [
                {data: 'sis_licenciasid', name: 'sis_licenciasid',visible:false},
                {data: 'numerocontrato', name: 'numerocontrato'},
                {data: 'tipo_licencia', name: 'tipo_licencia'},
                {data: 'fechacaduca', name: 'fechacaduca'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
            ],
        });
    });

    //Cargar select dependiendes del distribuidor
     $('#distribuidor').on('change', function(e){

        var distribuidor = e.target.value;
        $('#vendedor').empty();
        $('#vendedor').append('<option value="">Seleccione un Vendedor</option>');
        $('#revendedor').empty();
        $('#revendedor').append('<option value="">Seleccione un Revendedor</option>');

        $.ajax({
            type:"GET",
            url: '/revendedoresDistribuidor/' + distribuidor + '/2',
            success: function(data){
                $.each(data, function(fetch, vendedor){
                    for(i = 0; i < vendedor.length; i++){
                    $('#vendedor').append('<option value="'+ vendedor[i].sis_revendedoresid +'">'+ vendedor[i].razonsocial +'</option>');
                    }
                })
            }
        });
        $.ajax({
            type:"GET",
            url: '/revendedoresDistribuidor/' + distribuidor + '/1',
            success: function(data){
                $.each(data, function(fetch, vendedor){
                    for(i = 0; i < vendedor.length; i++){
                    $('#revendedor').append('<option value="'+ vendedor[i].sis_revendedoresid +'">'+ vendedor[i].razonsocial +'</option>');
                    }
                })
            }
        });
    });

    function recuperarInformacion() {

    var cad = document.getElementById('identificacion').value;
        $("#spinner").addClass("spinner spinner-success spinner-right");
        $.post('{{ route('recuperarInformacionPost') }}', {
            _token: '{{ csrf_token() }}',
            cedula: cad
        }, function(data) {
            $("#spinner").removeClass("spinner spinner-success spinner-right");
            if (data.identificacion) {
                $("#razonsocial").val(data.razon_social);
                $("#nombrecomercial").val(data.nombrecomercial);
                $("#direccion").val(data.direccion);
                $("#correo").val(data.correo);
            }
        });
    }
</script>
@endsection