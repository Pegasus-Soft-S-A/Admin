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
    <link href="{{ asset('assets/plugins/custom/tag.min.css') }}" rel="stylesheet" type="text/css"/>
    <title>Registro | Perseo Sistema Contable</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .registro-container {
            min-height: 100vh;
            display: flex;
        }

        /* Columna de imagen */
        .imagen-section {
            flex: 1.5;
            background-image: url('{{ asset('assets/media/perseo-registro.jpg') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
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
            /*overflow-y: hidden;*/
            overflow-x: hidden; /* Añadido para prevenir scroll horizontal */
            min-width: 0; /* Permite que el flex item se encoja */
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
            max-height: 90vh; /* Limitar altura */
            overflow-y: auto; /* Scroll si es necesario */
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

        /* Responsive */
        @media (max-width: 768px) {
            .contador-numero {
                font-size: 3rem;
            }

            .contador-stats {
                padding: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Estilos del formulario */
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
            height: 42px; /* Altura fija */
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

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: auto !important; /* Cambiar de 48px a auto */
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem 0.75rem 3rem !important; /* Igual que los inputs */
            font-size: 0.9rem !important; /* Igual que los inputs */
            background: white !important;
            transition: all 0.3s ease !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: normal !important; /* Cambiar de 44px a normal */
            color: #2d3748;
            padding: 0 !important; /* Remover padding extra */
            margin: 0 !important; /* Remover margin extra */
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 100% !important; /* Cambiar de 44px a 100% */
            right: 1rem !important;
            top: 0 !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4F46E5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
            outline: none !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #4F46E5 !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        }

        /* Estilos para el dropdown */
        .select2-dropdown {
            border: 2px solid #4F46E5 !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.15) !important;
            margin-top: 4px !important;
        }

        .select2-results__option {
            padding: 0.75rem 1rem !important;
            font-size: 0.9rem !important;
            transition: all 0.3s ease !important;
        }

        .select2-results__option--highlighted {
            background-color: #4F46E5 !important;
            color: white !important;
        }

        /* Placeholder del select2 */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #718096 !important;
            line-height: normal !important;
        }

        /* Arrow del select2 */
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #718096 transparent transparent transparent !important;
            border-width: 6px 6px 0 6px !important;
        }

        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #718096 transparent !important;
            border-width: 0 6px 6px 6px !important;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: block;
        }

        .error-message.d-none {
            display: none;
        }

        .btn-registro {
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

        .btn-registro:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }

        .btn-registro:active {
            transform: translateY(0);
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

        .formulario-wrapper {
            animation: fadeInUp 0.6s ease-out;
        }

        .contador-stats {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .registro-container {
                flex-direction: column;
            }

            .imagen-section {
                display: none; /* Cambiar esta línea */
            }

            .formulario-section {
                flex: none;
                min-height: 100vh; /* Cambiar a 100vh para ocupar toda la pantalla */
                padding: 1rem;
            }

            /* resto del código permanece igual */
        }

    </style>
</head>

<body>
@php
    $id = request()->id ?? "PERSEO";
    $link = App\Models\Links::where('codigo', $id)->first();
    $grupos = App\Models\Grupos::get();

    if (!$link) {
        $link = new App\Models\Links;
        $link->sis_linksid = 1;
        $link->cedula_ruc = 2;
    }
@endphp

<div class="registro-container">
    <!-- Columna de imagen -->
    <div class="imagen-section"></div>

    <!-- Columna del formulario -->
    <div class="formulario-section">
        <div class="formulario-wrapper">
            <!-- Logo y título -->
            <div class="logo-container">
                <img src="{{ asset('assets/media/login.png') }}" alt="Perseo Logo"/>
                <h3>Registro Perseo</h3>
                <p>Únete a miles de empresarios exitosos</p>
            </div>

            <!-- Contador de clientes -->
            <div class="contador-stats">
                <!-- Partículas decorativas -->
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>

                <!-- Número principal -->
                <div id="contador" class="contador-numero" data-stop="{{ App\Models\Clientes::count() }}">0</div>

                <!-- Label principal -->
                <div class="contador-label">Clientes Activos</div>
            </div>

            <!-- Formulario -->
            <form action="{{ route('post_registro') }}" method="POST" id="formulario">
                @csrf
                <h4 class="form-title">Crear mi cuenta</h4>
                <div class="form-group">
                    <!-- Identificación -->
                    <div class="input-wrapper">
                        <i class="far fa-address-card input-icon"></i>
                        @if ($link->cedula_ruc == 1)
                            <input
                                class="form-control-custom {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                type="text" name="identificacion" id="identificacion" autocomplete="off"
                                value="{{ old('identificacion') }}" placeholder="Cédula o RUC"
                                onblur="verificarIdentificacion('cedula_ruc')"
                                onkeypress="return validarNumero(event)"
                                style="padding-right: 3rem;"/>
                        @else
                            <input
                                class="form-control-custom {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                                type="text" name="identificacion" id="identificacion" autocomplete="off"
                                value="{{ old('identificacion') }}" placeholder="RUC"
                                onblur="verificarIdentificacion('ruc')"
                                onkeypress="return validarNumero(event)"
                                style="padding-right: 3rem;"/>
                        @endif
                        <div class="spinner-overlay" id="spinner">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Verificando...</span>
                            </div>
                        </div>
                    </div>
                    <span class="error-message d-none" id="mensajeBandera">El Ruc no es válido</span>
                    @if ($errors->has('identificacion'))
                        <span class=" error-message">{{ $errors->first('identificacion') }}</span>
                    @endif
                </div>

                <!-- Nombres -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fa fa-user input-icon"></i>
                        <input
                            class="form-control-custom {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                            type="text" name="nombres" id="nombres" autocomplete="off"
                            value="{{ old('nombres') }}" placeholder="Razón social o nombres"/>
                    </div>
                    @if ($errors->has('nombres'))
                        <span class="error-message">{{ $errors->first('nombres') }}</span>
                    @endif
                </div>

                <!-- Dirección -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fa fa-home input-icon"></i>
                        <input
                            class="form-control-custom {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
                            type="text" name="direccion" id="direccion" autocomplete="off"
                            value="{{ old('direccion') }}" placeholder="Dirección"/>
                    </div>
                    @if ($errors->has('direccion'))
                        <span class="error-message">{{ $errors->first('direccion') }}</span>
                    @endif
                </div>

                <!-- Provincia -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <select class="form-control-custom" name="provinciasid" id="provinciasid">
                            <option value="">Seleccionar provincia</option>
                            <option value="01">Azuay</option>
                            <option value="02">Bolivar</option>
                            <option value="03">Cañar</option>
                            <option value="04">Carchi</option>
                            <option value="05">Cotopaxi</option>
                            <option value="06">Chimborazo</option>
                            <option value="07">El Oro</option>
                            <option value="08">Esmeraldas</option>
                            <option value="09">Guayas</option>
                            <option value="20">Galapagos</option>
                            <option value="10">Imbabura</option>
                            <option value="11">Loja</option>
                            <option value="12">Los Rios</option>
                            <option value="13">Manabi</option>
                            <option value="14">Morona Santiago</option>
                            <option value="15">Napo</option>
                            <option value="22">Orellana</option>
                            <option value="16">Pastaza</option>
                            <option value="17">Pichincha</option>
                            <option value="24">Santa Elena</option>
                            <option value="23">Santo Domingo De Los Tsachilas</option>
                            <option value="21">Sucumbios</option>
                            <option value="18">Tungurahua</option>
                            <option value="19">Zamora Chinchipe</option>
                        </select>
                    </div>
                    @if ($errors->has('provinciasid'))
                        <span class="error-message">{{ $errors->first('provinciasid') }}</span>
                    @endif
                </div>

                <!-- Ciudad -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fa fa-city input-icon"></i>
                        <select class="form-control-custom" name="ciudadesid" id="ciudadesid">
                            <option value="">Seleccionar ciudad</option>
                        </select>
                    </div>
                    @if ($errors->has('ciudadesid'))
                        <span class="error-message">{{ $errors->first('ciudadesid') }}</span>
                    @endif
                </div>

                <!-- Tipo de negocio -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fa fa-briefcase input-icon"></i>
                        <select class="form-control-custom" name="grupo" id="grupo">
                            <option value="">Tipo de negocio</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo->gruposid }}" {{ old('grupo') == $grupo->gruposid ? 'selected' : '' }}>
                                    {{ $grupo->descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($errors->has('grupo'))
                        <span class="error-message">{{ $errors->first('grupo') }}</span>
                    @endif
                </div>

                <!-- WhatsApp -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fab fa-whatsapp input-icon"></i>
                        <input
                            class="form-control-custom {{ $errors->has('telefono2') ? 'is-invalid' : '' }}"
                            type="text" name="telefono2" id="telefono2" autocomplete="off"
                            value="{{ old('telefono2') }}" placeholder="WhatsApp"
                            onkeypress="return validarNumero(event)"/>
                    </div>
                    @if ($errors->has('telefono2'))
                        <span class="error-message">{{ $errors->first('telefono2') }}</span>
                    @endif
                </div>

                <!-- Correo -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fa fa-envelope input-icon"></i>
                        <input
                            class="form-control-custom {{ $errors->has('correos') ? 'is-invalid' : '' }}"
                            type="email" name="correos" id="correos" autocomplete="off"
                            value="{{ old('correos') }}" placeholder="Correo electrónico"/>
                    </div>
                    @if ($errors->has('correos'))
                        <span class="error-message">{{ $errors->first('correos') }}</span>
                    @endif
                </div>

                <!-- Red origen (oculto) -->
                <input type="hidden" name="red_origen" value="{{ $link->sis_linksid }}">
                <input type="hidden" name="texto_ciudad" id="texto_ciudad">

                <!-- Botón de registro -->
                <button type="submit" class="btn-registro" id="ingresar">
                    <i class="fas fa-rocket me-2 text-white"></i> <!-- AGREGAR ESTA LÍNEA -->
                    <span class="indicator-label">Crear mi cuenta gratis</span>
                </button>

            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('assets/plugins/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    // ====================================
    // CONFIGURACIÓN Y CONSTANTES
    // ====================================
    const RegistroApp = {
        config: {
            urls: {
                identificaciones: {!! json_encode(route('identificaciones.index')) !!},
                ciudades: {!! json_encode(route('registro.recuperarciudades')) !!},
                registro: {!! json_encode(route('post_registro')) !!}
            },
            validacion: {
                tipoValidacion: {!! json_encode($link->cedula_ruc == 1 ? 'cedula_ruc' : 'ruc') !!},
                tipos: {
                    cedula_ruc: {longitudes: [10, 13], sufijo: '001'},
                    ruc: {longitudes: [13], sufijo: '001'}
                }
            },
            contador: {
                final: {!! App\Models\Clientes::count() !!},
                duracion: 2000,
                pasos: 100
            },
            redirecciones: {
                @if($identificacion != 0)
                activa: true,
                url: {!! json_encode("https://perseo-data-c3.app/sistema?identificacion=" . $identificacion) !!},
                delay: 2000
                @else
                activa: false
                @endif
            },
            csrf: {!! json_encode(csrf_token()) !!}
        },

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        init() {
            this.inicializarComponentes();
            this.configurarEventos();
            this.inicializarContador();
            this.procesarNotificaciones();
            this.manejarRedirecciones();
        },

        inicializarComponentes() {
            // Select2
            $('#provinciasid, #ciudadesid, #grupo').select2({
                dropdownCssClass: 'select2-custom'
            });
        },

        configurarEventos() {
            // Eventos del formulario
            $('#ingresar').on('click', e => this.manejarEnvioFormulario(e));
            $('#identificacion').on('blur', e => this.validarIdentificacion(e.target.value));
            $('#provinciasid').on('change', e => this.cargarCiudades(e.target.value));
            $('#ciudadesid').on('change', e => this.actualizarTextoCiudad(e.target));

            // Solo números en campos específicos
            $('#identificacion, #telefono2').on('keypress', this.soloNumeros);
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
                    $contador.css('animation', 'counterGlow 2s ease-in-out infinite alternate');
                }
                $contador.text(actual.toLocaleString());
            }, intervalo);
        },

        // ====================================
        // VALIDACIÓN DE IDENTIFICACIÓN
        // ====================================
        validarIdentificacion(valor) {
            const tipo = this.config.validacion.tipoValidacion;
            const config = this.config.validacion.tipos[tipo];
            const longitud = valor.length;

            if (!valor) return this.mostrarErrorValidacion();

            const esValido = config.longitudes.some(len => {
                if (len === 10) return longitud === 10;
                return longitud === 13 && valor.substr(10, 3) === config.sufijo;
            });

            if (esValido) {
                this.ocultarErrorValidacion();
                this.consultarInformacion(valor);
            } else {
                this.mostrarErrorValidacion();
            }
        },

        mostrarErrorValidacion() {
            $('#identificacion').addClass("is-invalid");
            $('#mensajeBandera').removeClass("d-none");
            this.limpiarCampos();
        },

        ocultarErrorValidacion() {
            $('#identificacion').removeClass("is-invalid");
            $('#mensajeBandera').addClass("d-none");
        },

        consultarInformacion(identificacion) {
            $("#spinner").addClass('show');

            $.ajax({
                url: this.config.urls.identificaciones,
                method: 'POST',
                headers: {usuario: 'perseo', clave: 'Perseo1232*'},
                data: {_token: this.config.csrf, identificacion},
                success: data => {
                    $("#spinner").removeClass('show');
                    if (data.identificacion) {
                        this.llenarDatos({
                            nombres: data.razon_social,
                            direccion: data.direccion,
                            correos: data.correo,
                            telefono2: data.telefono2
                        });
                    }
                },
                error: () => $("#spinner").removeClass('show')
            });
        },

        // ====================================
        // GESTIÓN DE CIUDADES
        // ====================================
        cargarCiudades(provinciaId) {
            if (!provinciaId) return;

            $.ajax({
                url: this.config.urls.ciudades,
                method: 'POST',
                data: {_token: this.config.csrf, id: provinciaId},
                success: ciudades => {
                    const $select = $('#ciudadesid').empty().append('<option value="">Seleccionar ciudad</option>');
                    ciudades.forEach(ciudad => {
                        $select.append(`<option value="${ciudad.ciudadesid}">${ciudad.ciudad}</option>`);
                    });
                    $select.trigger('change');
                }
            });
        },

        actualizarTextoCiudad(select) {
            const texto = select.options[select.selectedIndex]?.text || '';
            $('#texto_ciudad').val(texto);
        },

        // ====================================
        // GESTIÓN DEL FORMULARIO
        // ====================================
        manejarEnvioFormulario(e) {
            e.preventDefault();

            const $form = $('#formulario');
            this.mostrarSpinnerGuardar();
            // Enviar formulario
            $form.submit();
        },

        mostrarSpinnerGuardar() {
            const btnGuardar = $('button[type="submit"]');
            const textoOriginal = btnGuardar.html() || btnGuardar.val();

            // Guardar texto original y mostrar spinner
            btnGuardar.data('texto-original', textoOriginal);
            btnGuardar.prop('disabled', true);
            btnGuardar.html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        },

        // ====================================
        // UTILIDADES
        // ====================================
        llenarDatos(datos) {
            Object.entries(datos).forEach(([campo, valor]) => {
                $(`#${campo}`).val(valor || '');
            });
        },

        limpiarCampos() {
            const campos = ['nombres', 'direccion', 'correos', 'telefono2'];
            campos.forEach(campo => $(`#${campo}`).val(''));
            $('#provinciasid').val('').trigger('change');
        },

        soloNumeros(e) {
            const codigo = e.which || e.keyCode;
            return codigo >= 48 && codigo <= 57;
        },

        // ====================================
        // NOTIFICACIONES Y REDIRECCIONES
        // ====================================
        procesarNotificaciones() {
            @if(session()->has('flash_notification'))
            @foreach (session('flash_notification', collect())->toArray() as $message)
            $.notify({
                message: {!! json_encode($message['message']) !!},
            }, {
                showProgressbar: true,
                delay: 2500,
                mouse_over: "pause",
                placement: {from: "top", align: "right"},
                animate: {
                    enter: "animated fadeInUp",
                    exit: "animated fadeOutDown"
                },
                type: {!! json_encode($message['level']) !!},
            });
            @endforeach
            @endif
        },

        manejarRedirecciones() {
            if (this.config.redirecciones.activa) {
                setTimeout(() => {
                    window.location.href = this.config.redirecciones.url;
                }, this.config.redirecciones.delay);
            }
        }
    };

    // ====================================
    // INICIALIZACIÓN GLOBAL
    // ====================================
    $(document).ready(() => RegistroApp.init());
</script>
</body>
</html>
