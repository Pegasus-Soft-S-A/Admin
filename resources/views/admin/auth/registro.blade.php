<html lang="es">
<!--begin::Head-->

<head>
    <meta charset="utf-8" />
    <title>Registro | Perseo Sistema Contable</title>
    <meta name="description" content="Perseo" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logoP.png') }}">
    <meta property="og:image" content="{{ asset('assets/media/imagenperseo.jpg') }}" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/tag.min.css') }}" rel="stylesheet" type="text/css" />
</head>

@php
$id = request()->id;
$grupos = App\Models\Grupos::get();
@endphp

<body>
    <div class="row h-100 w-100 mx-auto">

        <div class="col-md-8 d-none d-xl-block bg-danger"
            style="width : 100%;min-height : 100%;background-image: url({{ asset('assets/media/perseo-registro.jpg') }}); background-size: 100% 100%; background-repeat:no-repeat;">
        </div>
        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <div class="card card-custom">
                    <div class="card-body">
                        <form action="{{ route('post_registro') }}" method="POST" id="formulario">
                            @csrf

                            <div class="text-center mb-3">
                                <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                            </div>
                            <div class="card card-custom card-stretch mb-3">
                                <!--begin::Body-->
                                <div class="card-body d-flex align-items-center py-0 mt-3">
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                        <span id="contador" href="#"
                                            class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-2"
                                            data-stop="{{ App\Models\Clientes::count() }}">0</span>
                                        <span class="font-weight-bold text-muted font-size-lg">Clientes Activos</span>
                                    </div>
                                    <img src="{{ asset('assets/media/user.svg') }}" alt=""
                                        class="align-self-end h-80px">
                                </div>
                                <!--end::Body-->
                            </div>
                            <div class="fv-row mb-3">
                                <div class="input-group" id="spinner">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="far fa-address-card"></i>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-sm {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                        type="text" name="identificacion" id="identificacion" autocomplete="off"
                                        value="{{ old('identificacion') }}" placeholder="Ingrese RUC"
                                        onblur="verificarruc()" onkeypress="return validarNumero(event)" />
                                    <div id="spinner">
                                    </div>
                                </div>
                                <span class="text-danger d-none" id="mensajeBandera">El Ruc no es válido</span>
                                @if ($errors->has('identificacion'))
                                <span class=" text-danger">{{ $errors->first('identificacion') }}</span>
                                @endif
                            </div>

                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-user"></i>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-sm {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                                        type="text" name="nombres" id="nombres" autocomplete="off"
                                        value="{{ old('nombres') }}" placeholder="Ingrese Nombres" />
                                </div>
                                @if ($errors->has('nombres'))
                                <span class=" text-danger">{{ $errors->first('nombres') }}</span>
                                @endif
                            </div>

                            <style>
                                .select2 {
                                    max-width: 300px !important;
                                }

                                .select2-container .select2-selection--single {
                                    max-height: 32px !important;
                                    border-radius: 0px 4px 4px 0px !important;

                                }

                                .select2-selection__rendered {

                                    font-size: 0.925rem;
                                }
                            </style>
                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-home"></i>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-sm {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
                                        type="text" name="direccion" id="direccion" autocomplete="off"
                                        value="{{ old('direccion') }}" placeholder="Ingrese Dirección" />
                                </div>
                                @if ($errors->has('direccion'))
                                <span class=" text-danger">{{ $errors->first('direccion') }}</span>
                                @endif
                            </div>

                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                    </div>

                                    <select class="form-control selectDespegable" name="provinciasid" id="provinciasid"
                                        onchange="cambiarCiudad(this)">
                                        <option value=""></option>
                                        <option value="01">
                                            Azuay
                                        </option>
                                        <option value="02">
                                            Bolivar
                                        </option>
                                        <option value="03">
                                            Cañar
                                        </option>
                                        <option value="04">
                                            Carchi
                                        </option>
                                        <option value="05">
                                            Cotopaxi
                                        </option>
                                        <option value="06">
                                            Chimborazo
                                        </option>
                                        <option value="07">
                                            El Oro
                                        </option>
                                        <option value="08">
                                            Esmeraldas
                                        </option>
                                        <option value="09">
                                            Guayas
                                        </option>
                                        <option value="20">
                                            Galapagos
                                        </option>
                                        <option value="10">
                                            Imbabura
                                        </option>
                                        <option value="11">
                                            Loja</option>
                                        <option value="12">
                                            Los Rios
                                        </option>
                                        <option value="13">
                                            Manabi
                                        </option>
                                        <option value="14">
                                            Morona
                                            Santiago</option>
                                        <option value="15">
                                            Napo</option>
                                        <option value="22">
                                            Orellana
                                        </option>
                                        <option value="16">
                                            Pastaza
                                        </option>
                                        <option value="17">
                                            Pichincha
                                        </option>
                                        <option value="24">
                                            Santa Elena
                                        </option>
                                        <option value="23">
                                            Santo Domingo
                                            De Los Tsachilas</option>
                                        <option value="21">
                                            Sucumbios
                                        </option>
                                        <option value="18">
                                            Tungurahua
                                        </option>
                                        <option value="19">
                                            Zamora
                                            Chinchipe</option>
                                    </select>
                                </div>
                                @if ($errors->has('provinciasid'))
                                <span class=" text-danger">{{ $errors->first('provinciasid') }}</span>
                                @endif
                            </div>


                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-city"></i>
                                        </span>
                                    </div>
                                    <select class="form-control selectDespegable" name="ciudadesid" id="ciudadesid"
                                        onchange="setTextFieldCiudad(this)">
                                        <option value="">Seleccione una ciudad</option>

                                    </select>
                                </div>

                                @if ($errors->has('ciudadesid'))
                                <span class=" text-danger">{{ $errors->first('ciudadesid') }}</span>
                                @endif

                            </div>

                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-fas fa-briefcase"></i>
                                        </span>
                                    </div>
                                    <select class="form-control selectDespegable" name="grupo" id="grupo">
                                        <option value=""></option>
                                        @foreach ($grupos as $grupo)
                                        <option value="{{ $grupo->gruposid }}" {{ old('grupo')==$grupo->gruposid ?
                                            'selected' : '' }}>
                                            {{ $grupo->descripcion }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                @if ($errors->has('grupo'))
                                <span class=" text-danger">{{ $errors->first('grupo') }}</span>
                                @endif

                            </div>

                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fab fa-whatsapp"></i>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-sm {{ $errors->has('telefono2') ? 'is-invalid' : '' }}"
                                        type="text" name="telefono2" id="telefono2" autocomplete="off"
                                        value="{{ old('telefono2') }}" placeholder="Ingrese Whatsapp"
                                        onkeypress="return validarNumero(event)" />
                                </div>
                                @if ($errors->has('telefono2'))
                                <span class=" text-danger">{{ $errors->first('telefono2') }}</span>
                                @endif
                            </div>

                            <div class="fv-row mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="socicon-mail"></i>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-sm {{ $errors->has('correos') ? 'is-invalid' : '' }}"
                                        type="text" name="correos" id="correos" autocomplete="off"
                                        value="{{ old('correos') }}" placeholder="Ingrese Correo" />
                                </div>
                                @if ($errors->has('correos'))
                                <span class=" text-danger">{{ $errors->first('correos') }}</span>
                                @endif
                            </div>

                            <div class="fv-row mb-3 d-none">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="socicon-mail"></i>
                                        </span>
                                    </div>
                                    <select class="form-control form-control-sm" name="red_origen" id="red_origen">
                                        <option value="1" {{ $id=='' ? 'Selected' : '' }}>
                                            PERSEO
                                        </option>
                                        <option value="2" {{ $id=='contafacil' || $id=='CONTAFACIL' ? 'Selected' : ''
                                            }}>
                                            CONTAFACIL
                                        </option>
                                        <option value="3" {{ $id=='UIO-01' ? 'Selected' : '' }}>
                                            UIO-01
                                        </option>
                                        <option value="8" {{ $id=='UIO-02' ? 'Selected' : '' }}>
                                            UIO-02
                                        </option>
                                        <option value="6" {{ $id=='CUE-01' ? 'Selected' : '' }}>
                                            CUE-01
                                        </option>
                                        <option value="7" {{ $id=='STO-01' ? 'Selected' : '' }}>
                                            STO-01
                                        </option>
                                        <option value="10" {{ $id=='CNV-01' ? 'Selected' : '' }}>
                                            CNV-01
                                        </option>
                                        <option value="11" {{ $id=='MATRIZ' ? 'Selected' : '' }}>
                                            MATRIZ
                                        </option>
                                        <option value="12" {{ $id=='CUE-02' ? 'Selected' : '' }}>
                                            CUE-02
                                        </option>
                                        <option value="13" {{ $id=='CUE-03' ? 'Selected' : '' }}>
                                            CUE-03
                                        </option>
                                        <option value="14" {{ $id=='UIO-03' ? 'Selected' : '' }}>
                                            UIO-03
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <input id="texto_ciudad" type="hidden" name="texto_ciudad" value="" />

                            <div class="text-center">
                                <button type="submit" class="btn btn-lg btn-primary w-100 mb-2" id="ingresar">
                                    <span class="indicator-label">Registrarse</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--end::Main-->
    <script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/scripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/plugins/custom/tag.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        //Notificaciones
        @foreach (session('flash_notification', collect())->toArray() as $message)
            $.notify({
                // options
                message: '{{ $message['message'] }}',
            }, {
                // settings
                showProgressbar: true,
                delay: 2500,
                mouse_over: "pause",
                placement: {
                    from: "top",
                    align: "right",
                },
                animate: {
                    enter: "animated fadeInUp",
                    exit: "animated fadeOutDown",
                },
                type: '{{ $message['level'] }}',
            });
        @endforeach

        $(document).ready(function() {
            var $this = $($('#contador'));
            jQuery({
                Counter: 0
            }).animate({
                Counter: $this.attr('data-stop')
            }, {
                duration: 1000,
                easing: 'swing',
                step: function(now) {
                    $this.text(Math.ceil(now));
                }
            });

            //Iniciar select2

            $('#provinciasid').select2({
                placeholder: 'Seleccione una Provincia',

            });


            $('#ciudadesid').select2({
                placeholder: 'Seleccione una Ciudad',

            });

            $('#grupo').select2({
                placeholder: 'Seleccione un Tipo de Negocio',

            });

            if ('{{ $identificacion != 0 }}' == true) {
                setTimeout(function() {
                    window.location.href = "https://perseo-data-c3.app/sistema?identificacion=" +
                        '{{ $identificacion }}';
                }, 2000);
            }
        });

        function setTextFieldCiudad(ddl) {
            document.getElementById('texto_ciudad').value = ddl.options[ddl.selectedIndex].text;
        }

        function verificarruc() {
            var cad = document.getElementById('identificacion').value;

            var longitud = cad.length;
            if (longitud == 13 && cad !== "") {
                var extraer = cad.substr(10, 3);
                if (extraer == "001") {
                    recuperarInformacion(cad);
                    $('#mensajeBandera').addClass("d-none");
                    $('#identificacion').removeClass("is-invalid");
                } else {
                    $('#identificacion').focus();
                    $('#mensajeBandera').removeClass("d-none");
                    $('#identificacion').addClass("is-invalid");
                    camposvacios();
                }
            } else {
                $('#identificacion').focus();
                $('#mensajeBandera').removeClass("d-none");
                $('#identificacion').addClass("is-invalid");
                camposvacios();
            }
        }

        function recuperarInformacion() {


            var cad = document.getElementById('identificacion').value;
            $("#spinner").addClass("spinner spinner-success spinner-right");
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
                        $("#telefono1").val(data.telefono1);
                        $("#telefono2").val(data.telefono2);
                        $('#provinciasid').val(data.provinciasid);

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

                    data.map(ciudades =>
                        $('#ciudadesid').append('<option value="' + ciudades.ciudadesid + '">' + ciudades
                            .ciudad + '</option>')
                    );

                    document.getElementById("ciudadesid").onchange();
                }
            })
        }



        function camposvacios() {
            $("#nombres").val('');
            $("#direccion").val('');
            $("#correo").val('');
            $("#telefono2").val('');
            $('#provinciasid').val('');
        }

        $('#ingresar').click(function(event) {
            KTApp.blockPage({
                overlayColor: '#f3f6f9',
                state: 'primary',
                message: 'Registrando'
            });
        })
    </script>


</body>


</html>