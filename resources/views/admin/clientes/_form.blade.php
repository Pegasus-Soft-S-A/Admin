<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
@php
    $rol = Auth::user()->tipo;
    $accion = isset($cliente->sis_clientesid) ? 'Modificar' : 'Crear';
    $grupos = App\Models\Grupos::get();

@endphp
@csrf

<div class="form-group row">
    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control {{ $errors->has('identificacion') ? 'is-invalid' : '' }}" placeholder="Ingrese identificacion"
                name="identificacion" autocomplete="off" value="{{ old('identificacion', $cliente->identificacion) }}" id="identificacion"
                onkeypress="return validarNumero(event)"
                @if ($rol != 1 && $accion == 'Modificar') readonly @else
                onblur="validarIdentificacion()" @endif />
        </div>
        <span class="text-danger d-none" id="mensajeBandera">La cédula o Ruc no es válido</span>
        @if ($errors->has('identificacion'))
            <span class="text-danger">{{ $errors->first('identificacion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Nombres:</label>
        <input type="text" class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}" placeholder="Ingrese Nombres" name="nombres"
            autocomplete="off" value="{{ old('nombres', $cliente->nombres) }}" id="nombres" @if ($rol != 1 && $accion == 'Modificar') readonly @endif />
        @if ($errors->has('nombres'))
            <span class="text-danger">{{ $errors->first('nombres') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Dirección:</label>
        <input type="text" class="form-control {{ $errors->has('direccion') ? 'is-invalid' : '' }}" placeholder="Ingrese Dirección"
            name="direccion" autocomplete="off" id="direccion" value="{{ old('direccion', $cliente->direccion) }}"
            @if ($rol != 1 && $accion == 'Modificar') readonly @endif />
        @if ($errors->has('direccion'))
            <span class="text-danger">{{ $errors->first('direccion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Correo:</label>
        <input class="form-control {{ $errors->has('correos') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo" name="correos" autocomplete="off"
            value="{{ old('correos', $cliente->correos) }}" id="correos" @if ($rol != 1 && $accion == 'Modificar') readonly @endif />
        @if ($errors->has('correos'))
            <span class="text-danger">{{ $errors->first('correos') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Provincia:</label>

        @php
            $provincias = [
                ['id' => '01', 'nombre' => 'Azuay'],
                ['id' => '02', 'nombre' => 'Bolivar'],
                ['id' => '03', 'nombre' => 'Cañar'],
                ['id' => '04', 'nombre' => 'Carchi'],
                ['id' => '05', 'nombre' => 'Cotopaxi'],
                ['id' => '06', 'nombre' => 'Chimborazo'],
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

        <select class="form-control select2" name="provinciasid" id="provinciasid" onchange="cambiarCiudad(this);"
            @if ($rol != 1 && $accion == 'Modificar') disabled @endif>
            <option value="">Seleccione una provincia</option>
            @foreach ($provincias as $provincia)
                <option value="{{ $provincia['id'] }}" {{ $cliente->provinciasid == $provincia['id'] ? 'selected' : '' }}>
                    {{ $provincia['nombre'] }}
                </option>
            @endforeach
        </select>

        @if ($errors->has('provinciasid'))
            <span class="text-danger">{{ $errors->first('provinciasid') }}</span>
        @endif
    </div>

    <div class="col-lg-6">
        <label>Ciudad:</label>
        <select class="form-control select2" name="ciudadesid" id="ciudadesid">
            <option value="">Seleccione una Ciudad</option>

        </select>

        @if ($errors->has('ciudadesid'))
            <span class="text-danger">{{ $errors->first('ciudadesid') }}</span>
        @endif
    </div>

</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Grupo:</label>
        <select class="form-control select2" name="grupo" id="grupo">
            <option value="">Seleccione un Tipo de Negocio</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->gruposid }}" {{ old('grupo', $cliente->grupo) == $grupo->gruposid ? 'selected' : '' }}>
                    {{ $grupo->descripcion }}</option>
            @endforeach

        </select>
        @if ($errors->has('grupo'))
            <span class="text-danger">{{ $errors->first('grupo') }}</span>
        @endif

    </div>
    <div class="col-lg-3">
        <label>Convencional:</label>
        <input type="text" class="form-control {{ $errors->has('telefono1') ? 'is-invalid' : '' }}" placeholder="Ingrese Numero Convencional"
            name="telefono1" onkeypress="return validarNumero(event)" autocomplete="off" value="{{ old('telefono1', $cliente->telefono1) }}"
            id="telefono1" @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') readonly @endif />
        @if ($errors->has('telefono1'))
            <span class="text-danger">{{ $errors->first('telefono1') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Celular:</label>
        <input type="text" class="form-control {{ $errors->has('telefono2') ? 'is-invalid' : '' }}" placeholder="Ingrese Numero Celular"
            onkeypress="return validarNumero(event)" name="telefono2" autocomplete="off" value="{{ old('telefono2', $cliente->telefono2) }}"
            id="telefono2" @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') readonly @endif />
        @if ($errors->has('telefono2'))
            <span class="text-danger">{{ $errors->first('telefono2') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-3">
        <label>Distribuidor:</label>
        <select class="form-control select2 @if ($rol != 1 && $accion == 'Modificar') disabled @endif" name="sis_distribuidoresid" id="distribuidor"
            @if ($rol != 1 && $accion == 'Modificar') disabled @endif>
            <option value="">Seleccione un distribuidor</option>
            @foreach ($distribuidores as $distribuidor)
                <option value="{{ $distribuidor->sis_distribuidoresid }}"
                    {{ $distribuidor->sis_distribuidoresid == $cliente->sis_distribuidoresid ? 'Selected' : '' }}>
                    {{ $distribuidor->razonsocial }}</option>
            @endforeach
        </select>
        @if ($errors->has('sis_distribuidoresid'))
            <span class="text-danger">{{ $errors->first('sis_distribuidoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Vendedor:</label>
        <select class="form-control select2" name="sis_vendedoresid" id="vendedor" @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') disabled @endif>
            <option value="">Seleccione un Vendedor</option>
        </select>
        @if ($errors->has('sis_vendedoresid'))
            <span class="text-danger">{{ $errors->first('sis_vendedoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Revendedor:</label>
        <select class="form-control select2" name="sis_revendedoresid" id="revendedor" @if ($rol != 1 && $rol != 2 && $accion == 'Modificar') disabled @endif>
            <option value="">Seleccione un Revendedor</option>
        </select>
        @if ($errors->has('sis_revendedoresid'))
            <span class="text-danger">{{ $errors->first('sis_revendedoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Origen:</label>
        <select class="form-control select2" id="red_origen" name="red_origen">
            @foreach ($links as $link)
                <option value="{{ $link->sis_linksid }}" {{ $link->sis_linksid == $cliente->red_origen ? 'Selected' : '' }}>
                    {{ $link->codigo }}</option>
            @endforeach
        </select>
        @if ($errors->has('red_origen'))
            <span class="text-danger">{{ $errors->first('red_origen') }}</span>
        @endif
    </div>
</div>
