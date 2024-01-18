@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <label>Código:</label>
        <input type="text" class="form-control {{ $errors->has('codigo') ? 'is-invalid' : '' }}"
            placeholder="Ingrese codigo" name="codigo" autocomplete="off" value="{{ old('codigo', $links->codigo) }}"
            id="codigo" />
        @if ($errors->has('codigo'))
        <span class="text-danger">{{ $errors->first('codigo') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Distribuidor:</label>
        <select class="form-control select2" name="sis_distribuidoresid" id="distribuidor">
            @foreach ($distribuidores as $distribuidor)
            <option value="{{ $distribuidor->sis_distribuidoresid }}" {{ $distribuidor->sis_distribuidoresid==
                $links->sis_distribuidoresid
                ? 'Selected'
                : '' }}>
                {{ $distribuidor->razonsocial }}</option>
            @endforeach
        </select>
        @if ($errors->has('sis_distribuidoresid'))
        <span class="text-danger">{{ $errors->first('sis_distribuidoresid') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Registrar con Cédula o RUC:</label>
        <select class="form-control select2" id="cedula_ruc" name="cedula_ruc">
            <option value="1" {{ old('cedula_ruc', $links->cedula_ruc) == '1' ? 'Selected' : ''
                }}>Cédula/RUC
            </option>
            <option value="2" {{ old('cedula_ruc', $links->cedula_ruc) == '2' ? 'Selected' : ''
                }}>RUC
            </option>
        </select>
        @if ($errors->has('tipo'))
        <span class="text-danger">{{ $errors->first('tipo') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Registra Bitrix:</label>
        <span class="switch switch-outline switch-icon switch-primary">
            <label>
                <input type="checkbox" name="registra_bitrix" id="registra_bitrix" @if ($links->registra_bitrix== 1)
                checked="checked"
                @endif/>
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row {{ $links->registra_bitrix == 0 ? 'd-none' : '' }}" id="bitrix">
    <div class="col-lg-4">
        <label>ID Usuario de Bitrix:</label>
        <input type="number" class="form-control {{ $errors->has('usuarioid') ? 'is-invalid' : '' }}"
            placeholder="Ingrese usuarioid" name="usuarioid" autocomplete="off"
            value="{{ old('usuarioid', $links->usuarioid) }}" id="usuarioid" />
        @if ($errors->has('usuarioid'))
        <span class="text-danger">{{ $errors->first('usuarioid') }}</span>
        @endif
    </div>
    <div class="col-lg-4">
        <label>ID Origen de Bitrix:</label>
        <input type="number" class="form-control {{ $errors->has('origenid') ? 'is-invalid' : '' }}"
            placeholder="Ingrese origenid" name="origenid" autocomplete="off"
            value="{{ old('origenid', $links->origenid) }}" id="origenid" />
        @if ($errors->has('origenid'))
        <span class="text-danger">{{ $errors->first('origenid') }}</span>
        @endif
    </div>
    <div class="col-lg-4">
        <label>Descripción para Bitrix:</label>
        <input type="text" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}"
            placeholder="Ingrese descripcion" name="descripcion" autocomplete="off"
            value="{{ old('descripcion', $links->descripcion) }}" id="descripcion" />
        @if ($errors->has('descripcion'))
        <span class="text-danger">{{ $errors->first('descripcion') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-3">
        <label>Estado:</label>
        <span class="switch switch-outline switch-icon switch-primary">
            <label>
                <input type="checkbox" name="estado" id="estado" @if ($links->estado== 1) checked="checked" @endif/>
                <span></span>
            </label>
        </span>
    </div>
</div>

@section('script')
<script>
    $('#registra_bitrix').click(function(){
    if ($('#registra_bitrix').prop('checked')) {
        $('#bitrix').removeClass('d-none');
    }else{
        $('#bitrix').addClass('d-none');
    }
});
</script>
@endsection