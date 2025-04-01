@php
    $listadoDistribuidor = App\Models\Distribuidores::select('sis_distribuidoresid', 'razonsocial')->get();
@endphp
@csrf
<div class="form-group row">

    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control {{ $errors->has('identificacion') ? 'is-invalid' : '' }}" placeholder="Ingrese identificacion"
                name="identificacion" autocomplete="off" value="{{ old('identificacion', $usuarios->identificacion) }}" id="identificacion"
                onkeypress="return validarNumero(event)" onblur="validarIdentificacion()" />
        </div>
        <span class="text-danger d-none" id="mensajeBandera">La cédula o Ruc no es válido</span>
        @if ($errors->has('identificacion'))
            <span class="text-danger">{{ $errors->first('identificacion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Nombres:</label>
        <input type="text" class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}" placeholder="Ingrese Nombres" name="nombres"
            autocomplete="off" value="{{ old('nombres', $usuarios->nombres) }}" id="razonsocial" />
        @if ($errors->has('nombres'))
            <span class="text-danger">{{ $errors->first('nombres') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">


    <div class="col-lg-6">
        <label>Correo:</label>
        <input type="email" class="form-control {{ $errors->has('correo') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo" id="correo"
            name="correo" autocomplete="off" value="{{ old('correo', $usuarios->correo) }}" />
        @if ($errors->has('correo'))
            <span class="text-danger">{{ $errors->first('correo') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Clave:</label>
        <input type="password" class="form-control {{ $errors->has('contrasena') ? 'is-invalid' : '' }}" placeholder="Ingrese Clave" name="contrasena"
            value="{{ old('contrasena') }}" />
        @if ($usuarios->contrasena)
            <span class="form-text text-muted">La clave se modificará solo si se llena el campo</span>
        @endif
        @if ($errors->has('contrasena'))
            <span class="text-danger">{{ $errors->first('contrasena') }}</span>
        @endif
    </div>

</div>

<div class="form-group row">

    <div class="col-lg-6">
        <label>Distribuidor:</label>
        <select class="form-control select2 " id="sis_distribuidoresid" name="sis_distribuidoresid">
            @if (count($listadoDistribuidor) > 0)
                <option value="">
                    Escoja un distribuidor
                </option>
                @foreach ($listadoDistribuidor as $distribuidorL)
                    <option value="{{ $distribuidorL->sis_distribuidoresid }}"
                        {{ $distribuidorL->sis_distribuidoresid == $usuarios->sis_distribuidoresid ? 'selected' : '' }}>
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
        <select class="form-control  {{ $errors->has('tipo') ? 'is-invalid' : '' }} " name="tipo" id="tipo">
            <option value="">
                Escoja un tipo
            </option>
            <option value="1" {{ old('tipo', $usuarios->tipo) == '1' ? 'Selected' : '' }}>
                Admin
            </option>
            <option value="2" {{ old('tipo', $usuarios->tipo) == '2' ? 'Selected' : '' }}>
                Distribuidor
            </option>
            <option value="3" {{ old('tipo', $usuarios->tipo) == '3' ? 'Selected' : '' }}>
                Soporte distribuidor
            </option>
            <option value="7" {{ old('tipo', $usuarios->tipo) == '7' ? 'Selected' : '' }}>
                Soporte matriz
            </option>
            <option value="4" {{ old('tipo', $usuarios->tipo) == '4' ? 'Selected' : '' }}>
                Ventas
            </option>
            <option value="5" {{ old('tipo', $usuarios->tipo) == '5' ? 'Selected' : '' }}>
                Marketing
            </option>
            <option value="6" {{ old('tipo', $usuarios->tipo) == '6' ? 'Selected' : '' }}>
                Visor
            </option>
            <option value="8" {{ old('tipo', $usuarios->tipo) == '8' ? 'Selected' : '' }}>
                Comercial
            </option>
        </select>
        @if ($errors->has('tipo'))
            <span class="text-danger">{{ $errors->first('tipo') }}</span>
        @endif
    </div>


</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Activo:</label>
        <span class="switch switch-outline switch-icon switch-primary">
            <label>
                <input type="checkbox" name="estado" id="estado" @if ($usuarios->estado == 1) checked @endif />
                <span></span>
            </label>
        </span>
    </div>
</div>
