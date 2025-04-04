<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Perseo | Admin</title>
    <meta name="description" content="Login page example" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

    <link href="{{ asset('assets/css/login-1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logoP.png') }}">

</head>

<body>
    <div class="row h-100 w-100 mx-auto">
        <div class="col-md-8 d-none d-xl-block"
            style="width : 100%;height : 100%;background-image: url({{ asset('assets/media/perseo-login-admin.jpg') }}); background-size: 100% 100%; ">
        </div>

        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <div class="card card-custom">
                    <div class="card-body">
                        <form class="form" action="{{ route('post_login') }}" method="POST">
                            @csrf
                            <div class="text-center mb-10">
                                <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                            </div>
                            <div class="pb-13 pt-lg-0 pt-5 text-center">
                                <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Admin Perseo
                                </h3>
                            </div>
                            <div class="fv-row mb-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="far fa-address-card"></i>
                                        </span>
                                    </div>

                                    <input class="form-control form-control-lg  h-auto  {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                        type="text" name="identificacion" autocomplete="off" id="usuario" placeholder="Ingrese Identificación"
                                        value="{{ old('identificacion') }}" />

                                </div>

                                @if ($errors->has('identificacion'))
                                    <span class="font-size-h6 text-danger">{{ $errors->first('identificacion') }}</span>
                                @endif
                            </div>

                            <div class="fv-row mb-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <input class="form-control form-control-lg  h-auto  {{ $errors->has('contrasena') ? 'is-invalid' : '' }}"
                                        type="password" name="contrasena" autocomplete="off" placeholder="Ingrese clave" />
                                </div>
                                @if ($errors->has('contrasena'))
                                    <span class="font-size-h6 text-danger">{{ $errors->first('contrasena') }}</span><br>
                                @endif
                            </div>

                            <div class="form-group d-flex flex-wrap justify-content-between align-items-center mt-5">
                                <div class="checkbox-inline">
                                    <label class="checkbox m-0 text-muted">
                                        <input type="checkbox" name="recordar">
                                        <span></span>Recordarme</label>
                                </div>

                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                                    <span class="indicator-label">Iniciar Sesión</span>
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
    </script>
</body>
<!--end::Body-->

</html>
