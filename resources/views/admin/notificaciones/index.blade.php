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
                    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                        <div class="card-header ">
                            <div class="card-title">
                                <h3 class="card-label">Notificaciones</h3>

                            </div>
                            <div class="card-toolbar">

                                <a href="{{ route('notificaciones.crear') }}"
                                    class="btn btn-primary font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="flaticon2-plus-1"></i>
                                    </span>Nuevo
                                </a>
                            </div>

                        </div>
                        <div class="card-body">

                            <table class="table table-sm table-bordered table-head-custom table-hover"
                                id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="no-exportar">#</th>
                                        <th data-priority="1">Asunto</th>
                                        <th data-priority="2">Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!--end::Card-->
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
    $(document).ready(function() {
            var table = $('#kt_datatable').DataTable({
                dom: "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                responsive: true,
                processing: true,
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
                stateSave: true,
                //Trabajar del lado del server
                serverSide: true,
                //Peticion ajax que devuelve los registros
                ajax: {
                    url: "{{ route('notificaciones.index') }}",
                    type: 'GET',
                },
                columns: [{
                        data: 'sis_notificacionesid',
                        name: 'sis_notificacionesid',
                        searchable: false,
                        visible: false
                    },
                    {
                        data: 'asunto',
                        name: 'asunto',
                        
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                ]

            });
        });
</script>
@endsection