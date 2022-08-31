@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <label>Distribuidor:</label>
        <select class="form-control select2" name="sis_distribuidoresid" id="distribuidor">
            <option value="0">Todos</option>
            @foreach ($distribuidores as $distribuidor)
            <option value="{{ $distribuidor->sis_distribuidoresid }}" {{ $distribuidor->sis_distribuidoresid==
                $notificaciones->sis_distribuidoresid
                ? 'Selected'
                : '' }}>
                {{ $distribuidor->razonsocial }}</option>
            @endforeach
        </select>
        @if ($errors->has('sis_distribuidoresid'))
        <span class="text-danger">{{ $errors->first('sis_distribuidoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Usuarios:</label>
        <select class="form-control select2" id="usuarios" name="usuarios">
            <option value="0" {{ old('usuarios', $notificaciones->usuarios) == '0' ? 'Selected' : '' }}>Todos
            </option>
            <option value="1" {{ old('usuarios', $notificaciones->usuarios) == '1' ? 'Selected' : '' }}>Solo Admin
            </option>
        </select>
        @if ($errors->has('usuarios'))
        <span class="text-danger">{{ $errors->first('usuarios') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Tipo :</label>
        <select class="form-control select2" id="tipo" name="tipo">
            <option value="0" {{ old('tipo', $notificaciones->tipo) == '0' ? 'Selected' : '' }}>Todos
            </option>
            <option value="1" {{ old('tipo', $notificaciones->tipo) == '1' ? 'Selected' : '' }}>Web
            </option>
            <option value="2" {{ old('tipo', $notificaciones->tipo) == '2' ? 'Selected' : '' }}>PC
            </option>
        </select>
        @if ($errors->has('tipo'))
        <span class="text-danger">{{ $errors->first('tipo') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Tipo Mensaje:</label>
        <select class="form-control select2" id="tipo_mensaje" name="tipo_mensaje">
            <option value="1" {{ old('tipo_mensaje', $notificaciones->tipo_mensaje) == '1' ? 'Selected' : ''
                }}>Informativo
            </option>
            <option value="2" {{ old('tipo_mensaje', $notificaciones->tipo_mensaje) == '2' ? 'Selected' : '' }}>Alerta
            </option>
        </select>
        @if ($errors->has('tipo'))
        <span class="text-danger">{{ $errors->first('tipo') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Publicacion:</label>
        <input type="text" class="form-control {{ $errors->has('fechapublicacion') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Publicacion" name="fechapublicacion" id="fechapublicacion" autocomplete="off"
            value="{{ old('fechapublicacion', $notificaciones->fechapublicacion) }}" />
        @if ($errors->has('fechapublicacion'))
        <span class="text-danger">{{ $errors->first('fechapublicacion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Asunto:</label>
        <input type="text" class="form-control {{ $errors->has('asunto') ? 'is-invalid' : '' }}"
            placeholder="Ingrese asunto" name="asunto" autocomplete="off"
            value="{{ old('asunto', $notificaciones->asunto) }}" id="asunto" />
        @if ($errors->has('asunto'))
        <span class="text-danger">{{ $errors->first('asunto') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-12">
        <label>Contenido:</label>
        <textarea class="summernote" name="contenido" id="contenido">{{ old('contenido', $notificaciones->contenido) }}
        </textarea>
        @if ($errors->has('contenido'))
        <span class="text-danger">{{ $errors->first('contenido') }}</span>
        @endif
    </div>
</div>
@section('script')
<script>
    $(document).ready(function() {
         //Iniciar fecha 
        $('#fechapublicacion').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
        });
        $('.summernote').summernote({
            height: 200,
            lang:'es-ES'
        });

        if ("{{ isset($notificaciones->sis_notificacionesid) }}" == false) {
        var fecha = new Date();
        let fechaInicia = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear()
        $('#fechapublicacion').val(fechaInicia);
        }
    });
</script>
@endsection