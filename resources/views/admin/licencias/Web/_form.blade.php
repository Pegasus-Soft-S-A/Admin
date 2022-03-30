@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <input type="hidden" value="{{$cliente->sis_clientesid}}" name="sis_clientesid">
        <label>Numero Contrato:</label>
        <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}"
            placeholder="Contrato" name="numerocontrato" autocomplete="off" id="numerocontrato"
            value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly />
        @if ($errors->has('numerocontrato'))
        <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Producto:</label>
        <select class="form-control" name="producto" id="producto">
            <option value="2" {{ old('producto', $licencia->producto) == '2' ? 'Selected': '' }}>Facturación</option>
            <option value="3" {{ old('producto', $licencia->producto) == '3' ? 'Selected': '' }}>Servicios</option>
            <option value="4" {{ old('producto', $licencia->producto) == '4' ? 'Selected': '' }}>Comercial</option>
            <option value="5" {{ old('producto', $licencia->producto) == '5' ? 'Selected': '' }}>Soy Contador Comercial
            </option>
            <option value="8" {{ old('producto', $licencia->producto) == '8' ? 'Selected': '' }}>Soy Contador Servicios
            </option>
            <option value="6" {{ old('producto', $licencia->producto) == '6' ? 'Selected': '' }}>Perseo Lite</option>
        </select>
        @if ($errors->has('producto'))
        <span class="text-danger">{{ $errors->first('producto') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Periodo:</label>
        <select class="form-control" name="periodo" id="periodo">
            <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected': '' }}>Mensual</option>
            <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected': '' }}>Anual</option>
        </select>
        @if ($errors->has('periodo'))
        <span class="text-danger">{{ $errors->first('periodo') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Precio:</label>
        <input type="text" class="form-control {{ $errors->has('precio') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Precio" id="precio" name="precio" autocomplete="off"
            value="{{ old('precio', $licencia->precio) }}" />
        @if ($errors->has('precio'))
        <span class="text-danger">{{ $errors->first('precio') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Inicia:</label>
        <input type="text" class="form-control {{ $errors->has('fechainicia') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Caducidad" name="fechainicia" id="fechainicia" autocomplete="off"
            value="{{ old('fechainicia',$licencia->fechainicia) }}" />
        @if ($errors->has('fechainicia'))
        <span class="text-danger">{{ $errors->first('fechainicia') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Fecha Caduca:</label>
        <input type="text" class="form-control {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
            value="{{ old('fechacaduca',$licencia->fechacaduca) }}" />
        @if ($errors->has('fechacaduca'))
        <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Empresas:</label>
        <input type="text" class="form-control {{ $errors->has('empresas') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="empresas" autocomplete="off" id="empresas"
            value="{{ old('empresas', $licencia->empresas) }}" />
        @if ($errors->has('empresas'))
        <span class="text-danger">{{ $errors->first('empresas') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>N° Usuarios:</label>
        <input type="text" class="form-control {{ $errors->has('usuarios') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="usuarios" autocomplete="off" id="usuarios"
            value="{{ old('usuarios', $licencia->usuarios) }}" />
        @if ($errors->has('usuarios'))
        <span class="text-danger">{{ $errors->first('usuarios') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Móviles:</label>
        <input type="text" class="form-control {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="numeromoviles" autocomplete="off" id="numeromoviles"
            value="{{ old('numeromoviles', $licencia->numeromoviles) }}" />
        @if ($errors->has('numeromoviles'))
        <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>N° Sucursales:</label>
        <input type="text" class="form-control {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="numerosucursales" autocomplete="off" id="numerosucursales"
            value="{{ old('numerosucursales', $licencia->numerosucursales) }}" />
        @if ($errors->has('numerosucursales'))
        <span class="text-danger">{{ $errors->first('numerosucursales') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label class="col-3 col-form-label">Nómina</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->nomina== 1) checked="checked" @endif type="checkbox" name="nomina" id="nomina" />
                <span></span>
            </label>
        </span>
    </div>
    <label class="col-3 col-form-label">Activos Fijos</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->activos== 1) checked="checked" @endif type="checkbox" name="activos" id="activos"
                />
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row">
    <label class="col-3 col-form-label">Producción</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->produccion== 1) checked="checked" @endif type="checkbox" name="produccion"
                id="produccion" />
                <span></span>
            </label>
        </span>
    </div>
    <label class="col-3 col-form-label">Restaurantes</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->restaurantes== 1) checked="checked" @endif type="checkbox" name="restaurantes"
                id="restaurantes" />
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row">
    <label class="col-3 col-form-label">Talleres</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->talleres== 1) checked="checked" @endif type="checkbox" name="talleres"
                id="talleres" />
                <span></span>
            </label>
        </span>
    </div>
    <label class="col-3 col-form-label">Garantías</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->garantias== 1) checked="checked" @endif type="checkbox" name="garantias"
                id="garantias" />
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row">
    <label class="col-3 col-form-label">Ecommerce</label>
    <div class="col-3">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->ecommerce== 1) checked="checked" @endif type="checkbox" name="ecommerce"
                id="ecommerce" />
                <span></span>
            </label>
        </span>
    </div>
</div>

@section('script')
<script>
    $('#periodo').change(function(){
        cambiarComboWeb();
    });

    $('#producto').change(function(){
        cambiarComboWeb();
    });
    $(document).ready(function () {

        var fechainicia = new Date();

        if ("{{ isset($licencia->licenciasid) }}" == true ) {
            cambiarComboWeb();
        }else{
            let inicio = fechainicia.getDate() + "-" + ("0" + (fechainicia.getMonth()+1)).slice(-2) + "-" + fechainicia.getFullYear() 
            let fin = fechacaduca.getDate() + "-" + ("0" + (fechacaduca.getMonth()+2)).slice(-2) + "-" + fechacaduca.getFullYear() 
            $('#fechainicia').val(inicio);
        $('#fechacaduca').val(fin);
        }
    //Iniciar input numerico
        $('#precio').TouchSpin({
            buttondown_class: 'btn btn-secondary',
            buttonup_class: 'btn btn-secondary',
            min: 0,
            max: 10000000,
            step: 1,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 10,
            forcestepdivisibility: 'none'
        });

        //Iniciar fecha 
        $('#fechainicia').datepicker({
            language: "es",
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });
        $('#fechacaduca').datepicker({
            language: "es",
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });
    });

    function cambiarComboWeb(){
        if ("{{ isset($licencia->licenciasid) }}" == true ) {
            var fecha = new Date('{{ $licencia->fechacaduca }}');
        }else{
            var fecha = new Date('{{ $licencia->fechainicia }}');
        }

        switch ($('#producto').val()) {
            //Facturacion
            case '2':
                switch($('#periodo').val()){
                    case '1':
                        $('#precio').val('11.40');
                        fecha.setMonth(fecha.getMonth() + 1);
                    break;
                    case '2':
                        $('#precio').val('86.40');
                        fecha.setMonth(fecha.getMonth() + 12);
                    break;
                }

                $('#periodo').prop( "disabled", false );
                $('#usuarios').val('3');
                $('#numeromoviles').val('1');
                $('#ecommerce').prop('checked', false);
                $('#produccion').prop('checked', true);
                $('#nomina').prop('checked', false);
                $('#activos').prop('checked', false);
                $('#restaurantes').prop('checked', true);
                $('#talleres').prop('checked', false);
                $('#garantias').prop('checked', false);
            break;
            //Servicios
            case '3':
                switch($('#periodo').val()){
                    case '1':
                        $('#precio').val('20.40');
                        fecha.setMonth(fecha.getMonth() + 1);
                    break;
                    case '2':
                        $('#precio').val('180');
                        fecha.setMonth(fecha.getMonth() + 12);
                    break;
                }

                $('#periodo').prop( "disabled", false );
                $('#usuarios').val('6');
                $('#numeromoviles').val('2');
                $('#ecommerce').prop('checked', false);
                $('#produccion').prop('checked', false);
                $('#nomina').prop('checked', true);
                $('#activos').prop('checked', true);
                $('#restaurantes').prop('checked', false);
                $('#talleres').prop('checked', false);
                $('#garantias').prop('checked', false);
            break;
            //Comercial
            case '4':
                switch($('#periodo').val()){
                    case '1':
                        $('#precio').val('28.80');
                        fecha.setMonth(fecha.getMonth() + 1);
                        $('#activos').prop('checked', false);
                    break;
                    case '2':
                        $('#precio').val('228');
                        fecha.setMonth(fecha.getMonth() + 12);
                        $('#activos').prop('checked', true);
                    break;
                }

                $('#periodo').prop( "disabled", false );
                $('#usuarios').val('6');
                $('#numeromoviles').val('2');
                $('#ecommerce').prop('checked', true);
                $('#produccion').prop('checked', true);
                $('#nomina').prop('checked', true);
                $('#restaurantes').prop('checked', false);
                $('#talleres').prop('checked', true);
                $('#garantias').prop('checked', true);
            break;
            //Soy Contador Comercial
            case '5':
                switch($('#periodo').val()){
                    case '1':
                        $('#precio').val('13');
                        fecha.setMonth(fecha.getMonth() + 1);
                    break;
                    case '2':
                        $('#precio').val('108');
                        fecha.setMonth(fecha.getMonth() + 12);
                    break;
                }

                $('#periodo').prop( "disabled", false );
                $('#usuarios').val('6');
                $('#numeromoviles').val('0');
                $('#ecommerce').prop('checked', false);
                $('#produccion').prop('checked', false);
                $('#nomina').prop('checked', true);
                $('#activos').prop('checked', true);
                $('#restaurantes').prop('checked', true);
                $('#talleres').prop('checked', false);
                $('#garantias').prop('checked', false);
            break;
            //Perseo Lite
            case '6':
                fecha.setMonth(fecha.getMonth() + 12);
                $('#precio').val('0');
                $('#periodo').val('1');
                $('#periodo').prop( "disabled", true );
                $('#usuarios').val('3');
                $('#numeromoviles').val('1');
                $('#ecommerce').prop('checked', false);
                $('#produccion').prop('checked', true);
                $('#nomina').prop('checked', true);
                $('#activos').prop('checked', true);
                $('#restaurantes').prop('checked', true);
                $('#talleres').prop('checked', true);
                $('#garantias').prop('checked', true);
            break;
            //Soy Contador Servicios
            case '5':
                switch($('#periodo').val()){
                    case '1':
                        $('#precio').val('9.80');
                        fecha.setMonth(fecha.getMonth() + 1);
                    break;
                    case '2':
                        $('#precio').val('90');
                        fecha.setMonth(fecha.getMonth() + 12);
                    break;
                }

                $('#periodo').prop( "disabled", false );
                $('#usuarios').val('3');
                $('#numeromoviles').val('0');
                $('#ecommerce').prop('checked', false);
                $('#produccion').prop('checked', false);
                $('#nomina').prop('checked', false);
                $('#activos').prop('checked', false);
                $('#restaurantes').prop('checked', false);
                $('#talleres').prop('checked', false);
                $('#garantias').prop('checked', false);
            break;
        }
        let fechaFormato = fecha.getDate() + "-" + ("0" + (fecha.getMonth()+1)).slice(-2) + "-" + fecha.getFullYear() 

        $('#fechacaduca').val(fechaFormato);
    }
</script>
@endsection