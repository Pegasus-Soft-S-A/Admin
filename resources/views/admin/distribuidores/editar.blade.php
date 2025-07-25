@extends('admin.layouts.app')
@section('contenido')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->
                    <form class="form"
                        action="{{ route('distribuidores.actualizar', $distribuidor->sis_distribuidoresid) }}"
                        method="POST">
                        @method('PUT')
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header flex-wrap py-5">
                                <div class="card-title">
                                    <h3 class="card-label">Distribuidores </h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                        <div class="btn-group" role="group" aria-label="First group">

                                            <a href="{{ route('distribuidores.index') }}"
                                                class="btn btn-secondary btn-icon" data-toggle="tooltip"
                                                title="Volver"><i class="la la-long-arrow-left"></i></a>

                                            <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                title="Guardar"><i class="la la-save"></i></button>

                                            <a href="{{ route('distribuidores.crear') }}"
                                                class="btn btn-warning btn-icon" data-toggle="tooltip" title="Nuevo"><i
                                                    class="la la-user-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @include('admin.distribuidores._form')
                            </div>

                            <div class="card-footer pt-2 pb-2">
                                <div class="row">
                                    <div class="col-md-3">
                                        <span class="font-size-sm font-weight-bolder text-dark ml-2">Auditoría</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="font-size-sm font-weight-bolder text-dark ml-2">Creación</span>
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$distribuidor->usuariocreacion}}</span>
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$distribuidor->fechacreacion}}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="font-size-sm font-weight-bolder text-dark ml-2">Modificación</span>
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$distribuidor->usuariomodificacion}}</span>
                                        <span
                                            class="font-size-sm text-primary ml-2">{{$distribuidor->fechamodificacion}}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end::Card-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('#correo').tokenfield({
        delimiter: [","],
    });

    function recuperarInformacion() {
        
        $.ajaxSetup({
            headers: {
                'usuario': 'perseo',
                'clave':'Perseo1232*'
            }
        });

    var cad = document.getElementById('identificacion').value;
        $("#spinner").addClass("spinner spinner-success spinner-right");
        $.post('{{ route('identificaciones.index') }}', {
            _token: '{{ csrf_token() }}',
            identificacion: cad
        }, function(data) {
            $("#spinner").removeClass("spinner spinner-success spinner-right");
            data=JSON.parse(data);
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