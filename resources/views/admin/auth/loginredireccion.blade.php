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
    <title>Acceso al Sistema | Perseo Sistema Contable</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .redireccion-container {
            min-height: 100vh;
            display: flex;
        }

        /* Columna de imagen */
        .imagen-section {
            flex: 1.5;
            background-image: url('{{ asset('assets/media/perseo-login-redireccion.png') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .imagen-section:hover {
            filter: brightness(1.1);
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
            height: 60px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .logo-container h3 {
            color: #2d3748;
            font-weight: 600;
            margin-top: 0.8rem;
            margin-bottom: 0.3rem;
            font-size: 1.3rem;
        }

        .logo-container p {
            color: #718096;
            font-size: 0.85rem;
            margin: 0;
        }

        /* Contador principal más llamativo */
        .contador-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            border-radius: 24px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            inset: 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: pulseGlow 3s ease-in-out infinite;
        }

        /* Efectos de fondo más sofisticados */
        .contador-stats::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            animation: shimmer 4s ease-in-out infinite;
        }

        .contador-stats::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            animation: rotate 8s linear infinite;
        }

        /* Número principal con efectos */
        .contador-numero {
            font-size: 3rem;
            font-weight: 900;
            color: white;
            margin: 0;
            position: relative;
            z-index: 3;
            line-height: 1;
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.5),
            0 0 40px rgba(255, 255, 255, 0.3),
            0 4px 8px rgba(0, 0, 0, 0.3);
            animation: counterGlow 2s ease-in-out infinite alternate;
        }

        /* Label mejorado */
        .contador-label {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 0.3rem;
            position: relative;
            z-index: 3;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Partículas decorativas */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            animation: particles 6s linear infinite;
        }

        .particle:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            top: 80%;
            left: 30%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            top: 30%;
            left: 70%;
            animation-delay: 1s;
        }

        .particle:nth-child(5) {
            top: 70%;
            left: 20%;
            animation-delay: 3s;
        }

        /* Animaciones */
        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes particles {
            0% {
                transform: translateY(0px) rotate(0deg);
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Formulario */
        .form-title {
            text-align: center;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 1.2rem;
            font-size: 1.15rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.65rem 1rem 0.65rem 2.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: white;
            height: 42px;
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
            font-size: 1rem;
            z-index: 1;
        }

        .spinner-overlay {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            background: transparent;
            display: none;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            z-index: 10;
        }

        .spinner-overlay.show {
            display: flex !important;
        }

        .spinner-overlay .spinner-border {
            width: 1rem;
            height: 1rem;
            color: #4F46E5;
        }

        /* Select personalizado */
        .select-custom {
            width: 100%;
            padding: 0.65rem 1rem 0.65rem 2.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: white;
            height: 42px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        .select-custom:focus {
            outline: none;
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Botones */
        .btn-acceso {
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

        .btn-acceso:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-acceso:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Secciones de apps */
        .apps-section {
            margin-top: 2rem;
        }

        .app-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .app-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .app-header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 12px 12px 0 0;
            margin: -1rem -1rem 1rem -1rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .app-header.mobile {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .app-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .app-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            color: #374151;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .app-link:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #374151;
            text-decoration: none;
        }

        .app-link i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
            }
            50% {
                box-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
            }
        }

        @keyframes shimmer {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .formulario-wrapper {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Helper text */
        .helper-text {
            color: #718096;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .redireccion-container {
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
            .contador-numero {
                font-size: 2.5rem;
            }

            .formulario-wrapper {
                padding: 1.5rem;
            }

            .app-links {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="redireccion-container">
    <!-- Columna de imagen -->
    <div class="imagen-section" onclick="window.open('https://perseo.ec/implementaciones-globales/', '_blank')"></div>

    <!-- Columna del formulario -->
    <div class="formulario-section">
        <div class="formulario-wrapper">
            <!-- Logo y título -->
            <div class="logo-container">
                <img src="{{ asset('assets/media/login.png') }}" alt="Perseo Logo"/>
                <h3>Acceso al Sistema</h3>
                <p>Ingresa a tu cuenta empresarial</p>
            </div>

            <!-- Contador de clientes -->
            <div class="contador-stats">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div id="contador" class="contador-numero" data-stop="{{ App\Models\Clientes::count() }}">0</div>
                <div class="contador-label">Empresas Confiando en Perseo</div>
            </div>

            <!-- Formulario de acceso -->
            <div>
                <h4 class="form-title">Buscar mi empresa</h4>

                <!-- Input de identificación -->
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
                            placeholder="Ingrese su RUC o cédula"
                            onkeypress="return validarEnter(event)"
                            style="padding-right: 3rem;"/>
                        <div class="spinner-overlay" id="spinner">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Buscando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="helper-text">
                        Ingrese su identificación y presione <strong>ENTER</strong>
                    </div>
                    @if ($errors->has('identificacion'))
                        <div class="text-danger mt-2" style="font-size: 0.8rem;">{{ $errors->first('identificacion') }}</div>
                    @endif
                </div>

                <!-- Select de perfiles (inicialmente oculto) -->
                <div class="form-group d-none" id="perfilEscoger">
                    <div class="input-wrapper">
                        <i class="fa fa-server input-icon"></i>
                        <select class="select-custom" id="perfil" name="perfil">
                            <!-- Opciones se llenan dinámicamente -->
                        </select>
                    </div>
                    <div class="helper-text">
                        Seleccione el servidor donde desea ingresar
                    </div>
                </div>

                <!-- Botón de ingreso -->
                <a href="" id="redireccion">
                    <button type="button" disabled class="btn-acceso" id="ingresar">
                        <i class="fas fa-sign-in-alt mr-2 text-white"></i>
                        <span class="indicator-label">INGRESAR AL SISTEMA</span>
                    </button>
                </a>

                <!-- Sección de aplicaciones -->
                <div class="apps-section">
                    <!-- Aplicación de Escritorio -->
                    <div class="app-card">
                        <div class="app-header">
                            <i class="fa fa-desktop mr-2 text-white"></i>
                            Aplicación de Escritorio
                        </div>
                        <div class="app-links">
                            <a href="https://www.dropbox.com/s/iwwqywxfdcekiv1/Instalador%20Perseo%20Web.exe?dl=1"
                               target="_blank" class="app-link">
                                <i class="fab fa-windows"></i>
                                Windows
                            </a>
                            <a href="https://www.dropbox.com/s/jwl78lilc5su0hj/Perseo-Software-Web.dmg?dl=1"
                               target="_blank" class="app-link">
                                <i class="fab fa-apple"></i>
                                Mac OS
                            </a>
                        </div>
                    </div>

                    <!-- Aplicación Móvil -->
                    <div class="app-card">
                        <div class="app-header mobile">
                            <i class="fa fa-mobile-alt mr-2 text-white"></i>
                            Aplicación Móvil
                        </div>
                        <div class="app-links">
                            <a href="https://play.google.com/store/apps/details?id=com.perseo.perseomovil"
                               target="_blank" class="app-link">
                                <i class="fab fa-google-play"></i>
                                Google Play
                            </a>
                            <a href="https://apps.apple.com/us/app/perseo-movil/id1571805731"
                               target="_blank" class="app-link">
                                <i class="fab fa-apple"></i>
                                App Store
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>

<script>
    // ====================================
    // CONFIGURACIÓN Y CONSTANTES
    // ====================================
    const RedireccionApp = {
        config: {
            urls: {
                verificacion: {!! json_encode(route('post_loginredireccion')) !!}
            },
            contador: {
                final: {!! App\Models\Clientes::count() !!},
                duracion: 2000,
                pasos: 100
            },
            csrf: {!! json_encode(csrf_token()) !!}
        },

        // Estado interno
        state: {
            ultimaConsulta: null,
            consultando: false
        },

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        init() {
            this.configurarEventos();
            this.inicializarContador();
            this.procesarNotificaciones();
            document.getElementById("identificacion").focus();
        },

        configurarEventos() {
            // Eventos del formulario
            $('#identificacion').on('blur', e => this.verificarLogin());
            $('#identificacion').on('input', e => {
                // Limpiar estado si el usuario modifica el contenido
                const valorActual = e.target.value.trim();
                if (this.state.ultimaConsulta && this.state.ultimaConsulta !== valorActual) {
                    this.resetearFormulario();
                }
            });
            $('#perfil').on('change', e => this.actualizarRedireccion(e.target.value));

            // Solo números en identificación
            $('#identificacion').on('keypress', this.soloNumeros);
        },

        // ====================================
        // CONTADOR ANIMADO
        // ====================================
        inicializarContador() {
            const {final, duracion, pasos} = this.config.contador;
            const $contador = $('#contador');
            const incremento = Math.ceil(final / pasos);
            const intervalo = duracion / pasos;
            let actual = 0;

            const timer = setInterval(() => {
                actual += incremento;
                if (actual >= final) {
                    actual = final;
                    clearInterval(timer);
                }
                $contador.text(actual.toLocaleString());
            }, intervalo);
        },

        // ====================================
        // VERIFICACIÓN DE LOGIN
        // ====================================
        verificarLogin() {
            const identificacion = $('#identificacion').val().trim();

            // Solo procesar si hay contenido en el input
            if (!identificacion) {
                this.resetearFormulario();
                this.state.ultimaConsulta = null;
                return;
            }

            // Evitar consultas duplicadas
            if (this.state.ultimaConsulta === identificacion || this.state.consultando) {
                return;
            }

            this.state.consultando = true;
            this.state.ultimaConsulta = identificacion;
            this.mostrarCargando(true);
            $('#perfil').prop('disabled', true);

            $.ajax({
                url: this.config.urls.verificacion,
                method: 'POST',
                data: {
                    _token: this.config.csrf,
                    identificacion: identificacion
                },
                success: (resultado) => {
                    this.mostrarCargando(false);
                    this.state.consultando = false;
                    this.procesarResultado(resultado);
                },
                error: () => {
                    this.mostrarCargando(false);
                    this.state.consultando = false;
                    this.mostrarNotificacion('Error de conexión', 'danger');
                }
            });
        },

        procesarResultado(resultado) {
            if (!resultado || resultado === 0 || resultado === 'a') {
                this.mostrarNotificacion('El cliente no existe o no tiene licencias', 'warning');
                this.resetearFormulario();
                return;
            }

            if (resultado.length === 1) {
                // Un solo servidor, redirigir automáticamente
                this.configurarAccesoDirecto(resultado[0].dominio);
            } else {
                // Múltiples servidores, mostrar selector
                this.mostrarSelectorPerfiles(resultado);
            }
        },

        configurarAccesoDirecto(url) {
            $('#redireccion').attr('href', url);
            $('#ingresar').prop('disabled', false);
            $('#perfilEscoger').addClass('d-none');
            // Auto-click después de un breve delay para mejor UX
            setTimeout(() => $('#ingresar')[0].click(), 500);
        },

        mostrarSelectorPerfiles(servidores) {
            const $select = $('#perfil').empty();

            servidores.forEach((servidor, index) => {
                $select.append(`<option value="${servidor.dominio}">${servidor.descripcion}</option>`);
            });

            $('#perfilEscoger').removeClass('d-none');
            $('#perfil').prop('disabled', false);
            $('#redireccion').attr('href', servidores[0].dominio);
            $('#ingresar').prop('disabled', false);
        },

        actualizarRedireccion(url) {
            $('#redireccion').attr('href', url);
        },

        // ====================================
        // UTILIDADES DE UI
        // ====================================
        mostrarCargando(mostrar) {
            if (mostrar) {
                $('#spinner').addClass('show');
            } else {
                $('#spinner').removeClass('show');
            }
        },

        resetearFormulario() {
            $('#perfilEscoger').addClass('d-none');
            $('#ingresar').prop('disabled', true);
            $('#redireccion').attr('href', '');
            // Resetear estado solo si el input está vacío
            if (!$('#identificacion').val().trim()) {
                this.state.ultimaConsulta = null;
            }
        },

        soloNumeros(e) {
            const codigo = e.which || e.keyCode;
            // Permitir backspace, delete, tab, escape, enter
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
        // NOTIFICACIONES
        // ====================================
        mostrarNotificacion(mensaje, tipo) {
            $.notify({
                message: mensaje,
            }, {
                showProgressbar: true,
                delay: 2500,
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
            @foreach (session('flash_notification', collect())->toArray() as $message)
                this.mostrarNotificacion({!! json_encode($message['message']) !!}, {!! json_encode($message['level']) !!});
            @endforeach
        }
    };

    // ====================================
    // FUNCIONES GLOBALES PARA COMPATIBILIDAD
    // ====================================
    function verificarLogin() {
        RedireccionApp.verificarLogin();
    }

    function validarEnter(e) {
        if (e.keyCode === 13) {
            verificarLogin();
        }
        return RedireccionApp.soloNumeros(e);
    }

    // ====================================
    // INICIALIZACIÓN
    // ====================================
    $(document).ready(() => RedireccionApp.init());
</script>
</body>
</html>
