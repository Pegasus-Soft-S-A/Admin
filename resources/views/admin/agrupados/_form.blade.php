@php
$listadoClientes = App\Models\Clientes::select('sis_clientesid', 'nombres')->get();

@endphp
@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <input type="hidden" value="{{$agrupados->codigo}}" name="codigo">
        <label>Codigo:</label>
        <input type="text" class="form-control disabled {{ $errors->has('codigo') ? 'is-invalid' : '' }}"
            placeholder="Contrato" name="codigo" autocomplete="off" id="codigo"
            value="{{ old('codigo', $agrupados->codigo) }}" />
        @if ($errors->has('codigo'))
        <span class="text-danger">{{ $errors->first('codigo') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Cliente:</label>
        <select class="form-control select2" id="sis_clientesid" name="sis_clientesid">
            <option value="">
                Escoja un Cliente
            </option>
            @if (count($listadoClientes) > 0)
            @foreach ($listadoClientes as $cliente)
            <option value="{{ $cliente->sis_clientesid }}" {{ $cliente->sis_clientesid == old('sis_clientesid',
                $agrupados->sis_clientesid) ? 'selected' : '' }}>
                {{ $cliente->nombres }}
            </option>
            @endforeach
            @endif
        </select>
        @if ($errors->has('sis_clientesid'))
        <span class="text-danger">{{ $errors->first('sis_clientesid') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Inicio:</label>
        <input type="text" class="form-control fecha  {{ $errors->has('fechainicio') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Inicio" name="fechainicio" id="fechainicio" autocomplete="off"
            value="{{ old('fechainicio', $agrupados->fechainicio) }}" />
        @if ($errors->has('fechainicio'))
        <span class="text-danger">{{ $errors->first('fechainicio') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Fecha Caduca:</label>
        <input type="text" class="form-control fecha {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
            value="{{ old('fechacaduca', $agrupados->fechacaduca) }}" />
        @if ($errors->has('fechacaduca'))
        <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Precio:</label>
        <input type="text" class="form-control  {{ $errors->has('precio') ? 'is-invalid' : '' }}"
            placeholder="Ingrese precio" name="precio" id="precio" autocomplete="off"
            value="{{ old('precio', $agrupados->precio) }}" />
        @if ($errors->has('precio'))
        <span class="text-danger">{{ $errors->first('precio') }}</span>
        @endif
    </div>

    <div class="col-lg-6">
        <label>Empresas:</label>
        <input type="text" class="form-control  {{ $errors->has('empresas') ? 'is-invalid' : '' }}"
            placeholder="Numero de empresas" name="empresas" id="empresas" autocomplete="off"
            value="{{ old('empresas', $agrupados->empresas) }}" />
        @if ($errors->has('empresas'))
        <span class="text-danger">{{ $errors->first('empresas') }}</span>
        @endif
    </div>
</div>
@section('script')
<script>
    $(document).ready(function() {
            $('.fecha').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });
            $('#empresas').TouchSpin({
                buttondown_class: 'btn btn-secondary ',
                buttonup_class: 'btn btn-secondary ',
                min: 1,
                max: 100,
                step: 1,
                boostat: 5,
                maxboostedstep: 10,
                forcestepdivisibility: 'none'
            });
            $('#precio').TouchSpin({
                buttondown_class: 'btn btn-secondary ',
                buttonup_class: 'btn btn-secondary ',
                min: 0,
                max: 10000000,
                step: 1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
                forcestepdivisibility: 'none',
                prefix: '$'
            });
        });
</script>
@endsection