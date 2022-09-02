<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
@php
$rol = Auth::user()->tipo;
$accion = isset($licencia->sis_licenciasid) ? "Modificar" : "Crear";
$servidoresid = isset($licencia->sis_licenciasid) ? $licencia->sis_servidoresid : 0;
$licenciasid = isset($licencia->sis_licenciasid) ? $licencia->sis_licenciasid : 0;
@endphp
@csrf
<div class="form-group row">
    <div class="col-lg-6">
        <input type="hidden" name="sis_distribuidoresid" value="{{ $licencia->sis_distribuidoresid }}">
        <input type="hidden" name="tipo" id="tipo">
        <input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">
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
        <select class="form-control @if ($rol != 1 && $accion == 'Modificar') disabled @endif" name="producto"
            id="producto">
            <option value="2" {{ old('producto', $licencia->producto) == '2' ? 'Selected' : '' }}>Facturación
            </option>
            <option value="3" {{ old('producto', $licencia->producto) == '3' ? 'Selected' : '' }}>Servicios
            </option>
            <option value="4" {{ old('producto', $licencia->producto) == '4' ? 'Selected' : '' }}>Comercial
            </option>
            <option value="5" {{ old('producto', $licencia->producto) == '5' ? 'Selected' : '' }}>Soy Contador
                Comercial
            </option>
            <option value="8" {{ old('producto', $licencia->producto) == '8' ? 'Selected' : '' }}>Soy Contador
                Servicios
            </option>
            @if ($accion == 'Modificar' && $licencia->producto == '6')
            <option value="6" {{ old('producto', $licencia->producto) == '6' ? 'Selected' : '' }}>Perseo Lite Anterior
            </option>
            @endif
            <option value="9" {{ old('producto', $licencia->producto) == '9' ? 'Selected' : '' }}>Perseo Lite
            </option>
            <option value="10" {{ old('producto', $licencia->producto) == '10' ? 'Selected' : '' }}>Emprendedor
            </option>
            <option value="11" {{ old('producto', $licencia->producto) == '11' ? 'Selected' : '' }}>Socio Perseo
            </option>
        </select>
        @if ($errors->has('producto'))
        <span class="text-danger">{{ $errors->first('producto') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    @if (isset($licencia->sis_licenciasid))
    <div class="col-lg-6">
        <label>Periodo:</label>
        <div class="input-group">
            <select class="form-control @if ($rol != 1 && $accion == 'Modificar') disabled @endif" name="periodo"
                id="periodo">
                <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected' : '' }}>Mensual
                </option>
                <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected' : '' }}>Anual
                </option>
            </select>
            @if ($licencia->producto != 6 && $licencia->producto != 9 )
            <div class="input-group-append">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" @if ($rol==3 || $rol==4) disabled @endif>
                    Renovar
                </button>
                <div class="dropdown-menu">
                    @if ($licencia->producto != 10)
                    <a class="dropdown-item" href="#" id="renovarmensual">Renovar Mensual</a>
                    @endif
                    <a class="dropdown-item" href="#" id="renovaranual">Renovar Anual</a>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="col-lg-6">
        <label>Periodo:</label>
        <div class="input-group">
            <select class="form-control @if ($rol != 1 && $accion == 'Modificar') disabled @endif" name="periodo"
                id="periodo">
                <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected' : '' }}>Mensual
                </option>
                <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected' : '' }}>Anual
                </option>
            </select>
        </div>
    </div>
    @endif
    <div class="col-lg-6">
        <label>Precio:</label>
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('precio') ? 'is-invalid' : '' }}"
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
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('fechainicia') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Caducidad" name="fechainicia" id="fechainicia" autocomplete="off"
            value="{{ old('fechainicia', $licencia->fechainicia) }}" />
        @if ($errors->has('fechainicia'))
        <span class="text-danger">{{ $errors->first('fechainicia') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Fecha Caduca:</label>
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
            placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
            value="{{ old('fechacaduca', $licencia->fechacaduca) }}" />
        @if ($errors->has('fechacaduca'))
        <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Empresas:</label>
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('empresas') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="empresas" autocomplete="off" id="empresas"
            value="{{ old('empresas', $licencia->empresas) }}" />
        @if ($errors->has('empresas'))
        <span class="text-danger">{{ $errors->first('empresas') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>N° Usuarios:</label>
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('usuarios') ? 'is-invalid' : '' }}"
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
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="numeromoviles" autocomplete="off" id="numeromoviles"
            value="{{ old('numeromoviles', $licencia->numeromoviles) }}" />
        @if ($errors->has('numeromoviles'))
        <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>N° Sucursales:</label>
        <input type="text"
            class="form-control @if ($rol != 1) disabled @endif {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}"
            placeholder="N° Equipos" name="numerosucursales" autocomplete="off" id="numerosucursales"
            value="{{ old('numerosucursales', $licencia->numerosucursales) }}" />
        @if ($errors->has('numerosucursales'))
        <span class="text-danger">{{ $errors->first('numerosucursales') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Servidor:</label>
        <select class="form-control disabled" name="sis_servidoresid" id="sis_servidoresid">
            @foreach ($servidores as $servidor)
            <option value="{{ $servidor->sis_servidoresid }}" {{ $servidor->sis_servidoresid ==
                $licencia->sis_servidoresid ? 'selected' : '' }}>
                {{ $servidor->descripcion }}
            </option>
            @endforeach
        </select>
        @if ($errors->has('sis_servidoresid'))
        <span class="text-danger">{{ $errors->first('sis_servidoresid') }}</span>
        @endif
    </div>
    <div class="col-lg-6">
        <label>Agrupados:</label>
        <select class="form-control select2" name="sis_agrupadosid" id="sis_agrupadosid" @if ($rol !=1) disabled @endif>
            <option value="0">Sin grupo</option>
            @foreach ($agrupados as $agrupado)
            <option value="{{ $agrupado->sis_agrupadosid }}" {{ $agrupado->sis_agrupadosid == $licencia->sis_agrupadosid
                ? 'selected' : '' }}>
                {{ $agrupado->codigo }}-{{ $agrupado->nombres }}
            </option>
            @endforeach
        </select>
        @if ($errors->has('sis_servidoresid'))
        <span class="text-danger">{{ $errors->first('sis_servidoresid') }}</span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label class="col-4 col-form-label">Nómina</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->nomina == 1) checked="checked" @endif type="checkbox" name="nomina"
                id="nomina" @if ($rol != 1) class="deshabilitar"@endif />
                <span></span>
            </label>
        </span>
    </div>
    <label class="col-4 col-form-label">Activos Fijos</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->activos == 1) checked="checked" @endif type="checkbox" name="activos"
                id="activos" @if ($rol != 1) class="deshabilitar"@endif />
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row">
    <label class="col-4 col-form-label">Producción</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->produccion == 1) checked="checked" @endif type="checkbox" name="produccion"
                id="produccion" @if ($rol != 1) class="deshabilitar" @endif />
                <span></span>
            </label>
        </span>
    </div>
    <label class="col-4 col-form-label">Restaurantes</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->restaurantes == 1) checked="checked" @endif type="checkbox"
                name="restaurantes" id="restaurantes" @if ($rol != 1) class="deshabilitar" @endif />
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row">
    <label class="col-4 col-form-label">Talleres</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->talleres == 1) checked="checked" @endif type="checkbox" name="talleres"
                id="talleres" @if ($rol != 1) class="deshabilitar" @endif />
                <span></span>
            </label>
        </span>
    </div>
    <label class="col-4 col-form-label">Garantías</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->garantias == 1) checked="checked" @endif type="checkbox" name="garantias"
                id="garantias" @if ($rol != 1) class="deshabilitar" @endif />
                <span></span>
            </label>
        </span>
    </div>
</div>
<div class="form-group row">
    <label class="col-4 col-form-label">Ecommerce</label>
    <div class="col-2">
        <span class="switch switch-outline switch-icon switch-primary switch-sm">
            <label>
                <input @if ($modulos->ecommerce == 1) checked="checked" @endif type="checkbox" name="ecommerce"
                id="ecommerce" @if ($rol != 1) class="deshabilitar" @endif />
                <span></span>
            </label>
        </span>
    </div>
</div>

@section('script')
<script>
    $('#formulario').submit(function(event) {
            //Enviar swirch que estan disabled
            $("#sis_agrupadosid").prop("disabled", false);

        });

        $("#renovarmensual").click(function(e) {
            confirmar('mes', "Está seguro de Renovar la Licencia?");
        });

        $("#renovaranual").click(function(e) {
            confirmar('anual', "Está seguro de Renovar la Licencia?");
        });

        $("#recargar").click(function(e) {
            confirmar('recargar', "Esta Seguro de Recargar 120 Documentos Adicionales a la Licencia?");
        });

        $("#recargar240").click(function(e) {
            confirmar('recargar240', "Esta Seguro de Recargar 240 Documentos Adicionales a la Licencia?");
        });

        $("#resetear").click(function(e) {
            Swal.fire({
                title: "Advertencia",
                text: 'Esta seguro de resetear la clave del usuario?',
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $.get('{{ route('editar_clave', [$cliente->sis_clientesid, $servidoresid, $licenciasid]) }}',
                        function(data) {
                            $.notify({
                                message: data.mensaje,
                            }, {
                                showProgressbar: true,
                                delay: 2500,
                                mouse_over: "pause",
                                placement: {
                                    from: "top",
                                    align: "right",
                                },
                                animate: {
                                    enter: "animated fadeInUp",
                                    exit: "animated fadeOutDown",
                                },
                                type: data.tipo,
                            });
                        });
                }
            });
        });

        $('#periodo').change(function() {
            cambiarComboWeb();
        });

        $('#producto').change(function() {
            cambiarComboWeb();
        });

        $(document).ready(function() {
            $('.deshabilitar').click(function() {
                return false;
            });


            if ("{{ isset($licencia->sis_licenciasid) }}" == false) {
                var fecha = new Date();
                let fechaInicia = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha
                    .getFullYear()
                $('#fechainicia').val(fechaInicia);

                fecha.setMonth(fecha.getMonth() + 1);
                let fechaFin = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-
                    2) + "-" + fecha.getFullYear()
                $('#fechacaduca').val(fechaFin);

                $('#precio').val('9.50');
                $('#usuarios').val('3');
                $('#numeromoviles').val('1');
                $('#sis_servidoresid').val('1');
                $('#ecommerce').prop('checked', false);
                $('#produccion').prop('checked', true);
                $('#nomina').prop('checked', false);
                $('#activos').prop('checked', false);
                $('#restaurantes').prop('checked', true);
                $('#talleres').prop('checked', false);
                $('#garantias').prop('checked', false);
            } else {
                if ("{{ $licencia->producto }}" == 6 || "{{ $licencia->producto }}" == 9 || "{{ $licencia->producto }}" == 10) {
                    $('#periodo').addClass("disabled");
                }
            }

            var estado = '{{ $rol }}';
            if (estado != 1) {
                estado = 'disabled';
            }
            //Iniciar input numerico
            $('#precio').TouchSpin({
                buttondown_class: 'btn btn-secondary ' + estado,
                buttonup_class: 'btn btn-secondary ' + estado,
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

        function cambiarComboWeb() {

            if ("{{ isset($licencia->licenciasid) }}" == true) {
                var fecha = new Date('{{ $licencia->fechacaduca }}');
            } else {
                var fecha = new Date();
            }

            switch ($('#producto').val()) {
                //Facturacion
                case '2':
                    switch ($('#periodo').val()) {
                        case '1':
                            $('#precio').val('9.50');
                            fecha.setMonth(fecha.getMonth() + 1);
                            break;
                        case '2':
                            $('#precio').val('72');
                            fecha.setMonth(fecha.getMonth() + 15);
                            break;
                    }
                    $('#sis_servidoresid').val('1');
                    $('#periodo').removeClass("disabled");
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
                    switch ($('#periodo').val()) {
                        case '1':
                            $('#precio').val('17');
                            fecha.setMonth(fecha.getMonth() + 1);
                            break;
                        case '2':
                            $('#precio').val('150');
                            fecha.setMonth(fecha.getMonth() + 15);
                            break;
                    }
                    $('#sis_servidoresid').val('1');
                    $('#periodo').removeClass("disabled");
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
                    switch ($('#periodo').val()) {
                        case '1':
                            $('#precio').val('24');
                            fecha.setMonth(fecha.getMonth() + 1);
                            $('#activos').prop('checked', false);
                            break;
                        case '2':
                            $('#precio').val('190');
                            fecha.setMonth(fecha.getMonth() + 15);
                            $('#activos').prop('checked', true);
                            break;
                    }
                    $('#sis_servidoresid').val('1');
                    $('#periodo').removeClass("disabled");
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
                    switch ($('#periodo').val()) {
                        case '1':
                            $('#precio').val('13');
                            fecha.setMonth(fecha.getMonth() + 1);
                            break;
                        case '2':
                            $('#precio').val('108');
                            fecha.setMonth(fecha.getMonth() + 12);
                            break;
                    }
                    $('#sis_servidoresid').val('1');
                    $('#periodo').removeClass("disabled");
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
                    //Perseo Lite Anterior
                case '6':
                    fecha.setMonth(fecha.getMonth() + 12);
                    $('#sis_servidoresid').val('3');
                    $('#precio').val('0');
                    $('#periodo').val('1');
                    $('#periodo').addClass("disabled");
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
                case '8':
                    switch ($('#periodo').val()) {
                        case '1':
                            $('#precio').val('9.80');
                            fecha.setMonth(fecha.getMonth() + 1);
                            break;
                        case '2':
                            $('#precio').val('90');
                            fecha.setMonth(fecha.getMonth() + 12);
                            break;
                    }
                    $('#sis_servidoresid').val('1');
                    $('#periodo').removeClass("disabled");
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
                 //Perseo Lite
                case '9':
                    fecha.setMonth(fecha.getMonth() + 3);
                    $('#sis_servidoresid').val('3');
                    $('#precio').val('0');
                    $('#periodo').val('1');
                    $('#periodo').addClass("disabled");
                    $('#usuarios').val('6');
                    $('#numeromoviles').val('1');
                    $('#ecommerce').prop('checked', true);
                    $('#produccion').prop('checked', true);
                    $('#nomina').prop('checked', true);
                    $('#activos').prop('checked', true);
                    $('#restaurantes').prop('checked', true);
                    $('#talleres').prop('checked', true);
                    $('#garantias').prop('checked', true);
                    break;
                //Emprendedor
                case '10':
                    fecha.setMonth(fecha.getMonth() + 12);
                    $('#sis_servidoresid').val('1');
                    $('#precio').val('24.50');
                    $('#periodo').val('2');
                    $('#periodo').addClass("disabled");
                    $('#usuarios').val('6');
                    $('#numeromoviles').val('0');
                    $('#ecommerce').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#restaurantes').prop('checked', false);
                    $('#talleres').prop('checked', false);
                    $('#garantias').prop('checked', false);
                    break;
                //Socio Perseo
                case '11':
                    switch ($('#periodo').val()) {
                        case '1':
                            $('#precio').val('7');
                            fecha.setMonth(fecha.getMonth() + 1);
                            break;
                        case '2':
                            $('#precio').val('87.50');
                            fecha.setMonth(fecha.getMonth() + 12);
                            break;
                    }
                    $('#sis_servidoresid').val('1');
                    $('#periodo').removeClass("disabled");
                    $('#usuarios').val('1');
                    $('#numeromoviles').val('1');
                    $('#ecommerce').prop('checked', true);
                    $('#produccion').prop('checked', true);
                    $('#nomina').prop('checked', true);
                    $('#activos').prop('checked', true);
                    $('#restaurantes').prop('checked', true);
                    $('#talleres').prop('checked', true);
                    $('#garantias').prop('checked', true);
                    break;
            }
            let fechaFormato = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" +
                fecha.getFullYear()
            $('#fechacaduca').val(fechaFormato);
        }

        function confirmar(tipo, mensaje) {
            Swal.fire({
                title: "Advertencia",
                text: mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            }).then(function(result) {
                if (result.value) {
                    $('#tipo').val(tipo);
                    $("#formulario").submit();
                }
            });
        }
</script>
@endsection