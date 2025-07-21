@csrf

{{-- Configuración General --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-cog"></i> Configuración General</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Distribuidor:
            </label>
            <select class="form-control select2 {{ $errors->has('sis_distribuidoresid') ? 'is-invalid' : '' }}"
                    name="sis_distribuidoresid"
                    id="distribuidor">
                <option value="0">Todos</option>
                @foreach ($distribuidores as $distribuidor)
                    <option value="{{ $distribuidor->sis_distribuidoresid }}"
                        {{ $distribuidor->sis_distribuidoresid == $notificaciones->sis_distribuidoresid ? 'Selected' : '' }}>
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
                Usuarios:
            </label>
            <select class="form-control select2 {{ $errors->has('usuarios') ? 'is-invalid' : '' }}"
                    id="usuarios"
                    name="usuarios">
                <option value="0" {{ old('usuarios', $notificaciones->usuarios) == '0' ? 'Selected' : '' }}>
                    Todos
                </option>
                <option value="1" {{ old('usuarios', $notificaciones->usuarios) == '1' ? 'Selected' : '' }}>
                    Solo Admin
                </option>
            </select>
            @if ($errors->has('usuarios'))
                <div class="text-danger">{{ $errors->first('usuarios') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Tipo:
            </label>
            <select class="form-control select2 {{ $errors->has('tipo') ? 'is-invalid' : '' }}"
                    id="tipo"
                    name="tipo">
                <option value="0" {{ old('tipo', $notificaciones->tipo) == '0' ? 'Selected' : '' }}>
                    Todos
                </option>
                <option value="1" {{ old('tipo', $notificaciones->tipo) == '1' ? 'Selected' : '' }}>
                    Web
                </option>
                <option value="2" {{ old('tipo', $notificaciones->tipo) == '2' ? 'Selected' : '' }}>
                    PC
                </option>
            </select>
            @if ($errors->has('tipo'))
                <div class="text-danger">{{ $errors->first('tipo') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Tipo Mensaje:
            </label>
            <select class="form-control select2 {{ $errors->has('tipo_mensaje') ? 'is-invalid' : '' }}"
                    id="tipo_mensaje"
                    name="tipo_mensaje">
                <option value="1" {{ old('tipo_mensaje', $notificaciones->tipo_mensaje) == '1' ? 'Selected' : '' }}>
                    Informativo
                </option>
                <option value="2" {{ old('tipo_mensaje', $notificaciones->tipo_mensaje) == '2' ? 'Selected' : '' }}>
                    Alerta
                </option>
            </select>
            @if ($errors->has('tipo_mensaje'))
                <div class="text-danger">{{ $errors->first('tipo_mensaje') }}</div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Tipo Contenido:
            </label>
            <select class="form-control select2 {{ $errors->has('tipo_contenido') ? 'is-invalid' : '' }}"
                    id="tipo_contenido"
                    name="tipo_contenido">
                <option value="1" {{ old('tipo_contenido', $notificaciones->tipo_contenido) == '1' ? 'Selected' : '' }}>
                    HTML
                </option>
                <option value="2" {{ old('tipo_contenido', $notificaciones->tipo_contenido) == '2' ? 'Selected' : '' }}>
                    URL
                </option>
            </select>
            @if ($errors->has('tipo_contenido'))
                <div class="text-danger">{{ $errors->first('tipo_contenido') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Asunto:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('asunto') ? 'is-invalid' : '' }}"
                   placeholder="Ingrese asunto"
                   name="asunto"
                   autocomplete="off"
                   value="{{ old('asunto', $notificaciones->asunto) }}"
                   id="asunto"/>
            @if ($errors->has('asunto'))
                <div class="text-danger">{{ $errors->first('asunto') }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Fechas de Publicación --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-calendar-alt"></i> Fechas de Publicación</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Fecha Desde:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('fecha_publicacion_desde') ? 'is-invalid' : '' }}"
                   placeholder="Seleccione fecha de inicio"
                   name="fecha_publicacion_desde"
                   id="fecha_publicacion_desde"
                   autocomplete="off"
                   value="{{ old('fecha_publicacion_desde', $notificaciones->fecha_publicacion_desde) }}"/>
            @if ($errors->has('fecha_publicacion_desde'))
                <div class="text-danger">{{ $errors->first('fecha_publicacion_desde') }}</div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Fecha Hasta:
            </label>
            <input type="text"
                   class="form-control {{ $errors->has('fecha_publicacion_hasta') ? 'is-invalid' : '' }}"
                   placeholder="Seleccione fecha de fin"
                   name="fecha_publicacion_hasta"
                   id="fecha_publicacion_hasta"
                   autocomplete="off"
                   value="{{ old('fecha_publicacion_hasta', $notificaciones->fecha_publicacion_hasta) }}"/>
            @if ($errors->has('fecha_publicacion_hasta'))
                <div class="text-danger">{{ $errors->first('fecha_publicacion_hasta') }}</div>
            @endif
        </div>
    </div>
</div>

{{-- Contenido --}}
<p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-edit"></i> Contenido</p>
<div class="separator separator-dashed mb-4"></div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="font-weight-bold text-dark">
                Contenido de la Notificación:
            </label>
            <textarea class="summernote {{ $errors->has('contenido') ? 'is-invalid' : '' }}"
                      name="contenido"
                      id="contenido">{{ old('contenido', $notificaciones->contenido) }}</textarea>
            @if ($errors->has('contenido'))
                <div class="text-danger">{{ $errors->first('contenido') }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // ====================================
        // SCRIPT UNIFICADO PARA FORMULARIO NOTIFICACIONES
        // ====================================

        const FormularioNotificaciones = {
            // Inicialización principal
            init() {
                this.configurarFechas();
                this.configurarEditor();
                this.configurarEventos();
            },

            // Configurar campos de fecha
            configurarFechas() {
                $('#fecha_publicacion_desde, #fecha_publicacion_hasta').datepicker({
                    language: "es",
                    todayHighlight: true,
                    orientation: "bottom left",
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    templates: {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    }
                });

                // Establecer fecha actual si es formulario nuevo
                @if(!isset($notificaciones->sis_notificacionesid))
                    this.establecerFechaActual();
                @endif
            },

            // Configurar editor de contenido
            configurarEditor() {
                $('.summernote').summernote({
                    height: 400,
                    lang: 'es-ES',
                    placeholder: 'Escriba aquí el contenido de la notificación...',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            },

            // Configurar eventos del formulario
            configurarEventos() {
                // Evento de cambio en tipo de contenido
                $('#tipo_contenido').on('change', this.manejarTipoContenido);

            },

            // Manejar cambio de tipo de contenido
            manejarTipoContenido() {
                const tipoContenido = $('#tipo_contenido').val();

                if (tipoContenido === '2') { // URL
                    $('.summernote').summernote('destroy');
                    $('#contenido').attr('placeholder', 'Ingrese la URL completa');
                } else { // HTML
                    FormularioNotificaciones.configurarEditor();
                }
            },

            // Establecer fecha actual
            establecerFechaActual() {
                const fecha = new Date();
                const fechaFormateada = ("0" + fecha.getDate()).slice(-2) + "-" +
                    ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" +
                    fecha.getFullYear();

                $('#fecha_publicacion_desde, #fecha_publicacion_hasta').val(fechaFormateada);
            },

        };

        // ====================================
        // INICIALIZACIÓN
        // ====================================
        $(document).ready(function () {
            FormularioNotificaciones.init();
        });
    </script>
@endpush
