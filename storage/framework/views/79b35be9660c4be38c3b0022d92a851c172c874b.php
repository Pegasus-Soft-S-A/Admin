<html lang="es">
<!--begin::Head-->

<head>
    <meta charset="utf-8" />
    <title>Perseo Sistema Contable</title>
    <meta name="description" content="Perseo" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    
    <link href="<?php echo e(asset('assets/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/plugins/plugins.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('assets/media/logoP.png')); ?>">

</head>


<body>
    <div class="row h-100 w-100 mx-auto">

        <div class="col-md-8 d-none d-xl-block"
            style="width : 100%;height : 100%;background-image: url(<?php echo e(asset('assets/media/perseo-inicio3.jpg')); ?>); background-size: 100% 100%; ">
        </div>

        <div class="mx-auto col-md-4 m-0 p-0 d-flex align-items-center">
            <div class="login-form login-signin mx-auto">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="text-center mb-10">
                            <img src="<?php echo e(asset('assets/media/login.png')); ?>" height="105px" alt="" />
                        </div>

                        <div class="fv-row mb-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <li class="fa fa-user"></li>
                                    </span>
                                </div>
                                <input
                                    class="form-control form-control-lg <?php echo e($errors->has('identificacion') ? 'is-invalid' : ''); ?>"
                                    type="text" name="identificacion" id="identificacion" autocomplete="off"
                                    onblur="verificarLogin()" value="<?php echo e(old('identificacion')); ?>"
                                    placeholder="Ingrese Identificaci&#243;n" onkeypress="return validarEnter(event)" />

                                <div id="spinner">
                                </div>
                            </div>

                            <span>Ingrese identificaci&#243;n y presione <b>ENTER</b></span>
                            <?php if($errors->has('identificacion')): ?>
                            <span class=" text-danger"><?php echo e($errors->first('identificacion')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="fv-row mb-10 d-none" id="perfilEscoger">
                            <div class="d-flex flex-stack mb-2">
                                <label class="form-label fw-bolder text-dark fs-6 mb-0">Escoja el perfil con el
                                    que
                                    desea ingresar: </label>
                            </div>
                            <select class="form-control  form-control-solid" id="perfil" name="perfil">
                            </select>
                        </div>
                        <div class="text-center ">
                            <a href="" id="redireccion">
                                <button type="button" disabled="disabled" class="btn btn-lg btn-primary w-100 mb-5"
                                    id="ingresar">
                                    <span class="indicator-label">INGRESAR</span>
                                </button>
                            </a>
                        </div>


                    </div>


                </div>
                <div>
                    <div class="card card-custom gutter-b mt-5">
                        <div class="bg-success-o-50 p-0 pl-3">
                            <label class="mt-2" style="font-size: 13px; font-weight:bold">
                                Aplicaci&#243;n de Escritorio
                            </label>
                        </div>
                        <div class="p-2">
                            <div class="row">
                                <div class="col-6  text-center">
                                    <a target="_blank"
                                        href="https://www.dropbox.com/s/4ntn6njpyjihec8/Instalador%20Perseo%20Web.exe?dl=1"
                                        style="color:black">
                                        <i class="fab fa-windows">
                                        </i>
                                        Windows
                                    </a>
                                </div>
                                <div class="col-6 text-center">
                                    <a target="_blank"
                                        href="https://www.dropbox.com/s/jwl78lilc5su0hj/Perseo-Software-Web.dmg?dl=1"
                                        style="color:black">
                                        <i class="fab fa-apple">
                                        </i>
                                        Mac OS
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-custom ">
                        <div class="bg-primary-o-50 p-0 pl-3">
                            <label class="mt-1" style="font-size: 13px;font-weight:bold">
                                Aplicaci&#243;n M&#243;vil
                            </label>
                        </div>
                        <div class="p-2">
                            <div class="row">
                                <div class="col-6  text-center">
                                    <a target="_blank"
                                        href="https://play.google.com/store/apps/details?id=com.perseo.perseomovil"
                                        style="color:black">
                                        <i class="fab fa-google-play">
                                        </i>
                                        Google Play
                                    </a>
                                </div>
                                <div class="col-6  text-center">
                                    <a target="_blank" href="https://apps.apple.com/us/app/perseo-movil/id1571805731"
                                        style="color:black">
                                        <i class="fab fa-apple">
                                        </i>
                                        App Store
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--end::Main-->
    <script src="<?php echo e(asset('assets/plugins/plugins.bundle.js')); ?>"></script>
    <script>
        document.getElementById("identificacion").focus();
        //Notificaciones
        <?php $__currentLoopData = session('flash_notification', collect())->toArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            $.notify(
            {
            // options
            message: '<?php echo e($message['message']); ?>',
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
            type: '<?php echo e($message['level']); ?>',
            }
            );
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        function verificarLogin() {
            let identificacion = $('#identificacion').val();
 	
            if (identificacion.length > 0) {
		jQuery("#perfil").attr("disabled","disabled");
 		$("#spinner").addClass("spinner spinner-success spinner-right");
                var select = document.getElementById("perfil");
                $.post('<?php echo e(route('post_loginredireccion')); ?>', {
                    _token: '<?php echo e(csrf_token()); ?>',
                    identificacion

                }, function(resultado) {

                    if (resultado != 'a' && resultado != 0) {
                        $('#perfilEscoger').removeClass('d-none');
                        $('#perfil').empty();
                        if (resultado.length == 1) {
                            jQuery("#redireccion").attr("href", resultado[0].dominio);
                            jQuery("#ingresar").removeAttr("disabled");
                            $('#perfilEscoger').addClass('d-none');
                            $('#ingresar').click();
                        } else {
                            for (var i = 0; i < resultado.length; i++) {


                                var option = document.createElement("option");
                                option.setAttribute("value", resultado[i].dominio);
                                let optionTexto = document.createTextNode(resultado[i].descripcion);
                                option.appendChild(optionTexto);
                                select.appendChild(option);
                                jQuery("#ingresar").removeAttr("disabled");
                                jQuery("#redireccion").attr("href", resultado[0].dominio);

                            }
			
 			jQuery("#perfil").removeAttr("disabled");
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
			$("#spinner").removeClass("spinner spinner-success spinner-right");
                })
            } else {
                $.notify({
                    // options
                    message: 'Ingrese una identificaci&#243;n',
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


</html><?php /**PATH C:\laragon\www\admin\resources\views/admin/auth/loginredireccion.blade.php ENDPATH**/ ?>