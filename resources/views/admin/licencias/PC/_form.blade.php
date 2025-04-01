@csrf
<style>
    #aplicativos td {
        padding: 3px;
    }

    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
@php
    $rol = Auth::user()->tipo;
    $accion = isset($licencia->sis_licenciasid) ? 'Modificar' : 'Crear';

    // Definir constantes de roles
    define('ROL_ADMIN', 1);
    define('ROL_DISTRIBUIDOR', 2);
    define('ROL_SOPORTE_DISTRIBUIDOR', 3);
    define('ROL_SOPORTE_MATRIZ', 7);
    define('ROL_VENTAS', 4);
@endphp
@if ($errors->has('correopropietario') || $errors->has('correoadministrador') || $errors->has('correocontador'))
    <div class="alert alert-custom alert-notice alert-light-danger fade show" role="alert">
        <div class="alert-icon"><i class="flaticon-warning"></i></div>
        <div class="alert-text">
            @if ($errors->has('correopropietario'))
                {{ $errors->first('correopropietario') }} <br>
            @endif
            @if ($errors->has('correoadministrador'))
                {{ $errors->first('correoadministrador') }} <br>
            @endif
            @if ($errors->has('correocontador'))
                {{ $errors->first('correocontador') }} <br>
            @endif
        </div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
@endif

<ul class="nav nav-tabs nav-tabs-line nav-bold">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#datoslicencia">Datos Licencia</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#aplicaciones">Aplicaciones</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#soporte">Soporte</a>
    </li>
</ul>
<div class="tab-content mt-5" id="myTabContent">
    <div class="tab-pane fade show active" id="datoslicencia" role="tabpanel">
        <input type="hidden" name="sis_distribuidoresid" value="{{ $licencia->sis_distribuidoresid }}">
        <input type="hidden" name="tipo" id="tipo">
        <input type="hidden" id="permisos" name="aplicaciones_permisos" value="{{ $licencia->aplicaciones }}">
        <input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Numero Contrato:</label>
                <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}" placeholder="Contrato"
                    name="numerocontrato" autocomplete="off" id="numerocontrato" value="{{ old('numerocontrato', $licencia->numerocontrato) }}"
                    readonly />
                @if ($errors->has('numerocontrato'))
                    <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Fecha Caduca:</label>
                <div class="input-group">
                    <input type="text" class="form-control {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
                        value="{{ old('fechacaduca', $licencia->fechacaduca) }}" />

                    @if (isset($licencia->sis_licenciasid) && $licencia->periodo != 3)
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Renovar
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" id="renovarmensual">Renovar Mensual</a>
                                <a class="dropdown-item" href="#" id="renovaranual">Renovar Anual</a>
                            </div>
                        </div>
                    @endif
                </div>
                @if ($errors->has('fechacaduca'))
                    <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Estado:</label>
                <select class="form-control" name="estado" id="estado">
                    <option value="1" {{ old('estado', $licencia->estado) == '1' ? 'Selected' : '' }}>Activo
                    </option>
                    <option value="2" {{ old('estado', $licencia->estado) == '2' ? 'Selected' : '' }}>Pendiente de
                        pago
                    </option>
                    <option value="3" {{ old('estado', $licencia->estado) == '3' ? 'Selected' : '' }}>Inactivo
                    </option>
                </select>
                @if ($errors->has('estado'))
                    <span class="text-danger">{{ $errors->first('estado') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Identificador Servidor:</label>
                <input type="text" class="form-control {{ $errors->has('Identificador') ? 'is-invalid' : '' }}" placeholder="Identificador"
                    name="Identificador" autocomplete="off" id="Identificador" value="{{ old('Identificador', $licencia->Identificador) }}" />
                @if ($errors->has('Identificador'))
                    <span class="text-danger">{{ $errors->first('Identificador') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Local:</label>
                <input type="text" class="form-control {{ $errors->has('ipservidor') ? 'is-invalid' : '' }}" placeholder="IP Servidor Local"
                    name="ipservidor" autocomplete="off" id="ipservidor" value="{{ old('ipservidor', $licencia->ipservidor) }}" />
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
                <input type="text" class="form-control {{ $errors->has('numeroequipos') ? 'is-invalid' : '' }}" placeholder="N° Equipos"
                    name="numeroequipos" autocomplete="off" id="numeroequipos" value="{{ old('numeroequipos', $licencia->numeroequipos) }}" />
                @if ($errors->has('numeroequipos'))
                    <span class="text-danger">{{ $errors->first('numeroequipos') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Móviles:</label>
                <input type="text" class="form-control {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}" placeholder="N° Móviles"
                    name="numeromoviles" autocomplete="off" id="numeromoviles" value="{{ old('numeromoviles', $licencia->numeromoviles) }}" />
                @if ($errors->has('numeromoviles'))
                    <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Sucursales:</label>
                <input type="text" class="form-control {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}" placeholder="N° Sucursales"
                    name="numerosucursales" autocomplete="off" id="numerosucursales"
                    value="{{ old('numerosucursales', $licencia->numerosucursales) }}" />
                @if ($errors->has('numerosucursales'))
                    <span class="text-danger">{{ $errors->first('numerosucursales') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Puerto BD:</label>
                <input type="text" class="form-control {{ $errors->has('puerto') ? 'is-invalid' : '' }}" placeholder="Puerto BD"
                    name="puerto" autocomplete="off" id="puerto" value="{{ old('puerto', $licencia->puerto) }}" />
                @if ($errors->has('puerto'))
                    <span class="text-danger">{{ $errors->first('puerto') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Puerto Movil:</label>
                <input type="text" class="form-control {{ $errors->has('puertows') ? 'is-invalid' : '' }}" placeholder="Puerto Movil"
                    autocomplete="off" name="puertows" id="puertows" value="{{ old('puertows', $licencia->puertows) }}" maxlength="4"
                    pattern="\d{1,4}" />
                @if ($errors->has('puertows'))
                    <span class="text-danger">{{ $errors->first('puertows') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Usuario BD:</label>
                <input type="text" class="form-control {{ $errors->has('usuario') ? 'is-invalid' : '' }}" placeholder="Usuario BD"
                    name="usuario" autocomplete="off" id="usuario" value="{{ old('usuario', $licencia->usuario) }}" />
                @if ($errors->has('usuario'))
                    <span class="text-danger">{{ $errors->first('usuario') }}</span>
                @endif
            </div>

        </div>

        <div class="form-group row ">
            <div class="col-lg-4">
                <label>Clave BD:</label>
                <input type="text" class="form-control {{ $errors->has('clave') ? 'is-invalid' : '' }}" placeholder="Clave BD" name="clave"
                    autocomplete="off" id="clave" value="{{ old('clave', $licencia->clave) }}" />
                @if ($errors->has('clave'))
                    <span class="text-danger">{{ $errors->first('clave') }}</span>
                @endif
            </div>
            @if ($accion == 'Modificar')
                <div class="col-lg-4">
                    <label>Empresas Activas:</label>
                    <input type="text" class="form-control" name="empresas_activas" autocomplete="off"
                        value="{{ $empresas->empresas_activas }}" readonly />
                </div>
                <div class="col-lg-4">
                    <label>Empresas Inactivas:</label>
                    <input type="text" class="form-control" name="empresas_inactivas" autocomplete="off"
                        value="{{ $empresas->empresas_inactivas }}" readonly />
                </div>
            @endif
        </div>

        <div id="div_nube" style="display: none;">
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>Tipo:</label>
                    <select class="form-control" name="tipo_nube" id="tipo_nube">
                        <option value="1" {{ old('tipo_nube', $licencia->tipo_nube) == '1' ? 'Selected' : '' }}>
                            Prime
                        </option>
                        <option value="2" {{ old('tipo_nube', $licencia->tipo_nube) == '2' ? 'Selected' : '' }}>
                            Contaplus
                        </option>
                    </select>
                    <span class="text-danger">{{ $errors->first('clave') }}</span>
                </div>
                <div class="col-lg-4">
                    <label>Nivel:</label>
                    <select class="form-control" name="nivel_nube" id="nivel_nube">
                        <option value="1" {{ old('nivel_nube', $licencia->nivel_nube) == '1' ? 'Selected' : '' }}>
                            Nivel 1
                        </option>
                        <option value="2" {{ old('nivel_nube', $licencia->nivel_nube) == '2' ? 'Selected' : '' }}>
                            Nivel 2
                        </option>
                        <option value="3" {{ old('nivel_nube', $licencia->nivel_nube) == '3' ? 'Selected' : '' }}>
                            Nivel 3
                        </option>
                    </select>
                    <span class="text-danger">{{ $errors->first('clave') }}</span>
                </div>
                <div class="col-lg-4">
                    <label>Usuarios:</label>
                    <input type="text" class="form-control" name="usuarios_nube" id="usuarios_nube" autocomplete="off"
                        value="{{ $licencia->usuarios_nube }}" />
                    <span class="text-danger">{{ $errors->first('usuarios_nube') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4">
                    <label>Usuarios Activos:</label>
                    <input type="text" class="form-control" name="usuarios_activos" autocomplete="off"
                        value="{{ $licencia->usuarios_activos }}" readonly />
                    <span class="text-danger">{{ $errors->first('usuarios_activos') }}</span>
                </div>
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
                    <div class="col-lg-3">
                        <label>Sistema Perseo Práctico:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulopractico == 1) checked="checked" @endif type="checkbox" name="modulopractico"
                                    id="practico" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-3">
                        <label>Sistema Perseo Control:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulocontrol == 1) checked="checked" @endif type="checkbox" name="modulocontrol"
                                    id="control" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-3">
                        <label>Sistema Perseo Contable</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulocontable == 1) checked="checked" @endif type="checkbox" name="modulocontable"
                                    id="contable" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-3">
                        <label>Sistema Nube</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulonube == 1) checked="checked" @endif type="checkbox" name="modulonube"
                                    id="nube" />
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
                                <input @if ($licencia->actulizaciones == 1) checked="checked" @endif type="checkbox" name="actulizaciones"
                                    id="actualiza" />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Fecha Pagado Actualizaciones:</label>
                        <div class="input-group">
                            <input type="text" class="form-control {{ $errors->has('fechaactulizaciones') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese Fecha Caducidad" name="fechaactulizaciones" id="fechaactulizaciones" autocomplete="off"
                                value="{{ old('fechaactulizaciones', $licencia->fechaactulizaciones) }}" />

                            @if (isset($licencia->sis_licenciasid) && $licencia->periodo == 3)
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="renovaractualizacion">Renovar Anual</button>
                                </div>
                            @endif
                        </div>
                        @if ($errors->has('fechaactulizaciones'))
                            <span class="text-danger">{{ $errors->first('fechaactulizaciones') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label>Periodo:</label>
                        <select class="form-control" name="periodo" id="periodo">
                            <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected' : '' }}>
                                Mensual
                            </option>
                            <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected' : '' }}>
                                Anual
                            </option>
                            <option value="3" {{ old('periodo', $licencia->periodo) == '3' ? 'Selected' : '' }}>
                                Venta
                            </option>
                        </select>
                        @if ($errors->has('periodo'))
                            <span class="text-danger">{{ $errors->first('periodo') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Clave de Activación:</label>
                        <textarea rows="8" class="form-control {{ $errors->has('key') ? 'is-invalid' : '' }}" placeholder="Clave de Activación" name="key"
                            autocomplete="off" readonly id="key">{{ $licencia->key }}</textarea>
                        @if ($errors->has('key'))
                            <span class="text-danger">{{ $errors->first('key') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="modulosadicionales" role="tabpanel">
                <div class="form-group row">
                    <label class="col-4 col-form-label">Nómina</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->nomina)) @if ($modulos[0]->nomina == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="nomina" id="nomina"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Activos Fijos</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->activos)) @if ($modulos[0]->activos == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="activos" id="activos"/>
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
                                <input
                                    @if (isset($modulos[0]->produccion)) @if ($modulos[0]->produccion == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="produccion" id="produccion"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">TV-Cable e Internet</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->operadoras)) @if ($modulos[0]->operadoras == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="tvcable" id="tvcable"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Servicio de Encomiendas</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->encomiendas)) @if ($modulos[0]->encomiendas == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="encomiendas" id="encomiendas"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">CRM de Cartera</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->crm_cartera)) @if ($modulos[0]->crm_cartera == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="crmcartera" id="crmcartera"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">API Whatsapp</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->api_whatsapp)) @if ($modulos[0]->api_whatsapp == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="apiwhatsapp" id="apiwhatsapp"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Perseo Hybrid</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->perseo_hybrid)) @if ($modulos[0]->perseo_hybrid == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="hybrid" id="hybrid"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Plugin Woocomerce</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->tienda_woocommerce)) @if ($modulos[0]->tienda_woocommerce == true)) checked="checked" @endif @endif
                                type="checkbox" name="woocomerce" id="woocomerce"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Tienda Perseo</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->tienda_perseo_publico)) @if ($modulos[0]->tienda_perseo_publico == true) )checked="checked" @endif @endif
                                type="checkbox" name="tienda" id="tienda"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Restaurantes</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->restaurante)) @if ($modulos[0]->restaurante == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="restaurante" id="restaurante"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Servicio Técnico/Garantías</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->garantias)) @if ($modulos[0]->garantias == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="garantias" id="garantias"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Servicio Técnico Talleres Vehículos</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->talleres)) @if ($modulos[0]->talleres == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="talleres" id="talleres"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Integraciones</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->tienda_perseo_distribuidor)) @if ($modulos[0]->tienda_perseo_distribuidor == true)) checked="checked" @endif @endif
                                type="checkbox" name="integraciones" id="integraciones"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Cash Manager</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->cash_manager)) @if ($modulos[0]->cash_manager == true)) checked="checked" @endif @endif type="checkbox"
                                name="cashmanager" id="cashmanager"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Cash Debito</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->cash_debito)) @if ($modulos[0]->cash_debito == true))
                                checked="checked" @endif
                                    @endif type="checkbox"
                                name="cashdebito" id="cashdebito"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Reporte Equifax</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->reporte_equifax)) @if ($modulos[0]->reporte_equifax == true))
                                checked="checked" @endif
                                    @endif
                                type="checkbox" name="equifax" id="equifax"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Caja Ahorros</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->caja_ahorros)) @if ($modulos[0]->caja_ahorros == true))
                                checked="checked" @endif
                                    @endif
                                type="checkbox" name="ahorros" id="ahorros"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-4 col-form-label">Académico</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->academico)) @if ($modulos[0]->academico == true))
                                checked="checked" @endif
                                    @endif
                                type="checkbox" name="academico" id="academico"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>

                    <label class="col-4 col-form-label">Perseo Contador</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->perseo_contador)) @if ($modulos[0]->perseo_contador == true))
                                checked="checked" @endif
                                    @endif
                                type="checkbox" name="perseo_contador" id="perseo_contador"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-4 col-form-label">API Urbano</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input
                                    @if (isset($modulos[0]->api_urbano)) @if ($modulos[0]->api_urbano == true))
                                checked="checked" @endif
                                    @endif
                                type="checkbox" name="api_urbano" id="api_urbano"
                                />
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="correos" role="tabpanel">
                <div class="form-group row">
                    <label>Correo Propietario:</label>
                    <input class="form-control {{ $errors->has('correopropietario') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo"
                        name="correopropietario" autocomplete="off" value="{{ old('correopropietario', $licencia->correopropietario) }}"
                        id="correopropietario" />
                    @if ($errors->has('correopropietario'))
                        <span class="text-danger">{{ $errors->first('correopropietario') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Administrador:</label>
                    <input class="form-control {{ $errors->has('correoadministrador') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo"
                        name="correoadministrador" autocomplete="off" value="{{ old('correoadministrador', $licencia->correoadministrador) }}"
                        id="correoadministrador" />
                    @if ($errors->has('correoadministrador'))
                        <span class="text-danger">{{ $errors->first('correoadministrador') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Contador:</label>
                    <input class="form-control {{ $errors->has('correocontador') ? 'is-invalid' : '' }}" placeholder="Ingrese Correo"
                        name="correocontador" autocomplete="off" value="{{ old('correocontador', $licencia->correocontador) }}"
                        id="correocontador" />
                    @if ($errors->has('correocontador'))
                        <span class="text-danger">{{ $errors->first('correocontador') }}</span>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade show" id="respaldos" role="tabpanel">
                <div class="form-group row">
                    <label>Token Dropbox:</label>
                    <textarea rows=10 class="form-control {{ $errors->has('tokenrespaldo') ? 'is-invalid' : '' }}" placeholder="Token Respaldo" name="tokenrespaldo"
                        autocomplete="off" id="tokenrespaldo">{{ $licencia->tokenrespaldo }}</textarea>
                    <button type="button" class="btn btn-primary mt-2" onclick="copiarAlPortapapeles()">Copiar</button>
                    @if ($errors->has('tokenrespaldo'))
                        <span class="text-danger">{{ $errors->first('tokenrespaldo') }}</span>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade show" id="bloqueos" role="tabpanel">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Motivo Bloqueo:</label>
                        <input class="form-control {{ $errors->has('motivobloqueo') ? 'is-invalid' : '' }}" placeholder="Motivo bloqueo"
                            name="motivobloqueo" autocomplete="off" id="motivobloqueo"
                            value="{{ old('motivobloqueo', $licencia->motivobloqueo) }}" />
                        @if ($errors->has('motivobloqueo'))
                            <span class="text-danger">{{ $errors->first('motivobloqueo') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Mensaje Entrar al Sistema:</label>
                        <input class="form-control {{ $errors->has('mensaje') ? 'is-invalid' : '' }}" placeholder="Mensaje" name="mensaje"
                            autocomplete="off" id="mensaje" value="{{ old('mensaje', $licencia->mensaje) }}" />
                        @if ($errors->has('mensaje'))
                            <span class="text-danger">{{ $errors->first('mensaje') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Observaciones:</label>
                        <input class="form-control {{ $errors->has('observacion') ? 'is-invalid' : '' }}" placeholder="Observaciones"
                            name="observacion" autocomplete="off" id="observacion" value="{{ old('observacion', $licencia->observacion) }}" />
                        @if ($errors->has('observacion'))
                            <span class="text-danger">{{ $errors->first('observacion') }}</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="tab-pane fade" role="tabpanel" id="aplicaciones">
        <table class="table table-sm table-bordered table-head-custom table-hover" id="aplicativos">
            <thead>
                <tr>
                    <th>Categorias ID</th>
                    <th>ID</th>
                    <th>Opciones</th>
                    <th>Activo</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="tab-pane fade" role="tabpanel" id="soporte">
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Plan de Soporte:</label>
                <span class="switch switch-outline switch-icon switch-primary switch-sm">
                    <label>
                        <input @if ($licencia->plan_soporte == 1) checked="checked" @endif type="checkbox" name="plan_soporte"
                            @if ($rol != 1 && $accion == 'Modificar') disabled id="plan_soporte" @endif />
                        <span></span>
                    </label>
                </span>
            </div>
            <div class="col-lg-4">
                <label>Fecha Caducidad Plan Soporte:</label>
                <input type="text" class="form-control {{ $errors->has('fechacaduca_soporte') ? 'is-invalid' : '' }}"
                    placeholder="Ingrese Fecha Caducidad" name="fechacaduca_soporte" id="fechacaduca_soporte" autocomplete="off"
                    value="{{ old('fechacaduca_soporte', $licencia->fechacaduca_soporte) }}" />
                @if ($errors->has('fechacaduca_soporte'))
                    <span class="text-danger">{{ $errors->first('fechacaduca_soporte') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Numero Tickets Totales:</label>
                <input type="text" class="form-control {{ $errors->has('numero_tickets_totales') ? 'is-invalid' : '' }}"
                    placeholder="numero_tickets_totales" name="numero_tickets_totales" autocomplete="off" id="numero_tickets_totales"
                    value="{{ old('numero_tickets_totales', $licencia->numero_tickets_totales) }}" />
                @if ($errors->has('numero_tickets_totales'))
                    <span class="text-danger">{{ $errors->first('numero_tickets_totales') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Numero Tickets Mensuales:</label>
                <input type="text" class="form-control {{ $errors->has('numero_tickets_mensuales') ? 'is-invalid' : '' }}"
                    placeholder="numero_tickets_mensuales" name="numero_tickets_mensuales" autocomplete="off" id="numero_tickets_mensuales"
                    value="{{ old('numero_tickets_mensuales', $licencia->numero_tickets_mensuales) }}" />
                @if ($errors->has('numero_tickets_mensuales'))
                    <span class="text-danger">{{ $errors->first('numero_tickets_mensuales') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Tickets Utilizados:</label>
                <input type="text" class="form-control" readonly value="{{ $licencia->tickets_utilizados }}" />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Version ejecutable:</label>
                <input type="text" class="form-control" value="{{ $licencia->version_ejecutable }}" readonly />
            </div>
            <div class="col-lg-4">
                <label>Fecha Actualizacion Ejecutable:</label>
                <input type="text" class="form-control" value="{{ $licencia->fecha_actualizacion_ejecutable }}" readonly />
            </div>
            <div class="col-lg-4">
                <label>Fecha Respaldo:</label>
                <input type="text" class="form-control" value="{{ $licencia->fecha_respaldo }}" readonly />
            </div>
        </div>
    </div>
</div>

@section('script')
    <script>
        function copiarAlPortapapeles() {
            var textarea = document.getElementById("tokenrespaldo");
            textarea.select();
            textarea.setSelectionRange(0, 99999); // Para dispositivos móviles
            document.execCommand("copy");
        }

        // Constantes Globales

        const practico = ['105', '110', '111', '112', '113', '114', '115', '117', '118', '120', '125', '126', '127', '130', '131', '135', '136', '141',
            '142', '150', '305', '310', '315', '320', '325', '330', '335', '430', '431', '432', '433', '434', '435', '440', '445', '450', '455',
            '456', '460', '461', '462', '463', '464', '465', '466', '469', '470', '471', '475', '480', '491', '492', '495', '630', '905', '910',
            '915', '916', '917', '918', '919', '920', '925', '930', '931', '940', '960', '1105', '1110', '1115', '1120'
        ];

        const control = ['200', '142', '201', '205', '210', '215', '225', '230', '505', '510', '515', '516', '517', '462', '463', '485', '490', '116',
            '140', '605', '630', '635'
        ];

        const contable = ['605', '142', '606', '610', '615', '616', '620', '625', '626', '627', '628', '630', '635', '636', '640']

        const IDS_MODULO = {
            practico: practico,
            control: [...practico, ...control],
            contable: [...practico, ...control, ...contable],
            nube: [...practico, ...control, ...contable],
            nomina: ['705', '710', '715', '720', '725', '730', '735', '740', '741', '745'],
            activos: ['805', '806', '810', '815', '816', '820'],
            produccion: ['1005', '1010', '1015'],
            tvcable: ['1200', '1205', '1210', '1215', '1220'],
            encomiendas: ['1601', '1610', '1615', '1620', '1625'],
            crmcartera: ['220'],
            ahorros: ['1705', '1710', '1715', '1716', '1720', '1725'],
            apiwhatsapp: ['950'],
            hybrid: ['950'],
            woocomerce: ['950'],
            tienda: ['950'],
            restaurante: ['1500', '1505', '1510', '1515', '1520'],
            garantias: ['1300', '1305', '1310'],
            talleres: ['1400', '1405', '1410'],
            academico: ['1805', '1810', '1815', '1820', '1825', '1830']
        };

        const EQUIPOS_CONFIG = {
            practico: {
                equipos: 2,
                moviles: 0,
                sucursales: 1
            },
            control: {
                equipos: 3,
                moviles: 0,
                sucursales: 1
            },
            contable: {
                equipos: 4,
                moviles: 0,
                sucursales: 1
            },
            nube: {
                equipos: 4,
                moviles: 0,
                sucursales: 1
            }
        };

        //1: Mensual, 2: Anual, 3: Semestral
        const MESES_POR_PERIODO = {
            '1': 1,
            '2': 12,
            '3': 60
        };

        // Definir constantes para los roles
        const ROLES = {
            ADMIN: {{ ROL_ADMIN }},
            DISTRIBUIDOR: {{ ROL_DISTRIBUIDOR }},
            ROL_SOPORTE_DISTRIBUIDOR: {{ ROL_SOPORTE_DISTRIBUIDOR }},
            ROL_SOPORTE_MATRIZ: {{ ROL_SOPORTE_MATRIZ }},
            ROL_VENTAS: {{ ROL_VENTAS }},
        };

        // Definir permisos simplificados por rol
        const PERMISOS_EDICION = {
            // Admin puede editar todo
            [ROLES.ADMIN]: {
                todoElFormulario: true,
                renovarLicencia: true
            },

            // Distribuidor - Permisos diferentes para crear y modificar
            [ROLES.DISTRIBUIDOR]: {
                todoElFormulario: false,
                renovarLicencia: true,
                camposEditables: {
                    crear: [
                        'Identificador', 'ipservidor', 'ipservidorremoto', 'puertows',
                        'numeroequipos', 'numeromoviles', 'numerosucursales',
                        'correopropietario', 'correoadministrador', 'correocontador',
                        'tipo_nube', 'nivel_nube', 'usuarios_nube',
                        'periodo',
                        'practico', 'control', 'contable', 'nube',
                    ],
                    modificar: ['Identificador', 'ipservidor', 'ipservidorremoto', 'puertows']
                }
            },

            [ROLES.ROL_SOPORTE_DISTRIBUIDOR]: {
                todoElFormulario: false,
                renovarLicencia: true,
                camposEditables: {
                    crear: [
                        'Identificador', 'ipservidor', 'ipservidorremoto', 'puertows',
                        'numeroequipos', 'numeromoviles', 'numerosucursales',
                        'correopropietario', 'correoadministrador', 'correocontador',
                        'tipo_nube', 'nivel_nube', 'usuarios_nube',
                        'periodo',
                        'practico', 'control', 'contable', 'nube',
                    ],
                    modificar: ['Identificador', 'ipservidor', 'ipservidorremoto', 'puertows']
                }
            },

            [ROLES.ROL_SOPORTE_MATRIZ]: {
                todoElFormulario: false,
                renovarLicencia: true,
                camposEditables: {
                    crear: [
                        'Identificador', 'ipservidor', 'ipservidorremoto', 'puertows',
                        'numeroequipos', 'numeromoviles', 'numerosucursales',
                        'correopropietario', 'correoadministrador', 'correocontador',
                        'tipo_nube', 'nivel_nube', 'usuarios_nube',
                        'periodo',
                        'practico', 'control', 'contable', 'nube',
                    ],
                    modificar: ['Identificador', 'ipservidor', 'ipservidorremoto', 'puertows']
                }
            },

            [ROLES.ROL_VENTAS]: {
                todoElFormulario: false,
                renovarLicencia: false,

            },
        };

        // Función para aplicar restricciones a los campos del formulario
        function aplicarRestriccionesPorRol() {
            const rolUsuario = {{ $rol }};
            const accion = '{{ $accion }}'; // 'Crear' o 'Modificar'
            const permisosRol = PERMISOS_EDICION[rolUsuario] || {
                todoElFormulario: false,
                camposEditables: {
                    crear: [],
                    modificar: []
                }
            };

            // Si el usuario tiene permiso para editar todo, no hacemos nada
            if (permisosRol.todoElFormulario) {
                return;
            }

            // Obtenemos la lista de campos editables para la acción actual
            const camposEditablesParaAccion = permisosRol.camposEditables &&
                permisosRol.camposEditables[accion.toLowerCase()] || [];

            // Deshabilitamos TODOS los campos primero
            const todosLosCampos = document.querySelectorAll('input, select, textarea');
            todosLosCampos.forEach(campo => {
                campo.classList.add('disabled');
                campo.setAttribute('disabled', 'disabled');
            });

            camposEditablesParaAccion.forEach(campoId => {
                const campo = document.getElementById(campoId);
                if (campo) {
                    campo.classList.remove('disabled');
                    campo.removeAttribute('disabled');
                }
            });

            // Manejo especial para checkboxes de módulos
            if (accion.toLowerCase() === 'crear') {
                // Verificamos si los checkboxes de módulos principales están en la lista de editables
                const modulosPrincipales = ['practico', 'control', 'contable', 'nube'];
                const puedeEditarModulosPrincipales = modulosPrincipales.some(modulo =>
                    camposEditablesParaAccion.includes(modulo)
                );

                if (puedeEditarModulosPrincipales) {
                    // Habilitamos todos los checkboxes de módulos principales
                    modulosPrincipales.forEach(modulo => {
                        const checkbox = document.getElementById(modulo);
                        if (checkbox) {
                            checkbox.classList.remove('disabled');
                            checkbox.removeAttribute('disabled');
                        }
                    });
                }
            }

            // Botones de renovación (solo para quienes tienen el permiso)
            if (!permisosRol.renovarLicencia) {
                const botonesRenovacion = document.querySelectorAll('[id^="renovar"]');
                botonesRenovacion.forEach(boton => {
                    boton.classList.add('disabled');
                    boton.setAttribute('disabled', 'disabled');

                    // Si el botón está dentro de un dropdown, deshabilitar también el botón del dropdown
                    const dropdownToggle = boton.closest('.dropdown-menu')?.previousElementSibling;
                    if (dropdownToggle && dropdownToggle.classList.contains('dropdown-toggle')) {
                        dropdownToggle.classList.add('disabled');
                    }
                });
            }
        }

        // Función para recopilar y formatear correctamente los módulos seleccionados
        function prepararModulosParaEnvio() {
            // Obtenemos los IDs de los checkboxes marcados en la tabla de aplicativos
            const modulosSeleccionados = [];

            // Recorremos todos los checkboxes del datatable de aplicativos
            $('#aplicativos input[type="checkbox"]:checked').each(function() {
                // Obtenemos el ID del checkbox que está marcado
                const id = $(this).attr('id');
                if (id && id !== '') {
                    modulosSeleccionados.push(id);
                }
            });

            // También revisamos los módulos principales y adicionales que pueden no estar en la tabla
            const todosLosModulos = [
                'practico', 'control', 'contable', 'nube', 'nomina', 'activos',
                'produccion', 'tvcable', 'encomiendas', 'crmcartera', 'apiwhatsapp',
                'hybrid', 'woocomerce', 'tienda', 'restaurante', 'garantias',
                'talleres', 'integraciones', 'cashmanager', 'cashdebito', 'equifax',
                'ahorros', 'academico', 'perseo_contador', 'api_urbano'
            ];

            // Agregamos los IDs de cada módulo seleccionado
            todosLosModulos.forEach(modulo => {
                if ($('#' + modulo).prop('checked')) {
                    if (IDS_MODULO[modulo]) {
                        modulosSeleccionados.push(...IDS_MODULO[modulo]);
                    }
                }
            });

            // Eliminamos duplicados para evitar IDs repetidos
            const modulosUnicos = [...new Set(modulosSeleccionados)];

            // Ordenamos los IDs para mantener consistencia
            modulosUnicos.sort((a, b) => parseInt(a) - parseInt(b));

            const valorFormateado = modulosUnicos.join(';') + ';';

            $('#permisos').val(valorFormateado);

        }

        // Agregar al inicio de tus scripts
        $(document).ready(function() {
            // Inicializar el formulario
            inicializarFormulario();
            inicializarEventos();
            aplicarRestriccionesPorRol();

            // Actualiza tu manejador de envío de formulario
            $('#formulario').on('submit', function(e) {
                // Primero preparamos los módulos 
                prepararModulosParaEnvio();

                // Luego habilitamos todos los campos para que se incluyan en el envío
                $(this).find('input, select, textarea').removeAttr('disabled');

                // Continuar con el envío del formulario
                return true;
            });
        });

        // Función para inicializar formularios y componentes
        function inicializarFormulario() {
            if ($("#nube").prop("checked")) {
                mostrarDivNube(true);
            }
            inicializarDatepickers();
            inicializarDataTable();
        }

        // Función para inicializar eventos
        function inicializarEventos() {
            $("#renovarmensual").click(() => confirmarAccion('mes', "¿Está seguro de Renovar la Licencia?"));
            $("#renovaranual").click(() => confirmarAccion('anual', "¿Está seguro de Renovar la Licencia?"));
            $("#renovaractualizacion").click(() => confirmarAccion('actualizacion', "¿Está seguro de Renovar la Licencia?"));
            $("#periodo, #producto").change(cambiarComboPC);
            $(".deshabilitar").click(e => e.preventDefault());
            inicializarEventosCheckboxes();
        }

        // Inicializar Datepickers
        function inicializarDatepickers() {
            $("#fechacaduca, #fechaactulizaciones, #fechacaduca_soporte").datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });
        }

        // Inicializar DataTable
        function inicializarDataTable() {
            $('#aplicativos').DataTable({
                responsive: true,
                serverSide: true,
                searching: false,
                paging: false,
                ajax: "{{ route('subcategorias') }}",
                drawCallback: function(settings) {
                    const api = this.api();
                    const rows = api.rows({
                        page: 'current'
                    }).nodes();
                    let last = null;
                    api.column(0, {
                        page: 'current'
                    }).data().each(function(group, i) {
                        if (last !== group) {
                            $(rows).eq(i).before('<tr class="group"><td colspan="3">' + group + '</td></tr>');
                            last = group;
                        }
                    });
                },
                columns: [{
                        data: 'categoriasdescripcion',
                        name: 'categoriasdescripcion',
                        visible: false
                    },
                    {
                        data: 'sis_subcategoriasid',
                        orderable: false,
                        searchable: false,
                        name: 'sis_subcategoriasid'
                    },
                    {
                        data: 'descripcionsubcategoria',
                        orderable: false,
                        searchable: false,
                        name: 'descripcionsubcategoria'
                    },
                    {
                        data: 'activo',
                        name: 'activo',
                        orderable: false,
                        searchable: false
                    }
                ],
                initComplete: function(settings, json) {
                    const permisos = $("#permisos").val();
                    const array = permisos.split(';');
                    array.forEach(id => $('#' + id).prop('checked', true));
                }
            });
        }

        // Función para confirmar acción con SweetAlert
        function confirmarAccion(tipo, mensaje) {
            Swal.fire({
                title: "Advertencia",
                text: mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#tipo").val(tipo);
                    $("#formulario").submit();
                }
            });
        }

        // Función para cambiar fechas según el período seleccionado
        function cambiarComboPC() {
            const fecha = new Date();
            const fechaPagado = new Date();
            const periodo = $("#periodo").val();
            const meses = MESES_POR_PERIODO[periodo] || 0;
            fecha.setMonth(fecha.getMonth() + meses);
            fechaPagado.setMonth(fechaPagado.getMonth() + 12);
            $("#fechacaduca").val(formatearFecha(fecha));
            $("#fechaactulizaciones").val(formatearFecha(fechaPagado));
        }

        // Inicializar eventos para checkboxes de módulos
        function inicializarEventosCheckboxes() {
            const modulos = ["practico", "control", "contable", "nube", "nomina", "activos", "produccion", "tvcable", "encomiendas", "crmcartera",
                "apiwhatsapp", "hybrid", "woocommerce", "tienda", "restaurante", "garantias", "talleres", "integraciones", "ahorros", "academico"
            ];
            modulos.forEach(modulo => {
                $("#" + modulo).click(() => toggleModulo(modulo));
            });
        }

        // Actualiza los checkboxes en la tabla para el módulo dado
        function actualizarCheckboxesPorModulo(modulo, estado) {
            if (IDS_MODULO[modulo]) {
                IDS_MODULO[modulo].forEach(id => {
                    $("#" + id).prop("checked", estado);
                });
            }
        }

        // Configura los valores de equipos, móviles y sucursales según el módulo seleccionado
        function configurarEquipos(modulo, estado) {
            if (estado && EQUIPOS_CONFIG[modulo]) {
                $("#numeroequipos").val(EQUIPOS_CONFIG[modulo].equipos);
                $("#numeromoviles").val(EQUIPOS_CONFIG[modulo].moviles);
                $("#numerosucursales").val(EQUIPOS_CONFIG[modulo].sucursales);
            }
        }

        // Función para el toggle de módulos
        function toggleModulo(modulo) {
            const estado = $("#" + modulo).prop("checked");

            // Si se trata de un módulo principal, desmarcar los demás y reiniciar checkboxes
            if (["practico", "control", "contable", "nube"].includes(modulo)) {
                $("#practico, #control, #contable, #nube").not("#" + modulo).prop("checked", false);
                $("#aplicativos input[type='checkbox']").prop("checked", false);

                // Configurar módulos dependientes: "nomina" y "activos"
                if (["contable", "nube"].includes(modulo) && estado) {
                    $("#nomina, #activos").prop("checked", true);
                    ["nomina", "activos"].forEach(mod => actualizarCheckboxesPorModulo(mod, true));
                } else if (["practico", "control"].includes(modulo) && estado) {
                    $("#nomina, #activos").prop("checked", false);
                    ["nomina", "activos"].forEach(mod => actualizarCheckboxesPorModulo(mod, false));
                }

                configurarEquipos(modulo, estado);
                (modulo === "nube") ? mostrarDivNube(estado): mostrarDivNube(false);
            }

            // Actualizar checkboxes asociados al módulo
            actualizarCheckboxesPorModulo(modulo, estado);
        }

        // Muestra u oculta el div de configuración de Nube
        function mostrarDivNube(estado) {
            $("#div_nube").toggle(estado);
            $("#div_nube select, #div_nube input").prop("disabled", !estado);
        }

        // Función para formatear la fecha en formato DD-MM-YYYY
        function formatearFecha(fecha) {
            return `${("0" + fecha.getDate()).slice(-2)}-${("0" + (fecha.getMonth() + 1)).slice(-2)}-${fecha.getFullYear()}`;
        }
    </script>
@endsection
