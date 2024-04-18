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
        <div class="col-md-7 d-none d-xl-block"
            style="width : 100%;height : 100%;background-image: url({{ asset('assets/media/perseo-admin.jpg') }}); background-size: 100% 100%; ">
        </div>

        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <div class="card card-custom">
                    <div class="card-body">
                        <form class="form" action="{{ route('usuarios.update_password') }}" method="POST">
                            @csrf
                            <div class="text-center mb-10">
                                <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                            </div>
                            <div class="pb-13 pt-lg-0 pt-5 text-center">
                                <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h4-lg">Cambiar Clave
                                </h3>
                            </div>
                            <div class="fv-row mb-5">
                                <span class="text-center">La contraseña debe tener al menos 8 caracteres, contener
                                    al menos una letra mayúscula,
                                    una letra
                                    minúscula y un
                                    número.</span>
                            </div>
                            <div class="fv-row mb-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>

                                    <input
                                        class="form-control form-control-lg  h-auto  {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        type="password" name="password" autocomplete="off"
                                        placeholder="Ingrese password" value="{{ old('password') }}" />

                                </div>

                                @if ($errors->has('password'))
                                <span class=" text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="fv-row mb-5">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="width:45px">
                                            <i class="fa fa-key"></i>
                                        </span>
                                    </div>
                                    <input
                                        class="form-control form-control-lg  h-auto  {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                        type="password" name="password_confirmation" autocomplete="off"
                                        placeholder="Confirmar password" />
                                </div>

                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                                    <span class="indicator-label">Actualizar Password</span>
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
            $.notify(
            {
            // options
            message: '{{ $message['message'] }}',
            },
            {
            // settings
            showProgressbar: true,
            delay: 5000,
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
<!--end::Body-->

</html>