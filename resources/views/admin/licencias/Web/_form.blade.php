@csrf
<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }

    .custom-switch-datos {
        transform: scale(1.5);
        transform-origin: left center;
    }

    .tab-content {
        overflow-x: hidden;
    }
</style>

@php
    $accion = isset($licencia->sis_licenciasid) ? 'Modificar' : 'Crear';
    $servidoresid = isset($licencia->sis_licenciasid) ? $licencia->sis_servidoresid : 0;
    $licenciasid = isset($licencia->sis_licenciasid) ? $licencia->sis_licenciasid : 0;
@endphp

{{-- Campos ocultos --}}
<input type="hidden" name="sis_distribuidoresid" value="{{ $licencia->sis_distribuidoresid }}">
<input type="hidden" name="tipo" id="tipo">
<input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">

{{-- Navegación principal --}}
<ul class="nav nav-tabs nav-tabs-line mb-5">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#datos_licencia">
            <span class="nav-icon"><i class="fas fa-info-circle"></i></span>
            <span class="nav-text">Datos Licencia</span>
        </a>
    </li>
    @if($accion=="Modificar")
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#recursos_adicionales">
                <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>
                <span class="nav-text">Recursos Adicionales</span>
            </a>
        </li>
    @endif
</ul>

<div class="tab-content">
    {{-- TAB: Datos Licencia --}}
    <div class="tab-pane fade show active" id="datos_licencia" role="tabpanel" aria-labelledby="datos_licencia">

        {{-- Información básica --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-file-contract"></i> Información Básica</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Número Contrato</label>
                    <input type="text" class="form-control @error('numerocontrato') is-invalid @enderror"
                           name="numerocontrato" id="numerocontrato"
                           value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly>
                    @error('numerocontrato')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Producto</label>
                    <select class="form-control {{ !puede('web', 'editar_producto_modificar') && $accion == 'Modificar' ? 'disabled' : '' }}"
                            name="producto" id="producto">
                        @if ($accion == 'Modificar' && $licencia->producto == '12')
                            <option value="12" {{ old('producto', $licencia->producto) == '12' ? 'Selected' : '' }}>Facturito</option>
                        @else
                            <option value="2" {{ old('producto', $licencia->producto) == '2' ? 'Selected' : '' }}>Facturación</option>
                            <option value="3" {{ old('producto', $licencia->producto) == '3' ? 'Selected' : '' }}>Servicios</option>
                            <option value="4" {{ old('producto', $licencia->producto) == '4' ? 'Selected' : '' }}>Comercial</option>
                            <option value="5" {{ old('producto', $licencia->producto) == '5' ? 'Selected' : '' }}>Soy Contador Comercial</option>
                            <option value="8" {{ old('producto', $licencia->producto) == '8' ? 'Selected' : '' }}>Soy Contador Servicios</option>
                            @if ($accion == 'Modificar' && $licencia->producto == '6')
                                <option value="6" {{ old('producto', $licencia->producto) == '6' ? 'Selected' : '' }}>Perseo Lite Anterior</option>
                            @endif
                            <option value="9" {{ old('producto', $licencia->producto) == '9' ? 'Selected' : '' }}>Perseo Lite</option>
                            @if ($accion == 'Modificar' && $licencia->producto == '10')
                                <option value="10" {{ old('producto', $licencia->producto) == '10' ? 'Selected' : '' }}>Emprendedor</option>
                            @endif
                            <option value="11" {{ old('producto', $licencia->producto) == '11' ? 'Selected' : '' }}>Socio Perseo</option>
                            @if ($accion == 'Crear')
                                <option value="12" {{ old('producto', $licencia->producto) == '12' ? 'Selected' : '' }}>Facturito</option>
                            @endif
                        @endif
                    </select>
                    @error('producto')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Periodo</label>
                    <div class="input-group">
                        <select class="form-control {{ !puede('web', 'editar_periodo_' . strtolower($accion)) ? 'disabled' : '' }}"
                                name="periodo" id="periodo">
                            <option id="periodo1" value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected' : '' }}>Mensual</option>
                            <option id="periodo2" value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected' : '' }}>Anual</option>
                            <option id="periodo3" value="3" {{ old('periodo', $licencia->periodo) == '3' ? 'Selected' : '' }}>Premium</option>
                            <option id="periodo4" value="4" {{ old('periodo', $licencia->periodo) == '4' ? 'Selected' : '' }}>Gratis</option>
                        </select>
                        @if (isset($licencia->sis_licenciasid) && $licencia->producto != 6 && $licencia->producto != 9)
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    {{ !puede('web', 'mostrar_renovar') ? 'disabled' : '' }}>
                                    <i class="fas fa-sync-alt"></i> Renovar
                                </button>
                                <div class="dropdown-menu">
                                    @if ($licencia->producto != 10 && $licencia->producto != 12)
                                        <a class="dropdown-item" href="#" id="renovarmensual">Renovar Mensual</a>
                                    @endif
                                    <a class="dropdown-item" href="#" id="renovaranual">Renovar Anual</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('periodo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Precio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-success text-white">
                                <i class="fas fa-dollar-sign text-white"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control text-success font-weight-bold"
                               id="precio" name="precio" autocomplete="off" disabled>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fechas --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-calendar-alt"></i> Configuración de Fechas</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha Inicia</label>
                    <input type="text"
                           class="form-control {{ !puede('web', 'editar_fechas') ? 'disabled' : '' }} @error('fechainicia') is-invalid @enderror"
                           name="fechainicia" id="fechainicia"
                           value="{{ old('fechainicia', $licencia->fechainicia) }}">
                    @error('fechainicia')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha Caduca</label>
                    <input type="text"
                           class="form-control {{ !puede('web', 'editar_fechas') ? 'disabled' : '' }} @error('fechacaduca') is-invalid @enderror"
                           name="fechacaduca" id="fechacaduca"
                           value="{{ old('fechacaduca', $licencia->fechacaduca) }}">
                    @error('fechacaduca')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Configuración de recursos --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-cogs"></i> Configuración de Recursos</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>N° Empresas</label>
                    <input type="text"
                           class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} @error('empresas') is-invalid @enderror"
                           name="empresas" id="empresas"
                           value="{{ old('empresas', $licencia->empresas) }}">
                    @error('empresas')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>N° Usuarios</label>
                    <input type="text"
                           class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} @error('usuarios') is-invalid @enderror"
                           name="usuarios" id="usuarios"
                           value="{{ old('usuarios', $licencia->usuarios) }}">
                    @error('usuarios')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>N° Móviles</label>
                    <input type="text"
                           class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} @error('numeromoviles') is-invalid @enderror"
                           name="numeromoviles" id="numeromoviles"
                           value="{{ old('numeromoviles', $licencia->numeromoviles) }}">
                    @error('numeromoviles')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>N° Sucursales</label>
                    <input type="text"
                           class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} @error('numerosucursales') is-invalid @enderror"
                           name="numerosucursales" id="numerosucursales"
                           value="{{ old('numerosucursales', $licencia->numerosucursales) }}">
                    @error('numerosucursales')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Configuración del servidor --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-server"></i> Configuración del Servidor</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Servidor</label>
                    <select class="form-control disabled" name="sis_servidoresid" id="sis_servidoresid">
                        @foreach ($servidores as $servidor)
                            <option value="{{ $servidor->sis_servidoresid }}"
                                {{ $servidor->sis_servidoresid == $licencia->sis_servidoresid ? 'selected' : '' }}>
                                {{ $servidor->descripcion }}
                            </option>
                        @endforeach
                    </select>
                    @error('sis_servidoresid')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Módulos disponibles --}}
        <div id="seccion_modulos" style="{{ (isset($licencia->producto) && $licencia->producto == 12) ? 'display: none;' : '' }}">
            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-puzzle-piece"></i> Módulos Disponibles</p>
            <div class="separator separator-dashed mb-2"></div>

            @php
                $modulos = [
                    ['name' => 'nomina', 'label' => 'Nómina', 'icon' => 'fas fa-users', 'value' => $modulos->nomina],
                    ['name' => 'activos', 'label' => 'Activos Fijos', 'icon' => 'fas fa-boxes', 'value' => $modulos->activos],
                    ['name' => 'produccion', 'label' => 'Producción', 'icon' => 'fas fa-industry', 'value' => $modulos->produccion],
                    ['name' => 'restaurantes', 'label' => 'Restaurantes', 'icon' => 'fas fa-utensils', 'value' => $modulos->restaurantes],
                    ['name' => 'talleres', 'label' => 'Talleres', 'icon' => 'fas fa-car', 'value' => $modulos->talleres],
                    ['name' => 'garantias', 'label' => 'Garantías', 'icon' => 'fas fa-tools', 'value' => $modulos->garantias],
                    ['name' => 'ecommerce', 'label' => 'Ecommerce', 'icon' => 'fas fa-store', 'value' => $modulos->ecommerce],
                ];
            @endphp

            <div class="row">
                @foreach ($modulos as $modulo)
                    <div class="col-lg-3 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex align-items-center">
                                <i class="{{ $modulo['icon'] }} fa-2x text-muted mr-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="card-title mb-1">{{ $modulo['label'] }}</h6>
                                    <div class="custom-control custom-switch custom-switch-datos">
                                        <input type="checkbox" class="custom-control-input"
                                               id="{{ $modulo['name'] }}" name="{{ $modulo['name'] }}"
                                            {{ $modulo['value'] == 1 ? 'checked' : '' }}
                                            {{ !puede('web', 'editar_modulos') ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="{{ $modulo['name'] }}"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TAB: Recursos Adicionales --}}
    @if($accion=="Modificar")
        <div class="tab-pane fade" id="recursos_adicionales" role="tabpanel" aria-labelledby="recursos_adicionales">
            <div class="d-flex justify-content-between align-items-center">
                <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-plus-circle"></i> Agregar Recursos Adicionales</p>
                <div class="text-right">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-dark d-block">Total:</h6>
                            <strong id="total_general_recursos" class="text-success">$0.00</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator separator-dashed mb-2"></div>

            <!-- Mensaje cuando no hay producto seleccionado -->
            <div class="alert alert-warning" id="mensaje_sin_producto" style="display: none;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="alert-heading">Seleccione un producto</h6>
                        <p class="mb-0">Para ver los recursos adicionales disponibles, debe tener un producto seleccionado en la pestaña "Datos
                            Licencia".</p>
                    </div>
                </div>
            </div>

            <!-- Formularios dinámicos -->
            <div id="formularios_adicionales_container" style="display: none;">
                <div class="row" id="formularios_adicionales_row">
                    <!-- Se llenan dinámicamente -->
                </div>
            </div>
        </div>
    @endif
</div>

@section('script')
    <script>
        // ====================================
        // CONFIGURACIÓN GLOBAL SIMPLIFICADA
        // ====================================

        const AppConfigWeb = {
            //  Toda la configuración en una sola variable
            configuracion: @json(config('sistema')),
            accion: "{{ $accion }}",

            //  Solo el permiso específico que usa JavaScript
            permisos: {
                editarAdicionales: @json(puede('web', 'editar_adicionales_' . strtolower($accion)))
            },

            // URLs
            rutas: {
                obtenerAdicionales: "{{ route('licencias.obtener-adicionales') }}",
                agregarAdicional: "{{ route('licencias.agregar-adicional') }}",
                editarClave: "{{ route('licencias.Web.editarClave', [$cliente->sis_clientesid, $servidoresid, $licenciasid]) }}"
            }
        };

        // ====================================
        // GESTOR PRINCIPAL DEL FORMULARIO
        // ====================================

        const FormularioLicenciaWeb = {
            productoInicial: null, //  Guardar producto inicial para detectar cambios
            init() {
                this.inicializarFormulario();
                this.configurarEventos();
                this.inicializarDatepicker();
            },

            inicializarFormulario() {
                this.productoInicial = $('#producto').val();

                if (AppConfigWeb.accion === 'Crear') {
                    this.configurarFormularioNuevo();
                } else {
                    this.configurarFormularioExistente();
                }
            },

            //  Configuración dinámica para licencia nueva
            configurarFormularioNuevo() {
                const fecha = new Date();
                const fechaInicia = this.formatearFecha(fecha);

                //  Valores por defecto desde configuración
                const productoPorDefecto = '2'; // Facturación
                const configProducto = AppConfigWeb.configuracion.productos.web[productoPorDefecto];

                if (configProducto) {
                    const valoresDefecto = {
                        fechainicia: fechaInicia,
                        precio: configProducto.mensual?.precio || '0',
                        usuarios: configProducto.usuarios || '6',
                        numeromoviles: configProducto.moviles || '1',
                        numerosucursales: configProducto.sucursales || '0',
                        empresas: configProducto.empresas || '1',
                        sis_servidoresid: configProducto.servidor || '3'
                    };

                    this.aplicarValoresDefecto(valoresDefecto);
                    this.aplicarModulosProducto(productoPorDefecto, 'mensual');
                }

                this.actualizarConfiguraciones();
            },

            configurarFormularioExistente() {
                this.actualizarConfiguraciones(false); //  No aplicar módulos en carga inicial
                const producto = $('#producto').val();
                if (producto) {
                    this.manejarVisibilidadModulos(producto);
                }
            },

            aplicarValoresDefecto(valores) {
                Object.keys(valores).forEach(campo => {
                    $(`#${campo}`).val(valores[campo]);
                });
            },

            //  Aplicar módulos dinámicamente desde configuración
            aplicarModulosProducto(producto, periodo) {
                const configProducto = AppConfigWeb.configuracion.productos.web[producto];
                if (!configProducto) return;

                // Obtener módulos según período
                let modulos = configProducto.modulos;
                if (configProducto[periodo]?.modulos) {
                    modulos = configProducto[periodo].modulos;
                }

                if (modulos) {
                    Object.keys(modulos).forEach(modulo => {
                        $(`#${modulo}`).prop('checked', modulos[modulo]);
                    });
                }
            },

            configurarEventos() {
                // Eventos principales
                $('#periodo').on('change', () => {
                    this.actualizarConfiguraciones(false); //  Solo precio, no módulos
                });

                $('#producto').on('change', () => {
                    const productoActual = $('#producto').val();
                    const cambioDeProducto = productoActual !== this.productoInicial; //  Detectar cambio real
                    this.actualizarConfiguraciones(cambioDeProducto); //  Solo aplicar módulos si cambió

                    if (cambioDeProducto) {
                        this.productoInicial = productoActual; //  Actualizar referencia
                    }
                });

                // Eventos de botones de acción
                const eventosAccion = {
                    '#renovarmensual': 'mes',
                    '#renovaranual': 'anual',
                    '#recargar': 'recargar',
                    '#recargar240': 'recargar240'
                };

                Object.entries(eventosAccion).forEach(([selector, tipo]) => {
                    $(selector).on('click', () => this.confirmarAccion(tipo, this.obtenerMensajeConfirmacion(tipo)));
                });

                $('#resetear').on('click', () => this.confirmarResetearClave());

                // Prevenir clicks en elementos deshabilitados
                $('.deshabilitar').on('click', () => false);
            },

            //  Mensajes dinámicos según acción
            obtenerMensajeConfirmacion(tipo) {
                const mensajes = {
                    'mes': "¿Está seguro de Renovar la Licencia?",
                    'anual': "¿Está seguro de Renovar la Licencia?",
                    'recargar': "¿Está seguro de Recargar 120 Documentos Adicionales?",
                    'recargar240': "¿Está seguro de Recargar 240 Documentos Adicionales?"
                };
                return mensajes[tipo] || "¿Está seguro de continuar?";
            },

            //  Actualizar configuraciones usando datos dinámicos
            actualizarConfiguraciones(aplicarModulos = true) {
                const producto = $('#producto').val();
                const periodo = $('#periodo').val();

                if (!producto || !AppConfigWeb.configuracion.productos.web[producto]) {
                    return;
                }

                this.configurarPeriodoSegunProducto(producto);
                this.aplicarConfiguracionesProducto(producto, periodo, aplicarModulos);
                this.manejarVisibilidadModulos(producto);

                // Actualizar recursos adicionales
                setTimeout(() => {
                    RecursosAdicionalesWeb.actualizarTodo();
                }, 100);
            },

            //  Configurar período dinámicamente desde configuración
            configurarPeriodoSegunProducto(producto) {
                const configProducto = AppConfigWeb.configuracion.productos.web[producto];

                if (producto == 12) {
                    //  Facturito - períodos desde configuración
                    const periodos = Object.keys(configProducto);
                    const etiquetas = {
                        'inicial': 'Inicial',
                        'basico': 'Básico',
                        'premium': 'Premium',
                        'gratis': 'Gratis'
                    };

                    $('#periodo1').html(etiquetas['inicial'] || 'Inicial');
                    $('#periodo2').html(etiquetas['basico'] || 'Básico');
                    $('#periodo3').html(etiquetas['premium'] || 'Premium');
                    $('#periodo4').html(etiquetas['gratis'] || 'Gratis');
                    $('#periodo3, #periodo4').removeClass("d-none");
                } else {
                    //  Productos normales
                    $('#periodo1').html("Mensual");
                    $('#periodo2').html("Anual");
                    $('#periodo3, #periodo4').addClass("d-none");

                    const periodo = $('#periodo').val();
                    if (periodo > 2) {
                        $('#periodo').val(1);
                    }
                }

                this.manejarEstadoPeriodo(producto);
            },

            manejarEstadoPeriodo(producto) {
                const $periodo = $('#periodo');
                const productosEspeciales = [6, 9, 10];

                if (productosEspeciales.includes(parseInt(producto))) {
                    $periodo.addClass("disabled");
                } else if (AppConfigWeb.permisos.editarPeriodo) {
                    $periodo.removeClass("disabled");
                }
            },

            //  Aplicar configuraciones usando datos dinámicos
            aplicarConfiguracionesProducto(producto, periodo, aplicarModulos = false) {
                const config = this.obtenerConfiguracionProducto(producto, periodo);

                if (config) {
                    $('#precio').val(config.precio || '0');

                    if (AppConfigWeb.accion === 'Crear') {
                        this.aplicarConfiguracionesNuevaLicencia(config, producto, periodo);
                        aplicarModulos = true; //  Siempre aplicar en creación
                    }

                    //  Solo aplicar módulos cuando se solicite explícitamente
                    if (aplicarModulos) {
                        this.aplicarModulosProducto(producto, this.obtenerTipoPeriodo(producto, periodo));
                    }
                }
            },

            //  Obtener configuración usando estructura dinámica
            obtenerConfiguracionProducto(producto, periodo) {
                const configProducto = AppConfigWeb.configuracion.productos.web[producto];
                if (!configProducto) return null;

                const tipoPeriodo = this.obtenerTipoPeriodo(producto, periodo);
                const periodoConfig = configProducto[tipoPeriodo];

                if (!periodoConfig) return null;

                return {
                    precio: periodoConfig.precio,
                    usuarios: configProducto.usuarios,
                    moviles: configProducto.moviles,
                    sucursales: configProducto.sucursales,
                    empresas: configProducto.empresas,
                    servidor: configProducto.servidor,
                    modulos: periodoConfig.modulos || configProducto.modulos,
                    meses: periodoConfig.meses
                };
            },

            //  Mapear períodos dinámicamente
            obtenerTipoPeriodo(producto, periodo) {
                if (producto == 12) {
                    const mapaPeriodos = {
                        1: 'inicial', 2: 'basico', 3: 'premium', 4: 'gratis'
                    };
                    return mapaPeriodos[periodo] || 'inicial';
                }
                return periodo == 1 ? 'mensual' : 'anual';
            },

            aplicarConfiguracionesNuevaLicencia(config, producto, periodo) {
                const campos = ['usuarios', 'numeromoviles', 'numerosucursales', 'empresas'];
                campos.forEach(campo => {
                    const valor = config[campo === 'numeromoviles' ? 'moviles' :
                        campo === 'numerosucursales' ? 'sucursales' : campo];
                    if (valor !== undefined) {
                        $(`#${campo}`).val(valor);
                    }
                });

                if (config.servidor) {
                    $('#sis_servidoresid').val(config.servidor);
                }

                if (config.meses) {
                    this.calcularFechaCaducidad(config.meses);
                }
            },

            calcularFechaCaducidad(meses) {
                const fechaInicia = new Date();
                const fechaCaducidad = new Date(fechaInicia);
                // Si meses es decimal, convertir a días
                if (meses % 1 !== 0) { // Es decimal
                    const dias = Math.round(meses * 30); // 0.5 meses = 15 días
                    fechaCaducidad.setDate(fechaCaducidad.getDate() + dias);
                } else {
                    // Es entero, usar meses normalmente
                    fechaCaducidad.setMonth(fechaCaducidad.getMonth() + meses);
                }
                $('#fechacaduca').val(this.formatearFecha(fechaCaducidad));
            },

            inicializarDatepicker() {
                $('#fechainicia, #fechacaduca').datepicker({
                    language: "es",
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    }
                });
            },

            confirmarAccion(tipo, mensaje) {
                Swal.fire({
                    title: "Advertencia",
                    text: mensaje,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar",
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $('#tipo').val(tipo);
                        $("#formulario").submit();
                    }
                });
            },

            confirmarResetearClave() {
                Swal.fire({
                    title: "Advertencia",
                    text: '¿Está seguro de resetear la clave del usuario?',
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    cancelButtonText: "Cancelar",
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        this.ejecutarResetearClave();
                    }
                });
            },

            ejecutarResetearClave() {
                $.get(AppConfigWeb.rutas.editarClave, (data) => {
                    $.notify({
                        message: data.mensaje,
                    }, {
                        showProgressbar: true,
                        delay: 2500,
                        mouse_over: "pause",
                        placement: {from: "top", align: "right"},
                        animate: {
                            enter: "animated fadeInUp",
                            exit: "animated fadeOutDown"
                        },
                        type: data.tipo,
                    });
                });
            },

            manejarVisibilidadModulos(producto) {
                const $seccionModulos = $('#seccion_modulos');

                if (producto == 12) { // Facturito
                    $seccionModulos.hide();
                } else {
                    $seccionModulos.show();
                }
            },

            formatearFecha(fecha) {
                return ("0" + fecha.getDate()).slice(-2) + "-" +
                    ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" +
                    fecha.getFullYear();
            }
        };

        // ====================================
        // RECURSOS ADICIONALES WEB
        // ====================================

        const RecursosAdicionalesWeb = {
            cantidadesAdicionales: {},
            productoSeleccionado: null,

            init() {
                this.cargarConfiguracion();
                this.configurarEventos();
                this.cargarEstadoInicial();
            },

            cargarConfiguracion() {
                this.tiposAdicionales = AppConfigWeb.configuracion.tipos_adicionales;
                this.productosConfig = AppConfigWeb.configuracion.productos.web;

                // Inicializar cantidades
                this.cantidadesAdicionales = {};
                Object.keys(this.tiposAdicionales).forEach(tipoId => {
                    this.cantidadesAdicionales[tipoId] = 0;
                });
            },

            configurarEventos() {
                $(document).on('input change', '.cantidad-input', () => {
                    this.actualizarDisplayCantidades();
                });

                $(document).on('click', '.btn-agregar-recurso', (e) => {
                    const tipoId = $(e.target).data('tipo-id');
                    this.agregarRecurso(tipoId);
                });

                // Eventos para campos que afectan cantidades
                const mapasCampos = this.obtenerMapaCampos();
                const selectoresCampos = Object.values(mapasCampos).map(campo => '#' + campo).join(', ');

                $(selectoresCampos).on('input change', () => {
                    this.actualizarDisplayCantidades();
                });
            },

            cargarEstadoInicial() {
                this.cargarAdicionalesExistentes();
                this.detectarProductoSeleccionado();
                this.generarFormularios();
            },

            actualizarTodo() {
                this.detectarProductoSeleccionado();
                this.generarFormularios();
            },

            obtenerMapaCampos() {
                const mapaCampos = {};
                Object.keys(this.tiposAdicionales).forEach(tipoId => {
                    const tipoConfig = this.tiposAdicionales[tipoId];
                    if (tipoConfig.campo_licencia) {
                        mapaCampos[tipoId] = tipoConfig.campo_licencia;
                    }
                });
                return mapaCampos;
            },

            cargarAdicionalesExistentes() {
                const numerocontrato = $("#numerocontrato").val();
                if (!numerocontrato) return;

                // Reset
                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    this.cantidadesAdicionales[tipoId] = 0;
                });

                $.ajax({
                    url: AppConfigWeb.rutas.obtenerAdicionales,
                    method: 'GET',
                    data: {numerocontrato: numerocontrato},
                    success: (response) => {
                        if (response.success && response.adicionales) {
                            response.adicionales.forEach(adicional => {
                                const tipoId = adicional.tipo_adicional;
                                if (this.cantidadesAdicionales.hasOwnProperty(tipoId)) {
                                    this.cantidadesAdicionales[tipoId] = parseInt(adicional.cantidad) || 0;
                                }
                            });
                            this.actualizarDisplayCantidades();
                        }
                    },
                    error: () => console.warn('No se pudieron cargar los adicionales existentes')
                });
            },

            detectarProductoSeleccionado() {
                this.productoSeleccionado = $("#producto").val();
            },

            //  Generar formularios usando configuración dinámica
            generarFormularios() {
                const container = $("#formularios_adicionales_container");
                const row = $("#formularios_adicionales_row");
                const mensaje = $("#mensaje_sin_producto");

                if (!this.productoSeleccionado || !this.productosConfig[this.productoSeleccionado]) {
                    container.hide();
                    mensaje.show();
                    return;
                }

                mensaje.hide();
                container.show();
                row.empty();

                //  Usar configuración directa
                const productoConfig = this.productosConfig[this.productoSeleccionado];
                const adicionalesPermitidos = productoConfig.adicionales || [];

                if (adicionalesPermitidos.length === 0) {
                    row.append(`
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Este producto no tiene recursos adicionales disponibles.
                    </div>
                </div>
            `);
                    return;
                }

                adicionalesPermitidos.forEach(tipoId => {
                    if (this.tiposAdicionales[tipoId]) {
                        row.append(this.crearFormularioTipo(tipoId, this.tiposAdicionales[tipoId]));
                    }
                });

                this.actualizarDisplayCantidades();
                this.actualizarTotalGeneral();
            },

            crearFormularioTipo(tipoId, tipoConfig) {
                const cantidades = this.calcularCantidades(tipoId);
                const precios = this.calcularPrecios(tipoId, tipoConfig, cantidades);
                const precioInfo = this.obtenerPrecioInfo(tipoId, tipoConfig);

                return `
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card border border-primary">
                    <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                        <h6 class="card-title text-primary mb-0">
                            <i class="fa ${tipoConfig.icono} text-primary"></i> ${tipoConfig.nombre}
                        </h6>
                        <div class="text-right">
                            <small class="text-muted d-block">Valor Total</small>
                            <strong class="text-success" id="valor_total_${tipoId}">$${precios.valorTotal.toFixed(2)}</strong>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted font-size-sm mb-3">${tipoConfig.descripcion}</p>

                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Adicionales:</small>
                                <div class="font-weight-bold text-warning" id="cantidad_adicional_${tipoId}">${cantidades.adicional}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Total:</small>
                                <div class="font-weight-bold text-primary" id="cantidad_total_${tipoId}">${cantidades.total}</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-size-sm">Cantidad a Agregar:</label>
                            <div class="input-group">
                                <input type="number" class="form-control text-center cantidad-input"
                                       id="cantidad_${tipoId}" data-tipo-id="${tipoId}"
                                       min="0" max="100" value="0" placeholder="0">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary btn-agregar-recurso"
                                            data-tipo-id="${tipoId}" ${!AppConfigWeb.permisos.editarAdicionales ? 'disabled' : ''}>
                                        <i class="fa fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>${precioInfo}</div>
                            <div class="text-right">
                                <small class="text-muted">Costo a agregar:</small>
                                <div class="font-weight-bold text-warning" id="costo_agregar_${tipoId}">$0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
            },

            calcularCantidades(tipoId) {
                const cantidadAdicional = this.cantidadesAdicionales[tipoId] || 0;
                return {
                    adicional: cantidadAdicional,
                    total: cantidadAdicional
                };
            },

            calcularPrecios(tipoId, tipoConfig, cantidades) {
                const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                return {
                    unitario: precioUnitario,
                    valorTotal: cantidades.adicional * precioUnitario
                };
            },

            //  Calcular precio usando configuración directa
            obtenerPrecioUnitario(tipoId, tipoConfig) {
                const periodo = $("#periodo").val() || '2';
                const periodoTexto = periodo == '1' ? 'mensual' : 'anual';

                if (tipoConfig.precios && tipoConfig.precios.web) {
                    return tipoConfig.precios.web[periodoTexto] || 0;
                }

                return 0;
            },

            obtenerPrecioInfo(tipoId, tipoConfig) {
                const periodo = $("#periodo").val() || '2';
                const periodoTexto = periodo == '1' ? 'mensual' : 'anual';
                const precio = this.obtenerPrecioUnitario(tipoId, tipoConfig);

                if (precio === 0) {
                    return '<small class="text-success"><i class="fa fa-check"></i> Sin costo adicional</small>';
                }
                return `<small class="text-muted">Precio: $${precio}/${periodoTexto} c/u</small>`;
            },

            actualizarDisplayCantidades() {
                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    const cantidades = this.calcularCantidades(tipoId);
                    const inputCantidad = parseInt($(`#cantidad_${tipoId}`).val()) || 0;
                    const totalConInput = cantidades.adicional + inputCantidad;

                    $(`#cantidad_adicional_${tipoId}`).text(cantidades.adicional);
                    $(`#cantidad_total_${tipoId}`).text(totalConInput);

                    // Actualizar colores
                    const elemento = $(`#cantidad_total_${tipoId}`);
                    if (inputCantidad > 0) {
                        elemento.removeClass('text-primary').addClass('text-success');
                    } else {
                        elemento.removeClass('text-success').addClass('text-primary');
                    }

                    // Actualizar precios
                    this.actualizarPreciosDisplay(tipoId, cantidades, inputCantidad);
                });

                this.actualizarTotalGeneral();
            },

            actualizarPreciosDisplay(tipoId, cantidades, inputCantidad) {
                const valorElement = $(`#valor_total_${tipoId}`);
                const costoElement = $(`#costo_agregar_${tipoId}`);

                if (valorElement.length && costoElement.length) {
                    const tipoConfig = this.tiposAdicionales[tipoId];
                    if (tipoConfig) {
                        const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                        const valorTotalActual = cantidades.adicional * precioUnitario;
                        const costoAgregar = inputCantidad * precioUnitario;

                        valorElement.text(`$${valorTotalActual.toFixed(2)}`);
                        costoElement.text(`$${costoAgregar.toFixed(2)}`);

                        if (inputCantidad > 0) {
                            costoElement.removeClass('text-warning').addClass('text-danger');
                        } else {
                            costoElement.removeClass('text-danger').addClass('text-warning');
                        }
                    }
                }
            },

            actualizarTotalGeneral() {
                let totalGeneral = 0;

                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    if (this.tiposAdicionales[tipoId]) {
                        const cantidadAdicional = this.cantidadesAdicionales[tipoId] || 0;
                        const cantidadAgregar = parseInt($(`#cantidad_${tipoId}`).val()) || 0;
                        const totalAdicionales = cantidadAdicional + cantidadAgregar;

                        const precioUnitario = this.obtenerPrecioUnitario(tipoId, this.tiposAdicionales[tipoId]);
                        totalGeneral += totalAdicionales * precioUnitario;
                    }
                });

                const elemento = $("#total_general_recursos");
                elemento.text(`$${totalGeneral.toFixed(2)}`);

                if (totalGeneral > 0) {
                    elemento.removeClass('text-warning').addClass('text-success');
                } else {
                    elemento.removeClass('text-success').addClass('text-warning');
                }
            },

            agregarRecurso(tipoId) {
                const cantidad = parseInt($(`#cantidad_${tipoId}`).val());

                if (!cantidad || cantidad < 1) {
                    Swal.fire({
                        title: "Error",
                        text: "Debe ingresar una cantidad válida mayor a 0",
                        icon: "error"
                    });
                    return;
                }

                const datosAdicional = {
                    numerocontrato: $("#numerocontrato").val(),
                    fechainicia: new Date().toISOString().split('T')[0],
                    fechacaduca: $("#fechacaduca").val(),
                    tipo_adicional: tipoId,
                    tipo_licencia: 2, // WEB
                    periodo: $("#periodo").val(),
                    cantidad: cantidad,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                const btn = $(`.btn-agregar-recurso[data-tipo-id="${tipoId}"]`);
                const tipoNombre = this.tiposAdicionales[tipoId].nombre;

                $.ajax({
                    url: AppConfigWeb.rutas.agregarAdicional,
                    method: 'POST',
                    data: datosAdicional,
                    beforeSend: () => {
                        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
                    },
                    success: (response) => {
                        if (response.success) {
                            this.cantidadesAdicionales[tipoId] += cantidad;

                            // Actualizar campo del formulario si corresponde
                            const tipoConfig = this.tiposAdicionales[tipoId];

                            if (response.licencia_actualizada) {
                                const tipoConfig = this.tiposAdicionales[tipoId];
                                if (tipoConfig.campo_licencia && response.licencia_actualizada[tipoConfig.campo_licencia] !== undefined) {
                                    $(`#${tipoConfig.campo_licencia}`).val(response.licencia_actualizada[tipoConfig.campo_licencia]);
                                }
                            }

                            $(`#cantidad_${tipoId}`).val(0);
                            this.actualizarDisplayCantidades();

                            const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                            const valorAgregado = cantidad * precioUnitario;

                            Swal.fire({
                                title: "¡Éxito!",
                                html: `
                            <div class="text-center">
                                <p>Se agregaron <strong>${cantidad} ${tipoNombre.toLowerCase()}</strong> correctamente</p>
                                <p class="text-muted">Adicionales contratados: <strong>${this.cantidadesAdicionales[tipoId]}</strong></p>
                                <p class="text-success">Valor agregado: <strong>$${valorAgregado.toFixed(2)}</strong></p>
                            </div>
                        `,
                                icon: "success",
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: response.message || "Ocurrió un error al agregar el recurso",
                                icon: "error"
                            });
                        }
                    },
                    error: (xhr) => {
                        let errorMessage = "Error de comunicación con el servidor";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: "Error",
                            text: errorMessage,
                            icon: "error"
                        });
                    },
                    complete: () => {
                        btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Agregar');
                    }
                });
            }
        };

        // ====================================
        // INICIALIZACIÓN
        // ====================================

        $(document).ready(function () {
            FormularioLicenciaWeb.init();
            RecursosAdicionalesWeb.init();
        });
    </script>
@endsection
