<html lang="es">
<!--begin::Head-->

<head>
    <meta charset="utf-8" />
    <title>Perseo | Redireccion</title>
    <meta name="description" content="Perseo" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ asset('assets/css/login-1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logoP.png') }}">

</head>


<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    <div class="d-flex flex-column flex-root">

        <div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white"
            id="kt_login">

            <div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #1F86FC;">

                <div class="d-flex flex-column-auto flex-column pt-lg-10 pt-15">

                    <a href="#" class="text-center mb-5 mx-auto">
                        <img src="{{ asset('assets/media/login.png') }}" height="105px" alt="" />
                    </a>

                    <h3 class=" font-weight-bolder text-center font-size-h4 font-size-h1-lg pb-10"
                        style="color: #ffffff;">
                        Sistema de Redirección </h3>

                </div>

                <div class="d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center"
                    style="background-image: url({{ asset('assets/media/login-fondo.png') }})">
                </div>

            </div>

            <div
                class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">

                <div class="d-flex flex-column-fluid flex-center">

                    <div class="login-form login-signin">


                        <div class="text-center mb-10">
                            <h1 class="text-dark mb-3">Perseo Software</h1>
                        </div>


                        <div class="fv-row mb-10">
                            <label class="form-label fs-6 fw-bolder text-dark">Cliente: </label>
                            <input
                                class="form-control form-control-lg form-control-solid  {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                type="text" name="identificacion" id="identificacion" autocomplete="off"
                                onblur="verificarLogin()" value="{{ old('identificacion') }}"
                                onkeypress="return validarEnter(event)" />
                            @if ($errors->has('identificacion'))
                                <span class=" text-danger">{{ $errors->first('identificacion') }}</span>
                            @endif
                        </div>


                        <div class="fv-row mb-10 d-none" id="perfilEscoger">
                            <div class="d-flex flex-stack mb-2">
                                <label class="form-label fw-bolder text-dark fs-6 mb-0">Escoja el perfil con el que
                                    desea ingresar: </label>
                            </div>
                            <select class="form-control  form-control-solid" id="perfil" name="perfil">

                            </select>

                        </div>
                        <div class="text-center">
                            <a href="" id="redireccion" target="_blank">
                                <button type="button" disabled="disabled" class="btn btn-lg btn-primary w-100 mb-5"
                                    id="ingresar">
                                    <span class="indicator-label">INGRESAR</span>

                                </button>
                            </a>

                        </div>


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

        function verificarLogin() {
            let identificacion = $('#identificacion').val();
            var select = document.getElementById("perfil");
            $.post('{{ route('post_loginredireccion') }}', {
                _token: '{{ csrf_token() }}',
                identificacion

            }, function(resultado) {
                if (resultado != 'a' && resultado != 0) {

                    $('#perfilEscoger').removeClass('d-none');
                    $('#perfil').empty();

                    for (var i = 0; i < resultado.length; i++) {
                        var option = document.createElement("option");
                        option.setAttribute("value", resultado[i].dominio);
                        let optionTexto = document.createTextNode(resultado[i].descripcion);
                        option.appendChild(optionTexto);
                        select.appendChild(option);
                        jQuery("#ingresar").removeAttr("disabled");
                        jQuery("#redireccion").attr("href", resultado[0].dominio);
                    }
                } else {
                    $.notify({
                        // options
                        message: 'El cliente no existe o no tiene licencias',
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
                        type: 'warning',
                    });
                    $('#perfilEscoger').addClass('d-none');
                    jQuery("#ingresar").attr("disabled", "disabled");
                }
            })
        }
        $("#perfil").change(function() {
            var escoger = $("#perfil").val();
            console.log(escoger);
            jQuery("#redireccion").attr("href", escoger);
        });

        function validarEnter(e) {
            if (event.keyCode == 13) {
                verificarLogin();

            }
        }
    </script>
</body>


</html>
