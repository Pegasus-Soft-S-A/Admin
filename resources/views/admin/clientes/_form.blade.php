<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }

    /* Prevenir scroll horizontal */
    .row {
        margin-left: 0;
        margin-right: 0;
    }

    .form-control.is-invalid {
        border-color: #F64E60;
    }

    .text-danger {
        color: #F64E60 !important;
        font-size: 0.875rem;
    }
</style>

@php
    $rol = Auth::user()->tipo;
    $accion = isset($cliente->sis_clientesid) ? 'Modificar' : 'Crear';
    $grupos = App\Models\Grupos::get();
@endphp

@csrf

{{-- Información Personal --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-id-card"></i> Información Personal</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Identificación:
            </label>
            <div id="spinner">
                <input type="text"
                       class="form-control {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                       placeholder="Ingrese identificación"
                       name="identificacion"
                       autocomplete="off"
                       value="{{ old('identificacion', $cliente->identificacion) }}"
                       id="identificacion"
                       onkeypress="return validarNumero(event)"
                       @if ($rol != 1 && $accion == 'Modificar') readonly
                       @else onblur="validarIdentificacion()" @endif />
            </div>
            <span class="text-danger d-none" id="mensajeBandera">La cédula o RUC no es válido</span>
            @if ($errors->has('identificacion'))
                <div class="text-danger">{{ $errors->first('identificacion') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Nombres:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
                   placeholder="Ingrese nombres o razón social"
                   name="nombres"
                   autocomplete="off"
                   value="{{ old('nombres', $cliente->nombres) }}"
                   id="nombres"
                   @if ($rol != 1 && $accion == 'Modificar') readonly @endif />
            @if ($errors->has('nombres'))
                <div class="text-danger">{{ $errors->first('nombres') }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Información de Contacto --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-address-book"></i> Información de Contacto</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Dirección:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
                   placeholder="Ingrese dirección"
                   name="direccion"
                   autocomplete="off"
                   id="direccion"
                   value="{{ old('direccion', $cliente->direccion) }}"
                   @if ($rol != 1 && $accion == 'Modificar') readonly @endif />
            @if ($errors->has('direccion'))
                <div class="text-danger">{{ $errors->first('direccion') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Correo Electrónico:
            </label>
            <input type="email"
                   class="form-control {{ $errors->has('correos') ? 'is-invalid' : '' }}"
                   placeholder="Ingrese correo electrónico"
                   name="correos"
                   autocomplete="off"
                   value="{{ old('correos', $cliente->correos) }}"
                   id="correos"
                   @if ($rol != 1 && $accion == 'Modificar') readonly @endif />
            @if ($errors->has('correos'))
                <div class="text-danger">{{ $errors->first('correos') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Teléfono Convencional:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('telefono1') ? 'is-invalid' : '' }}"
                   placeholder="Ingrese número convencional"
                   name="telefono1"
                   onkeypress="return validarNumero(event)"
                   autocomplete="off"
                   value="{{ old('telefono1', $cliente->telefono1) }}"
                   id="telefono1"
                   @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') readonly @endif />
            @if ($errors->has('telefono1'))
                <div class="text-danger">{{ $errors->first('telefono1') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Teléfono Celular:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('telefono2') ? 'is-invalid' : '' }}"
                   placeholder="Ingrese número celular"
                   onkeypress="return validarNumero(event)"
                   name="telefono2"
                   autocomplete="off"
                   value="{{ old('telefono2', $cliente->telefono2) }}"
                   id="telefono2"
                   @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') readonly @endif />
            @if ($errors->has('telefono2'))
                <div class="text-danger">{{ $errors->first('telefono2') }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Ubicación Geográfica --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-globe-americas"></i> Ubicación Geográfica</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Provincia:
            </label>
            @php
                $provincias = [
                    ['id' => '01', 'nombre' => 'Azuay'],
                    ['id' => '02', 'nombre' => 'Bolívar'],
                    ['id' => '03', 'nombre' => 'Cañar'],
                    ['id' => '04', 'nombre' => 'Carchi'],
                    ['id' => '05', 'nombre' => 'Cotopaxi'],
                    ['id' => '06', 'nombre' => 'Chimborazo'],
                    ['id' => '07', 'nombre' => 'El Oro'],
                    ['id' => '08', 'nombre' => 'Esmeraldas'],
                    ['id' => '09', 'nombre' => 'Guayas'],
                    ['id' => '20', 'nombre' => 'Galápagos'],
                    ['id' => '10', 'nombre' => 'Imbabura'],
                    ['id' => '11', 'nombre' => 'Loja'],
                    ['id' => '12', 'nombre' => 'Los Ríos'],
                    ['id' => '13', 'nombre' => 'Manabí'],
                    ['id' => '14', 'nombre' => 'Morona Santiago'],
                    ['id' => '15', 'nombre' => 'Napo'],
                    ['id' => '22', 'nombre' => 'Orellana'],
                    ['id' => '16', 'nombre' => 'Pastaza'],
                    ['id' => '17', 'nombre' => 'Pichincha'],
                    ['id' => '24', 'nombre' => 'Santa Elena'],
                    ['id' => '23', 'nombre' => 'Santo Domingo de los Tsáchilas'],
                    ['id' => '21', 'nombre' => 'Sucumbíos'],
                    ['id' => '18', 'nombre' => 'Tungurahua'],
                    ['id' => '19', 'nombre' => 'Zamora Chinchipe'],
                ];
            @endphp
            <select class="form-control select2 {{ $errors->has('provinciasid') ? 'is-invalid' : '' }}"
                    name="provinciasid"
                    id="provinciasid"
                    onchange="cambiarCiudad(this);"
                    @if ($rol != 1 && $accion == 'Modificar') disabled @endif>
                <option value="">Seleccione una provincia</option>
                @foreach ($provincias as $provincia)
                    <option value="{{ $provincia['id'] }}" {{ $cliente->provinciasid == $provincia['id'] ? 'selected' : '' }}>
                        {{ $provincia['nombre'] }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('provinciasid'))
                <div class="text-danger">{{ $errors->first('provinciasid') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Ciudad:
            </label>
            <select class="form-control select2 {{ $errors->has('ciudadesid') ? 'is-invalid' : '' }}"
                    name="ciudadesid"
                    id="ciudadesid">
                <option value="">Seleccione una ciudad</option>
            </select>
            @if ($errors->has('ciudadesid'))
                <div class="text-danger">{{ $errors->first('ciudadesid') }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Clasificación --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-tags"></i> Clasificación</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Tipo de Negocio:
            </label>
            <select class="form-control select2 {{ $errors->has('grupo') ? 'is-invalid' : '' }}"
                    name="grupo"
                    id="grupo">
                <option value="">Seleccione un tipo de negocio</option>
                @foreach ($grupos as $grupo)
                    <option value="{{ $grupo->gruposid }}" {{ old('grupo', $cliente->grupo) == $grupo->gruposid ? 'selected' : '' }}>
                        {{ $grupo->descripcion }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('grupo'))
                <div class="text-danger">{{ $errors->first('grupo') }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Información Comercial --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-handshake"></i> Información Comercial</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Distribuidor:
            </label>
            <select
                class="form-control select2 {{ $errors->has('sis_distribuidoresid') ? 'is-invalid' : '' }} @if ($rol != 1 && $accion == 'Modificar') disabled @endif"
                name="sis_distribuidoresid"
                id="distribuidor"
                @if ($rol != 1 && $accion == 'Modificar') disabled @endif>
                <option value="">Seleccione un distribuidor</option>
                @foreach ($distribuidores as $distribuidor)
                    <option value="{{ $distribuidor->sis_distribuidoresid }}"
                        {{ $distribuidor->sis_distribuidoresid == $cliente->sis_distribuidoresid ? 'Selected' : '' }}>
                        {{ $distribuidor->razonsocial }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('sis_distribuidoresid'))
                <div class="text-danger">{{ $errors->first('sis_distribuidoresid') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Vendedor:
            </label>
            <select class="form-control select2 {{ $errors->has('sis_vendedoresid') ? 'is-invalid' : '' }}"
                    name="sis_vendedoresid"
                    id="vendedor"
                    @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') disabled @endif>
                <option value="">Seleccione un vendedor</option>
            </select>
            @if ($errors->has('sis_vendedoresid'))
                <div class="text-danger">{{ $errors->first('sis_vendedoresid') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Revendedor:
            </label>
            <select class="form-control select2 {{ $errors->has('sis_revendedoresid') ? 'is-invalid' : '' }}"
                    name="sis_revendedoresid"
                    id="revendedor"
                    @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') disabled @endif>
                <option value="">Seleccione un revendedor</option>
            </select>
            @if ($errors->has('sis_revendedoresid'))
                <div class="text-danger">{{ $errors->first('sis_revendedoresid') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Origen:
            </label>
            <select class="form-control select2 {{ $errors->has('red_origen') ? 'is-invalid' : '' }}"
                    id="red_origen"
                    name="red_origen">
                @foreach ($links as $link)
                    <option value="{{ $link->sis_linksid }}" {{ $link->sis_linksid == $cliente->red_origen ? 'Selected' : '' }}>
                        {{ $link->codigo }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('red_origen'))
                <div class="text-danger">{{ $errors->first('red_origen') }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // ====================================
        // SCRIPT UNIFICADO PARA FORMULARIO CLIENTE
        // ====================================

        const FormularioCliente = {
            // Inicialización principal
            init() {
                this.configurarEventos();
                this.configurarValidaciones();

                // Solo ejecutar si estamos en modo edición
                @if(isset($cliente->sis_clientesid))
                    this.cargarDatosExistentes();
                @endif
            },

            // Configurar todos los eventos del formulario
            configurarEventos() {
                // Evento de envío del formulario
                $('#formulario').on('submit', this.prepararEnvioFormulario);

                // Evento de cambio de distribuidor
                $('#distribuidor').on('change', this.manejarCambioDistribuidor);

                // Evento de cambio de provincia
                $('#provinciasid').on('change', this.manejarCambioProvinciasid);
            },

            // Configurar validaciones
            configurarValidaciones() {
                // Aquí puedes agregar validaciones adicionales si necesitas
            },

            // Preparar envío del formulario
            prepararEnvioFormulario() {
                // Habilitar campos que podrían estar deshabilitados para el envío
                const camposAHabilitar = [
                    '#provinciasid', '#distribuidor', '#vendedor',
                    '#revendedor', '#red_origen'
                ];

                camposAHabilitar.forEach(campo => {
                    $(campo).prop("disabled", false);
                });

                return true; // Permitir envío
            },

            // Manejar cambio de distribuidor
            manejarCambioDistribuidor(e) {
                const distribuidorId = e.target.value;

                // Limpiar selectores dependientes
                FormularioCliente.limpiarSelectores();

                if (!distribuidorId) return;

                // Cargar vendedores y revendedores
                FormularioCliente.cargarVendedores(distribuidorId);
                FormularioCliente.cargarRevendedores(distribuidorId);
            },

            // Limpiar selectores dependientes
            limpiarSelectores() {
                $('#vendedor').empty().append('<option value="">Seleccione un Vendedor</option>');
                $('#revendedor').empty().append('<option value="">Seleccione un Revendedor</option>');
            },

            // Cargar vendedores según distribuidor
            cargarVendedores(distribuidorId) {
                $.ajax({
                    type: "GET",
                    url: `/admin/revendedoresdistribuidor/${distribuidorId}/2`,
                    success: function (data) {
                        $.each(data, function (fetch, vendedor) {
                            for (let i = 0; i < vendedor.length; i++) {
                                const selected = @if(isset($cliente->sis_vendedoresid))
                                    vendedor[i].sis_revendedoresid == '{{ $cliente->sis_vendedoresid }}' ? 'selected' : ''
                                @else
                                    ''
                                @endif;

                                $('#vendedor').append(`
                                <option value="${vendedor[i].sis_revendedoresid}" ${selected}>
                                    ${vendedor[i].razonsocial}
                                </option>
                            `);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error cargando vendedores:', error);
                    }
                });
            },

            // Cargar revendedores según distribuidor
            cargarRevendedores(distribuidorId) {
                $.ajax({
                    type: "GET",
                    url: `/admin/revendedoresdistribuidor/${distribuidorId}/1`,
                    success: function (data) {
                        $.each(data, function (fetch, vendedor) {
                            for (let i = 0; i < vendedor.length; i++) {
                                const selected = @if(isset($cliente->sis_revendedoresid))
                                    vendedor[i].sis_revendedoresid == '{{ $cliente->sis_revendedoresid }}' ? 'selected' : ''
                                @else
                                    ''
                                @endif;

                                $('#revendedor').append(`
                                <option value="${vendedor[i].sis_revendedoresid}" ${selected}>
                                    ${vendedor[i].razonsocial}
                                </option>
                            `);
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error cargando revendedores:', error);
                    }
                });
            },

            // Manejar cambio de provincia (mantiene compatibilidad con función global)
            manejarCambioProvinciasid(e) {
                cambiarCiudad(e.target);
            },

            // Cargar datos existentes (solo en modo edición)
            cargarDatosExistentes() {
                @if(isset($cliente->sis_clientesid))
                // Cargar ciudades según provincia existente
                this.cargarCiudadesExistentes();

                // Cargar vendedores/revendedores según distribuidor existente
                const distribuidorExistente = '{{ $cliente->sis_distribuidoresid }}';
                if (distribuidorExistente) {
                    this.cargarVendedores(distribuidorExistente);
                    this.cargarRevendedores(distribuidorExistente);
                }
                @endif
            },

            // Cargar ciudades para provincia existente
            cargarCiudadesExistentes() {
                const provinciaId = '{{ $cliente->provinciasid ?? '' }}';
                const ciudadSeleccionada = '{{ $cliente->ciudadesid ?? '' }}';

                if (!provinciaId) return;

                $.ajax({
                    url: '{{ route('registro.recuperarciudades') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: provinciaId
                    },
                    success: function (data) {
                        $('#ciudadesid').empty();

                        data.forEach(function (ciudad) {
                            const selected = ciudad.ciudadesid == ciudadSeleccionada ? 'selected' : '';
                            $('#ciudadesid').append(`
                            <option value="${ciudad.ciudadesid}" ${selected}>
                                ${ciudad.ciudad}
                            </option>
                        `);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error cargando ciudades:', error);
                    }
                });
            }
        };

        // ====================================
        // FUNCIONES GLOBALES (Mantener compatibilidad)
        // ====================================

        // Función global para cambiar ciudad (mantener compatibilidad)
        function cambiarCiudad(selectElement) {
            const provinciaId = selectElement.value;

            if (!provinciaId) {
                $('#ciudadesid').empty().append('<option value="">Seleccione una ciudad</option>');
                return;
            }

            $.ajax({
                url: '{{ route('registro.recuperarciudades') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: provinciaId
                },
                success: function (data) {
                    $('#ciudadesid').empty();
                    $('#ciudadesid').append('<option value="">Seleccione una ciudad</option>');

                    data.forEach(function (ciudad) {
                        $('#ciudadesid').append(`
                        <option value="${ciudad.ciudadesid}">
                            ${ciudad.ciudad}
                        </option>
                    `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error cargando ciudades:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar las ciudades',
                        icon: 'error'
                    });
                }
            });
        }

        // Función global para recuperar información por identificación
        function recuperarInformacion() {
            const identificacion = document.getElementById('identificacion').value;

            // Mostrar indicador de carga
            $("#spinner").addClass("spinner spinner-success spinner-right");

            $.ajax({
                url: '{{ route('identificaciones.index') }}',
                headers: {
                    'usuario': 'perseo',
                    'clave': 'Perseo1232*'
                },
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    identificacion: identificacion
                },
                success: function (data) {
                    $("#spinner").removeClass("spinner spinner-success spinner-right");

                    // Parsear datos si vienen como string
                    if (typeof data === 'string') {
                        try {
                            data = JSON.parse(data);
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            return;
                        }
                    }

                    if (data.identificacion) {
                        $("#nombres").val(data.razon_social);
                        $("#direccion").val(data.direccion);
                        $("#correos").val(data.correo);

                    }
                },
                error: function (xhr, status, error) {
                    $("#spinner").removeClass("spinner spinner-success spinner-right");
                }
            });
        }

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        $(document).ready(function () {
            FormularioCliente.init();
        });
    </script>
@endpush
