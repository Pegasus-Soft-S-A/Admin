<html lang="es">
<!--begin::Head-->

<head>
    <meta charset="utf-8" />
    <title>Perseo Sistema Contable</title>
    <meta name="description" content="Perseo" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    {{--
    <link href="{{ asset('assets/css/login-1.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logoP.png') }}">

</head>


<body>
    <div class="row h-100 w-100 mx-auto">

        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <div class="card card-custom">
                    <div class="card-body">
                        <form action="{{route('soporte')}}" method="POST">
                            @csrf
                            <div class="text-center mb-10">
                                <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                            </div>

                            <div class="fv-row mb-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <li class="fa fa-list"></li>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-lg {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}"
                                        type="text" name="numerocontrato" id="numerocontrato" autocomplete="off"
                                        onblur="verificarLogin()" value="{{ old('numerocontrato') }}"
                                        placeholder="Ingrese Contrato" onkeypress="return validarEnter(event)" />

                                    <div id="spinner">
                                    </div>
                                </div>

                                @if ($errors->has('numerocontrato'))
                                <span class=" text-danger">{{ $errors->first('numerocontrato') }}</span>
                                @endif
                            </div>

                            <div class="text-center ">
                                <button type="submit" class="btn btn-lg btn-primary w-100 mb-5" id="ingresar">
                                    <span class="indicator-label">Buscar</span>
                                </button>
                            </div>
                            @if(session('url'))
                            <div class="text-center ">
                                <a href=" {{session('url')}}" type="submit" class="btn btn-lg btn-success w-100 mb-5">
                                    Acceso Admin Soporte
                                </a>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--end::Main-->
    <script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>
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
            type: '{{ $message['level'] }}',
            }
            );
        @endforeach

    </script>
</body>


</html>