@extends('admin.layouts.app')
@section('contenido')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->
                    <form class="form" id="formulario" method="POST">
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header flex-wrap py-5">
                                <div class="card-title">
                                    <h3 class="card-label">Cambio de Servidor </h3>
                                </div>
                            </div>

                            <div class="card-body">

                                @csrf
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label>Cliente:</label>
                                        <select class="form-control select2" id="sis_clientesid" name="sis_clientesid">
                                            <option value="">
                                                Seleccione un cliente
                                            </option>
                                            @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->sis_clientesid }}">
                                                {{ $cliente->identificacion }}-{{ $cliente->nombres }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label>Servidor Origen:</label>
                                        <select class="form-control" id="servidororigen" name="servidororigen">
                                            <option value="">
                                                Seleccione Servidor
                                            </option>
                                            @foreach ($servidores as $servidor)
                                            <option value="{{ $servidor->sis_servidoresid }}">
                                                {{ $servidor->descripcion }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Licencia Origen:</label>
                                        <select class="form-control" id="licenciaorigen" name="licenciaorigen">
                                            <option value="">
                                                Seleccione Licencia
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label>Servidor Destino:</label>
                                        <select class="form-control" id="servidorodestino" name="servidorodestino">
                                            <option value="">
                                                Seleccione Servidor
                                            </option>
                                            @foreach ($servidores as $servidor)
                                            <option value="{{ $servidor->sis_servidoresid }}">
                                                {{ $servidor->descripcion }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Licencia Destino:</label>
                                        <select class="form-control" id="licenciaodestino" name="licenciaodestino">
                                            <option value="">
                                                Seleccione Licencia
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="#" type="reset" class="btn btn-primary mr-2">Migrar</a>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Card-->
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('#servidororigen').on('change', function(e){

        var servidororigen = e.target.value;
        var cliente = $('#sis_clientesid').val();

        $('#licenciaorigen').empty();
        //$('#licenciaorigen').append('<option value="">Seleccione Licencia</option>');
        
        $.ajax({
            type:"GET",
            url: '/licencia/' + servidororigen + '/' + cliente,
            success: function(data){
                $.each(data, function(fetch, licencia){
                    for(i = 0; i < licencia.length; i++){
                    $('#licenciaorigen').append('<option value="'+ licencia[i].sis_licenciasid +'">'+ licencia[i].numerocontrato +'</option>');
                    }
                })
            }
        });
    });
</script>
@endsection