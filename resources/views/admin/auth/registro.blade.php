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

@php
$id=request()->id;
@endphp

<body>
    <div class="row h-100 w-100 mx-auto">

        <div class="col-md-8 d-none d-xl-block"
            style="width : 100%;height : 100%;background-image: url({{ asset('assets/media/login-fondo.png') }}); background-size: 100% 100%; ">
        </div>
        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <form action="{{ route('post_registro') }}" method="POST" id="formulario">
                    @csrf
                    <div class="text-center mb-10">
                        <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                    </div>
                    <div class="fv-row mb-5">
                        <div class="input-group" id="spinner">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="width:45px">
                                    <i class="far fa-address-card"></i>
                                </span>
                            </div>
                            <input
                                class="form-control form-control-lg {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                type="text" name="identificacion" id="identificacion" autocomplete="off"
                                value="{{ old('identificacion') }}" placeholder="Ingrese RUC"
                                onblur="recuperarInformacion()" />
                        </div>
                        @if ($errors->has('identificacion'))
                        <span class=" text-danger">{{ $errors->first('identificacion') }}</span>
                        @endif
                    </div>

                    <div class="fv-row mb-5">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="width:45px">
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
                                <span class="input-group-text" style="width:45px">
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
                                <span class="input-group-text" style="width:45px">
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
                                <span class="input-group-text" style="width:45px">
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
                                <span class="input-group-text" style="width:45px">
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

                    <div class="fv-row mb-5 d-none">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="width:45px">
                                    <i class="socicon-mail"></i>
                                </span>
                            </div>
                            <select class="form-control form-control-lg" name="red_origen" id="red_origen">
                                <option value="1" {{$id=='' ? 'Selected' : '' }}>
                                    PERSEO
                                </option>
                                <option value="2" {{$id=='contafacil' ? 'Selected' : '' }}>
                                    CONTAFACIL
                                </option>
                                <option value="3" {{$id=='UIO-01' ? 'Selected' : '' }}>
                                    UIO-01
                                </option>
                                <option value="4" {{$id=='GYE-02' ? 'Selected' : '' }}>
                                    GYE-02
                                </option>
                                <option value="5" {{$id=='CUE-01' ? 'Selected' : '' }}>
                                    CUE-01
                                </option>
                                <option value="6" {{$id=='STO-01' ? 'Selected' : '' }}>
                                    STO-01
                                </option>
                                <option value="7" {{$id=='CNV-01' ? 'Selected' : '' }}>
                                    CNV-01
                                </option>
                                <option value="8" {{$id=='MATRIZ' ? 'Selected' : '' }}>
                                    MATRIZ
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="card card-custom gutter-b" style="height: 150px">
                        <div class="card-body">
                            <span class="svg-icon svg-icon-3x svg-icon-success">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                        <path
                                            d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                            fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                        <path
                                            d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                            fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <div class="text-dark font-weight-bolder font-size-h2 mt-3">8,600</div>
                            <a href="#" class="text-muted text-hover-primary font-weight-bold font-size-lg mt-1">New
                                Customers</a>
                        </div>
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
    <script src="{{ asset('assets/plugins/scripts.bundle.js') }}"></script>
    <script>
        //Notificaciones
        @foreach (session('flash_notification', collect())->toArray() as $message)
            $.notify(
                {
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
                    type: '{{$message['level']}}',
                }
            );
	    @endforeach

        $(document).ready(function () {
            //Iniciar select2
            $('.select2').select2({
                width: '100%'
            });

            if('{{$identificacion!=0}}'==true){
                setTimeout(function() {
                    window.location.href = "https://perseo-data-c3.app/sistema?identificacion="+'{{$identificacion}}';
                }, 1000);
            }
        });

        function recuperarInformacion() {

            $.ajaxSetup({
                headers: {
                    'usuario': 'perseo',
                    'clave': 'Perseo1232*'
                }
            });

            var cad = document.getElementById('identificacion').value;
            $("#spinner").addClass("spinner spinner-success spinner-right");
            $.post('{{ route('identificaciones.index') }}', {
                _token: '{{ csrf_token() }}',
                identificacion: cad
            }, function(data) {
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
            });
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