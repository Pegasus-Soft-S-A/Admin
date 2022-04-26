<html lang="es">
<!--begin::Head-->

<head>
    <meta charset="utf-8" />
    <title>Registro | Perseo Sistema Contable</title>
    <meta name="description" content="Perseo" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logoP.png') }}">

</head>


<body>
    <div class="row h-100 w-100 mx-auto">

        <div class="col-md-8 d-none d-xl-block"
            style="width : 100%;height : 100%;background-image: url({{ asset('assets/media/login-fondo.png') }}); background-size: 100% 100%; ">
        </div>
        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <form action="{{ route('post_registro') }}" method="POST">
                    @csrf
                    <div class="text-center mb-10">
                        <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                    </div>
                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-address-card"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                type="text" name="identificacion" id="identificacion" autocomplete="off"
                                value="{{ old('identificacion') }}" placeholder="Ingrese RUC" />
                        </div>
                        @if ($errors->has('identificacion'))
                        <span class=" text-danger">{{ $errors->first('identificacion') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                                type="text" name="nombres" id="nombres" autocomplete="off" value="{{ old('nombres') }}"
                                placeholder="Ingrese Nombres" />
                        </div>
                        @if ($errors->has('nombres'))
                        <span class=" text-danger">{{ $errors->first('nombres') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-home"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
                                type="text" name="direccion" id="direccion" autocomplete="off"
                                value="{{ old('direccion') }}" placeholder="Ingrese Dirección" />
                        </div>
                        @if ($errors->has('direccion'))
                        <span class=" text-danger">{{ $errors->first('direccion') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-city"></i>
                                </span>
                            </div>
                            <select class="form-control form-control-lg" name="provinciasid" id="provinciasid">
                                <option value="">Seleccione una provincia</option>
                                <option value="01" {{ old('provinciasid')=='01' ? 'Selected' : '' }}>
                                    Azuay
                                </option>
                                <option value="02" {{ old('provinciasid')=='02' ? 'Selected' : '' }}>
                                    Bolivar
                                </option>
                                <option value="03" {{ old('provinciasid')=='03' ? 'Selected' : '' }}>
                                    Cañar
                                </option>
                                <option value="04" {{ old('provinciasid')=='04' ? 'Selected' : '' }}>
                                    Carchi
                                </option>
                                <option value="05" {{ old('provinciasid')=='05' ? 'Selected' : '' }}>
                                    Chimborazo
                                </option>
                                <option value="06" {{ old('provinciasid')=='06' ? 'Selected' : '' }}>
                                    Cotopaxi
                                </option>
                                <option value="07" {{ old('provinciasid')=='07' ? 'Selected' : '' }}>
                                    El Oro
                                </option>
                                <option value="08" {{ old('provinciasid')=='08' ? 'Selected' : '' }}>
                                    Esmeraldas
                                </option>
                                <option value="09" {{ old('provinciasid')=='09' ? 'Selected' : '' }}>
                                    Guayas
                                </option>
                                <option value="20" {{ old('provinciasid')=='20' ? 'Selected' : '' }}>
                                    Galapagos
                                </option>
                                <option value="10" {{ old('provinciasid')=='10' ? 'Selected' : '' }}>
                                    Imbabura
                                </option>
                                <option value="11" {{ old('provinciasid')=='11' ? 'Selected' : '' }}>
                                    Loja</option>
                                <option value="12" {{ old('provinciasid')=='12' ? 'Selected' : '' }}>
                                    Los Rios
                                </option>
                                <option value="13" {{ old('provinciasid')=='13' ? 'Selected' : '' }}>
                                    Manabi
                                </option>
                                <option value="14" {{ old('provinciasid')=='14' ? 'Selected' : '' }}>
                                    Morona
                                    Santiago</option>
                                <option value="15" {{ old('provinciasid')=='15' ? 'Selected' : '' }}>
                                    Napo</option>
                                <option value="22" {{ old('provinciasid')=='22' ? 'Selected' : '' }}>
                                    Orellana
                                </option>
                                <option value="16" {{ old('provinciasid')=='16' ? 'Selected' : '' }}>
                                    Pastaza
                                </option>
                                <option value="17" {{ old('provinciasid')=='17' ? 'Selected' : '' }}>
                                    Pichincha
                                </option>
                                <option value="24" {{ old('provinciasid')=='24' ? 'Selected' : '' }}>
                                    Santa Elena
                                </option>
                                <option value="23" {{ old('provinciasid')=='23' ? 'Selected' : '' }}>
                                    Santo Domingo
                                    De Los Tsachilas</option>
                                <option value="21" {{ old('provinciasid')=='21' ? 'Selected' : '' }}>
                                    Sucumbios
                                </option>
                                <option value="18" {{ old('provinciasid')=='18' ? 'Selected' : '' }}>
                                    Tungurahua
                                </option>
                                <option value="19" {{ old('provinciasid')=='19' ? 'Selected' : '' }}>
                                    Zamora
                                    Chinchipe</option>
                            </select>
                        </div>
                        @if ($errors->has('provinciasid'))
                        <span class=" text-danger">{{ $errors->first('provinciasid') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('telefono1') ? 'is-invalid' : '' }}"
                                type="text" name="telefono1" id="telefono1" autocomplete="off"
                                value="{{ old('telefono1') }}" placeholder="Ingrese Convencional" />
                        </div>
                        @if ($errors->has('telefono1'))
                        <span class=" text-danger">{{ $errors->first('telefono1') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fab fa-whatsapp"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('telefono2') ? 'is-invalid' : '' }}"
                                type="text" name="telefono2" id="telefono2" autocomplete="off"
                                value="{{ old('telefono2') }}" placeholder="Ingrese Whatsapp" />
                        </div>
                        @if ($errors->has('telefono2'))
                        <span class=" text-danger">{{ $errors->first('telefono2') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="socicon-mail"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('correos') ? 'is-invalid' : '' }}"
                                type="text" name="correos" id="correos" autocomplete="off" value="{{ old('correos') }}"
                                placeholder="Ingrese Correo" />
                        </div>
                        @if ($errors->has('correos'))
                        <span class=" text-danger">{{ $errors->first('correos') }}</span>
                        @endif
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-lg btn-primary w-100 mb-5" id="ingresar">
                            <span class="indicator-label">Registrarse</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--end::Main-->
    <script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>
    <script>
        //Notificaciones
        @foreach (session('flash_notification', collect())->toArray() as $message)
            $.notify({
                // options
                message: '{{ $message['message'] }}',
                },
                {
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

        $(document).ready(function () {
            //Iniciar select2
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
</body>


</html>