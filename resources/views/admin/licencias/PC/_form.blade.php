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
$rol=Auth::user()->tipo;
$accion=isset($licencia->sis_licenciasid) ? "Modificar" : "Crear";
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
</ul>
<div class="tab-content mt-5" id="myTabContent">
    <div class="tab-pane fade show active" id="datoslicencia" role="tabpanel">
        <input type="hidden" name="sis_distribuidoresid" value="{{$licencia->sis_distribuidoresid}}">
        <input type="hidden" name="tipo" id="tipo">
        <input type="hidden" id="permisos" name="aplicaciones" value="{{$licencia->aplicaciones}}">
        <input type="hidden" value="{{$cliente->sis_clientesid}}" name="sis_clientesid">
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Numero Contrato:</label>
                <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}"
                    placeholder="Contrato" name="numerocontrato" autocomplete="off" id="numerocontrato"
                    value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly />
                @if ($errors->has('numerocontrato'))
                <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
                @endif
            </div>
            @if (isset($licencia->sis_licenciasid) && $licencia->periodo!=3)
            <div class="col-lg-4">
                <label>Fecha Caduca:</label>
                <div class="input-group">
                    <input type="text"
                        class="form-control @if($rol!=1) disabled @endif {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
                        value="{{ old('fechacaduca',$licencia->fechacaduca) }}" />
                    <div class="input-group-append">
                        <button type="button"
                            class="btn btn-primary dropdown-toggle @if($rol==4 || $rol==3) disabled @endif"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Renovar
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" id="renovarmensual">Renovar Mensual</a>
                            <a class="dropdown-item" href="#" id="renovaranual">Renovar Anual</a>
                        </div>
                    </div>
                </div>
                @if ($errors->has('fechacaduca'))
                <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
                @endif
            </div>
            @else
            <div class="col-lg-4">
                <label>Fecha Caduca:</label>
                <input type="text"
                    class="form-control @if($rol!=1) disabled @endif {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}"
                    placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
                    value="{{ old('fechacaduca',$licencia->fechacaduca) }}" />
                @if ($errors->has('fechacaduca'))
                <span class="text-danger">{{ $errors->first('fechacaduca') }}</span>
                @endif
            </div>
            @endif
            <div class="col-lg-4">
                <label>Estado:</label>
                <select class="form-control @if($rol!=1) disabled @endif" name="estado" id="estado">
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
                <input type="text"
                    class="form-control @if($rol==4) disabled @endif {{ $errors->has('Identificador') ? 'is-invalid' : '' }}"
                    placeholder="Identificador" name="Identificador" autocomplete="off" id="Identificador"
                    value="{{ old('Identificador', $licencia->Identificador) }}" />
                @if ($errors->has('Identificador'))
                <span class="text-danger">{{ $errors->first('Identificador') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Local:</label>
                <input type="text"
                    class="form-control @if($rol==4) disabled @endif {{ $errors->has('ipservidor') ? 'is-invalid' : '' }}"
                    placeholder="IP Servidor Local" name="ipservidor" autocomplete="off" id="ipservidor"
                    value="{{ old('ipservidor', $licencia->ipservidor) }}" />
                @if ($errors->has('ipservidor'))
                <span class="text-danger">{{ $errors->first('ipservidor') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Remoto:</label>
                <input type="text"
                    class="form-control @if($rol==4) disabled @endif {{ $errors->has('ipservidorremoto') ? 'is-invalid' : '' }}"
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
                <input type="text" class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif {{
                    $errors->has('numeroequipos') ? 'is-invalid' : '' }}" placeholder="N° Equipos" name="numeroequipos"
                    autocomplete="off" id="numeroequipos"
                    value="{{ old('numeroequipos', $licencia->numeroequipos) }}" />
                @if ($errors->has('numeroequipos'))
                <span class="text-danger">{{ $errors->first('numeroequipos') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Móviles:</label>
                <input type="text"
                    class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}"
                    placeholder="N° Móviles" name="numeromoviles" autocomplete="off" id="numeromoviles"
                    value="{{ old('numeromoviles', $licencia->numeromoviles) }}" />
                @if ($errors->has('numeromoviles'))
                <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Sucursales:</label>
                <input type="text"
                    class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}"
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
                <input type="text"
                    class="form-control @if($rol!=1) disabled @endif {{ $errors->has('puerto') ? 'is-invalid' : '' }}"
                    placeholder="Puerto BD" name="puerto" autocomplete="off" id="puerto"
                    value="{{ old('puerto', $licencia->puerto) }}" />
                @if ($errors->has('puerto'))
                <span class="text-danger">{{ $errors->first('puerto') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Puerto Movil:</label>
                <select class="form-control @if($rol != 1 && $rol != 2 && $rol != 3) disabled @endif" name="puertows"
                    id="puertows">
                    <option value="80" {{ old('puertows', $licencia->puertows) == '80' ? 'Selected': '' }}>80</option>
                    <option value="2900" {{ old('puertows', $licencia->puertows) == '2900' ? 'Selected': '' }}>2900
                    </option>
                </select>
                @if ($errors->has('puertows'))
                <span class="text-danger">{{ $errors->first('puertows') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Usuario BD:</label>
                <input type="text"
                    class="form-control @if($rol!=1) disabled @endif {{ $errors->has('usuario') ? 'is-invalid' : '' }}"
                    placeholder="Usuario BD" name="usuario" autocomplete="off" id="usuario"
                    value="{{ old('usuario', $licencia->usuario) }}" />
                @if ($errors->has('usuario'))
                <span class="text-danger">{{ $errors->first('usuario') }}</span>
                @endif
            </div>

        </div>

        <div class="form-group row">
            <div class="col-lg-4">
                <label>Clave BD:</label>
                <input type="text"
                    class="form-control @if($rol!=1) disabled @endif {{ $errors->has('clave') ? 'is-invalid' : '' }}"
                    placeholder="Clave BD" name="clave" autocomplete="off" id="clave"
                    value="{{ old('clave', $licencia->clave) }}" />
                @if ($errors->has('clave'))
                <span class="text-danger">{{ $errors->first('clave') }}</span>
                @endif
            </div>
            @if ($accion=="Modificar")
            <div class="col-lg-4">
                <label>Empresas Activas:</label>
                <input type="text" class="form-control" name="empresas_activas" autocomplete="off"
                    value="{{ $empresas->empresas_activas}}" readonly />
            </div>
            <div class="col-lg-4">
                <label>Empresas Inactivas:</label>
                <input type="text" class="form-control" name="empresas_inactivas" autocomplete="off"
                    value="{{ $empresas->empresas_inactivas}}" readonly />
            </div>
            @endif
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
                                <input @if ($licencia->modulopractico== 1) checked="checked" @endif type="checkbox"
                                name="modulopractico" id="practico" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Sistema Perseo Control:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulocontrol== 1) checked="checked" @endif type="checkbox"
                                name="modulocontrol" id="control" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Sistema Perseo Contable</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulocontable== 1) checked="checked" @endif type="checkbox"
                                name="modulocontable" id="contable" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
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
                                <input @if ($licencia->actulizaciones== 1) checked="checked" @endif type="checkbox"
                                name="actulizaciones" id="actualiza" @if($rol!=1) disabled @endif />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    @if (isset($licencia->sis_licenciasid) && $licencia->periodo==3)
                    <div class="col-lg-4">
                        <label>Fecha Pagado Actualizaciones:</label>
                        <div class="input-group">
                            <input type="text"
                                class="form-control @if($rol!=1) disabled @endif {{ $errors->has('fechaactulizaciones') ? 'is-invalid' : '' }}"
                                placeholder="Ingrese Fecha Caducidad" name="fechaactulizaciones"
                                id="fechaactulizaciones" autocomplete="off"
                                value="{{ old('fechaactulizaciones',$licencia->fechaactulizaciones) }}" />
                            <div class="input-group-append">
                                <button class="btn btn-primary @if($rol==4 || $rol==3) disabled @endif" type="button"
                                    id="renovaractualizacion">Renovar
                                    Anual</button>
                            </div>
                        </div>
                        @if ($errors->has('fechaactulizaciones'))
                        <span class="text-danger">{{ $errors->first('fechaactulizaciones') }}</span>
                        @endif
                    </div>
                    @else
                    <div class="col-lg-4">
                        <label>Fecha Pagado Actualizaciones:</label>
                        <input type="text"
                            class="form-control @if($rol!=1) disabled @endif {{ $errors->has('fechaactulizaciones') ? 'is-invalid' : '' }}"
                            placeholder="Ingrese Fecha Caducidad" name="fechaactulizaciones" id="fechaactulizaciones"
                            autocomplete="off"
                            value="{{ old('fechaactulizaciones',$licencia->fechaactulizaciones) }}" />
                        @if ($errors->has('fechaactulizaciones'))
                        <span class="text-danger">{{ $errors->first('fechaactulizaciones') }}</span>
                        @endif
                    </div>
                    @endif
                    <div class="col-lg-4">
                        <label>Periodo:</label>
                        <select class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif"
                            name="periodo" id="periodo">
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
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Clave de Activación:</label>
                        <textarea rows="8" class="form-control {{ $errors->has('key') ? 'is-invalid' : '' }}"
                            placeholder="Clave de Activación" name="key" autocomplete="off" readonly
                            id="key">{{$licencia->key}}</textarea>
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
                                <input @if (isset($modulos[0]->nomina)) @if( $modulos[0]->nomina== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="nomina" id="nomina" @if($rol!=1 && $accion == 'Modificar') disabled @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Activos Fijos</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->activos)) @if ($modulos[0]->activos== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="activos" id="activos" @if($rol!=1 && $accion == 'Modificar') disabled @endif/>
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
                                <input @if (isset($modulos[0]->produccion)) @if ($modulos[0]->produccion== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="produccion" id="produccion" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">TV-Cable e Internet</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->operadoras)) @if ($modulos[0]->operadoras== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="tvcable" id="tvcable" @if($rol!=1 && $accion == 'Modificar') disabled @endif/>
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
                                <input @if (isset($modulos[0]->encomiendas)) @if ($modulos[0]->encomiendas== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="encomiendas" id="encomiendas" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">CRM de Cartera</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->crm_cartera)) @if ($modulos[0]->crm_cartera== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="crmcartera" id="crmcartera" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
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
                                <input @if (isset($modulos[0]->api_whatsapp)) @if ($modulos[0]->api_whatsapp== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="apiwhatsapp" id="apiwhatsapp" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Perseo Hybrid</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->perseo_hybrid)) @if ($modulos[0]->perseo_hybrid== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="hybrid" id="hybrid" @if($rol!=1 && $accion == 'Modificar') disabled @endif/>
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
                                <input @if (isset($modulos[0]->tienda_woocommerce))
                                @if($modulos[0]->tienda_woocommerce==true)) checked="checked" @endif @endif
                                type="checkbox" name="woocomerce" id="woocomerce" @if($rol!=1 && $accion == 'Modificar')
                                disabled @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Tienda Perseo</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->tienda_perseo_publico))
                                @if( $modulos[0]->tienda_perseo_publico== true) )checked="checked" @endif @endif
                                type="checkbox" name="tienda" id="tienda" @if($rol!=1 && $accion == 'Modificar')
                                disabled @endif/>
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
                                <input @if (isset($modulos[0]->restaurante)) @if ($modulos[0]->restaurante== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="restaurante" id="restaurante" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Servicio Técnico/Garantías</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->garantias)) @if ($modulos[0]->garantias== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="garantias" id="garantias" @if($rol!=1 && $accion == 'Modificar') disabled @endif/>
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
                                <input @if (isset($modulos[0]->talleres)) @if ($modulos[0]->talleres== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="talleres" id="talleres" @if($rol!=1 && $accion == 'Modificar') disabled @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Integraciones</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->tienda_perseo_distribuidor))
                                @if ( $modulos[0]->tienda_perseo_distribuidor== true)) checked="checked" @endif @endif
                                type="checkbox" name="integraciones" id="integraciones" @if($rol!=1 && $accion ==
                                'Modificar') disabled @endif/>
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
                                <input @if (isset($modulos[0]->cash_manager)) @if ($modulos[0]->cash_manager== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="cashmanager" id="cashmanager" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Cash Debito</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->cash_debito)) @if ($modulos[0]->cash_debito== true))
                                checked="checked" @endif @endif type="checkbox"
                                name="cashdebito" id="cashdebito" @if($rol!=1 && $accion == 'Modificar') disabled
                                @endif/>
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
                                <input @if (isset($modulos[0]->reporte_equifax)) @if ($modulos[0]->reporte_equifax==
                                true))
                                checked="checked" @endif @endif
                                type="checkbox" name="equifax" id="equifax" @if($rol!=1 && $accion == 'Modificar')
                                disabled @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <label class="col-4 col-form-label">Caja Ahorros</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->caja_ahorros)) @if ($modulos[0]->caja_ahorros==
                                true))
                                checked="checked" @endif @endif
                                type="checkbox" name="ahorros" id="ahorros" @if($rol!=1 && $accion == 'Modificar')
                                disabled @endif/>
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
                                <input @if (isset($modulos[0]->academico)) @if ($modulos[0]->academico==
                                true))
                                checked="checked" @endif @endif
                                type="checkbox" name="academico" id="academico" @if($rol!=1 && $accion == 'Modificar')
                                disabled @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>

                    <label class="col-4 col-form-label">Perseo Contador</label>
                    <div class="col-2">
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if (isset($modulos[0]->perseo_contador)) @if ($modulos[0]->perseo_contador==
                                true))
                                checked="checked" @endif @endif
                                type="checkbox" name="perseo_contador" id="perseo_contador" @if($rol!=1 && $accion ==
                                'Modificar')
                                disabled @endif/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade show" id="correos" role="tabpanel">
                <div class="form-group row">
                    <label>Correo Propietario:</label>
                    <input
                        class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif {{ $errors->has('correopropietario') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Correo" name="correopropietario" autocomplete="off"
                        value="{{ old('correopropietario', $licencia->correopropietario) }}" id="correo" />
                    @if ($errors->has('correopropietario'))
                    <span class="text-danger">{{ $errors->first('correopropietario') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Administrador:</label>
                    <input
                        class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif {{ $errors->has('correoadministrador') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Correo" name="correoadministrador" autocomplete="off"
                        value="{{ old('correoadministrador', $licencia->correoadministrador) }}" id="correo" />
                    @if ($errors->has('correoadministrador'))
                    <span class="text-danger">{{ $errors->first('correoadministrador') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Contador:</label>
                    <input
                        class="form-control @if($rol!=1 && $accion == 'Modificar') disabled @endif {{ $errors->has('correocontador') ? 'is-invalid' : '' }}"
                        placeholder="Ingrese Correo" name="correocontador" autocomplete="off"
                        value="{{ old('correocontador', $licencia->correocontador) }}" id="correo" />
                    @if ($errors->has('correocontador'))
                    <span class="text-danger">{{ $errors->first('correocontador') }}</span>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade show" id="respaldos" role="tabpanel">
                <div class="form-group row">
                    <label>Token Dropbox:</label>
                    <textarea rows="8"
                        class="form-control @if($rol!=1) disabled @endif {{ $errors->has('tokenrespaldo') ? 'is-invalid' : '' }}"
                        placeholder="Token Respaldo" name="tokenrespaldo" autocomplete="off"
                        id="tokenrespaldo">{{$licencia->tokenrespaldo}}</textarea>
                    @if ($errors->has('tokenrespaldo'))
                    <span class="text-danger">{{ $errors->first('tokenrespaldo') }}</span>
                    @endif
                </div>
            </div>

            <div class="tab-pane fade show" id="bloqueos" role="tabpanel">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Motivo Bloqueo:</label>
                        <input
                            class="form-control @if($rol!=1) disabled @endif {{ $errors->has('motivobloqueo') ? 'is-invalid' : '' }}"
                            placeholder="Motivo bloqueo" name="motivobloqueo" autocomplete="off" id="motivobloqueo"
                            value="{{ old('motivobloqueo', $licencia->motivobloqueo) }}" />
                        @if ($errors->has('motivobloqueo'))
                        <span class="text-danger">{{ $errors->first('motivobloqueo') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Mensaje Entrar al Sistema:</label>
                        <input
                            class="form-control @if($rol!=1) disabled @endif {{ $errors->has('mensaje') ? 'is-invalid' : '' }}"
                            placeholder="Mensaje" name="mensaje" autocomplete="off" id="mensaje"
                            value="{{ old('mensaje', $licencia->mensaje) }}" />
                        @if ($errors->has('mensaje'))
                        <span class="text-danger">{{ $errors->first('mensaje') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Observaciones:</label>
                        <input
                            class="form-control @if($rol!=1) disabled @endif {{ $errors->has('observacion') ? 'is-invalid' : '' }}"
                            placeholder="Observaciones" name="observacion" autocomplete="off" id="observacion"
                            value="{{ old('observacion', $licencia->observacion) }}" />
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
</div>

@section('script')
<script>
    $("#renovarmensual").click(function(e) {
        confirmar('mes',"Está seguro de Renovar la Licencia?");
    });

    $("#renovaranual").click(function(e) {
        confirmar('anual',"Está seguro de Renovar la Licencia?");
    });

    $("#renovaractualizacion").click(function(e) {
        confirmar('actualizacion',"Está seguro de Renovar la Licencia?");
    });

    //recorrer tabla de permisos y hacer una sola cadena con los ids
    $('#formulario').submit(function(event) {

        //Enviar swirch que estan disabled
        $("#actualiza").prop("disabled", false);
        $("#contable").prop("disabled", false);
        $("#control").prop("disabled", false);
        $("#practico").prop("disabled", false);
        $("#nomina").prop("disabled", false);
        $("#activos").prop("disabled", false);
        $("#produccion").prop("disabled", false);
        $("#tvcable").prop("disabled", false);
        $("#encomiendas").prop("disabled", false);
        $("#crmcartera").prop("disabled", false);
        $("#apiwhatsapp").prop("disabled", false);
        $("#hybrid").prop("disabled", false);
        $("#woocomerce").prop("disabled", false);
        $("#tienda").prop("disabled", false);
        $("#restaurante").prop("disabled", false);
        $("#garantias").prop("disabled", false);
        $("#talleres").prop("disabled", false);
        $("#integraciones").prop("disabled", false);
        $("#cashmanager").prop("disabled", false);
        $("#equifax").prop("disabled", false);
        $("#ahorros").prop("disabled", false);
        $("#academico").prop("disabled", false);
        $("#perseo_contador").prop("disabled", false);

        event.preventDefault();
        permisos='';
        $("#aplicaciones tbody td input").each(function(){
            if ($(this).prop('checked')) {
                permisos=permisos+$(this).attr('id') + ';';
            }
        });

        let inputPractico = $('#practico').prop('checked');
        let inputControl = $('#control').prop('checked');
        let inputContable = $('#contable').prop('checked');

        if (inputPractico == false && inputControl == false && inputContable == false ) {
            $.notify({
            // options
                message: 'Seleccione si es Perseo: Práctico, Control o Contable',
            },{
                // settings
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
                ype: 'warning',
            });
        } else {
            $('#permisos').val(permisos)
            $(this).unbind('submit').submit();
        }
    })

    $('#periodo').change(function(){
        cambiarComboPC();
    });

    $('#practico').click(function(){
        $('#control').prop('checked', false);
        $('#contable').prop('checked', false);
        if ($('#practico').prop('checked')) {
            moduloPerseoPractico(true);
        }else{
            moduloPerseoPractico(false);
        }
        cambiarComboPC();
    });

    $('#control').click(function(){
        $('#practico').prop('checked', false);
        $('#contable').prop('checked', false);

        if ($('#control').prop('checked')) {
            moduloPerseoPractico(true);
            moduloPerseoControl(true);
        }else{
            moduloPerseoPractico(false);
            moduloPerseoControl(false);
        }
        cambiarComboPC();
    });

    $('#contable').click(function(){
        $('#control').prop('checked', false);
        $('#practico').prop('checked', false);

        if ($('#contable').prop('checked')) {
            moduloPerseoPractico(true);
            moduloPerseoControl(true);
            moduloPerseoContable(true);
        }else{
            moduloPerseoPractico(false);
            moduloPerseoControl(false);
            moduloPerseoContable(false);
        }
        cambiarComboPC();
    });

    $('#nomina').click(function(){
        if ($('#nomina').prop('checked')) {
            moduloPerseoNomina(true);
        }else{
            moduloPerseoNomina(false);
        }
    });

    $('#activos').click(function(){
        if ($('#activos').prop('checked')) {
            moduloPerseoActivos(true);
        }else{
            moduloPerseoActivos(false);
        }
    });

    $('#produccion').click(function(){
        if ($('#produccion').prop('checked')) {
            moduloPerseoProduccion(true);
        }else{
            moduloPerseoProduccion(false);
        }
    });

    $('#tvcable').click(function(){
        if ($('#tvcable').prop('checked')) {
            moduloPerseoOperadoras(true);
        }else{
            moduloPerseoOperadoras(false);
        }
    });

    $('#encomiendas').click(function(){
        if ($('#encomiendas').prop('checked')) {
            moduloPerseoEncomiendas(true);
        }else{
            moduloPerseoEncomiendas(false);
        }
    });

    $('#crmcartera').click(function(){
        if ($('#crmcartera').prop('checked')) {
            moduloPerseoCrmCartera(true);
        }else{
            moduloPerseoCrmCartera(false);
        }
    });

    $('#apiwhatsapp').click(function(){
        if ($('#apiwhatsapp').prop('checked')) {
            moduloPerseoIntegraciones(true);
        }else{
            moduloPerseoIntegraciones(false);
        }
    });

    $('#hybrid').click(function(){
        if ($('#hybrid').prop('checked')) {
            moduloPerseoIntegraciones(true);
        }else{
            moduloPerseoIntegraciones(false);
        }
    });

    $('#woocomerce').click(function(){
        if ($('#woocomerce').prop('checked')) {
            moduloPerseoIntegraciones(true);
        }else{
            moduloPerseoIntegraciones(false);
        }
    });

    $('#tienda').click(function(){
        if ($('#tienda').prop('checked')) {
            moduloPerseoIntegraciones(true);
        }else{
            moduloPerseoIntegraciones(false);
        }
    });

    $('#restaurante').click(function(){
        if ($('#restaurante').prop('checked')) {
            moduloPerseoRestaurantes(true);
        }else{
            moduloPerseoRestaurantes(false);
        }
    });

    $('#garantias').click(function(){
        if ($('#garantias').prop('checked')) {
            moduloPerseoGarantias(true);
        }else{
            moduloPerseoGarantias(false);
        }
    });

    $('#talleres').click(function(){
        if ($('#talleres').prop('checked')) {
            moduloPerseoTalleres(true);
        }else{
            moduloPerseoTalleres(false);
        }
    });

    $('#integraciones').click(function(){
        if ($('#integraciones').prop('checked')) {
            moduloPerseoIntegraciones(true);
        }else{
            moduloPerseoIntegraciones(false);
        }
    });

    $('#ahorros').click(function(){
        if ($('#ahorros').prop('checked')) {
            moduloPerseoAhorros(true);
        }else{
            moduloPerseoAhorros(false);
        }
    });

    $('#academico').click(function(){
        if ($('#academico').prop('checked')) {
            moduloPerseoAcademico(true);
        }else{
            moduloPerseoAcademico(false);
        }
    });


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

        //inicializar datatable
        var table = $('#aplicativos').DataTable({
            responsive: true,
            //processing: true,
            //Guardar pagina, busqueda, etc
            //stateSave: true,
            //Trabajar del lado del server
            serverSide: true,
            searching: false,
            paging: false,
            //Peticion ajax que devuelve los registros
            ajax: "{{ route('subcategorias') }}",
            drawCallback: function (settings) {

				var api = this.api();
				var rows = api.rows({ page: 'current' }).nodes();
				var last = null;

				api.column(0, { page: 'current' }).data().each(function (group, i) {
					if (last !== group) {
						$(rows).eq(i).before(
							'<tr class="group"><td colspan="3">' + group + '</td></tr>',
						);
						last = group;
					}
				});
			},
            //Columnas de la tabla (Debe contener misma cantidad que thead)
            columns: [
                {data: 'categoriasdescripcion', name: 'categoriasdescripcion',visible:false},
                {data: 'sis_subcategoriasid', orderable: false, searchable: false,name: 'sis_subcategoriasid'},
                {data: 'descripcionsubcategoria',orderable: false, searchable: false, name: 'descripcionsubcategoria'},
                {data: 'activo', name: 'activo', orderable: false, searchable: false},
            ],
            initComplete: function(settings, json) {
                //Al terminar de llenar tabla, cargar permisos
                var permisos=$("#permisos").val();
                var array =  permisos.split(';');

                for (var i = 0; i <array.length ; i++) {
                    $('#'+array[i]).prop('checked', true);
                }
            }
        });


    });

    function moduloPerseoPractico(estado){
        //Activar o desactivar modulos
        $('#105').prop('checked', estado);
        $('#110').prop('checked', estado);
        $('#111').prop('checked', estado);
        $('#112').prop('checked', estado);
        $('#113').prop('checked', estado);
        $('#114').prop('checked', estado);
        $('#115').prop('checked', estado);
        $('#117').prop('checked', estado);
        $('#118').prop('checked', estado);
        $('#120').prop('checked', estado);
        $('#125').prop('checked', estado);
        $('#126').prop('checked', estado);
        $('#127').prop('checked', estado);
        $('#130').prop('checked', estado);
        $('#131').prop('checked', estado);
        $('#135').prop('checked', estado);
        $('#136').prop('checked', estado);
        $('#141').prop('checked', estado);
        $('#142').prop('checked', estado);
        $('#150').prop('checked', estado);
        $('#305').prop('checked', estado);
        $('#310').prop('checked', estado);
        $('#315').prop('checked', estado);
        $('#320').prop('checked', estado);
        $('#325').prop('checked', estado);
        $('#330').prop('checked', estado);
        $('#335').prop('checked', estado);
        $('#430').prop('checked', estado);
        $('#431').prop('checked', estado);
        $('#432').prop('checked', estado);
        $('#433').prop('checked', estado);
        $('#434').prop('checked', estado);
        $('#435').prop('checked', estado);
        $('#440').prop('checked', estado);
        $('#445').prop('checked', estado);
        $('#450').prop('checked', estado);
        $('#455').prop('checked', estado);
        $('#460').prop('checked', estado);
        $('#461').prop('checked', estado);
        $('#462').prop('checked', estado);
        $('#463').prop('checked', estado);
        $('#464').prop('checked', estado);
        $('#465').prop('checked', estado);
        $('#466').prop('checked', estado);
        $('#470').prop('checked', estado);
        $('#471').prop('checked', estado);
        $('#475').prop('checked', estado);
        $('#480').prop('checked', estado);
        $('#491').prop('checked', estado);
        $('#492').prop('checked', estado);
        $('#495').prop('checked', estado);
        $('#905').prop('checked', estado);
        $('#910').prop('checked', estado);
        $('#915').prop('checked', estado);
        $('#916').prop('checked', estado);
        $('#917').prop('checked', estado);
        $('#918').prop('checked', estado);
        $('#919').prop('checked', estado);
        $('#920').prop('checked', estado);
        $('#925').prop('checked', estado);
        $('#930').prop('checked', estado);
        $('#931').prop('checked', estado);
        $('#940').prop('checked', estado);
        $('#960').prop('checked', estado);
        $('#1105').prop('checked', estado);
        $('#1110').prop('checked', estado);
        $('#1115').prop('checked', estado);
        $('#1120').prop('checked', estado);
    }

    function moduloPerseoControl(estado){
        //Activar o desactivar modulos
        $('#200').prop('checked', estado);
        $('#142').prop('checked', estado);
        $('#201').prop('checked', estado);
        $('#205').prop('checked', estado);
        $('#210').prop('checked', estado);
        $('#215').prop('checked', estado);
        $('#505').prop('checked', estado);
        $('#510').prop('checked', estado);
        $('#515').prop('checked', estado);
        $('#516').prop('checked', estado);
        $('#517').prop('checked', estado);
        $('#462').prop('checked', estado);
        $('#463').prop('checked', estado);
        $('#485').prop('checked', estado);
        $('#490').prop('checked', estado);
        $('#116').prop('checked', estado);
        $('#140').prop('checked', estado);
        $('#605').prop('checked', estado);
        $('#630').prop('checked', estado);
        $('#635').prop('checked', estado);
    }

    function moduloPerseoContable(estado){
        //Activar o desactivar modulos
        $('#605').prop('checked', estado);
        $('#142').prop('checked', estado);
        $('#606').prop('checked', estado);
        $('#610').prop('checked', estado);
        $('#615').prop('checked', estado);
        $('#616').prop('checked', estado);
        $('#620').prop('checked', estado);
        $('#625').prop('checked', estado);
        $('#626').prop('checked', estado);
        $('#627').prop('checked', estado);
        $('#628').prop('checked', estado);
        $('#630').prop('checked', estado);
        $('#635').prop('checked', estado);
        $('#640').prop('checked', estado);
    }

    function moduloPerseoNomina(estado){
        //Activar o desactivar modulos
        $('#705').prop('checked', estado);
        $('#710').prop('checked', estado);
        $('#715').prop('checked', estado);
        $('#720').prop('checked', estado);
        $('#725').prop('checked', estado);
        $('#730').prop('checked', estado);
        $('#735').prop('checked', estado);
        $('#740').prop('checked', estado);
        $('#741').prop('checked', estado);
        $('#745').prop('checked', estado);
    }

    function moduloPerseoActivos(estado){
        //Activar o desactivar modulos
        $('#805').prop('checked', estado);
        $('#806').prop('checked', estado);
        $('#810').prop('checked', estado);
        $('#815').prop('checked', estado);
        $('#816').prop('checked', estado);
        $('#820').prop('checked', estado);
    }

    function moduloPerseoProduccion(estado){
        //Activar o desactivar modulos
        $('#1005').prop('checked', estado);
        $('#1010').prop('checked', estado);
        $('#1015').prop('checked', estado);
    }

    function moduloPerseoOperadoras(estado){
        //Activar o desactivar modulos
        $('#1200').prop('checked', estado);
        $('#1205').prop('checked', estado);
        $('#1210').prop('checked', estado);
        $('#1215').prop('checked', estado);
        $('#1220').prop('checked', estado);
    }

    function moduloPerseoEncomiendas(estado){
        //Activar o desactivar modulos
        $('#1601').prop('checked', estado);
        $('#1610').prop('checked', estado);
        $('#1615').prop('checked', estado);
        $('#1620').prop('checked', estado);
        $('#1625').prop('checked', estado);
    }

    function moduloPerseoCrmCartera(estado){
        //Activar o desactivar modulos
        $('#220').prop('checked', estado);
    }

    function moduloPerseoAhorros(estado){
        //Activar o desactivar modulos
        $('#1705').prop('checked', estado);
        $('#1710').prop('checked', estado);
        $('#1715').prop('checked', estado);
        $('#1716').prop('checked', estado);
        $('#1720').prop('checked', estado);
        $('#1725').prop('checked', estado);
    }

    function moduloPerseoIntegraciones(estado){
        //Activar o desactivar modulos
        $('#950').prop('checked', estado);
    }

    function moduloPerseoRestaurantes(estado){
        //Activar o desactivar modulos
        $('#1500').prop('checked', estado);
        $('#1505').prop('checked', estado);
        $('#1510').prop('checked', estado);
        $('#1515').prop('checked', estado);
        $('#1520').prop('checked', estado);
    }

    function moduloPerseoGarantias(estado){
        //Activar o desactivar modulos
        $('#1300').prop('checked', estado);
        $('#1305').prop('checked', estado);
        $('#1310').prop('checked', estado);
    }

    function moduloPerseoTalleres(estado){
        //Activar o desactivar modulos
        $('#1400').prop('checked', estado);
        $('#1405').prop('checked', estado);
        $('#1410').prop('checked', estado);
    }

    function moduloPerseoAcademico(estado){
        //Activar o desactivar modulos
        $('#1805').prop('checked', estado);
        $('#1810').prop('checked', estado);
        $('#1815').prop('checked', estado);
        $('#1820').prop('checked', estado);
        $('#1825').prop('checked', estado);
        $('#1830').prop('checked', estado);
    }

    function cambiarComboPC(){
        var fecha = new Date();
        var fechaPagado = new Date();

        switch ($('#periodo').val()) {
            case '1':
                fecha.setMonth(fecha.getMonth() + 1);
                fechaPagado.setMonth(fechaPagado.getMonth() + 1);

                if ($('#practico').prop('checked')) {
                    $('#numeroequipos').val('1');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(false);
                    moduloPerseoActivos(false);
                    moduloPerseoProduccion(false);
                }
                if ($('#control').prop('checked')) {
                    $('#numeroequipos').val('3');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(false);
                    moduloPerseoActivos(false);
                    moduloPerseoProduccion(false);
                }
                if ($('#contable').prop('checked')) {
                    $('#numeroequipos').val('3');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', true);
                    $('#activos').prop('checked', true);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(true);
                    moduloPerseoActivos(true);
                    moduloPerseoProduccion(false);
                }
                break;
            case '2':
                fecha.setYear(fecha.getFullYear() + 1);
                fechaPagado.setYear(fechaPagado.getFullYear() + 1);
                if ($('#practico').prop('checked')) {
                    $('#numeroequipos').val('1');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(false);
                    moduloPerseoActivos(false);
                    moduloPerseoProduccion(false);
                }
                if ($('#control').prop('checked')) {
                    $('#numeroequipos').val('3');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(false);
                    moduloPerseoActivos(false);
                    moduloPerseoProduccion(false);
                }
                if ($('#contable').prop('checked')) {
                    $('#numeroequipos').val('3');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', true);
                    $('#activos').prop('checked', true);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(true);
                    moduloPerseoActivos(true);
                    moduloPerseoProduccion(false);
                }
                break;
            case '3':
                fecha.setYear(fecha.getFullYear() + 5);
                fechaPagado.setYear(fechaPagado.getFullYear() + 1);
                if ($('#practico').prop('checked')) {
                    $('#numeroequipos').val('1');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(false);
                    moduloPerseoActivos(false);
                    moduloPerseoProduccion(false);
                }
                if ($('#control').prop('checked')) {
                    $('#numeroequipos').val('3');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', false);
                    $('#activos').prop('checked', false);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(false);
                    moduloPerseoActivos(false);
                    moduloPerseoProduccion(false);
                }
                if ($('#contable').prop('checked')) {
                    $('#numeroequipos').val('3');
                    $('#numeromoviles').val('0');
                    $('#numerosucursales').val('1');
                    $('#nomina').prop('checked', true);
                    $('#activos').prop('checked', true);
                    $('#produccion').prop('checked', false);
                    moduloPerseoNomina(true);
                    moduloPerseoActivos(true);
                    moduloPerseoProduccion(false);
                }
                break;
        }
        let fechaFormato = ("0"+(fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth()+1)).slice(-2) + "-" + fecha.getFullYear()
        let fechaPagadoFormato = ("0"+(fechaPagado.getDate())).slice(-2) + "-" + ("0" + (fechaPagado.getMonth()+1)).slice(-2) + "-" + fechaPagado.getFullYear()

        $('#fechacaduca').val(fechaFormato);
        $('#fechaactulizaciones').val(fechaPagadoFormato);
    }

    function confirmar(tipo,mensaje){
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