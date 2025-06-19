<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
@php
    $accion = isset($licencia->sis_licenciasid) ? 'Modificar' : 'Crear';
    $servidoresid = isset($licencia->sis_licenciasid) ? $licencia->sis_servidoresid : 0;
    $licenciasid = isset($licencia->sis_licenciasid) ? $licencia->sis_licenciasid : 0;
@endphp
@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <input type="hidden" name="sis_distribuidoresid" value="{{ $licencia->sis_distribuidoresid }}">
        <input type="hidden" name="tipo" id="tipo">
        <input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">
        <label>Numero Contrato:</label>
        <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}" placeholder="Contrato"
            name="numerocontrato" autocomplete="off" id="numerocontrato" value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly />
        @if ($errors->has('numerocontrato'))
            <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Producto:</label>
        <select class="form-control {{ !puede('web', 'editar_producto_modificar') && $accion == 'Modificar' ? 'disabled' : '' }}" name="producto"
            id="producto">
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
        @if ($errors->has('producto'))
            <span class="text-danger">{{ $errors->first('producto') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Periodo:</label>
        <div class="input-group">
            <select class="form-control {{ !puede('web', 'editar_periodo_' . strtolower($accion)) ? 'disabled' : '' }}" name="periodo"
                id="periodo">
                <option id="periodo1" value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected' : '' }}>Mensual</option>
                <option id="periodo2" value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected' : '' }}>Anual</option>
                <option id="periodo3" value="3" {{ old('periodo', $licencia->periodo) == '3' ? 'Selected' : '' }}>Premium</option>
                <option id="periodo4" value="4" {{ old('periodo', $licencia->periodo) == '4' ? 'Selected' : '' }}>Gratis</option>
            </select>
            @if (isset($licencia->sis_licenciasid) && $licencia->producto != 6 && $licencia->producto != 9)
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        {{ !puede('web', 'mostrar_renovar') ? 'disabled' : '' }}>
                        Renovar
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
    </div>
    <div class="col-lg-6">
        <label> Precio </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text text-white">
                    <i class="la la-dollar"></i>
                </span>
            </div>
            <input type="text" class="form-control text-success font-weight-bold disabled" id="precio" name="precio" autocomplete="off" />
        </div>
    </div>

</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Inicia:</label>
        <input type="text"
            class="form-control {{ !puede('web', 'editar_fechas') ? 'disabled' : '' }} 
                {{ $errors->has('fechainicia') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Inicio" name="fechainicia" id="fechainicia" autocomplete="off"
            value="{{ old('fechainicia', $licencia->fechainicia) }}" />
        @if ($errors->has('fechainicia'))
            <span class="text-danger">{{ $errors->first('fechainicia') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Fecha Caduca:</label>
        <input type="text"
            class="form-control {{ !puede('web', 'editar_fechas') ? 'disabled' : '' }} 
                {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
            value="{{ old('fechacaduca', $licencia->fechacaduca) }}" />
        @if ($errors->has('fechacaduca'))
            <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Empresas:</label>
        <input type="text"
            class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} 
                {{ $errors->has('empresas') ? 'is-invalid' : '' }}"
            placeholder="N° Empresas" name="empresas" autocomplete="off" id="empresas" value="{{ old('empresas', $licencia->empresas) }}" />
        @if ($errors->has('empresas'))
            <span class="text-danger">{{ $errors->first('empresas') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>N° Usuarios:</label>
        <input type="text"
            class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} 
                {{ $errors->has('usuarios') ? 'is-invalid' : '' }}"
            placeholder="N° Usuarios" name="usuarios" autocomplete="off" id="usuarios" value="{{ old('usuarios', $licencia->usuarios) }}" />
        @if ($errors->has('usuarios'))
            <span class="text-danger">{{ $errors->first('usuarios') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Móviles:</label>
        <input type="text"
            class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} 
                {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}"
            placeholder="N° Móviles" name="numeromoviles" autocomplete="off" id="numeromoviles"
            value="{{ old('numeromoviles', $licencia->numeromoviles) }}" />
        @if ($errors->has('numeromoviles'))
            <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>N° Sucursales:</label>
        <input type="text"
            class="form-control {{ !puede('web', 'editar_campos_numericos') ? 'disabled' : '' }} 
                {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}"
            placeholder="N° Sucursales" name="numerosucursales" autocomplete="off" id="numerosucursales"
            value="{{ old('numerosucursales', $licencia->numerosucursales) }}" />
        @if ($errors->has('numerosucursales'))
            <span class="text-danger">{{ $errors->first('numerosucursales') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Servidor:</label>
        <select class="form-control disabled" name="sis_servidoresid" id="sis_servidoresid">
            @foreach ($servidores as $servidor)
                <option value="{{ $servidor->sis_servidoresid }}"
                    {{ $servidor->sis_servidoresid == $licencia->sis_servidoresid ? 'selected' : '' }}>
                    {{ $servidor->descripcion }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('sis_servidoresid'))
            <span class="text-danger">{{ $errors->first('sis_servidoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Agrupados:</label>
        <select class="form-control select2" name="sis_agrupadosid" id="sis_agrupadosid"
            {{ !puede('web', 'editar_agrupados') ? 'disabled' : '' }}>
            <option value="0">Sin grupo</option>
            @foreach ($agrupados as $agrupado)
                <option value="{{ $agrupado->sis_agrupadosid }}" {{ $agrupado->sis_agrupadosid == $licencia->sis_agrupadosid ? 'selected' : '' }}>
                    {{ $agrupado->codigo }}-{{ $agrupado->nombres }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('sis_servidoresid'))
            <span class="text-danger">{{ $errors->first('sis_servidoresid') }}</span>
        @endif
    </div>
</div>

<!-- Módulos con switch - simplificada la repetición de código -->
@php
    $modulos = [
        ['name' => 'nomina', 'label' => 'Nómina', 'value' => $modulos->nomina],
        ['name' => 'activos', 'label' => 'Activos Fijos', 'value' => $modulos->activos],
        ['name' => 'produccion', 'label' => 'Producción', 'value' => $modulos->produccion],
        ['name' => 'restaurantes', 'label' => 'Restaurantes', 'value' => $modulos->restaurantes],
        ['name' => 'talleres', 'label' => 'Talleres', 'value' => $modulos->talleres],
        ['name' => 'garantias', 'label' => 'Garantías', 'value' => $modulos->garantias],
        ['name' => 'ecommerce', 'label' => 'Ecommerce', 'value' => $modulos->ecommerce],
    ];
@endphp

@foreach (array_chunk($modulos, 2) as $moduloRow)
    <div class="form-group row">
        @foreach ($moduloRow as $modulo)
            <label class="col-4 col-form-label">{{ $modulo['label'] }}</label>
            <div class="col-2">
                <span class="switch switch-outline switch-icon switch-primary switch-sm">
                    <label>
                        <input @if ($modulo['value'] == 1) checked="checked" @endif type="checkbox" name="{{ $modulo['name'] }}"
                            id="{{ $modulo['name'] }}" @if (!puede('web', 'editar_modulos')) class="deshabilitar" @endif />
                        <span></span>
                    </label>
                </span>
            </div>
        @endforeach
    </div>
@endforeach

@section('script')
    <script>
        // Obtener configuraciones de productos desde PHP
        const configuracionesProductos = @json(config('sistema.productos.web'));

        $(document).ready(function() {
            inicializarFormulario();

            // Eventos de los botones
            $("#renovarmensual").click(() => confirmarAccion('mes', "Está seguro de Renovar la Licencia?"));
            $("#renovaranual").click(() => confirmarAccion('anual', "Está seguro de Renovar la Licencia?"));
            $("#recargar").click(() => confirmarAccion('recargar', "¿Está seguro de Recargar 120 Documentos Adicionales a la Licencia?"));
            $("#recargar240").click(() => confirmarAccion('recargar240', "¿Está seguro de Recargar 240 Documentos Adicionales a la Licencia?"));
            $("#resetear").click(confirmarResetearClave);

            // Eventos de cambio
            $('#periodo, #producto').change(cambiarComboWeb);

            // Deshabilitar clics en elementos deshabilitados
            $('.deshabilitar').click(() => false);

        });

        function inicializarFormulario() {
            const fecha = new Date();
            const fechaInicia = formatearFecha(fecha);

            if (!"{{ isset($licencia->sis_licenciasid) }}") {
                configurarFormularioNuevo(fechaInicia);
            } else {
                configurarFormularioExistente();
            }

            inicializarDatepicker();
        }

        function configurarFormularioNuevo(fechaInicia) {
            $('#fechainicia').val(fechaInicia);
            $('#periodo1').html("Mensual");
            $('#periodo2').html("Anual");
            $('#periodo3, #periodo4').addClass("d-none");
            $('#periodo').removeClass("disabled");
            $('#precio').val('11.69');
            $('#usuarios').val('6');
            $('#numeromoviles').val('1');
            $('#sis_servidoresid').val('3');
            $('#ecommerce').prop('checked', false);
            $('#produccion').prop('checked', true);
            $('#nomina').prop('checked', false);
            $('#activos').prop('checked', false);
            $('#restaurantes').prop('checked', true);
            $('#talleres').prop('checked', false);
            $('#garantias').prop('checked', false);

            cambiarComboWeb();
        }

        function configurarFormularioExistente() {
            const producto = "{{ $licencia->producto }}";
            if (producto == 12) {
                llenarComboPeriodoProducto12();
            } else if ([6, 9, 10].includes(parseInt(producto))) {
                $('#periodo1').html("Mensual");
                $('#periodo2').html("Anual");
                $('#periodo3, #periodo4').addClass("d-none");
                $('#periodo').addClass("disabled");
            } else {
                $('#periodo1').html("Mensual");
                $('#periodo2').html("Anual");
                $('#periodo3, #periodo4').addClass("d-none");
            }

            cambiarComboWeb();
        }

        function inicializarDatepicker() {
            $('#fechainicia, #fechacaduca').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });
        }

        function llenarComboPeriodoProducto12() {
            $('#periodo1').html("Inicial");
            $('#periodo2').html("Básico");
            $('#periodo3').html("Premium");
            $('#periodo4').html("Gratis");
            $('#periodo3, #periodo4').removeClass("d-none");
        }

        // Ajustar la función cambiarComboWeb para que respete los valores existentes en modo edición
        function cambiarComboWeb() {
            const producto = $('#producto').val();
            let periodo = $('#periodo').val();
            const esEdicion = "{{ isset($licencia->sis_licenciasid) }}";
            const productoAnterior = "{{ $licencia->producto }}";
            const periodoAnterior = "{{ $licencia->periodo }}";
            const fecha = esEdicion ?
                new Date('{{ isset($licencia->fechacaduca) ? date('Y-m-d', strtotime($licencia->fechacaduca)) : '' }}') :
                new Date();

            // Configurar período según el producto
            if (producto == 12) {
                llenarComboPeriodoProducto12();
            } else {
                $('#periodo1').html("Mensual");
                $('#periodo2').html("Anual");
                $('#periodo3, #periodo4').addClass("d-none");

                if (periodo > 2) {
                    $('#periodo').val(1);
                    periodo = 1;
                }
            }

            // Manejar estado disabled del período
            if ([6, 9, 10].includes(parseInt(producto))) {
                $('#periodo').addClass("disabled");
            } else {
                // Verificar permisos usando la nueva lógica centralizada
                const accion = "{{ $accion }}";
                const puedeEditarPeriodo = @json(puede('web', 'editar_periodo_' . strtolower($accion)));

                if (puedeEditarPeriodo) {
                    $('#periodo').removeClass("disabled");
                }
            }

            // Determinar si debemos actualizar los módulos
            const debeActualizarModulos = esEdicion && producto != productoAnterior;

            // Solo aplicar configuraciones predeterminadas cuando sea necesario
            const configuraciones = obtenerConfiguraciones(producto, periodo);
            aplicarConfiguraciones(configuraciones, fecha, esEdicion, debeActualizarModulos);
        }

        function aplicarConfiguraciones(config, fecha, esEdicion, debeActualizarModulos) {
            if (config) {
                // SIEMPRE aplicar el precio desde las configuraciones (tanto en creación como en edición)
                $('#precio').val(config.precio);
                // Solo aplicar valores por defecto en modo creación
                if (!esEdicion) {
                    // Para nueva licencia, aplicar todos los valores predeterminados
                    $('#precio').val(config.precio);
                    $('#usuarios').val(config.usuarios);
                    $('#numeromoviles').val(config.moviles);
                    $('#numerosucursales').val(config.sucursales || 0);
                    $('#empresas').val(config.empresas || 1);
                    $('#sis_servidoresid').val(config.servidor);

                    // Actualizar módulos para nueva licencia
                    actualizarModulos(config.modulos);

                    // Calcular fecha de caducidad
                    const fechaCaducidad = new Date(fecha);
                    fechaCaducidad.setMonth(fechaCaducidad.getMonth() + config.meses);
                    $('#fechacaduca').val(formatearFecha(fechaCaducidad));
                } else if (debeActualizarModulos) {
                    // En modo edición, solo actualizar módulos si cambia el producto
                    // pero NUNCA los valores numéricos o configurables por el usuario
                    actualizarModulos(config.modulos);
                }
                // En cualquier otro caso de edición, no hacemos nada para preservar los valores del usuario
            }
        }

        // Función auxiliar para actualizar los módulos
        function actualizarModulos(modulos) {
            $('#ecommerce').prop('checked', modulos.ecommerce);
            $('#produccion').prop('checked', modulos.produccion);
            $('#nomina').prop('checked', modulos.nomina);
            $('#activos').prop('checked', modulos.activos);
            $('#restaurantes').prop('checked', modulos.restaurantes);
            $('#talleres').prop('checked', modulos.talleres);
            $('#garantias').prop('checked', modulos.garantias);
        }

        function obtenerConfiguraciones(producto, periodo) {
            const config = configuracionesProductos[producto];
            if (!config) return null;

            // Mapear período a clave de configuración
            let tipoPeriodo;
            if (producto == 12) {
                const mapaPeriodos = {
                    1: 'inicial',
                    2: 'basico',
                    3: 'premium',
                    4: 'gratis'
                };
                tipoPeriodo = mapaPeriodos[periodo] || 'inicial';
            } else {
                tipoPeriodo = periodo == 1 ? 'mensual' : 'anual';
            }

            // Si no existe el período solicitado, usar el primero disponible
            const periodoConfig = config[tipoPeriodo] || config[Object.keys(config).find(key => typeof config[key] === 'object' && config[key].precio)];

            if (!periodoConfig) return null;

            // Determinar qué módulos usar
            let modulosAUsar;
            if (periodoConfig.modulos) {
                // Usar módulos específicos del período (ej: Comercial mensual vs anual)
                modulosAUsar = periodoConfig.modulos;
            } else {
                // Usar módulos generales del producto
                modulosAUsar = config.modulos;
            }

            return {
                precio: periodoConfig.precio,
                usuarios: config.usuarios,
                moviles: config.moviles,
                sucursales: config.sucursales,
                empresas: config.empresas,
                servidor: config.servidor,
                modulos: modulosAUsar,
                meses: periodoConfig.meses
            };
        }

        function confirmarAccion(tipo, mensaje) {
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
        }

        function confirmarResetearClave() {
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
                    $.get('{{ route('editar_clave', [$cliente->sis_clientesid, $servidoresid, $licenciasid]) }}', function(data) {
                        $.notify({
                            message: data.mensaje,
                        }, {
                            showProgressbar: true,
                            delay: 2500,
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            animate: {
                                enter: "animated fadeInUp",
                                exit: "animated fadeOutDown"
                            },
                            type: data.tipo,
                        });
                    });
                }
            });
        }

        function formatearFecha(fecha) {
            return ("0" + fecha.getDate()).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear();
        }
    </script>
@endsection
