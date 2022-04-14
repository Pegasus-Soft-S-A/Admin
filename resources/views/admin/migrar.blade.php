@extends('admin.layouts.app')
@section('contenido')
<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
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
                                            <option value="0">
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
                                        <select class="form-control disabled" id="servidororigen" name="servidororigen">
                                            <option value="0">
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
                                        <select class="form-control disabled" id="licenciaorigen" name="licenciaorigen">
                                            <option value="0">
                                                Seleccione Licencia
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <label>Servidor Destino:</label>
                                        <select class="form-control disabled" id="servidordestino"
                                            name="servidorodestino">
                                            <option value="0">
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
                                        <select class="form-control disabled" id="licenciadestino"
                                            name="licenciaodestino">
                                            <option value="0">
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
        $('#licenciaorigen').empty();
        if($('#servidororigen').val()==0){
            $('#licenciaorigen').addClass( "disabled");
            $('#licenciaorigen').append('<option value="0">Seleccione Licencia</option>');
        }else{  
            $('#licenciaorigen').removeClass( "disabled");
            llenarLicencias($('#servidororigen').val(),1);
        }
    });

    $('#servidordestino').on('change', function(e){
        $('#licenciadestino').empty();
        if($('#servidordestino').val()==0){
            $('#licenciadestino').addClass( "disabled");
            $('#licenciadestino').append('<option value="0">Seleccione Licencia</option>');
        }else{  
            $('#licenciadestino').removeClass( "disabled");
            llenarLicencias($('#servidordestino').val(),2);
        }
    });

    $('#sis_clientesid').on('change', function(e){
        if($('#sis_clientesid').val()!=0){
            $('#servidororigen').removeClass( "disabled");
            $('#servidordestino').removeClass( "disabled");
        }else{
            $('#servidororigen').addClass( "disabled");
            $('#licenciaorigen').addClass( "disabled");
            $('#servidordestino').addClass( "disabled");
            $('#licenciadestino').addClass( "disabled");
        }
        $('#servidororigen').val(0);
        $('#licenciaorigen').empty();
        $('#licenciaorigen').append('<option value="0">Seleccione Licencia</option>'); 
        $('#servidordestino').val(0);
        $('#licenciadestino').empty();
        $('#licenciadestino').append('<option value="0">Seleccione Licencia</option>');  
    });

    function llenarLicencias(servidor,tipo){

        $.ajax({
            type:"GET",
            url: '/admin/licencia/' + servidor + '/' + $('#sis_clientesid').val(),
            success: function(data){
                $.each(data, function(fetch, licencia){
                    for(i = 0; i < licencia.length; i++){
                        if(tipo==1){
                            $('#licenciaorigen').append('<option value="'+ licencia[i].sis_licenciasid +'">'+ licencia[i].numerocontrato + '-' + licencia[i].producto +'</option>');
                        }else{
                            $('#licenciadestino').append('<option value="'+ licencia[i].sis_licenciasid +'">'+ licencia[i].numerocontrato + '-' + licencia[i].producto +'</option>');
                        }
                    }
                })
            }
        });
    }
</script>
@endsection