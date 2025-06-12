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
    $accion = isset($licencia->sis_licenciasid) ? 'Modificar' : 'Crear';
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
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#datoslicencia">Datos Licencia</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#aplicaciones">Aplicaciones</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#soporte">Soporte</a></li>
</ul>

<div class="tab-content mt-5" id="myTabContent">
    <div class="tab-pane fade show active" id="datoslicencia" role="tabpanel">
        <input type="hidden" name="sis_distribuidoresid" value="{{ $licencia->sis_distribuidoresid }}">
        <input type="hidden" name="tipo" id="tipo">
        <input type="hidden" id="permisos" name="aplicaciones_permisos" value="{{ $licencia->aplicaciones }}">
        <input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">

        <!-- Datos básicos -->
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Numero Contrato:</label>
                <input type="text" class="form-control {{ $errors->has('numerocontrato') ? 'is-invalid' : '' }}" name="numerocontrato"
                    id="numerocontrato" value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly />
                @if ($errors->has('numerocontrato'))
                    <span class="text-danger">{{ $errors->first('numerocontrato') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Fecha Caduca:</label>
                <div class="input-group">
                    <input type="text" class="form-control {{ $errors->has('fechacaduca') ? 'is-invalid' : '' }}" name="fechacaduca"
                        id="fechacaduca" value="{{ old('fechacaduca', $licencia->fechacaduca) }}"
                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                    @if (isset($licencia->sis_licenciasid) && $licencia->periodo != 3)
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                {{ !puede('pc', 'renovar_licencia') ? 'disabled' : '' }}>Renovar</button>
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
                <select class="form-control" name="estado" id="estado"
                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                    <option value="1" {{ old('estado', $licencia->estado) == '1' ? 'Selected' : '' }}>Activo</option>
                    <option value="2" {{ old('estado', $licencia->estado) == '2' ? 'Selected' : '' }}>Pendiente de pago</option>
                    <option value="3" {{ old('estado', $licencia->estado) == '3' ? 'Selected' : '' }}>Inactivo</option>
                </select>
                @if ($errors->has('estado'))
                    <span class="text-danger">{{ $errors->first('estado') }}</span>
                @endif
            </div>
        </div>

        <!-- Configuración técnica -->
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Identificador Servidor:</label>
                <input type="text" class="form-control {{ $errors->has('Identificador') ? 'is-invalid' : '' }}" name="Identificador"
                    id="Identificador" value="{{ old('Identificador', $licencia->Identificador) }}"
                    {{ !puede('pc', 'editar_configuracion_tecnica_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('Identificador'))
                    <span class="text-danger">{{ $errors->first('Identificador') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Local:</label>
                <input type="text" class="form-control {{ $errors->has('ipservidor') ? 'is-invalid' : '' }}" name="ipservidor" id="ipservidor"
                    value="{{ old('ipservidor', $licencia->ipservidor) }}"
                    {{ !puede('pc', 'editar_configuracion_tecnica_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('ipservidor'))
                    <span class="text-danger">{{ $errors->first('ipservidor') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>IP Servidor Remoto:</label>
                <input type="text" class="form-control {{ $errors->has('ipservidorremoto') ? 'is-invalid' : '' }}" name="ipservidorremoto"
                    id="ipservidorremoto" value="{{ old('ipservidorremoto', $licencia->ipservidorremoto) }}"
                    {{ !puede('pc', 'editar_configuracion_tecnica_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('ipservidorremoto'))
                    <span class="text-danger">{{ $errors->first('ipservidorremoto') }}</span>
                @endif
            </div>
        </div>

        <!-- Números y configuración -->
        <div class="form-group row">
            <div class="col-lg-4">
                <label>N° Equipos:</label>
                <input type="text" class="form-control {{ $errors->has('numeroequipos') ? 'is-invalid' : '' }}" name="numeroequipos"
                    id="numeroequipos" value="{{ old('numeroequipos', $licencia->numeroequipos) }}"
                    {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('numeroequipos'))
                    <span class="text-danger">{{ $errors->first('numeroequipos') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Móviles:</label>
                <input type="text" class="form-control {{ $errors->has('numeromoviles') ? 'is-invalid' : '' }}" name="numeromoviles"
                    id="numeromoviles" value="{{ old('numeromoviles', $licencia->numeromoviles) }}"
                    {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('numeromoviles'))
                    <span class="text-danger">{{ $errors->first('numeromoviles') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>N° Sucursales:</label>
                <input type="text" class="form-control {{ $errors->has('numerosucursales') ? 'is-invalid' : '' }}" name="numerosucursales"
                    id="numerosucursales" value="{{ old('numerosucursales', $licencia->numerosucursales) }}"
                    {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('numerosucursales'))
                    <span class="text-danger">{{ $errors->first('numerosucursales') }}</span>
                @endif
            </div>
        </div>

        <!-- Puertos y BD -->
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Puerto BD:</label>
                <input type="text" class="form-control {{ $errors->has('puerto') ? 'is-invalid' : '' }}" name="puerto" id="puerto"
                    value="{{ old('puerto', $licencia->puerto) }}"
                    {{ !puede('pc', 'editar_puerto_bd_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('puerto'))
                    <span class="text-danger">{{ $errors->first('puerto') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Puerto Movil:</label>
                <input type="text" class="form-control {{ $errors->has('puertows') ? 'is-invalid' : '' }}" name="puertows" id="puertows"
                    value="{{ old('puertows', $licencia->puertows) }}" maxlength="4" pattern="\d{1,4}"
                    {{ !puede('pc', 'editar_puerto_movil_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('puertows'))
                    <span class="text-danger">{{ $errors->first('puertows') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Usuario BD:</label>
                <input type="text" class="form-control {{ $errors->has('usuario') ? 'is-invalid' : '' }}" name="usuario" id="usuario"
                    value="{{ old('usuario', $licencia->usuario) }}" {{ !puede('pc', 'editar_bd_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('usuario'))
                    <span class="text-danger">{{ $errors->first('usuario') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-4">
                <label>Clave BD:</label>
                <input type="text" class="form-control {{ $errors->has('clave') ? 'is-invalid' : '' }}" name="clave" id="clave"
                    value="{{ old('clave', $licencia->clave) }}" {{ !puede('pc', 'editar_bd_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('clave'))
                    <span class="text-danger">{{ $errors->first('clave') }}</span>
                @endif
            </div>
            @if ($accion == 'Modificar')
                <div class="col-lg-4">
                    <label>Empresas Activas:</label>
                    <input type="text" class="form-control" value="{{ $empresas->empresas_activas }}" readonly />
                </div>
                <div class="col-lg-4">
                    <label>Empresas Inactivas:</label>
                    <input type="text" class="form-control" value="{{ $empresas->empresas_inactivas }}" readonly />
                </div>
            @endif
        </div>

        <!-- Configuración Nube (se muestra/oculta según selección) -->
        <div id="div_nube" style="display: none;">
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>Tipo:</label>
                    <select class="form-control" name="tipo_nube" id="tipo_nube"
                        {{ !puede('pc', 'editar_nube_' . strtolower($accion)) ? 'disabled' : '' }}>
                        <option value="1" {{ old('tipo_nube', $licencia->tipo_nube) == '1' ? 'Selected' : '' }}>Prime</option>
                        <option value="2" {{ old('tipo_nube', $licencia->tipo_nube) == '2' ? 'Selected' : '' }}>Contaplus</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Nivel:</label>
                    <select class="form-control" name="nivel_nube" id="nivel_nube"
                        {{ !puede('pc', 'editar_nube_' . strtolower($accion)) ? 'disabled' : '' }}>
                        <option value="1" {{ old('nivel_nube', $licencia->nivel_nube) == '1' ? 'Selected' : '' }}>Nivel 1</option>
                        <option value="2" {{ old('nivel_nube', $licencia->nivel_nube) == '2' ? 'Selected' : '' }}>Nivel 2</option>
                        <option value="3" {{ old('nivel_nube', $licencia->nivel_nube) == '3' ? 'Selected' : '' }}>Nivel 3</option>
                    </select>
                </div>
                <div class="col-lg-4">
                    <label>Usuarios:</label>
                    <input type="text" class="form-control" name="usuarios_nube" id="usuarios_nube" value="{{ $licencia->usuarios_nube }}"
                        {{ !puede('pc', 'editar_nube_' . strtolower($accion)) ? 'disabled' : '' }} />
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>Usuarios Activos:</label>
                    <input type="text" class="form-control" value="{{ $licencia->usuarios_activos }}" readonly />
                </div>
            </div>
        </div>

        <!-- Sub-tabs para módulos -->
        <ul class="nav nav-tabs nav-tabs-line nav-bold">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#aplicacionesprincipales">Aplicaciones Principales</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#modulosadicionales">Módulos Adicionales</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#correos">Correos</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#respaldos">Respaldos</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#bloqueos">Bloqueos</a></li>
        </ul>

        <div class="tab-content mt-5">
            <!-- Aplicaciones Principales -->
            <div class="tab-pane fade show active" id="aplicacionesprincipales" role="tabpanel">
                <div class="form-group row">
                    <div class="col-lg-3">
                        <label>Sistema Perseo Práctico:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulopractico == 1) checked @endif type="checkbox" name="modulopractico" id="practico"
                                    {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }} />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-3">
                        <label>Sistema Perseo Control:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulocontrol == 1) checked @endif type="checkbox" name="modulocontrol" id="control"
                                    {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }} />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-3">
                        <label>Sistema Perseo Contable:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulocontable == 1) checked @endif type="checkbox" name="modulocontable" id="contable"
                                    {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }} />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-3">
                        <label>Sistema Nube:</label>
                        <span class="switch switch-outline switch-icon switch-primary switch-sm">
                            <label>
                                <input @if ($licencia->modulonube == 1) checked @endif type="checkbox" name="modulonube" id="nube"
                                    {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }} />
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
                                <input @if ($licencia->actulizaciones == 1) checked @endif type="checkbox" name="actulizaciones" id="actualiza"
                                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                                <span></span>
                            </label>
                        </span>
                    </div>
                    <div class="col-lg-4">
                        <label>Fecha Pagado Actualizaciones:</label>
                        <div class="input-group">
                            <input type="text" class="form-control {{ $errors->has('fechaactulizaciones') ? 'is-invalid' : '' }}"
                                name="fechaactulizaciones" id="fechaactulizaciones"
                                value="{{ old('fechaactulizaciones', $licencia->fechaactulizaciones) }}"
                                {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                            @if (isset($licencia->sis_licenciasid) && $licencia->periodo == 3)
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="renovaractualizacion"
                                        {{ !puede('pc', 'renovar_licencia') ? 'disabled' : '' }}>Renovar Anual</button>
                                </div>
                            @endif
                        </div>
                        @if ($errors->has('fechaactulizaciones'))
                            <span class="text-danger">{{ $errors->first('fechaactulizaciones') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label>Periodo:</label>
                        <select class="form-control" name="periodo" id="periodo"
                            {{ !puede('pc', 'editar_periodo_' . strtolower($accion)) ? 'disabled' : '' }}>
                            <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'Selected' : '' }}>Mensual</option>
                            <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'Selected' : '' }}>Anual</option>
                            <option value="3" {{ old('periodo', $licencia->periodo) == '3' ? 'Selected' : '' }}>Venta</option>
                        </select>
                        @if ($errors->has('periodo'))
                            <span class="text-danger">{{ $errors->first('periodo') }}</span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Clave de Activación:</label>
                        <textarea rows="8" class="form-control {{ $errors->has('key') ? 'is-invalid' : '' }}" name="key" id="key" readonly>{{ $licencia->key }}</textarea>
                        @if ($errors->has('key'))
                            <span class="text-danger">{{ $errors->first('key') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Módulos Adicionales -->
            <div class="tab-pane fade" id="modulosadicionales" role="tabpanel">
                @php
                    $modulosAdicionales = [
                        ['name' => 'nomina', 'label' => 'Nómina', 'value' => $modulos[0]->nomina ?? false],
                        ['name' => 'activos', 'label' => 'Activos Fijos', 'value' => $modulos[0]->activos ?? false],
                        ['name' => 'produccion', 'label' => 'Producción', 'value' => $modulos[0]->produccion ?? false],
                        ['name' => 'tvcable', 'label' => 'TV-Cable e Internet', 'value' => $modulos[0]->operadoras ?? false],
                        ['name' => 'encomiendas', 'label' => 'Servicio de Encomiendas', 'value' => $modulos[0]->encomiendas ?? false],
                        ['name' => 'crmcartera', 'label' => 'CRM de Cartera', 'value' => $modulos[0]->crm_cartera ?? false],
                        ['name' => 'apiwhatsapp', 'label' => 'API Whatsapp', 'value' => $modulos[0]->api_whatsapp ?? false],
                        ['name' => 'hybrid', 'label' => 'Perseo Hybrid', 'value' => $modulos[0]->perseo_hybrid ?? false],
                        ['name' => 'woocomerce', 'label' => 'Plugin Woocomerce', 'value' => $modulos[0]->tienda_woocommerce ?? false],
                        ['name' => 'tienda', 'label' => 'Tienda Perseo', 'value' => $modulos[0]->tienda_perseo_publico ?? false],
                        ['name' => 'restaurante', 'label' => 'Restaurantes', 'value' => $modulos[0]->restaurante ?? false],
                        ['name' => 'garantias', 'label' => 'Servicio Técnico/Garantías', 'value' => $modulos[0]->garantias ?? false],
                        ['name' => 'talleres', 'label' => 'Servicio Técnico Talleres Vehículos', 'value' => $modulos[0]->talleres ?? false],
                        ['name' => 'integraciones', 'label' => 'Integraciones', 'value' => $modulos[0]->tienda_perseo_distribuidor ?? false],
                        ['name' => 'cashmanager', 'label' => 'Cash Manager', 'value' => $modulos[0]->cash_manager ?? false],
                        ['name' => 'cashdebito', 'label' => 'Cash Debito', 'value' => $modulos[0]->cash_debito ?? false],
                        ['name' => 'equifax', 'label' => 'Reporte Equifax', 'value' => $modulos[0]->reporte_equifax ?? false],
                        ['name' => 'ahorros', 'label' => 'Caja Ahorros', 'value' => $modulos[0]->caja_ahorros ?? false],
                        ['name' => 'academico', 'label' => 'Académico', 'value' => $modulos[0]->academico ?? false],
                        ['name' => 'perseo_contador', 'label' => 'Perseo Contador', 'value' => $modulos[0]->perseo_contador ?? false],
                        ['name' => 'api_urbano', 'label' => 'API Urbano', 'value' => $modulos[0]->api_urbano ?? false],
                    ];
                @endphp

                @foreach (array_chunk($modulosAdicionales, 2) as $moduloRow)
                    <div class="form-group row">
                        @foreach ($moduloRow as $modulo)
                            <label class="col-4 col-form-label">{{ $modulo['label'] }}</label>
                            <div class="col-2">
                                <span class="switch switch-outline switch-icon switch-primary switch-sm">
                                    <label>
                                        <input @if ($modulo['value']) checked @endif type="checkbox" name="{{ $modulo['name'] }}"
                                            id="{{ $modulo['name'] }}"
                                            {{ !puede('pc', 'editar_modulos_adicionales_' . strtolower($accion)) ? 'disabled' : '' }} />
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Correos -->
            <div class="tab-pane fade" id="correos" role="tabpanel">
                <div class="form-group row">
                    <label>Correo Propietario:</label>
                    <input class="form-control {{ $errors->has('correopropietario') ? 'is-invalid' : '' }}" name="correopropietario"
                        id="correopropietario" value="{{ old('correopropietario', $licencia->correopropietario) }}"
                        {{ !puede('pc', 'editar_correos_' . strtolower($accion)) ? 'disabled' : '' }} />
                    @if ($errors->has('correopropietario'))
                        <span class="text-danger">{{ $errors->first('correopropietario') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Administrador:</label>
                    <input class="form-control {{ $errors->has('correoadministrador') ? 'is-invalid' : '' }}" name="correoadministrador"
                        id="correoadministrador" value="{{ old('correoadministrador', $licencia->correoadministrador) }}"
                        {{ !puede('pc', 'editar_correos_' . strtolower($accion)) ? 'disabled' : '' }} />
                    @if ($errors->has('correoadministrador'))
                        <span class="text-danger">{{ $errors->first('correoadministrador') }}</span>
                    @endif
                </div>
                <div class="form-group row">
                    <label>Correo Contador:</label>
                    <input class="form-control {{ $errors->has('correocontador') ? 'is-invalid' : '' }}" name="correocontador"
                        id="correocontador" value="{{ old('correocontador', $licencia->correocontador) }}"
                        {{ !puede('pc', 'editar_correos_' . strtolower($accion)) ? 'disabled' : '' }} />
                    @if ($errors->has('correocontador'))
                        <span class="text-danger">{{ $errors->first('correocontador') }}</span>
                    @endif
                </div>
            </div>

            <!-- Respaldos -->
            <div class="tab-pane fade" id="respaldos" role="tabpanel">
                <div class="form-group row">
                    <label>Token Dropbox:</label>
                    <textarea rows="10" class="form-control {{ $errors->has('tokenrespaldo') ? 'is-invalid' : '' }}" name="tokenrespaldo" id="tokenrespaldo"
                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>{{ $licencia->tokenrespaldo }}</textarea>
                    <button type="button" class="btn btn-primary mt-2" onclick="copiarAlPortapapeles()">Copiar</button>
                    @if ($errors->has('tokenrespaldo'))
                        <span class="text-danger">{{ $errors->first('tokenrespaldo') }}</span>
                    @endif
                </div>
            </div>

            <!-- Bloqueos -->
            <div class="tab-pane fade" id="bloqueos" role="tabpanel">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Motivo Bloqueo:</label>
                        <input class="form-control {{ $errors->has('motivobloqueo') ? 'is-invalid' : '' }}" name="motivobloqueo"
                            id="motivobloqueo" value="{{ old('motivobloqueo', $licencia->motivobloqueo) }}"
                            {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                        @if ($errors->has('motivobloqueo'))
                            <span class="text-danger">{{ $errors->first('motivobloqueo') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Mensaje Entrar al Sistema:</label>
                        <input class="form-control {{ $errors->has('mensaje') ? 'is-invalid' : '' }}" name="mensaje" id="mensaje"
                            value="{{ old('mensaje', $licencia->mensaje) }}"
                            {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                        @if ($errors->has('mensaje'))
                            <span class="text-danger">{{ $errors->first('mensaje') }}</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label>Observaciones:</label>
                        <input class="form-control {{ $errors->has('observacion') ? 'is-invalid' : '' }}" name="observacion" id="observacion"
                            value="{{ old('observacion', $licencia->observacion) }}"
                            {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                        @if ($errors->has('observacion'))
                            <span class="text-danger">{{ $errors->first('observacion') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Aplicaciones -->
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

    <!-- Tab Soporte -->
    <div class="tab-pane fade" role="tabpanel" id="soporte">
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Plan de Soporte:</label>
                <span class="switch switch-outline switch-icon switch-primary switch-sm">
                    <label>
                        <input @if ($licencia->plan_soporte == 1) checked @endif type="checkbox" name="plan_soporte" id="plan_soporte"
                            {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                        <span></span>
                    </label>
                </span>
            </div>
            <div class="col-lg-4">
                <label>Fecha Caducidad Plan Soporte:</label>
                <input type="text" class="form-control {{ $errors->has('fechacaduca_soporte') ? 'is-invalid' : '' }}"
                    name="fechacaduca_soporte" id="fechacaduca_soporte" value="{{ old('fechacaduca_soporte', $licencia->fechacaduca_soporte) }}"
                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('fechacaduca_soporte'))
                    <span class="text-danger">{{ $errors->first('fechacaduca_soporte') }}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-4">
                <label>Numero Tickets Totales:</label>
                <input type="text" class="form-control {{ $errors->has('numero_tickets_totales') ? 'is-invalid' : '' }}"
                    name="numero_tickets_totales" id="numero_tickets_totales"
                    value="{{ old('numero_tickets_totales', $licencia->numero_tickets_totales) }}"
                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('numero_tickets_totales'))
                    <span class="text-danger">{{ $errors->first('numero_tickets_totales') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Numero Tickets Mensuales:</label>
                <input type="text" class="form-control {{ $errors->has('numero_tickets_mensuales') ? 'is-invalid' : '' }}"
                    name="numero_tickets_mensuales" id="numero_tickets_mensuales"
                    value="{{ old('numero_tickets_mensuales', $licencia->numero_tickets_mensuales) }}"
                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }} />
                @if ($errors->has('numero_tickets_mensuales'))
                    <span class="text-danger">{{ $errors->first('numero_tickets_mensuales') }}</span>
                @endif
            </div>
            <div class="col-lg-4">
                <label>Tickets Utilizados:</label>
                <input type="text" class="form-control" value="{{ $licencia->tickets_utilizados }}" readonly />
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
        // Configuración desde PHP
        const configuracionPC = @json(config('sistema.productos.pc'));

        function copiarAlPortapapeles() {
            var textarea = document.getElementById("tokenrespaldo");
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            document.execCommand("copy");
        }

        $(document).ready(function() {
            inicializarFormulario();
            inicializarEventos();

            $('#formulario').on('submit', function(e) {
                prepararModulosParaEnvio();
                $(this).find('input, select, textarea').removeAttr('disabled');
                return true;
            });
        });

        function inicializarFormulario() {
            if ($("#nube").prop("checked")) {
                mostrarDivNube(true);
            }

            inicializarDatepickers();
            inicializarDataTable();
        }

        function inicializarEventos() {
            $("#renovarmensual").click(() => confirmarAccion('mes', "¿Está seguro de Renovar la Licencia?"));
            $("#renovaranual").click(() => confirmarAccion('anual', "¿Está seguro de Renovar la Licencia?"));
            $("#renovaractualizacion").click(() => confirmarAccion('actualizacion', "¿Está seguro de Renovar la Licencia?"));
            $("#periodo").change(cambiarComboPC);
            inicializarEventosCheckboxes();
        }

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

        function cambiarComboPC() {
            const fecha = new Date();
            const periodo = $("#periodo").val();
            const configuracionPeriodo = configuracionPC.periodos[periodo];

            if (configuracionPeriodo) {
                const fechaCaduca = new Date(fecha);
                fechaCaduca.setMonth(fechaCaduca.getMonth() + configuracionPeriodo.meses);
                $("#fechacaduca").val(formatearFecha(fechaCaduca));

                const fechaActualizaciones = new Date(fecha);
                fechaActualizaciones.setMonth(fechaActualizaciones.getMonth() + configuracionPeriodo.meses_actualizaciones);
                $("#fechaactulizaciones").val(formatearFecha(fechaActualizaciones));
            }
        }

        function inicializarEventosCheckboxes() {
            const modulosPrincipales = ["practico", "control", "contable", "nube"];
            const modulosAdicionales = Object.keys(configuracionPC.modulos_adicionales);

            modulosPrincipales.forEach(modulo => {
                $("#" + modulo).click(() => toggleModuloPrincipal(modulo));
            });

            modulosAdicionales.forEach(modulo => {
                $("#" + modulo).click(() => toggleModuloAdicional(modulo));
            });
        }

        function toggleModuloPrincipal(modulo) {
            const estado = $("#" + modulo).prop("checked");

            // Desmarcar otros módulos principales
            const modulosPrincipales = ["practico", "control", "contable", "nube"];
            modulosPrincipales.forEach(mod => {
                if (mod !== modulo) {
                    $("#" + mod).prop("checked", false);
                }
            });

            // Limpiar aplicativos
            $("#aplicativos input[type='checkbox']").prop("checked", false);

            if (estado) {
                const config = configuracionPC.modulos_principales[modulo];
                if (config) {
                    // Configurar equipos
                    $("#numeroequipos").val(config.equipos);
                    $("#numeromoviles").val(config.moviles);
                    $("#numerosucursales").val(config.sucursales);

                    // Activar módulos incluidos
                    if (config.incluye_nomina) {
                        $("#nomina").prop("checked", true);
                        actualizarCheckboxesPorModulo('nomina', true);
                    }
                    if (config.incluye_activos) {
                        $("#activos").prop("checked", true);
                        actualizarCheckboxesPorModulo('activos', true);
                    }

                    // Activar aplicativos del módulo y sus dependencias
                    activarModuloConDependencias(modulo);
                }

                // Mostrar/ocultar configuración de nube
                mostrarDivNube(modulo === "nube");
            } else {
                mostrarDivNube(false);
            }
        }

        function activarModuloConDependencias(modulo) {
            const config = configuracionPC.modulos_principales[modulo];

            // Activar aplicativos del módulo actual
            if (config.ids_aplicativos) {
                config.ids_aplicativos.forEach(id => {
                    $("#" + id).prop("checked", true);
                });
            }

            // Activar aplicativos de módulos padre si hereda
            if (config.hereda_de) {
                activarModuloConDependencias(config.hereda_de);
            }
        }

        function toggleModuloAdicional(modulo) {
            const estado = $("#" + modulo).prop("checked");
            actualizarCheckboxesPorModulo(modulo, estado);
        }

        function actualizarCheckboxesPorModulo(modulo, estado) {
            const config = configuracionPC.modulos_adicionales[modulo];
            if (config && config.ids_aplicativos) {
                config.ids_aplicativos.forEach(id => {
                    $("#" + id).prop("checked", estado);
                });
            }
        }

        function mostrarDivNube(estado) {
            $("#div_nube").toggle(estado);
            if (estado && configuracionPC.configuracion_nube) {
                $("#usuarios_nube").val(configuracionPC.configuracion_nube.usuarios_defecto);
            }
        }

        function prepararModulosParaEnvio() {
            const modulosSeleccionados = [];

            // Recopilar IDs de aplicativos marcados
            $('#aplicativos input[type="checkbox"]:checked').each(function() {
                const id = $(this).attr('id');
                if (id && id !== '') {
                    modulosSeleccionados.push(id);
                }
            });

            // Eliminar duplicados y ordenar
            const modulosUnicos = [...new Set(modulosSeleccionados)];
            modulosUnicos.sort((a, b) => parseInt(a) - parseInt(b));

            $('#permisos').val(modulosUnicos.join(';') + ';');
        }

        function formatearFecha(fecha) {
            return `${("0" + fecha.getDate()).slice(-2)}-${("0" + (fecha.getMonth() + 1)).slice(-2)}-${fecha.getFullYear()}`;
        }
    </script>
@endsection
