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
                        <form class="form" id="formulario">
                            @csrf
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
                                            <span class="text-danger d-none" id="mensajeCliente">Seleccione un
                                                Cliente</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <label>Servidor Origen:</label>
                                            <select class="form-control disabled" id="servidororigen" name="servidororigen">
                                                <option value="">
                                                    Seleccione Servidor
                                                </option>
                                                @foreach ($servidores as $servidor)
                                                    <option value="{{ $servidor->sis_servidoresid }}">
                                                        {{ $servidor->descripcion }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger d-none" id="mensajeOrigen">Seleccione un Servidor de
                                                Origen</span>

                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <label>Licencia Origen:</label>
                                            <select class="form-control disabled" id="licenciaorigen" name="licenciaorigen">
                                                <option value="">
                                                    Seleccione Licencia
                                                </option>
                                            </select>
                                            <span class="text-danger d-none" id="mensajeLicencia">Seleccione una
                                                Licencia</span>

                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <label>Servidor Destino:</label>
                                            <select class="form-control disabled" id="servidordestino"
                                                name="servidordestino">
                                                <option value="">
                                                    Seleccione Servidor
                                                </option>
                                                @foreach ($servidores as $servidor)
                                                    <option value="{{ $servidor->sis_servidoresid }}">
                                                        {{ $servidor->descripcion }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger d-none" id="mensajeDestino">Seleccione un Servidor de
                                                Destino</span>

                                        </div>

                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button id="enviar" type="button" class="btn btn-primary mr-2">Migrar</button>
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
@section('modal')
<div class="modal" id="carga">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
        role="document">
        <div class="modal-content position-relative">
            <div class="c-preloader text-center p-3">
                <i class="las la-spinner la-spin la-3x"></i>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
        $('#servidororigen').on('change', function(e) {
            $('#licenciaorigen').empty();
            if ($('#servidororigen').val() == 0) {
                $('#licenciaorigen').addClass("disabled");
                $('#licenciaorigen').append('<option value="0">Seleccione Licencia</option>');
            } else {
                $('#licenciaorigen').removeClass("disabled");
                llenarLicencias($('#servidororigen').val());
            }
        });


        $('#sis_clientesid').on('change', function(e) {
            if ($('#sis_clientesid').val() != 0) {
                $('#servidororigen').removeClass("disabled");
                $('#servidordestino').removeClass("disabled");
            } else {
                $('#servidororigen').addClass("disabled");
                $('#licenciaorigen').addClass("disabled");
                $('#servidordestino').addClass("disabled");

            }
            $('#servidororigen').val(0);
            $('#licenciaorigen').empty();
            $('#licenciaorigen').append('<option value="0">Seleccione Licencia</option>');
            $('#servidordestino').val(0);

        });

        function llenarLicencias(servidor) {

            $.ajax({
                type: "GET",
                url: '/admin/licencia/' + servidor + '/' + $('#sis_clientesid').val(),
                success: function(data) {
                    $.each(data, function(fetch, licencia) {
                        for (i = 0; i < licencia.length; i++) {
                            $('#licenciaorigen').append('<option value="' + licencia[i]
                                .sis_licenciasid + '">' + licencia[i].numerocontrato + '-' +
                                licencia[i].producto + '</option>');

                        }
                    })
                }
            });
        }
        $('#enviar').click(function(event) {

            var sis_clientesid = $("#sis_clientesid").val();
            var servidororigen = $("#servidororigen").val();
            var licenciaorigen = $("#licenciaorigen").val();
            var servidordestino = $("#servidordestino").val();

            if (sis_clientesid == "" || sis_clientesid == null) {
                $('#mensajeCliente').removeClass("d-none");
                var clientes = 1;
            } else {
                $('#mensajeCliente').addClass("d-none");
                var clientes = 0;
            }

            if (servidororigen == "" || servidororigen == null) {
                $('#mensajeOrigen').removeClass("d-none");
                var origen = 1;
            } else {
                $('#mensajeOrigen').addClass("d-none");
                var origen = 0;
            }

            if (licenciaorigen == "" || licenciaorigen == null || licenciaorigen == "0" ) {
                $('#mensajeLicencia').removeClass("d-none");
                var licencia = 1;
            } else {
                $('#mensajeLicencia').addClass("d-none");
                var licencia = 0;
            }
            if (servidordestino == "" || servidordestino == null) {
                $('#mensajeDestino').removeClass("d-none");
                var destino = 1;
            } else {
                $('#mensajeDestino').addClass("d-none");
                var licencia = 0;
            }
            
            if(clientes == 1 || origen== 1 || licencia == 1 || destino== 1 ) return;
            
            $('#carga').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('.c-preloader').show();

            $.ajax({
                type: "POST",
                url: '{{ route('servidores.migrar') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    sis_clientesid: sis_clientesid,
                    servidororigen: servidororigen,
                    licenciaorigen: licenciaorigen,
                    servidordestino: servidordestino,
                },
                success: function(data) {
                    $('#carga').modal('hide');
                    if (data == 1) {
                        notificaciones('Restaurado Correctamente', "success");
                        $("#sis_clientesid").val("");
                        $('#sis_clientesid').change();
                        $('#servidororigen').val("");
                        $('#licenciaorigen').empty();
                        $('#licenciaorigen').append('<option value="0">Seleccione Licencia</option>');
                        $('#servidordestino').val("");

                    }
                    if (data == 2) {
                        notificaciones('Error Restaurando Empresa', "warning");

                    }
                    if (data == 3) {
                        notificaciones('Error Respaldando Base', "danger");


                    }
                }
            });
        });
    </script>
@endsection
