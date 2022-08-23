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
            <input type="text" class="form-control {{ $errors->has('identificacion') ? 'is-invalid' : '' }}"
                placeholder="Ingrese identificacion" name="identificacion" autocomplete="off"
                value="{{ old('identificacion', $cliente->identificacion) }}" id="identificacion"
                onkeypress="return validarNumero(event)" @if ($rol !=1 && $accion=='Modificar' ) readonly @else
                onblur="validarIdentificacion()" @endif />
        </div>
        <span class="text-danger d-none" id="mensajeBandera">La cédula o Ruc no es válido</span>
        @if ($errors->has('identificacion'))
        <span class="text-danger">{{ $errors->first('identificacion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Nombres:</label>
        <input type="text" class="form-control {{ $errors->has('nombres') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Nombres" name="nombres" autocomplete="off"
            value="{{ old('nombres', $cliente->nombres) }}" id="nombres" @if ($rol !=1 && $accion=='Modificar' )
            readonly @endif />
        @if ($errors->has('nombres'))
        <span class="text-danger">{{ $errors->first('nombres') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Dirección:</label>
        <input type="text" class="form-control {{ $errors->has('direccion') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Dirección" name="direccion" autocomplete="off" id="direccion"
            value="{{ old('direccion', $cliente->direccion) }}" @if ($rol !=1 && $accion=='Modificar' ) readonly
            @endif />
        @if ($errors->has('direccion'))
        <span class="text-danger">{{ $errors->first('direccion') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Correo:</label>
        <input class="form-control {{ $errors->has('correos') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo"
            name="correos" autocomplete="off" value="{{ old('correos', $cliente->correos) }}" id="correos" @if ($rol !=1
            && $accion=='Modificar' ) readonly @endif />
        @if ($errors->has('correos'))
        <span class="text-danger">{{ $errors->first('correos') }}</span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Provincia:</label>
        <select class="form-control select2" name="provinciasid" id="provinciasid" onchange="cambiarCiudad(this);      "
            @if ($rol !=1 && $accion=='Modificar' ) disabled @endif>
            <option value="">Seleccione una provincia</option>
            <option value="01" {{ $cliente->provinciasid == '01' ? 'Selected' : '' }}>
                Azuay
            </option>
            <option value="02" {{ $cliente->provinciasid== '02' ? 'Selected' : '' }}>
                Bolivar
            </option>
            <option value="03" {{ $cliente->provinciasid == '03' ? 'Selected' : '' }}>
                Cañar
            </option>
            <option value="04" {{ $cliente->provinciasid == '04' ? 'Selected' : '' }}>
                Carchi
            </option>
            <option value="05" {{ $cliente->provinciasid == '05' ? 'Selected' : '' }}>
                Cotopaxi
            </option>
            <option value="06" {{ $cliente->provinciasid == '06' ? 'Selected' : '' }}>
                Chimborazo
            </option>
            <option value="07" {{ $cliente->provinciasid == '07' ? 'Selected' : '' }}>
                El Oro
            </option>
            <option value="08" {{ $cliente->provinciasid == '08' ? 'Selected' : '' }}>
                Esmeraldas
            </option>
            <option value="09" {{ $cliente->provinciasid == '09' ? 'Selected' : '' }}>
                Guayas
            </option>
            <option value="20" {{ $cliente->provinciasid == '20' ? 'Selected' : '' }}>
                Galapagos
            </option>
            <option value="10" {{ $cliente->provinciasid == '10' ? 'Selected' : '' }}>
                Imbabura
            </option>
            <option value="11" {{ $cliente->provinciasid == '11' ? 'Selected' : '' }}>
                Loja</option>
            <option value="12" {{ $cliente->provinciasid == '12' ? 'Selected' : '' }}>
                Los Rios
            </option>
            <option value="13" {{ $cliente->provinciasid == '13' ? 'Selected' : '' }}>
                Manabi
            </option>
            <option value="14" {{ $cliente->provinciasid == '14' ? 'Selected' : '' }}>
                Morona
                Santiago</option>
            <option value="15" {{ $cliente->provinciasid == '15' ? 'Selected' : '' }}>
                Napo</option>
            <option value="22" {{ $cliente->provinciasid == '22' ? 'Selected' : '' }}>
                Orellana
            </option>
            <option value="16" {{ $cliente->provinciasid == '16' ? 'Selected' : '' }}>
                Pastaza
            </option>
            <option value="17" {{ $cliente->provinciasid == '17' ? 'Selected' : '' }}>
                Pichincha
            </option>
            <option value="24" {{ $cliente->provinciasid == '24' ? 'Selected' : '' }}>
                Santa Elena
            </option>
            <option value="23" {{ $cliente->provinciasid== '23' ? 'Selected' : '' }}>
                Santo Domingo
                De Los Tsachilas</option>
            <option value="21" {{ $cliente->provinciasid == '21' ? 'Selected' : '' }}>
                Sucumbios
            </option>
            <option value="18" {{ $cliente->provinciasid == '18' ? 'Selected' : '' }}>
                Tungurahua
            </option>
            <option value="19" {{ $cliente->provinciasid == '19' ? 'Selected' : '' }}>
                Zamora
                Chinchipe</option>
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
            <option value="{{ $grupo->gruposid }}" {{ old('grupo', $cliente->grupo) == $grupo->gruposid ? 'selected' :
                '' }}>
                {{ $grupo->descripcion }}</option>
            @endforeach

        </select>
        @if ($errors->has('grupo'))
        <span class="text-danger">{{ $errors->first('grupo') }}</span>
        @endif

    </div>
    <div class="col-lg-3">
        <label>Convencional:</label>
        <input type="text" class="form-control {{ $errors->has('telefono1') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Numero Convencional" name="telefono1" onkeypress="return validarNumero(event)"
            autocomplete="off" value="{{ old('telefono1', $cliente->telefono1) }}" id="telefono1" @if ($rol !=1 &&
            $accion=='Modificar' ) readonly @endif />
        @if ($errors->has('telefono1'))
        <span class="text-danger">{{ $errors->first('telefono1') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Celular:</label>
        <input type="text" class="form-control {{ $errors->has('telefono2') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Numero Celular" onkeypress="return validarNumero(event)" name="telefono2"
            autocomplete="off" value="{{ old('telefono2', $cliente->telefono2) }}" id="telefono2" @if ($rol !=1 &&
            $accion=='Modificar' ) readonly @endif />
        @if ($errors->has('telefono2'))
        <span class="text-danger">{{ $errors->first('telefono2') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-3">
        <label>Distribuidor:</label>
        <select class="form-control select2 @if ($rol != 1 && $accion == 'Modificar') disabled @endif"
            name="sis_distribuidoresid" id="distribuidor" @if ($rol !=1 && $accion=='Modificar' ) disabled @endif>
            <option value="">Seleccione un distribuidor</option>
            @foreach ($distribuidores as $distribuidor)
            <option value="{{ $distribuidor->sis_distribuidoresid }}" {{ $distribuidor->sis_distribuidoresid==
                $cliente->sis_distribuidoresid
                ? 'Selected'
                : '' }}>
                {{ $distribuidor->razonsocial }}</option>
            @endforeach
        </select>
        @if ($errors->has('sis_distribuidoresid'))
        <span class="text-danger">{{ $errors->first('sis_distribuidoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Vendedor:</label>
        <select class="form-control select2" name="sis_vendedoresid" id="vendedor" @if ($rol !=1 && $rol !=2 &&
            $accion=='Modificar' ) disabled @endif>
            <option value="">Seleccione un Vendedor</option>
        </select>
        @if ($errors->has('sis_vendedoresid'))
        <span class="text-danger">{{ $errors->first('sis_vendedoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Revendedor:</label>
        <select class="form-control select2" name="sis_revendedoresid" id="revendedor" @if ($rol !=1 && $rol !=2 &&
            $accion=='Modificar' ) disabled @endif>
            <option value="">Seleccione un Revendedor</option>
        </select>
        @if ($errors->has('sis_revendedoresid'))
        <span class="text-danger">{{ $errors->first('sis_revendedoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-3">
        <label>Origen:</label>
        <select class="form-control select2" id="red_origen" name="red_origen">
            {{-- <option value="">Seleccione un Origen</option> --}}
            <option value="1" {{ old('red_origen', $cliente->red_origen) == '1' ? 'Selected' : '' }}>Perseo
            </option>
            @if ($rol == 1)
            <option value="2" {{ old('red_origen', $cliente->red_origen) == '2' ? 'Selected' : '' }}>
                Contafácil</option>
            <option value="3" {{ old('red_origen', $cliente->red_origen) == '3' ? 'Selected' : '' }}>UIO-01
            </option>
            <option value="6" {{ old('red_origen', $cliente->red_origen) == '6' ? 'Selected' : '' }}>CUE-01
            </option>
            <option value="7" {{ old('red_origen', $cliente->red_origen) == '7' ? 'Selected' : '' }}>STO-01
            </option>
            <option value="10" {{ old('red_origen', $cliente->red_origen) == '10' ? 'Selected' : '' }}>CNV-01
            </option>
            <option value="11" {{ old('red_origen', $cliente->red_origen) == '11' ? 'Selected' : '' }}>MATRIZ
            </option>
            @endif
        </select>
        @if ($errors->has('red_origen'))
        <span class="text-danger">{{ $errors->first('red_origen') }}</span>
        @endif
    </div>
</div>