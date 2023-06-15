@csrf
<input type="hidden" value="{{$cliente->sis_clientesid}}" name="sis_clientesid">
<div class="form-group row">
    <div class="col-lg-6">
        <label>Numero Contrato:</label>
        <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}"
            placeholder="Contrato" name="numerocontrato" autocomplete="off" id="numerocontrato"
            value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly />
        @if ($errors->has('numerocontrato'))
        <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>IP:</label>
        <input type="text" class="form-control {{ $errors->has('ip') ? 'is-invalid' : '' }}" placeholder="IP" name="ip"
            autocomplete="off" id="ip" value="{{ old('ip', $licencia->ip) }}" />
        @if ($errors->has('ip'))
        <span class="text-danger">{{ $errors->first('ip') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Usuario:</label>
        <input type="text" class="form-control {{ $errors->has('usuario') ? 'is-invalid' : '' }}" placeholder="Usuario"
            name="usuario" autocomplete="off" id="usuario" value="{{ old('usuario', $licencia->usuario) }}" />
        @if ($errors->has('usuario'))
        <span class="text-danger">{{ $errors->first('usuario') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Clave:</label>
        <input type="text" class="form-control {{ $errors->has('clave') ? 'is-invalid' : '' }}" placeholder="Clave"
            name="clave" autocomplete="off" id="clave" value="{{ old('clave', $licencia->clave) }}" />
        @if ($errors->has('clave'))
        <span class="text-danger">{{ $errors->first('clave') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Corte Proveedor:</label>
        <input type="text" class="form-control {{ $errors->has('fecha_corte_proveedor') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Corte Proveedor" name="fecha_corte_proveedor" id="fecha_corte_proveedor"
            autocomplete="off" value="{{ old('fecha_corte_proveedor', $licencia->fecha_corte_proveedor) }}" />
        @if ($errors->has('fecha_corte_proveedor'))
        <span class="text-danger">{{ $errors->first('fecha_corte_proveedor') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Fecha Corte Cliente:</label>
        <input type="text" class="form-control {{ $errors->has('fecha_corte_cliente') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Corte Cliente" name="fecha_corte_cliente" id="fecha_corte_cliente"
            autocomplete="off" value="{{ old('fecha_corte_cliente', $licencia->fecha_corte_cliente) }}" />
        @if ($errors->has('fecha_corte_cliente'))
        <span class="text-danger">{{ $errors->first('fecha_corte_cliente') }}</span>
        @endif
    </div>
</div>

@section('script')
<script>
    $(document).ready(function() {
            $('.deshabilitar').click(function() {
                return false;
            });

            //Iniciar fecha
            $('#fecha_corte_proveedor').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });

            $('#fecha_corte_cliente').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });

            if ("{{ isset($licencia->sis_licenciasid) }}" == false) {
                var fecha = new Date();
                let fecha_corte = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear()
                $('#fecha_corte_proveedor').val(fecha_corte);
                $('#fecha_corte_cliente').val(fecha_corte);
            }
        });

</script>
@endsection