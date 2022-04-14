@extends('admin.layouts.app')
@section('contenido')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->
                    <form class="form" action="{{ route('clientes.guardar') }}" method="POST">
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header flex-wrap py-5">
                                <div class="card-title">
                                    <h3 class="card-label">Clientes </h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                        <div class="btn-group" role="group" aria-label="">
                                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-icon"
                                                data-toggle="tooltip" title="Volver"><i
                                                    class="la la-long-arrow-left"></i></a>

                                            <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                title="Guardar"><i class="la la-save"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">

                                @include('admin.clientes._form')

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
    $('#formulario').submit(function(event) {
        $("#provinciasid").prop("disabled", false);
        $("#distribuidor").prop("disabled", false);
        $("#vendedor").prop("disabled", false);
        $("#revendedor").prop("disabled", false);
        $("#red_origen").prop("disabled", false);
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
            url: '/admin/revendedoresdistribuidor/' + distribuidor + '/2',
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
            url: '/admin/revendedoresdistribuidor/' + distribuidor + '/1',
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
                $("#nombres").val(data.razon_social);
                $("#direccion").val(data.direccion);
                $("#correo").val(data.correo);
            
            }
        });
    }
</script>
@endsection