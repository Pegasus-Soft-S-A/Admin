@php
$listadoDistribuidor = App\Models\Distribuidores::select('sis_distribuidoresid', 'razonsocial')->get();
@endphp
@csrf
<div class="form-group row">

    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                placeholder="Ingrese identificacion" name="identificacion" autocomplete="off"
                value="{{ old('identificacion', $revendedor->identificacion) }}" id="identificacion"
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
            value="{{ old('razonsocial', $revendedor->razonsocial) }}" id="razonsocial" />
        @if ($errors->has('razonsocial'))
            <span class="text-danger">{{ $errors->first('razonsocial') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">

    <div class="col-lg-6">
        <label>Direccion:</label>
        <input type="text" class="form-control {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Direccion" name="direccion" autocomplete="off"
            value="{{ old('direccion', $revendedor->direccion) }}" id="direccion" />
        @if ($errors->has('direccion'))
            <span class="text-danger">{{ $errors->first('direccion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Correo:</label>
        <input type="email" class="form-control {{ $errors->has('correo') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Correo"  id="correo" name="correo" autocomplete="off"
            value="{{ old('correo', $revendedor->correo) }}" />
        @if ($errors->has('correo'))
            <span class="text-danger">{{ $errors->first('correo') }}</span>
        @endif
    </div>

</div>

<div class="form-group row">

    <div class="col-lg-6">
        <label>Distribuidor:</label>
        <select class="form-control select2" id="sis_distribuidoresid" name="sis_distribuidoresid">
            @if (count($listadoDistribuidor) > 0)
                <option value="">
                   Escoja un distribuidor
                </option>
                @foreach ($listadoDistribuidor as $distribuidorL)
                    <option value="{{ $distribuidorL->sis_distribuidoresid }}" {{ $distribuidorL->sis_distribuidoresid == $revendedor->sis_distribuidoresid ? 'selected' : '' }}>
                        {{ $distribuidorL->razonsocial }}
                    </option>
                @endforeach
            @else
                <option value="">
                    No existe un distribuidor
                </option>
            @endif
        </select>
        @if ($errors->has('sis_distribuidoresid'))
            <span class="text-danger">{{ $errors->first('sis_distribuidoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Tipo:</label>
        <select class="form-control " name="tipo" id="tipo">
            <option value="1" {{ old('tipo', $revendedor->tipo) == '1' ? 'Selected' : '' }}>
                Contador
            </option>
            <option value="2" {{ old('tipo', $revendedor->tipo) == '2' ? 'Selected' : '' }}>
                Vendedor
            </option>

        </select>
    </div>
</div>
