@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                placeholder="Ingrese identificacion" name="identificacion" autocomplete="off"
                value="{{ old('identificacion', $distribuidor->identificacion) }}" id="identificacion"
                onkeypress="return validarNumero(event)" onblur="validarIdentificacion()" />
        </div>
        <span class="text-danger d-none" id="mensajeBandera">La cédula o Ruc no es válido</span>
        @if ($errors->has('identificacion'))
        <span class="text-danger">{{ $errors->first('identificacion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Razon Social:</label>
        <input type="text" class="form-control {{ $errors->has('razonsocial') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Razon Social" name="razonsocial" autocomplete="off"
            value="{{ old('razonsocial', $distribuidor->razonsocial) }}" id="razonsocial" />
        @if ($errors->has('razonsocial'))
        <span class="text-danger">{{ $errors->first('razonsocial') }}</span>
        @endif
    </div>

</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Nombre Comercial:</label>
        <input type="text" class="form-control {{ $errors->has('nombrecomercial') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Nombre Comercial" name="nombrecomercial" autocomplete="off" id="nombrecomercial"
            value="{{ old('nombrecomercial', $distribuidor->nombrecomercial) }}" />
        @if ($errors->has('nombrecomercial'))
        <span class="text-danger">{{ $errors->first('nombrecomercial') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Correo(s):</label>
        <input class="form-control {{ $errors->has('correos') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo"
            name="correos" autocomplete="off" value="{{ old('correos', $distribuidor->correos) }}" id="correo" />
        @if ($errors->has('correos'))
        <span class="text-danger">{{ $errors->first('correos') }}</span>
        @endif
    </div>
</div>