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
    <title>Soporte | Perseo Sistema Contable</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .soporte-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        .soporte-container::before {
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
            height: 70px;
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

        /* Formulario */
        .form-title {
            text-align: center;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
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

        .btn-buscar {
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
            margin-bottom: 1rem;
        }

        .btn-buscar:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-buscar:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-acceso {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-acceso:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            color: white;
            text-decoration: none;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .helper-text {
            color: #718096;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            text-align: center;
        }

        .success-section {
            background: rgba(79, 70, 229, 0.1);
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: center;
        }

        .success-icon {
            font-size: 2rem;
            color: #4F46E5;
            margin-bottom: 0.5rem;
        }

        .success-text {
            color: #312e81;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* Animaciones principales */
        .formulario-wrapper {
            animation: fadeInUp 0.6s ease-out;
        }

        .soporte-stats {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-numero {
                font-size: 2rem;
            }

            .formulario-wrapper {
                padding: 1.5rem;
            }

            .soporte-container {
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body>
<div class="soporte-container">
    <div class="formulario-wrapper">
        <!-- Logo y título -->
        <div class="logo-container">
            <img src="{{ asset('assets/media/login.png') }}" alt="Perseo Logo"/>
            <h3>Soporte Técnico</h3>
            <p>Acceso rápido para asistencia</p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('soporte') }}" method="POST" id="soporteForm">
            @csrf
            <h4 class="form-title">Buscar Contrato</h4>

            <!-- Número de contrato -->
            <div class="form-group">
                <div class="input-wrapper">
                    <i class="fa fa-list input-icon"></i>
                    <input
                        class="form-control-custom {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}"
                        type="text"
                        name="numerocontrato"
                        id="numerocontrato"
                        autocomplete="off"
                        value="{{ old('numerocontrato') }}"
                        placeholder="Ingrese número de contrato"
                        onkeypress="return validarEnter(event)"
                        style="padding-right: 3rem;"/>
                    <div class="spinner-overlay" id="spinner">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Buscando...</span>
                        </div>
                    </div>
                </div>
                <div class="helper-text">
                    Ingrese el número de contrato para buscar la licencia
                </div>
                @if ($errors->has('numerocontrato'))
                    <div class="error-message">{{ $errors->first('numerocontrato') }}</div>
                @endif
            </div>

            <!-- Botón de búsqueda -->
            <button type="submit" class="btn-buscar" id="ingresar">
                <i class="fas fa-search mr-2 text-white"></i>
                <span class="indicator-label">Buscar Contrato</span>
            </button>

            <!-- Resultado de búsqueda -->
            @if(session('url'))
                <div class="success-section">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="success-text">
                        ¡Contrato encontrado! Puede acceder al sistema.
                    </div>
                    <a href="{{ session('url') }}" class="btn-acceso">
                        <i class="fas fa-tools mr-2 text-white"></i>
                        Acceso Admin Soporte
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>

<script>
    // ====================================
    // CONFIGURACIÓN Y CONSTANTES
    // ====================================
    const SoporteApp = {
        config: {
            validacion: {
                tiempoEspera: 300
            }
        },

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        init() {
            this.configurarEventos();
            this.procesarNotificaciones();
            this.enfocarInput();
        },

        configurarEventos() {
            // Eventos del formulario
            $('#soporteForm').on('submit', e => this.manejarEnvioFormulario(e));

            // Solo números y letras para el contrato
            $('#numerocontrato').on('keypress', this.validarCaracteres);
        },

        enfocarInput() {
            $('#numerocontrato').focus();
        },

        // ====================================
        // VALIDACIONES
        // ====================================
        validarCaracteres(e) {
            const codigo = e.which || e.keyCode;
            // Permitir números, letras y caracteres especiales de control
            if ([8, 9, 27, 13, 46].includes(codigo) ||
                // Permitir Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (codigo === 65 && e.ctrlKey) ||
                (codigo === 67 && e.ctrlKey) ||
                (codigo === 86 && e.ctrlKey) ||
                (codigo === 88 && e.ctrlKey)) {
                return true;
            }
            // Permitir números (0-9)
            if (codigo >= 48 && codigo <= 57) {
                return true;
            }
     
            return false;
        },

        // ====================================
        // GESTIÓN DEL FORMULARIO
        // ====================================
        manejarEnvioFormulario(e) {
            const numerocontrato = $('#numerocontrato').val().trim();

            if (!numerocontrato) {
                this.mostrarNotificacion('Ingrese un número de contrato', 'warning');
                e.preventDefault();
                return;
            }

            // Mostrar loading
            this.mostrarCargando(true);
            const $btn = $('#ingresar');
            $btn.html(`
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Buscando...
            `).prop('disabled', true);

            // Simular tiempo de procesamiento
            setTimeout(() => {
                // El formulario se enviará naturalmente
            }, this.config.validacion.tiempoEspera);
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
    function validarEnter(e) {
        if (e.keyCode === 13) {
            $('#soporteForm').submit();
        }
        return SoporteApp.validarCaracteres(e);
    }

    // ====================================
    // INICIALIZACIÓN
    // ====================================
    $(document).ready(() => SoporteApp.init());
</script>
</body>
</html>
