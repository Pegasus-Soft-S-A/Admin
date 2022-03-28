@csrf

<ul class="nav nav-tabs nav-tabs-line nav-bold">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#datoslicencia">Datos Licencia</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#aplicaciones">Aplicaciones</a>
    </li>
</ul>
<div class="tab-content mt-5" id="myTabContent">
    <div class="tab-pane fade show active" id="datoslicencia" role="tabpanel">
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Numero Contrato:</label>
                <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}"
                    placeholder="Contrato" name="numerocontrato" autocomplete="off" id="numerocontrato"
                    value="{{ old('numerocontrato', $licencia->numerocontrato) }}" disabled />
                @if ($errors->has('numerocontrato'))
                <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Fecha Caduca:</label>
                <input type="text" class="form-control {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
                    placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
                    value="{{ old('fechacaduca',$licencia->fechacaduca) }}" />
                @if ($errors->has('fechacaduca'))
                <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Estado:</label>
                <select class="form-control" name="estado" id="estado">
                    <option value="1" {{ old('estado', $licencia->estado) == '1' ? 'Selected': '' }}>Activo</option>
                    <option value="2" {{ old('estado', $licencia->estado) == '2' ? 'Selected': '' }}>Pendiente de pago
                    </option>
                    <option value="3" {{ old('estado', $licencia->estado) == '3' ? 'Selected': '' }}>Inactivo</option>
                </select>
                @if ($errors->has('estado'))
                <span class="text-danger">{{ $errors->first('estado') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Identificador Servidor:</label>
                <input type="text" class="form-control {{ $errors->has('identificador') ? 'is-invalid' : '' }}"
                    placeholder="Identificador" name="identificador" autocomplete="off" id="identificador"
                    value="{{ old('identificador', $licencia->identificador) }}" />
                @if ($errors->has('identificador'))
                <span class="text-danger">{{ $errors->first('identificador') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Local:</label>
                <input type="text" class="form-control {{ $errors->has('ipservidor') ? 'is-invalid' : '' }}"
                    placeholder="IP Servidor Local" name="ipservidor" autocomplete="off" id="ipservidor"
                    value="{{ old('ipservidor', $licencia->ipservidor) }}" />
                @if ($errors->has('ipservidor'))
                <span class="text-danger">{{ $errors->first('ipservidor') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Remoto:</label>
                <input type="text" class="form-control {{ $errors->has('ipservidorremoto') ? 'is-invalid' : '' }}"
                    placeholder="IP Servidor Remoto" name="ipservidorremoto" autocomplete="off" id="ipservidorremoto"
                    value="{{ old('ipservidorremoto', $licencia->ipservidorremoto) }}" />
                @if ($errors->has('ipservidorremoto'))
                <span class="text-danger">{{ $errors->first('ipservidorremoto') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>N° Equipos:</label>
                <input type="text" class="form-control {{ $errors->has('numeroequipos') ? 'is-invalid' : '' }}"
                    placeholder="N° Equipos" name="numeroequipos" autocomplete="off" id="numeroequipos"
                    value="{{ old('numeroequipos', $licencia->numeroequipos) }}" />
                @if ($errors->has('numeroequipos'))
                <span class="text-danger">{{ $errors->first('numeroequipos') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Móviles:</label>
                <input type="text" class="form-control {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}"
                    placeholder="N° Móviles" name="numeromoviles" autocomplete="off" id="numeromoviles"
                    value="{{ old('numeromoviles', $licencia->numeromoviles) }}" />
                @if ($errors->has('numeromoviles'))
                <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Sucursales:</label>
                <input type="text" class="form-control {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}"
                    placeholder="N° Sucursales" name="numerosucursales" autocomplete="off" id="numerosucursales"
                    value="{{ old('numerosucursales', $licencia->numerosucursales) }}" />
                @if ($errors->has('numerosucursales'))
                <span class="text-danger">{{ $errors->first('numerosucursales') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Puerto BD:</label>
                <input type="text" class="form-control {{ $errors->has('puerto') ? 'is-invalid' : '' }}"
                    placeholder="Puerto BD" name="puerto" autocomplete="off" id="puerto"
                    value="{{ old('puerto', $licencia->puerto) }}" />
                @if ($errors->has('puerto'))
                <span class="text-danger">{{ $errors->first('puerto') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Usuario BD:</label>
                <input type="text" class="form-control {{ $errors->has('usuario') ? 'is-invalid' : '' }}"
                    placeholder="Usuario BD" name="usuario" autocomplete="off" id="usuario"
                    value="{{ old('usuario', $licencia->usuario) }}" />
                @if ($errors->has('usuario'))
                <span class="text-danger">{{ $errors->first('usuario') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Clave BD:</label>
                <input type="text" class="form-control {{ $errors->has('clave') ? 'is-invalid' : '' }}"
                    placeholder="Clave BD" name="clave" autocomplete="off" id="clave"
                    value="{{ old('clave', $licencia->clave) }}" />
                @if ($errors->has('clave'))
                <span class="text-danger">{{ $errors->first('clave') }}</span>
                @endif
            </div>
        </div>

        <ul class="nav nav-tabs nav-tabs-line nav-bold">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#aplicacionesprincipales">Aplicaciones
                    Principales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#modulosadicionales">Módulos Adicionales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#correos">Correos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#respaldos">Respaldos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#bloqueos">Bloqueos</a>
            </li>
        </ul>
        <div class="tab-content mt-5" id="myTabContent">

            <div class="tab-pane fade show active" id="aplicacionesprincipales" role="tabpanel">
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label>Sistema Perseo Práctico:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="estado" id="estado" @if ($licencia->modulopractico== 1)
                                checked="checked" @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Sistema Perseo Control:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="estado" id="estado" @if ($licencia->modulocontrol== 1)
                                checked="checked" @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Sistema Perseo Contable</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="estado" id="estado" @if ($licencia->modulocontable== 1)
                                checked="checked" @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-4">
                        <label>Actualizaciones Automáticas:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="estado" id="estado" @if ($licencia->actulizaciones== 1)
                                checked="checked" @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Fecha Pagado Actualizaciones:</label>
                        <input type="text"
                            class="form-control {{ $errors->has('fechaactulizaciones') ? 'is-invalid' : '' }}"
                            placeholder="Ingrese Fecha Caducidad" name="fechaactulizaciones" id="fechaactulizaciones"
                            autocomplete="off"
                            value="{{ old('fechaactulizaciones',$licencia->fechaactulizaciones) }}" />
                        @if ($errors->has('fechaactulizaciones'))
                        <span class="text-danger">{{ $errors->first('fechaactulizaciones') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label>Periodo:</label>
                        <select class="form-control" name="periodo" id="periodo">
                            <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected': '' }}>Mensual
                            </option>
                            <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected': '' }}>Anual
                            </option>
                            <option value="3" {{ old('periodo', $licencia->periodo) == '3' ? 'Selected': '' }}>Venta
                            </option>
                        </select>
                        @if ($errors->has('periodo'))
                        <span class="text-danger">{{ $errors->first('periodo') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="modulosadicionales" role="tabpanel">
                <div class="form-group row">
                    <label class="col-3 col-form-label">Nómina</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="nomina" id="nomina" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">Activos Fijos</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="activos" id="activos" />
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
                                <input type="checkbox" name="produccion" id="produccion" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">TV-Cable e Internet</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="tvcable" id="tvcable" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3 col-form-label">Servicio de Encomiendas</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="encomiendas" id="encomiendas" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">CRM de Cartera</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="crmcartera" id="crmcartera" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3 col-form-label">API Whatsapp</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="apiwhatsapp" id="apiwhatsapp" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">Perseo Hybrid</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="hybrid" id="hybrid" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3 col-form-label">Plugin Woocomerce</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="woocomerce" id="woocomerce" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">Tienda Perseo</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="tienda" id="tienda" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3 col-form-label">Restaurantes</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="restaurante" id="restaurante" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">Servicio Técnico/Garantías</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="garantias" id="garantias" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3 col-form-label">Servicio Técnico Talleres Vehículos</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="talleres" id="talleres" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">Integraciones</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="integraciones" id="integraciones" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-3 col-form-label">Cash Manager</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="cashmanager" id="cashmanager" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-3 col-form-label">Reporte Equifax</label>
                    <div class="col-3">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input type="checkbox" name="equifax" id="equifax" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="correos" role="tabpanel">
                <div class="form-group row">
                    <label>Correo Propietario:</label>
                    <input class="form-control {{ $errors->has('correopropietario') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Correo" name="correopropietario" autocomplete="off"
                        value="{{ old('correopropietario', $cliente->correopropietario) }}" id="correo" />
                    @if ($errors->has('correopropietario'))
                    <span class="text-danger">{{ $errors->first('correopropietario') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Administrador:</label>
                    <input class="form-control {{ $errors->has('correoadministrador') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Correo" name="correoadministrador" autocomplete="off"
                        value="{{ old('correoadministrador', $cliente->correoadministrador) }}" id="correo" />
                    @if ($errors->has('correoadministrador'))
                    <span class="text-danger">{{ $errors->first('correoadministrador') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Contador:</label>
                    <input class="form-control {{ $errors->has('correocontador') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Correo" name="correocontador" autocomplete="off"
                        value="{{ old('correocontador', $cliente->correocontador) }}" id="correo" />
                    @if ($errors->has('correocontador'))
                    <span class="text-danger">{{ $errors->first('correocontador') }}</span>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade show" id="respaldos" role="tabpanel">
                <div class="form-group">
                    <label>Token Dropbox:</label>
                    <textarea class="form-control {{ $errors->has('tokenrespaldo') ? 'is-invalid' : '' }}"
                        placeholder="Token Respaldo" name="tokenrespaldo" autocomplete="off" id="tokenrespaldo"
                        value="{{ old('tokenrespaldo', $licencia->tokenrespaldo) }}"></textarea>
                    @if ($errors->has('tokenrespaldo'))
                    <span class="text-danger">{{ $errors->first('tokenrespaldo') }}</span>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade show" id="bloqueos" role="tabpanel">
                <div class="form-group">
                    <label>Motivo Bloqueo:</label>
                    <textarea class="form-control {{ $errors->has('motivobloqueo') ? 'is-invalid' : '' }}"
                        placeholder="Token Respaldo" name="motivobloqueo" autocomplete="off" id="motivobloqueo"
                        value="{{ old('motivobloqueo', $licencia->motivobloqueo) }}"></textarea>
                    @if ($errors->has('motivobloqueo'))
                    <span class="text-danger">{{ $errors->first('motivobloqueo') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label>Mensaje Entrar al Sistema:</label>
                    <textarea class="form-control {{ $errors->has('mensaje') ? 'is-invalid' : '' }}"
                        placeholder="Token Respaldo" name="mensaje" autocomplete="off" id="mensaje"
                        value="{{ old('mensaje', $licencia->mensaje) }}"></textarea>
                    @if ($errors->has('mensaje'))
                    <span class="text-danger">{{ $errors->first('mensaje') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label>Observaciones:</label>
                    <textarea class="form-control {{ $errors->has('observacion') ? 'is-invalid' : '' }}"
                        placeholder="Token Respaldo" name="observacion" autocomplete="off" id="observacion"
                        value="{{ old('observacion', $licencia->observacion) }}"></textarea>
                    @if ($errors->has('observacion'))
                    <span class="text-danger">{{ $errors->first('observacion') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="aplicaciones" role="tabpanel">Aplicaciones

    </div>
</div>

@section('script')
<script>
    $(document).ready(function () {
        //Iniciar fecha de bloqueo
        $('#fechacaduca').datepicker({
            language: "es",
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });

        $('#fechaactulizaciones').datepicker({
            language: "es",
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });

    });
</script>
@endsection