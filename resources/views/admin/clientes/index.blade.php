@extends('admin.layouts.app')
@section('contenido')
    <style>

        /* ✅ Mejorar separación visual */
        .filter-section {
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        /*Eliminar :  en responsive */
        .dtr-title:after,
        .dtr-title::after {
            content: "" !important;
            display: none !important;
        }

        /* Filas más compactas y texto en una línea */
        #kt_datatable tbody td {
            padding: 6px 8px !important;
            font-size: 12px !important;
        }

    </style>

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header py-4">
                                <div class="card-title">
                                    <h3 class="card-label font-weight-bold text-dark">
                                        <i class="fas fa-users text-primary mr-3"></i>Gestión de Clientes
                                    </h3>
                                </div>

                                <div class="card-toolbar">
                                    <div class="btn-group mr-2">
                                        <button type="button" class="btn btn-light-primary font-weight-bold" id="filtrar">
                                            <i class="fas fa-filter mr-2"></i>Filtros
                                        </button>
                                    </div>

                                    <div class="btn-group mr-2">
                                        <button type="button" class="btn btn-light-success font-weight-bold dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-download mr-2"></i>Exportar
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                            <h6 class="dropdown-header font-weight-bold text-primary d-flex align-items-center">
                                                <i class="fas fa-file-export mr-2"></i>Formato de exportación
                                            </h6>
                                            <div class="dropdown-divider"></div>

                                            <a href="#" class="dropdown-item d-flex align-items-center" id="export_print">
                                                <i class="fas fa-print text-info mr-3"></i>Imprimir
                                            </a>
                                            <a href="#" class="dropdown-item d-flex align-items-center" id="export_copy">
                                                <i class="fas fa-copy text-secondary mr-3"></i>Copiar
                                            </a>
                                            <a href="#" class="dropdown-item d-flex align-items-center" id="export_excel">
                                                <i class="fas fa-file-excel text-success mr-3"></i>Excel
                                            </a>
                                            <a href="#" class="dropdown-item d-flex align-items-center" id="export_pdf">
                                                <i class="fas fa-file-pdf text-danger mr-3"></i>PDF
                                            </a>
                                        </div>
                                    </div>

                                    @if (puede('clientes', 'crear_clientes'))
                                        <a href="{{ route('clientes.crear') }}" class="btn btn-primary font-weight-bold">
                                            <i class="fas fa-plus mr-2"></i>Nuevo Cliente
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <!-- ✅ Sección de filtros mejorada -->
                                <div class="filter-section p-4 mb-6" id="filtro" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="font-weight-bold text-dark mb-0">
                                            <i class="fas fa-search text-primary mr-2"></i>Filtros de Búsqueda
                                        </h5>
                                        <button type="button" class="btn btn-sm btn-light-danger pr-2" onclick="$('#filtro').hide();">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <!-- ✅ Filtros agrupados por categorías -->
                                    <div class="row">
                                        <!-- Grupo: Configuración de Fecha -->
                                        <div class="col-12">
                                            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-calendar-alt"></i> Configuración de Fechas
                                            </p>
                                            <div class="separator separator-dashed mb-2"></div>
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Tipo de Fecha:
                                                    </label>
                                                    <select class="form-control form-control datatable-input" id="tipofecha">
                                                        <option value="1">📅 Fecha Inicio</option>
                                                        <option value="2">⏰ Fecha Caduca</option>
                                                        <option value="3">🔄 Fecha Actualización</option>
                                                        <option value="4">✏️ Fecha Modificación</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-8 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Rango de Fechas:
                                                    </label>
                                                    <div class="input-group" id='kt_fecha'>
                                                        <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-calendar-check"></i>
                                                                </span>
                                                        </div>
                                                        <input type="text" class="form-control form-control"
                                                               autocomplete="off" placeholder="Seleccione rango de fechas" id="fecha">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grupo: Configuración de Productos -->
                                        <div class="col-12">
                                            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-box"></i> Productos y Licencias
                                            </p>
                                            <div class="separator separator-dashed mb-2"></div>
                                            <div class="row">
                                                <div class="col-lg-3 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Tipo Licencia:
                                                    </label>
                                                    <select class="form-control form-control datatable-input" id="tipolicencia">
                                                        <option value="1">🌐 Todos</option>
                                                        <option value="2">💻 Web</option>
                                                        <option value="3">🖥️ PC</option>
                                                        <option value="4">☁️ VPS</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Producto:
                                                    </label>
                                                    <select class="form-control form-control datatable-input" id="producto" name="producto">
                                                        <option value="">Todos los productos</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Período:
                                                    </label>
                                                    <select class="form-control form-control datatable-input" id="periodo" name="periodo">
                                                        <option value="">Todos</option>
                                                        <option id="periodo1" value="1">📅 Mensual</option>
                                                        <option id="periodo2" value="2">📆 Anual</option>
                                                        <option class="d-none" id="periodo3" value="3">⭐ Premium</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grupo: Configuración Comercial -->
                                        <div class="col-12">
                                            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-handshake"></i> Información Comercial
                                            </p>
                                            <div class="separator separator-dashed mb-2"></div>
                                            <div class="row">
                                                <div class="col-lg-4 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Distribuidor:
                                                    </label>
                                                    <select class="form-control form-control-solid datatable-input select2" id="distribuidor"
                                                            name="distribuidor">
                                                        @if (Auth::user()->tipo == 1)
                                                            <option value="">Todos los distribuidores</option>
                                                        @else
                                                            <option value="">Seleccione distribuidor</option>
                                                        @endif
                                                        @foreach ($distribuidores as $distribuidor)
                                                            <option value="{{ $distribuidor->sis_distribuidoresid }}">
                                                                {{ $distribuidor->razonsocial }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Vendedor:
                                                    </label>
                                                    <select class="form-control form-control-solid datatable-input select2" id="vendedor"
                                                            name="vendedor">
                                                        <option value="">Todos los vendedores</option>
                                                        @foreach ($vendedores as $vendedor)
                                                            <option value="{{ $vendedor->sis_revendedoresid }}">
                                                                {{ $vendedor->razonsocial }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-6 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Revendedor:
                                                    </label>
                                                    <select class="form-control form-control-solid datatable-input select2" id="revendedor"
                                                            name="revendedor">
                                                        <option value="">Todos los revendedores</option>
                                                        @foreach ($revendedores as $vendedor)
                                                            <option value="{{ $vendedor->sis_revendedoresid }}">
                                                                {{ $vendedor->razonsocial }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Grupo: Ubicación y Origen -->
                                        <div class="col-12">
                                            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-map-marker-alt"></i> Ubicación y Origen
                                            </p>
                                            <div class="separator separator-dashed mb-2"></div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-12 mb-3">
                                                    <label class="font-weight-bold text-dark">
                                                        Provincia:
                                                    </label>
                                                    <select class="form-control form-control-solid datatable-input select2" id="provinciasid"
                                                            name="provinciasid">
                                                        <option value="">🗺️ Todas las provincias</option>
                                                        @php
                                                            $provincias = [
                                                                ['id' => '01', 'nombre' => 'Azuay'],
                                                                ['id' => '02', 'nombre' => 'Bolivar'],
                                                                ['id' => '03', 'nombre' => 'Cañar'],
                                                                ['id' => '04', 'nombre' => 'Carchi'],
                                                                ['id' => '05', 'nombre' => 'Chimborazo'],
                                                                ['id' => '06', 'nombre' => 'Cotopaxi'],
                                                                ['id' => '07', 'nombre' => 'El Oro'],
                                                                ['id' => '08', 'nombre' => 'Esmeraldas'],
                                                                ['id' => '09', 'nombre' => 'Guayas'],
                                                                ['id' => '20', 'nombre' => 'Galapagos'],
                                                                ['id' => '10', 'nombre' => 'Imbabura'],
                                                                ['id' => '11', 'nombre' => 'Loja'],
                                                                ['id' => '12', 'nombre' => 'Los Rios'],
                                                                ['id' => '13', 'nombre' => 'Manabi'],
                                                                ['id' => '14', 'nombre' => 'Morona Santiago'],
                                                                ['id' => '15', 'nombre' => 'Napo'],
                                                                ['id' => '22', 'nombre' => 'Orellana'],
                                                                ['id' => '16', 'nombre' => 'Pastaza'],
                                                                ['id' => '17', 'nombre' => 'Pichincha'],
                                                                ['id' => '24', 'nombre' => 'Santa Elena'],
                                                                ['id' => '23', 'nombre' => 'Santo Domingo De Los Tsachilas'],
                                                                ['id' => '21', 'nombre' => 'Sucumbios'],
                                                                ['id' => '18', 'nombre' => 'Tungurahua'],
                                                                ['id' => '19', 'nombre' => 'Zamora Chinchipe'],
                                                            ];
                                                        @endphp
                                                        @foreach ($provincias as $provincia)
                                                            <option value="{{ $provincia['id'] }}">{{ $provincia['nombre'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @if (Auth::user()->tipo == 1)
                                                    <div class="col-lg-6 col-md-12 mb-3">
                                                        <label class="font-weight-bold text-dark">
                                                            Origen:
                                                        </label>
                                                        <select class="form-control form-control-solid datatable-input select2" id="origen"
                                                                name="origen">
                                                            @php
                                                                $origenes = [
                                                                    ['id' => '', 'nombre' => 'Todos'],
                                                                    ['id' => '1', 'nombre' => 'Perseo'],
                                                                    ['id' => '2', 'nombre' => 'Contafácil'],
                                                                    ['id' => '3', 'nombre' => 'UIO-01'],
                                                                    ['id' => '8', 'nombre' => 'UIO-02'],
                                                                    ['id' => '5', 'nombre' => 'GYE-02'],
                                                                    ['id' => '6', 'nombre' => 'CUE-01'],
                                                                    ['id' => '7', 'nombre' => 'STO-01'],
                                                                    ['id' => '10', 'nombre' => 'CNV-01'],
                                                                    ['id' => '11', 'nombre' => 'MATRIZ'],
                                                                    ['id' => '12', 'nombre' => 'CUE-02'],
                                                                    ['id' => '13', 'nombre' => 'CUE-03'],
                                                                    ['id' => '14', 'nombre' => 'UIO-03'],
                                                                    ['id' => '15', 'nombre' => 'UIO-04'],
                                                                    ['id' => '16', 'nombre' => 'UIO-05'],
                                                                    ['id' => '18', 'nombre' => 'SP-01'],
                                                                    ['id' => '19', 'nombre' => 'SP-02'],
                                                                    ['id' => '20', 'nombre' => 'SP-03'],
                                                                    ['id' => '21', 'nombre' => 'SP-04'],
                                                                    ['id' => '22', 'nombre' => 'SP-05'],
                                                                    ['id' => '17', 'nombre' => 'Tienda'],
                                                                ];
                                                            @endphp
                                                            @foreach ($origenes as $origen)
                                                                <option value="{{ $origen['id'] }}">
                                                                    @if($origen['id'] == '')
                                                                        🌐
                                                                    @endif{{ $origen['nombre'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ✅ Botones de acción mejorados -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="btn-group">
                                                    <button class="btn btn-primary font-weight-bold" id="kt_search">
                                                        <i class="fas fa-search mr-2"></i>Buscar Clientes
                                                        <input type="hidden" name="buscar_filtro" id="buscar_filtro">
                                                    </button>
                                                    <button class="btn btn-secondary font-weight-bold ml-2" id="kt_reset">
                                                        <i class="fas fa-undo mr-2"></i>Limpiar Filtros
                                                    </button>
                                                </div>

                                                <!-- ✅ Indicador de filtros activos -->
                                                <div id="filtros-activos" class="badge badge-light-primary" style="display: none;">
                                                    <i class="fas fa-filter mr-1"></i>
                                                    <span id="count-filtros">0</span> filtros activos
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ Tabla con header mejorado -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-head-custom" id="kt_datatable">
                                        <thead class="thead-light">
                                        <tr>
                                            <th class="no-exportar text-center">
                                                <i class="fas fa-check-circle text-success" title="Estado"></i>
                                            </th>
                                            <th class="no-exportar">#</th>
                                            <th data-priority="1">
                                                <span>Contrato</span>
                                            </th>
                                            <th class="no-exportar">
                                                Identificador
                                            </th>
                                            <th data-priority="2">
                                                <span>Identificación</span>
                                            </th>
                                            <th data-priority="3">
                                                <span>Cliente</span>
                                            </th>
                                            <th data-priority="4">
                                                <span>Distribuidor</span>
                                            </th>
                                            <th>
                                                <span>Celular</span>
                                            </th>
                                            <th style="display:none">Correos</th>
                                            <th data-priority="5">
                                                <span>Tipo</span>
                                            </th>
                                            <th data-priority="6">
                                                <span>Producto</span>
                                            </th>
                                            <th data-priority="7">
                                                <span>Inicio</span>
                                            </th>
                                            <th data-priority="8">
                                                <span>Vence</span>
                                            </th>
                                            <!-- Columnas ocultas permanecen igual -->
                                            <th style="display:none">Grupo</th>
                                            <th style="display:none">Dias Hasta Vencer</th>
                                            <th style="display:none">Precio</th>
                                            <th style="display:none">Periodo</th>
                                            <th style="display:none">Producto</th>
                                            <th style="display:none">Fecha Ultimo Pago</th>
                                            <th style="display:none">Fecha Actualizaciones</th>
                                            <th style="display:none">Vendedor</th>
                                            <th style="display:none">Revendedor</th>
                                            <th style="display:none">Origen</th>
                                            <th style="display:none">Provincia</th>
                                            <th style="display:none">Usuarios</th>
                                            <th style="display:none">Empresas</th>
                                            <th style="display:none">Moviles</th>
                                            <th style="display:none">Cantidad Empresas</th>
                                            <th style="display:none">Usuarios Activos</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script>
        // ====================================
        // CONFIGURACIÓN DINÁMICA
        // ====================================

        const ConfigClientes = {
            configuracion: @json(config('sistema')),

            // ✅ Mapeos dinámicos para productos
            actualizarProductos(tipoLicencia) {
                $('#producto').empty();
                $('#producto').append('<option value="">Todos los productos</option>');

                if (!tipoLicencia || tipoLicencia === '1') {
                    return; // "Todos" seleccionado
                }

                // Usar configuración dinámica según tipo
                let productos = [];
                switch (tipoLicencia) {
                    case '2': // Web
                        productos = this.obtenerProductosWeb();
                        break;
                    case '3': // PC
                        productos = this.obtenerProductosPC();
                        break;
                    case '4': // VPS
                        productos = [{id: 'vps', nombre: 'Perseo VPS'}];
                        break;
                }

                productos.forEach(producto => {
                    $('#producto').append(`<option value="${producto.id}">${producto.nombre}</option>`);
                });
            },

            // ✅ Obtener productos Web desde configuración
            obtenerProductosWeb() {
                const productosWeb = this.configuracion.productos.web;
                const productos = [];

                Object.keys(productosWeb).forEach(id => {
                    let nombre = this.obtenerNombreProducto(id);
                    productos.push({id: id, nombre: nombre});
                });

                return productos;
            },

            // ✅ Obtener productos PC desde configuración
            obtenerProductosPC() {
                const modulosPC = this.configuracion.productos.pc.modulos_principales;
                const productos = [];

                Object.keys(modulosPC).forEach(modulo => {
                    productos.push({
                        id: modulo,
                        nombre: modulo.charAt(0).toUpperCase() + modulo.slice(1)
                    });
                });

                return productos;
            },

            // ✅ Mapeo de nombres de productos
            obtenerNombreProducto(id) {
                const nombres = {
                    '2': 'Facturación',
                    '3': 'Servicios',
                    '4': 'Comercial',
                    '5': 'Soy Contador Comercial',
                    '6': 'Perseo Lite Anterior',
                    '8': 'Soy Contador Servicios',
                    '9': 'Perseo Lite',
                    '10': 'Emprendedor',
                    '11': 'Socio Perseo',
                    '12': 'Facturito'
                };
                return nombres[id] || `Producto ${id}`;
            },

            // ✅ Actualizar períodos según producto
            actualizarPeriodos(producto) {
                if (producto == '12') {
                    $('#periodo1').html("📋 Inicial");
                    $('#periodo2').html("💼 Básico");
                    $('#periodo3').html("⭐ Premium").removeClass("d-none");
                } else {
                    $('#periodo1').html("📅 Mensual");
                    $('#periodo2').html("📆 Anual");
                    $('#periodo3').addClass("d-none");
                }
            },

            // ✅ Contar filtros activos
            contarFiltrosActivos() {
                let count = 0;
                const filtros = ['#tipofecha', '#tipolicencia', '#fecha', '#periodo', '#producto',
                    '#distribuidor', '#vendedor', '#revendedor', '#provinciasid', '#origen'];

                filtros.forEach(filtro => {
                    const valor = $(filtro).val();
                    if (valor && valor !== '' && valor !== '1') { // '1' es "Todos" en tipolicencia
                        count++;
                    }
                });

                const elemento = $('#filtros-activos');
                if (count > 0) {
                    elemento.show();
                    $('#count-filtros').text(count);
                } else {
                    elemento.hide();
                }
            }
        };

        // ====================================
        // EVENT HANDLERS PRINCIPALES
        // ====================================

        // ✅ Cambio de tipo de licencia
        $('#tipolicencia').on('change', function (e) {
            const tipoLicencia = e.target.value;
            ConfigClientes.actualizarProductos(tipoLicencia);
            ConfigClientes.contarFiltrosActivos();
        });

        // ✅ Cambio de producto
        $('#producto').on('change', function (e) {
            const producto = e.target.value;
            ConfigClientes.actualizarPeriodos(producto);
            ConfigClientes.contarFiltrosActivos();
        });

        // ✅ Contar filtros en todos los cambios
        $('.datatable-input').on('change', function () {
            ConfigClientes.contarFiltrosActivos();
        });

        // ====================================
        // INICIALIZACIÓN PRINCIPAL
        // ====================================

        $(document).ready(function () {

            // ✅ Inicializar contador de filtros
            ConfigClientes.contarFiltrosActivos();

            // ====================================
            // CONFIGURACIÓN DE DATERANGEPICKER
            // ====================================

            $('#kt_fecha').daterangepicker({
                autoUpdateInput: false,
                format: "DD-MM-YYYY",
                locale: {
                    "separator": " - ",
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "DE",
                    "toLabel": "HASTA",
                    "customRangeLabel": "Personalizado",
                    "daysOfWeek": [
                        "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sáb"
                    ],
                    "monthNames": [
                        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                    ],
                    "firstDay": 1
                },
                ranges: {
                    'Hoy': [moment(), moment()],
                    'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
                    'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
                    'Mes Actual': [moment().startOf('month'), moment().endOf('month')],
                    'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Año Actual': [moment().startOf('year'), moment().endOf('year')],
                    'Año Anterior': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                },
                buttonClasses: ' btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                alwaysShowCalendars: true,
                showDropdowns: true,
            }, function (start, end, label) {
                $('#kt_fecha .form-control').val(start.format('DD-MM-YYYY') + ' / ' + end.format('DD-MM-YYYY'));
                ConfigClientes.contarFiltrosActivos();
            });

            // ====================================
            // CONFIGURACIÓN AJAX
            // ====================================

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ====================================
            // CONFIGURACIÓN DE DATATABLE
            // ====================================

            var table = $('#kt_datatable').DataTable({
                // Posición de los elementos de la datatable
                dom: "<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

                responsive: true,
                processing: true,
                search: {
                    return: true,
                },

                // Combo cantidad de registros a mostrar por pantalla
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, 'Todos']
                ],

                // Registros por página
                pageLength: 15,

                // Orden inicial
                order: [[1, 'desc']],

                // Trabajar del lado del server
                serverSide: true,

                // Petición ajax que devuelve los registros
                ajax: {
                    url: "{{ route('clientes.tabla') }}",
                    type: 'POST',
                    data: function (d) {
                        // Valores de filtro a enviar
                        d.tipofecha = $("#tipofecha").val();
                        d.tipolicencia = $("#tipolicencia").val();
                        d.fecha = $("#fecha").val();
                        d.periodo = $("#periodo").val();
                        d.producto = $("#producto").val();
                        d.distribuidor = $("#distribuidor").val();
                        d.vendedor = $("#vendedor").val();
                        d.revendedor = $("#revendedor").val();
                        d.origen = $("#origen").val();
                        d.validado = $("#validado").val();
                        d.provinciasid = $("#provinciasid").val();
                        d.buscar_filtro = $("#buscar_filtro").val();
                    }
                },

                // Columnas de la tabla
                columns: [
                    {
                        data: 'validado',
                        name: 'validado',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    },
                    {
                        data: 'sis_clientesid',
                        name: 'sis_clientesid',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'numerocontrato',
                        name: 'numerocontrato',
                        visible: true
                    },
                    {
                        data: 'Identificador',
                        name: 'Identificador',
                        visible: false
                    },
                    {
                        data: 'identificacion',
                        name: 'identificacion'
                    },
                    {
                        data: 'nombres',
                        name: 'nombres'
                    },
                    {
                        data: 'sis_distribuidoresid',
                        name: 'sis_distribuidoresid',
                        searchable: false
                    },
                    {
                        data: 'telefono2',
                        name: 'telefono2',
                        searchable: true
                    },
                    {
                        data: 'correos',
                        name: 'correos',
                        visible: false,
                        searchable: true
                    },
                    {
                        data: 'tipo_licencia',
                        name: 'tipo_licencia',
                        searchable: false
                    },
                    {
                        data: 'producto',
                        name: 'producto',
                        searchable: false
                    },
                    {
                        data: 'fechainicia',
                        name: 'fechainicia',
                        searchable: false
                    },
                    {
                        data: 'fechacaduca',
                        name: 'fechacaduca',
                        searchable: false
                    },
                    {
                        data: 'grupo',
                        name: 'grupo',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'diasvencer',
                        name: 'diasvencer',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'precio',
                        name: 'precio',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'periodo',
                        name: 'periodo',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'producto',
                        name: 'producto',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'fechaultimopago',
                        name: 'fechaultimopago',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'fechaactulizaciones',
                        name: 'fechaactulizaciones',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'sis_vendedoresid',
                        name: 'sis_vendedoresid',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'sis_revendedoresid',
                        name: 'sis_revendedoresid',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'red_origen',
                        name: 'red_origen',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'provinciasid',
                        name: 'provinciasid',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'usuarios',
                        name: 'usuarios',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'empresas',
                        name: 'empresas',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'numeromoviles',
                        name: 'numeromoviles',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'cantidadempresas',
                        name: 'cantidadempresas',
                        visible: false,
                        searchable: false
                    },
                    {
                        data: 'usuarios_activos',
                        name: 'usuarios_activos',
                        visible: false,
                        searchable: false
                    },
                ],

                // Botones para exportar
                buttons: [
                    {
                        extend: 'copyHtml5',
                        title: 'Clientes',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'Clientes',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Clientes',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Clientes',
                        exportOptions: {
                            columns: ':not(.no-exportar)'
                        }
                    }
                ],

                // ====================================
                // INICIALIZACIÓN COMPLETA
                // ====================================

                initComplete: function () {
                    const self = this.api();

                    // Si está en móvil agregar botón buscar
                    if ($(window).width() < 768) {
                        const input = $('.dataTables_filter input').unbind();
                        const $searchButton = $('<button>')
                            .text('Buscar')
                            .addClass('btn btn-sm btn-primary ml-1')
                            .click(function () {
                                self.search(input.val()).draw();
                            });

                        $('.dataTables_filter').append($searchButton);
                    } else {
                        // Buscar con enter en desktop
                        $('.dataTables_filter input').unbind();
                        $('.dataTables_filter input').bind('keyup', function (e) {
                            const code = e.keyCode || e.which;
                            if (code == 13) {
                                table.search(this.value).draw();
                            }
                        });
                    }

                    // Buscar al borrar y no hay caracteres
                    $('.dataTables_filter input').off('.DT').on('keyup.DT', function (e) {
                        if (e.keyCode == 8 && this.value.length == 0) {
                            self.search('').draw();
                        }
                    });

                    // Buscar al hacer clic en limpiar
                    $('input[type="search"]').on('search', function () {
                        self.search('').draw();
                    });
                },
            });

            // ====================================
            // EVENT HANDLERS DE EXPORTACIÓN
            // ====================================

            // Botón copiar
            $('#export_copy').on('click', function (e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            // Botón Excel
            $('#export_excel').on('click', function (e) {
                e.preventDefault();
                table.button(1).trigger();
            });

            // Botón Imprimir
            $('#export_print').on('click', function (e) {
                e.preventDefault();
                table.button(2).trigger();
            });

            // Botón PDF
            $('#export_pdf').on('click', function (e) {
                e.preventDefault();
                table.button(3).trigger();
            });

            // ====================================
            // EVENT HANDLERS DE FILTROS
            // ====================================

            // Botón buscar
            $('#kt_search').on('click', function (e) {
                e.preventDefault();
                $("#buscar_filtro").val('1');
                table.draw();
                ConfigClientes.contarFiltrosActivos();
            });

            // ✅ Botón resetear mejorado
            $('#kt_reset').on('click', function (e) {
                e.preventDefault();

                // Limpiar todos los campos
                $("#tipofecha").val('1');
                $("#tipolicencia").val('1');
                $("#fecha").val('');
                $("#periodo").val('');
                $("#producto").val('');
                $("#distribuidor").val('').trigger('change');
                $("#vendedor").val('').trigger('change');
                $("#revendedor").val('').trigger('change');
                $("#origen").val('');
                $("#provinciasid").val('').trigger('change');
                $("#buscar_filtro").val('');

                // Actualizar contador y tabla
                ConfigClientes.contarFiltrosActivos();
                table.draw();

                // Limpiar daterangepicker
                $('#kt_fecha').data('daterangepicker').setStartDate(moment());
                $('#kt_fecha').data('daterangepicker').setEndDate(moment());
                $('#kt_fecha .form-control').val('');
            });

            // Mostrar/ocultar div de búsqueda
            $('#filtrar').on('click', function (e) {
                e.preventDefault();
                $("#filtro").toggle(500);
            });

            // ====================================
            // EVENT HANDLERS ADICIONALES DE FILTROS
            // ====================================

            // Actualizar contador cuando cambian los select2
            $('#distribuidor, #vendedor, #revendedor, #provinciasid').on('change', function () {
                ConfigClientes.contarFiltrosActivos();
            });

            // ====================================
            // FUNCIONES AUXILIARES
            // ====================================

            // ✅ Función para limpiar filtro individual
            window.limpiarFiltro = function (filtroId) {
                $(filtroId).val('').trigger('change');
                ConfigClientes.contarFiltrosActivos();
            };

            // ✅ Función para aplicar filtro rápido
            window.aplicarFiltroRapido = function (campo, valor) {
                $(campo).val(valor).trigger('change');
                $("#buscar_filtro").val('1');
                table.draw();
                ConfigClientes.contarFiltrosActivos();
            };

            // ====================================
            // INICIALIZACIÓN FINAL
            // ====================================

            // Cargar productos inicial si hay tipo seleccionado
            const tipoInicial = $('#tipolicencia').val();
            if (tipoInicial && tipoInicial !== '1') {
                ConfigClientes.actualizarProductos(tipoInicial);
            }

            // Actualizar períodos inicial si hay producto seleccionado
            const productoInicial = $('#producto').val();
            if (productoInicial) {
                ConfigClientes.actualizarPeriodos(productoInicial);
            }

            // Mensaje de inicialización
            console.log('🎯 Sistema de clientes inicializado correctamente');
            console.log('📊 Configuración dinámica cargada:', ConfigClientes.configuracion ? '✅' : '❌');
        });

        // ====================================
        // FUNCIONES GLOBALES ADICIONALES
        // ====================================

        // ✅ Función para exportar con filtros personalizados
        window.exportarConFiltros = function (tipo) {
            const filtrosActivos = ConfigClientes.contarFiltrosActivos();
            const nombreArchivo = `Clientes_${moment().format('YYYY-MM-DD')}${filtrosActivos > 0 ? '_Filtrado' : ''}`;

            switch (tipo) {
                case 'excel':
                    $('#export_excel').click();
                    break;
                case 'pdf':
                    $('#export_pdf').click();
                    break;
                case 'print':
                    $('#export_print').click();
                    break;
                default:
                    $('#export_copy').click();
            }
        };

        // ✅ Función para obtener resumen de filtros
        window.obtenerResumenFiltros = function () {
            const filtros = [];

            const campos = [
                {id: '#tipofecha', nombre: 'Tipo Fecha'},
                {id: '#tipolicencia', nombre: 'Tipo Licencia'},
                {id: '#fecha', nombre: 'Rango Fechas'},
                {id: '#periodo', nombre: 'Período'},
                {id: '#producto', nombre: 'Producto'},
                {id: '#distribuidor', nombre: 'Distribuidor'},
                {id: '#vendedor', nombre: 'Vendedor'},
                {id: '#revendedor', nombre: 'Revendedor'},
                {id: '#provinciasid', nombre: 'Provincia'},
                {id: '#origen', nombre: 'Origen'}
            ];

            campos.forEach(campo => {
                const valor = $(campo.id).val();
                if (valor && valor !== '' && valor !== '1') {
                    const texto = $(campo.id + ' option:selected').text() || valor;
                    filtros.push(`${campo.nombre}: ${texto}`);
                }
            });

            return filtros;
        };
    </script>
@endsection
