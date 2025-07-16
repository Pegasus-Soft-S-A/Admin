<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta name="description" content="Perseo"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta property="og:image" content="{{ asset('assets/media/imagenperseo.jpg') }}"/>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>
    <link href="{{ asset('assets/plugins/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logoP.png') }}">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <title>Admin Perseo | Sistema de Gestión</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
        }

        /* Columna de imagen */
        .imagen-section {
            flex: 1.5;
            background-image: url('{{ asset('assets/media/perseo-login-admin.jpg') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            transition: all 0.3s ease;
        }

        /* Columna del formulario */
        .formulario-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
            min-width: 0;
        }

        .formulario-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .formulario-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 2;
            max-height: 90vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            height: 80px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .logo-container h3 {
            color: #2d3748;
            font-weight: 600;
            margin-top: 0.8rem;
            margin-bottom: 0.3rem;
            font-size: 1.4rem;
        }

        .logo-container p {
            color: #718096;
            font-size: 0.9rem;
            margin: 0;
        }

        /* Formulario */
        .form-title {
            text-align: center;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
            height: 48px;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-control-custom.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
            font-size: 1.1rem;
            z-index: 1;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .checkbox-container {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .checkbox-custom {
            opacity: 0;
            position: absolute;
            width: 0;
            height: 0;
        }

        .checkbox-styled {
            width: 20px;
            height: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
            position: relative;
        }

        .checkbox-styled::after {
            content: '';
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .checkbox-custom:checked + .checkbox-styled {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .checkbox-custom:checked + .checkbox-styled::after {
            opacity: 1;
        }

        .checkbox-custom:focus + .checkbox-styled {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .checkbox-label {
            color: #718096;
            font-size: 0.85rem;
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-login:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        /* Animaciones principales */
        .formulario-wrapper {
            animation: fadeInUp 0.6s ease-out;
        }

        .stats-container {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .login-container {
                flex-direction: column;
            }

            .imagen-section {
                display: none;
            }

            .formulario-section {
                flex: none;
                min-height: 100vh;
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .stats-numero {
                font-size: 2rem;
            }

            .formulario-wrapper {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
<div class="login-container">
    <!-- Columna de imagen -->
    <div class="imagen-section"></div>

    <!-- Columna del formulario -->
    <div class="formulario-section">
        <div class="formulario-wrapper">
            <!-- Logo y título -->
            <div class="logo-container">
                <img src="{{ asset('assets/media/login.png') }}" alt="Perseo Logo"/>
                <h3>Admin Perseo</h3>
                <p>Sistema de Gestión Empresarial</p>
            </div>

            <!-- Formulario -->
            <form action="{{ route('post_login') }}" method="POST" id="loginForm">
                @csrf
                <h4 class="form-title">Acceso Administrativo</h4>

                <!-- Identificación -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="far fa-address-card input-icon"></i>
                        <input
                            class="form-control-custom {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                            type="text"
                            name="identificacion"
                            id="identificacion"
                            autocomplete="off"
                            value="{{ old('identificacion') }}"
                            placeholder="Ingrese su identificación"
                            onkeypress="return validarNumero(event)"/>
                    </div>
                    @if ($errors->has('identificacion'))
                        <div class="error-message">{{ $errors->first('identificacion') }}</div>
                    @endif
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fa fa-key input-icon"></i>
                        <input
                            class="form-control-custom {{ $errors->has('contrasena') ? 'is-invalid' : '' }}"
                            type="password"
                            name="contrasena"
                            id="contrasena"
                            autocomplete="off"
                            placeholder="Ingrese su contraseña"/>
                    </div>
                    @if ($errors->has('contrasena'))
                        <div class="error-message">{{ $errors->first('contrasena') }}</div>
                    @endif
                </div>

                <!-- Recordar sesión -->
                <div class="checkbox-wrapper">
                    <label class="checkbox-container">
                        <input type="checkbox" name="recordar" id="recordar" class="checkbox-custom">
                        <span class="checkbox-styled"></span>
                        <span class="checkbox-label">Mantener sesión iniciada</span>
                    </label>
                </div>

                <!-- Botón de login -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="fas fa-sign-in-alt mr-2 text-white"></i>
                    <span class="indicator-label">Iniciar Sesión</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>

<script>
    // ====================================
    // CONFIGURACIÓN Y CONSTANTES
    // ====================================
    const LoginApp = {
        config: {
            contador: {
                final: 15000,
                duracion: 2000,
                pasos: 100
            },
            validacion: {
                tiempoEspera: 500 // ms antes de enviar
            }
        },

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        init() {
            this.configurarEventos();
            this.inicializarContador();
            this.procesarNotificaciones();
            this.enfocarPrimerInput();
        },

        configurarEventos() {
            // Eventos del formulario
            $('#loginForm').on('submit', e => this.manejarEnvioFormulario(e));
            $('#identificacion').on('keypress', this.validarNumero);

            // Enter para enviar formulario
            $('#identificacion, #contrasena').on('keypress', e => {
                if (e.keyCode === 13) {
                    $('#loginForm').submit();
                }
            });
        },

        enfocarPrimerInput() {
            $('#identificacion').focus();
        },

        // ====================================
        // VALIDACIONES
        // ====================================
        validarNumero(e) {
            const codigo = e.which || e.keyCode;
            // Permitir backspace, delete, tab, escape, enter y números
            if ([8, 9, 27, 13, 46].includes(codigo) ||
                // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (codigo === 65 && e.ctrlKey) ||
                (codigo === 67 && e.ctrlKey) ||
                (codigo === 86 && e.ctrlKey) ||
                (codigo === 88 && e.ctrlKey)) {
                return true;
            }
            // Permitir solo números
            return codigo >= 48 && codigo <= 57;
        },

        // ====================================
        // GESTIÓN DEL FORMULARIO
        // ====================================
        manejarEnvioFormulario(e) {
            const $btn = $('#btnLogin');
            const $form = $('#loginForm');

            // Validar campos requeridos
            const identificacion = $('#identificacion').val().trim();
            const contrasena = $('#contrasena').val();

            if (!identificacion || !contrasena) {
                this.mostrarNotificacion('Por favor complete todos los campos', 'warning');
                e.preventDefault();
                return;
            }

            // Cambiar estado del botón
            $btn.html(`
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Validando...
            `).prop('disabled', true);

            // Simular tiempo de procesamiento
            setTimeout(() => {
                // El formulario se enviará naturalmente
            }, this.config.validacion.tiempoEspera);
        },

        // ====================================
        // NOTIFICACIONES
        // ====================================
        mostrarNotificacion(mensaje, tipo) {
            $.notify({
                message: mensaje,
            }, {
                showProgressbar: true,
                delay: 3000,
                mouse_over: "pause",
                placement: {from: "top", align: "right"},
                animate: {
                    enter: "animated fadeInUp",
                    exit: "animated fadeOutDown"
                },
                type: tipo,
            });
        },

        procesarNotificaciones() {
            @if(session()->has('flash_notification'))
                @foreach (session('flash_notification', collect())->toArray() as $message)
                this.mostrarNotificacion({!! json_encode($message['message']) !!}, {!! json_encode($message['level']) !!});
            @endforeach
            @endif
        }
    };

    // ====================================
    // FUNCIONES GLOBALES PARA COMPATIBILIDAD
    // ====================================
    function validarNumero(e) {
        return LoginApp.validarNumero(e);
    }

    // ====================================
    // INICIALIZACIÓN
    // ====================================
    $(document).ready(() => LoginApp.init());
</script>
</body>
</html>
