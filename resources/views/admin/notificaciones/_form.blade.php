@csrf
<div class="form-group row">
    <div class="col-lg-4">
        <label>Distribuidor:</label>
        <select class="form-control select2" name="sis_distribuidoresid" id="distribuidor">
            @if (Auth::user()->tipo==1)
            <option value="0">Todos</option>
            @endif
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
    <div class="col-lg-4">
        <label>Usuarios:</label>
        <select class="form-control select2" id="usuarios" name="usuarios">
            <option value="0" {{ old('usuarios', $notificaciones->usuarios) == '1' ? 'Selected' : '' }}>Todos
            </option>
            <option value="1" {{ old('usuarios', $notificaciones->usuarios) == '2' ? 'Selected' : '' }}>Solo Admin
            </option>
        </select>
        @if ($errors->has('red_origen'))
        <span class="text-danger">{{ $errors->first('red_origen') }}</span>
        @endif
    </div>
    <div class="col-lg-4">
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