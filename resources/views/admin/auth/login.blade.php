<!DOCTYPE html>
<!--
Template Name: Metronic - Bootstrap 4 HTML, React, Angular 11 & VueJS Admin Dashboard Theme
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: https://1.envato.market/EA4JP
Renew Support: https://1.envato.market/EA4JP
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="es">
<!--begin::Head-->

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
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    <!--begin::Main-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Login-->
        <div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white"
            id="kt_login">
            <!--begin::Aside-->
            <div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #1F86FC;">
                <!--begin::Aside Top-->
                <div class="d-flex flex-column-auto flex-column pt-lg-10 pt-15">
                    <!--begin::Aside header-->
                    <a href="#" class="text-center mb-5 mx-auto">
                        <img src="{{ asset('assets/media/login-admin.png') }}" height="130px" " alt="" />
                    </a>
                    <!--end::Aside header-->
                    <!--begin::Aside title-->
                    <h3 class=" font-weight-bolder text-center font-size-h4 font-size-h1-lg pb-10"
                            style="color: #ffffff;">
                        Sistema de Registros </h3>
                        <!--end::Aside title-->
                </div>
                <!--end::Aside Top-->
                <!--begin::Aside Bottom-->
                <div class="d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center"
                    style="background-image: url({{ asset('assets/media/login-fondo-admin.png') }})">
                </div>
                <!--end::Aside Bottom-->
            </div>
            <!--begin::Aside-->
            <!--begin::Content-->
            <div
                class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
                <!--begin::Content body-->
                <div class="d-flex flex-column-fluid flex-center">
                    <!--begin::Signin-->
                    <div class="login-form login-signin">
                        <!--begin::Form-->
                        <form class="form" action="{{ route('post_login') }}" method="POST">
                            @csrf
                            <!--begin::Title-->
                            <div class="pb-13 pt-lg-0 pt-5">
                                <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Admin Perseo
                                </h3>
                            </div>
                            <!--begin::Title-->
                            <!--begin::Form group-->
                            <div class="form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark">Identificacion</label>
                                <input
                                    class="form-control form-control-lg form-control-solid h-auto py-6 px-6 rounded-lg {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                    type="text" name="identificacion" autocomplete="off"
                                    value="{{ old('identificacion') }}" />
                                @if ($errors->has('identificacion'))
                                <span class="font-size-h6 text-danger">{{ $errors->first('identificacion') }}</span>
                                @endif
                            </div>
                            <!--end::Form group-->
                            <!--begin::Form group-->
                            <div class="form-group">
                                <div class="d-flex justify-content-between mt-n5">
                                    <label class="font-size-h6 font-weight-bolder text-dark pt-5">Contraseña</label>
                                </div>
                                <input
                                    class="form-control form-control-lg form-control-solid h-auto py-6 px-6 rounded-lg {{ $errors->has('contrasena') ? 'is-invalid' : '' }}"
                                    type="password" name="contrasena" autocomplete="off" />
                                @if ($errors->has('contrasena'))
                                <span class="font-size-h6 text-danger">{{ $errors->first('contrasena') }}</span><br>
                                @endif
                                <div
                                    class="form-group d-flex flex-wrap justify-content-between align-items-center mt-5">
                                    <div class="checkbox-inline">
                                        <label class="font-size-h6 checkbox m-0 text-muted">
                                            <input type="checkbox" name="recordar">
                                            <span></span>Recordarme</label>
                                    </div>
                                    {{-- <a href="javascript:;" id="kt_login_forgot"
                                        class="font-size-h6 text-muted text-hover-primary ml-2">Olvidó su
                                        contraseña?</a> --}}
                                </div>
                            </div>
                            <!--end::Form group-->
                            <!--begin::Action-->
                            <div class="pb-lg-0 pb-5">
                                <button type="submit" id="kt_login_signin_submit"
                                    class="btn btn-primary btn-block font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Iniciar
                                    Sesión</button>

                            </div>
                            <!--end::Action-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Signin-->
                </div>
                <!--end::Content body-->

            </div>
            <!--end::Content-->
        </div>
        <!--end::Login-->
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
                    type: '{{$message['level']}}',
                }
            );
	    @endforeach
    </script>
</body>
<!--end::Body-->

</html>