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

    .custom-switch-productos {
        transform: scale(2);
        transform-origin: center;
    }

    .custom-switch-datos {
        transform: scale(1.5);
        transform-origin: left center;
    }

    .tab-content {
        overflow-x: hidden;
    }

    .input-mobile {
        border-left: 4px solid #28a745 !important;
    }

    .input-sales {
        border-left: 4px solid #fd7e14 !important;
    }
</style>

@php
    $accion = isset($licencia->sis_licenciasid) ? 'Modificar' : 'Crear';
@endphp

{{-- Alertas de errores --}}
@if ($errors->has('correopropietario') || $errors->has('correoadministrador') || $errors->has('correocontador'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle text-white"></i> Errores de validación</h5>
        @if ($errors->has('correopropietario'))
            <p class="mb-1">{{ $errors->first('correopropietario') }}</p>
        @endif
        @if ($errors->has('correoadministrador'))
            <p class="mb-1">{{ $errors->first('correoadministrador') }}</p>
        @endif
        @if ($errors->has('correocontador'))
            <p class="mb-0">{{ $errors->first('correocontador') }}</p>
        @endif
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Campos ocultos --}}
<input type="hidden" name="sis_distribuidoresid" value="{{ $licencia->sis_distribuidoresid }}">
<input type="hidden" name="tipo" id="tipo">
<input type="hidden" id="permisos" name="aplicaciones_permisos" value="{{ $licencia->aplicaciones }}">
<input type="hidden" value="{{ $cliente->sis_clientesid }}" name="sis_clientesid">

{{-- Navegación principal --}}
<ul class="nav nav-tabs nav-tabs-line mb-5">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#datos_licencia">
            <span class="nav-icon"><i class="fas fa-info-circle"></i></span>
            <span class="nav-text">Datos Licencia</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#aplicaciones">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span>
            <span class="nav-text">Aplicaciones</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#soporte">
            <span class="nav-icon"><i class="fas fa-life-ring"></i></span>
            <span class="nav-text">Soporte</span>
        </a>
    </li>
    @if($accion=="Modificar")
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#recursos_adicionales">
                <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>
                <span class="nav-text">Recursos Adicionales</span>
            </a>
        </li>
    @endif
</ul>

<div class="tab-content">
    {{-- TAB: Datos Licencia --}}
    <div class="tab-pane fade show active" id="datos_licencia" role="tabpanel" aria-labelledby="datos_licencia">
        {{-- Información básica --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-file-contract"></i> Información Básica</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Número Contrato</label>
                    <input type="text" class="form-control @error('numerocontrato') is-invalid @enderror"
                           name="numerocontrato" id="numerocontrato"
                           value="{{ old('numerocontrato', $licencia->numerocontrato) }}" readonly>
                    @error('numerocontrato')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Fecha Caducidad</label>
                    <div class="input-group">
                        <input type="text" class="form-control @error('fechacaduca') is-invalid @enderror"
                               name="fechacaduca" id="fechacaduca"
                               value="{{ old('fechacaduca', $licencia->fechacaduca) }}"
                            {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                        @if (isset($licencia->sis_licenciasid) && $licencia->periodo != 3)
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    {{ !puede('pc', 'renovar_licencia') ? 'disabled' : '' }}>
                                    <i class="fas fa-sync-alt"></i> Renovar
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" id="renovarmensual">
                                        Renovar Mensual
                                    </a>
                                    <a class="dropdown-item" href="#" id="renovaranual">
                                        Renovar Anual
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    @error('fechacaduca')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control @error('estado') is-invalid @enderror" name="estado" id="estado"
                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                        <option value="1" {{ old('estado', $licencia->estado) == '1' ? 'selected' : '' }}>
                            <i class="fas fa-check-circle"></i> Activo
                        </option>
                        <option value="2" {{ old('estado', $licencia->estado) == '2' ? 'selected' : '' }}>
                            <i class="fas fa-clock"></i> Pendiente de pago
                        </option>
                        <option value="3" {{ old('estado', $licencia->estado) == '3' ? 'selected' : '' }}>
                            <i class="fas fa-times-circle"></i> Inactivo
                        </option>
                    </select>
                    @error('estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Configuración técnica --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-server"></i> Configuración Técnica</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Identificador Servidor</label>
                    <input type="text" class="form-control @error('Identificador') is-invalid @enderror"
                           name="Identificador" id="Identificador"
                           value="{{ old('Identificador', $licencia->Identificador) }}"
                        {{ !puede('pc', 'editar_configuracion_tecnica_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('Identificador')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>IP Servidor Local</label>
                    <input type="text" class="form-control @error('ipservidor') is-invalid @enderror"
                           name="ipservidor" id="ipservidor"
                           value="{{ old('ipservidor', $licencia->ipservidor) }}"
                        {{ !puede('pc', 'editar_configuracion_tecnica_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('ipservidor')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>IP Servidor Remoto</label>
                    <input type="text" class="form-control @error('ipservidorremoto') is-invalid @enderror"
                           name="ipservidorremoto" id="ipservidorremoto"
                           value="{{ old('ipservidorremoto', $licencia->ipservidorremoto) }}"
                        {{ !puede('pc', 'editar_configuracion_tecnica_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('ipservidorremoto')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Configuración de recursos --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-cogs"></i> Configuración de Recursos</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>N° Equipos</label>
                    <input type="text" class="form-control @error('numeroequipos') is-invalid @enderror"
                           name="numeroequipos" id="numeroequipos"
                           value="{{ old('numeroequipos', $licencia->numeroequipos) }}"
                        {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('numeroequipos')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>App Móvil</label>
                    <input type="text" class="form-control input-mobile @error('numeromoviles') is-invalid @enderror"
                           name="numeromoviles" id="numeromoviles"
                           value="{{ old('numeromoviles', $licencia->numeromoviles) }}"
                        {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('numeromoviles')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>App Ventas</label>
                    <input type="text" class="form-control input-sales @error('numeromoviles2') is-invalid @enderror"
                           name="numeromoviles2" id="numeromoviles2"
                           value="{{ old('numeromoviles2', $licencia->numeromoviles2) }}"
                        {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('numeromoviles2')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>N° Sucursales</label>
                    <input type="text" class="form-control @error('numerosucursales') is-invalid @enderror"
                           name="numerosucursales" id="numerosucursales"
                           value="{{ old('numerosucursales', $licencia->numerosucursales) }}"
                        {{ !puede('pc', 'editar_numeros_configuracion_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('numerosucursales')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Configuración de puertos y base de datos --}}
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-database"></i> Configuración de Puertos y Base de Datos</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Puerto BD</label>
                    <input type="text" class="form-control @error('puerto') is-invalid @enderror"
                           name="puerto" id="puerto"
                           value="{{ old('puerto', $licencia->puerto) }}"
                        {{ !puede('pc', 'editar_puerto_bd_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('puerto')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Puerto Móvil</label>
                    <input type="text" class="form-control @error('puertows') is-invalid @enderror"
                           name="puertows" id="puertows"
                           value="{{ old('puertows', $licencia->puertows) }}"
                           maxlength="4" pattern="\d{1,4}"
                        {{ !puede('pc', 'editar_puerto_movil_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('puertows')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Usuario BD</label>
                    <input type="text" class="form-control @error('usuario') is-invalid @enderror"
                           name="usuario" id="usuario"
                           value="{{ old('usuario', $licencia->usuario) }}"
                        {{ !puede('pc', 'editar_bd_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('usuario')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Clave BD</label>
                    <input type="text" class="form-control @error('clave') is-invalid @enderror"
                           name="clave" id="clave"
                           value="{{ old('clave', $licencia->clave) }}"
                        {{ !puede('pc', 'editar_bd_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('clave')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Configuración Nube --}}
        <div id="div_nube" style="display: none;">
            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-cloud"></i> Configuración Nube</p>
            <div class="separator separator-dashed mb-2"></div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select class="form-control @error('tipo_nube') is-invalid @enderror"
                                name="tipo_nube" id="tipo_nube"
                            {{ !puede('pc', 'editar_nube_' . strtolower($accion)) ? 'disabled' : '' }}>
                            <option value="1" {{ old('tipo_nube', $licencia->tipo_nube) == '1' ? 'selected' : '' }}>Prime</option>
                            <option value="2" {{ old('tipo_nube', $licencia->tipo_nube) == '2' ? 'selected' : '' }}>Contaplus</option>
                        </select>
                        @error('tipo_nube')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Nivel</label>
                        <select class="form-control @error('nivel_nube') is-invalid @enderror"
                                name="nivel_nube" id="nivel_nube"
                            {{ !puede('pc', 'editar_nube_' . strtolower($accion)) ? 'disabled' : '' }}>
                            <option value="1" {{ old('nivel_nube', $licencia->nivel_nube) == '1' ? 'selected' : '' }}>Nivel 1</option>
                            <option value="2" {{ old('nivel_nube', $licencia->nivel_nube) == '2' ? 'selected' : '' }}>Nivel 2</option>
                            <option value="3" {{ old('nivel_nube', $licencia->nivel_nube) == '3' ? 'selected' : '' }}>Nivel 3</option>
                        </select>
                        @error('nivel_nube')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Usuarios</label>
                        <input type="text" class="form-control @error('usuarios_nube') is-invalid @enderror"
                               name="usuarios_nube" id="usuarios_nube"
                               value="{{ old('usuarios_nube', $licencia->usuarios_nube) }}"
                            {{ !puede('pc', 'editar_nube_' . strtolower($accion)) ? 'disabled' : '' }}>
                        @error('usuarios_nube')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Usuarios Activos</label>
                        <input type="text" class="form-control"
                               value="{{ $licencia->usuarios_activos }}" readonly>
                    </div>
                </div>
            </div>

        </div>

        {{-- Información de empresas (solo en modificar) --}}
        @if ($accion == 'Modificar')
            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-building"></i> Información de Empresas</p>
            <div class="separator separator-dashed mb-2"></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Empresas Activas</label>
                        <input type="text" class="form-control"
                               value="{{ $empresas->empresas_activas }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Empresas Inactivas</label>
                        <input type="text" class="form-control"
                               value="{{ $empresas->empresas_inactivas }}" readonly>
                    </div>
                </div>
            </div>
        @endif


        <ul class="nav nav-tabs nav-tabs-line mb-5">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#aplicaciones_principales">
                    <span class="nav-icon"><i class="fas fa-th-large"></i></span>
                    <span class="nav-text">Aplicaciones Principales</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#modulos_adicionales">
                    <span class="nav-icon"><i class="fas fa-puzzle-piece"></i></span>
                    <span class="nav-text">Módulos Adicionales</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#correos">
                    <span class="nav-icon"><i class="fas fa-envelope"></i></span>
                    <span class="nav-text">Correos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#respaldos">
                    <span class="nav-icon"><i class="fas fa-cloud-upload-alt"></i></span>
                    <span class="nav-text">Respaldos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#bloqueos">
                    <span class="nav-icon"><i class="fas fa-lock"></i></span>
                    <span class="nav-text">Bloqueos</span>
                </a>
            </li>
        </ul>
        <div class="tab-content mt-10">
            <div class="tab-content">
                {{-- TAB: Aplicaciones Principales --}}
                <div class="tab-pane fade show active" id="aplicaciones_principales" role="tabpanel"
                     aria-labelledby="aplicaciones_principales">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-desktop fa-2x text-primary mb-2"></i>
                                    <h6 class="card-title">Sistema Perseo Práctico</h6>
                                    <div class="custom-control custom-switch custom-switch-productos">
                                        <input type="checkbox" class="custom-control-input" id="practico"
                                               name="modulopractico" {{ $licencia->modulopractico == 1 ? 'checked' : '' }}
                                            {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="practico"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-cog fa-2x text-info mb-2"></i>
                                    <h6 class="card-title">Sistema Perseo Control</h6>
                                    <div class="custom-control custom-switch custom-switch-productos">
                                        <input type="checkbox" class="custom-control-input" id="control"
                                               name="modulocontrol" {{ $licencia->modulocontrol == 1 ? 'checked' : '' }}
                                            {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="control"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-calculator fa-2x text-warning mb-2"></i>
                                    <h6 class="card-title">Sistema Perseo Contable</h6>
                                    <div class="custom-control custom-switch custom-switch-productos">
                                        <input type="checkbox" class="custom-control-input" id="contable"
                                               name="modulocontable" {{ $licencia->modulocontable == 1 ? 'checked' : '' }}
                                            {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="contable"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-cloud fa-2x text-success mb-2"></i>
                                    <h6 class="card-title">Sistema Perseo Nube</h6>
                                    <div class="custom-control custom-switch custom-switch-productos">
                                        <input type="checkbox" class="custom-control-input" id="nube"
                                               name="modulonube" {{ $licencia->modulonube == 1 ? 'checked' : '' }}
                                            {{ !puede('pc', 'editar_modulos_principales_' . strtolower($accion)) ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="nube"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Actualizaciones Automáticas</label>
                                <div class="custom-control custom-switch custom-switch-datos">
                                    <input type="checkbox" class="custom-control-input" id="actualiza"
                                           name="actulizaciones" {{ $licencia->actulizaciones == 1 ? 'checked' : '' }}
                                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="actualiza"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Pagado Actualizaciones</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('fechaactulizaciones') is-invalid @enderror"
                                           name="fechaactulizaciones" id="fechaactulizaciones"
                                           value="{{ old('fechaactulizaciones', $licencia->fechaactulizaciones) }}"
                                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                                    @if (isset($licencia->sis_licenciasid) && $licencia->periodo == 3)
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="renovaractualizacion"
                                                {{ !puede('pc', 'renovar_licencia') ? 'disabled' : '' }}>
                                                <i class="fas fa-sync-alt"></i> Renovar Anual
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('fechaactulizaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Periodo</label>
                                <select class="form-control @error('periodo') is-invalid @enderror"
                                        name="periodo" id="periodo"
                                    {{ !puede('pc', 'editar_periodo_' . strtolower($accion)) ? 'disabled' : '' }}>
                                    <option value="1" {{ old('periodo', $licencia->periodo) == '1' ? 'selected' : '' }}>Mensual</option>
                                    <option value="2" {{ old('periodo', $licencia->periodo) == '2' ? 'selected' : '' }}>Anual</option>
                                    <option value="3" {{ old('periodo', $licencia->periodo) == '3' ? 'selected' : '' }}>Venta</option>
                                </select>
                                @error('periodo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Precio</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fas fa-dollar-sign text-white"></i>
                                    </span>
                                    </div>
                                    <input type="text" class="form-control text-success font-weight-bold"
                                           id="precio" name="precio" autocomplete="off" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Clave de Activación</label>
                                <textarea rows="15" class="form-control @error('key') is-invalid @enderror"
                                          name="key" id="key" readonly>{{ $licencia->key }}</textarea>
                                @error('key')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                {{-- TAB: Modulos Adicionales --}}
                <div class="tab-pane fade show" id="modulos_adicionales" role="tabpanel"
                     aria-labelledby="modulos_adicionales">
                    @php
                        $modulosAdicionales = [
                            ['name' => 'nomina', 'label' => 'Nómina', 'icon' => 'fas fa-users', 'value' => $modulos[0]->nomina ?? false],
                            ['name' => 'activos', 'label' => 'Activos Fijos', 'icon' => 'fas fa-boxes', 'value' => $modulos[0]->activos ?? false],
                            ['name' => 'produccion', 'label' => 'Producción', 'icon' => 'fas fa-industry', 'value' => $modulos[0]->produccion ?? false],
                            ['name' => 'tvcable', 'label' => 'TV-Cable e Internet', 'icon' => 'fas fa-tv', 'value' => $modulos[0]->operadoras ?? false],
                            ['name' => 'encomiendas', 'label' => 'Servicio de Encomiendas', 'icon' => 'fas fa-shipping-fast', 'value' => $modulos[0]->encomiendas ?? false],
                            ['name' => 'crmcartera', 'label' => 'CRM de Cartera', 'icon' => 'fas fa-chart-line', 'value' => $modulos[0]->crm_cartera ?? false],
                            ['name' => 'apiwhatsapp', 'label' => 'API Whatsapp', 'icon' => 'fab fa-whatsapp', 'value' => $modulos[0]->api_whatsapp ?? false],
                            ['name' => 'hybrid', 'label' => 'Perseo Hybrid', 'icon' => 'fas fa-code-branch', 'value' => $modulos[0]->perseo_hybrid ?? false],
                            ['name' => 'woocomerce', 'label' => 'Plugin Woocomerce', 'icon' => 'fab fa-wordpress', 'value' => $modulos[0]->tienda_woocommerce ?? false],
                            ['name' => 'tienda', 'label' => 'Tienda Perseo', 'icon' => 'fas fa-store', 'value' => $modulos[0]->tienda_perseo_publico ?? false],
                            ['name' => 'restaurante', 'label' => 'Restaurantes', 'icon' => 'fas fa-utensils', 'value' => $modulos[0]->restaurante ?? false],
                            ['name' => 'garantias', 'label' => 'Servicio Técnico/Garantías', 'icon' => 'fas fa-tools', 'value' => $modulos[0]->garantias ?? false],
                            ['name' => 'talleres', 'label' => 'Servicio Técnico Talleres Vehículos', 'icon' => 'fas fa-car', 'value' => $modulos[0]->talleres ?? false],
                            ['name' => 'integraciones', 'label' => 'Integraciones', 'icon' => 'fas fa-plug', 'value' => $modulos[0]->tienda_perseo_distribuidor ?? false],
                            ['name' => 'cashmanager', 'label' => 'Cash Manager', 'icon' => 'fas fa-cash-register', 'value' => $modulos[0]->cash_manager ?? false],
                            ['name' => 'cashdebito', 'label' => 'Cash Debito', 'icon' => 'fas fa-credit-card', 'value' => $modulos[0]->cash_debito ?? false],
                            ['name' => 'equifax', 'label' => 'Reporte Equifax', 'icon' => 'fas fa-file-invoice', 'value' => $modulos[0]->reporte_equifax ?? false],
                            ['name' => 'ahorros', 'label' => 'Caja Ahorros', 'icon' => 'fas fa-piggy-bank', 'value' => $modulos[0]->caja_ahorros ?? false],
                            ['name' => 'academico', 'label' => 'Académico', 'icon' => 'fas fa-graduation-cap', 'value' => $modulos[0]->academico ?? false],
                            ['name' => 'perseo_contador', 'label' => 'Perseo Contador', 'icon' => 'fas fa-calculator', 'value' => $modulos[0]->perseo_contador ?? false],
                            ['name' => 'api_urbano', 'label' => 'API Urbano', 'icon' => 'fas fa-city', 'value' => $modulos[0]->api_urbano ?? false],
                        ];
                    @endphp

                    <div class="row">
                        @foreach($modulosAdicionales as $modulo)
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="form-group">
                                    <div class="card card-custom card-border">
                                        <div class="card-body text-center">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="{{ $modulo['icon'] }} fa-lg text-primary mr-2"></i>
                                                    <span class="font-weight-bold">{{ $modulo['label'] }}</span>
                                                </div>
                                                <div class="custom-control custom-switch custom-switch-datos">
                                                    <input type="checkbox" class="custom-control-input modulo-checkbox"
                                                           id="{{ $modulo['name'] }}" name="{{ $modulo['name'] }}"
                                                        {{ $modulo['value'] == 1 ? 'checked' : '' }}
                                                        {{ !puede('web', 'editar_modulos') ? 'disabled' : '' }}>
                                                    <label class="custom-control-label" for="{{ $modulo['name'] }}"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- TAB: Correos --}}
                <div class="tab-pane fade show" id="correos" role="tabpanel"
                     aria-labelledby="correos">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-user-tie"></i> Correo Propietario
                                </label>
                                <input type="email" class="form-control @error('correopropietario') is-invalid @enderror"
                                       name="correopropietario" id="correopropietario"
                                       value="{{ old('correopropietario', $licencia->correopropietario) }}"
                                    {{ !puede('pc', 'editar_correos_' . strtolower($accion)) ? 'disabled' : '' }}>
                                @error('correopropietario')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-user-cog"></i> Correo Administrador
                                </label>
                                <input type="email" class="form-control @error('correoadministrador') is-invalid @enderror"
                                       name="correoadministrador" id="correoadministrador"
                                       value="{{ old('correoadministrador', $licencia->correoadministrador) }}"
                                    {{ !puede('pc', 'editar_correos_' . strtolower($accion)) ? 'disabled' : '' }}>
                                @error('correoadministrador')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-user-check"></i> Correo Contador
                                </label>
                                <input type="email" class="form-control @error('correocontador') is-invalid @enderror"
                                       name="correocontador" id="correocontador"
                                       value="{{ old('correocontador', $licencia->correocontador) }}"
                                    {{ !puede('pc', 'editar_correos_' . strtolower($accion)) ? 'disabled' : '' }}>
                                @error('correocontador')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                {{-- TAB: Respaldos --}}
                <div class="tab-pane fade show" id="respaldos" role="tabpanel"
                     aria-labelledby="respaldos">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <i class="fab fa-dropbox"></i> Token Dropbox
                                </label>
                                <textarea rows="10" class="form-control @error('tokenrespaldo') is-invalid @enderror"
                                          name="tokenrespaldo" id="tokenrespaldo"
                                              {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>{{ $licencia->tokenrespaldo }}</textarea>
                                <button type="button" class="btn btn-outline-primary mt-2" onclick="copiarAlPortapapeles()">
                                    <i class="fas fa-copy"></i> Copiar al Portapapeles
                                </button>
                                @error('tokenrespaldo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                {{-- TAB: Bloqueos --}}
                <div class="tab-pane fade show" id="bloqueos" role="tabpanel"
                     aria-labelledby="bloqueos">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-ban"></i> Motivo Bloqueo
                                </label>
                                <input type="text" class="form-control @error('motivobloqueo') is-invalid @enderror"
                                       name="motivobloqueo" id="motivobloqueo"
                                       value="{{ old('motivobloqueo', $licencia->motivobloqueo) }}"
                                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                                @error('motivobloqueo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-exclamation-triangle"></i> Mensaje Entrar al Sistema
                                </label>
                                <input type="text" class="form-control @error('mensaje') is-invalid @enderror"
                                       name="mensaje" id="mensaje"
                                       value="{{ old('mensaje', $licencia->mensaje) }}"
                                    {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                                @error('mensaje')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-sticky-note"></i> Observaciones
                                </label>
                                <textarea rows="3" class="form-control @error('observacion') is-invalid @enderror"
                                          name="observacion" id="observacion"
                                              {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>{{ old('observacion', $licencia->observacion) }}</textarea>
                                @error('observacion')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {{-- TAB: Aplicaciones --}}
    <div class="tab-pane fade show" id="aplicaciones" role="tabpanel" aria-labelledby="aplicaciones">
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-th-large"></i>Gestión de Aplicaciones</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="aplicativos">
                <thead>
                <tr>
                    <th>Categorias ID</th>
                    <th>ID</th>
                    <th>Opciones</th>
                    <th>Activo</th>
                </tr>
                </thead>
                <tbody>
                {{-- Se llena dinámicamente --}}
                </tbody>
            </table>
        </div>
    </div>
    {{-- TAB: Soporte --}}
    <div class="tab-pane fade show" id="soporte" role="tabpanel" aria-labelledby="soporte">
        <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-life-ring"></i> Configuración de Soporte</p>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Plan de Soporte</label>
                    <div class="custom-control custom-switch custom-switch-datos">
                        <input type="checkbox" class="custom-control-input" id="plan_soporte"
                               name="plan_soporte" {{ $licencia->plan_soporte == 1 ? 'checked' : '' }}
                            {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                        <label class="custom-control-label" for="plan_soporte"></label>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Fecha Caducidad Plan Soporte</label>
                    <input type="text" class="form-control @error('fechacaduca_soporte') is-invalid @enderror"
                           name="fechacaduca_soporte" id="fechacaduca_soporte"
                           value="{{ old('fechacaduca_soporte', $licencia->fechacaduca_soporte) }}"
                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('fechacaduca_soporte')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <h6><i class="fas fa-ticket-alt"></i> Información de Tickets</h6>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Número Tickets Totales</label>
                    <input type="text" class="form-control @error('numero_tickets_totales') is-invalid @enderror"
                           name="numero_tickets_totales" id="numero_tickets_totales"
                           value="{{ old('numero_tickets_totales', $licencia->numero_tickets_totales) }}"
                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('numero_tickets_totales')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Número Tickets Mensuales</label>
                    <input type="text" class="form-control @error('numero_tickets_mensuales') is-invalid @enderror"
                           name="numero_tickets_mensuales" id="numero_tickets_mensuales"
                           value="{{ old('numero_tickets_mensuales', $licencia->numero_tickets_mensuales) }}"
                        {{ !puede('pc', 'editar_avanzados_' . strtolower($accion)) ? 'disabled' : '' }}>
                    @error('numero_tickets_mensuales')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tickets Utilizados</label>
                    <input type="text" class="form-control" value="{{ $licencia->tickets_utilizados }}" readonly>
                </div>
            </div>
        </div>
        <h6><i class="fas fa-info-circle"></i> Información del Sistema</h6>
        <div class="separator separator-dashed mb-2"></div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Versión Ejecutable</label>
                    <input type="text" class="form-control" value="{{ $licencia->version_ejecutable }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Fecha Actualización Ejecutable</label>
                    <input type="text" class="form-control" value="{{ $licencia->fecha_actualizacion_ejecutable }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Fecha Respaldo</label>
                    <input type="text" class="form-control" value="{{ $licencia->fecha_respaldo }}" readonly>
                </div>
            </div>
        </div>
    </div>
    {{-- TAB: Recursos Adicionales --}}
    <div class="tab-pane fade show" id="recursos_adicionales" role="tabpanel" aria-labelledby="recursos_adicionales">
        <div class="d-flex justify-content-between align-items-center">
            <p class="font-size-lg font-weight-bold mb-1"><i class="fas fa-plus-circle"></i> Agregar Recursos Adicionales</p>
            <div class="text-right">
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-dark d-block">Total:</h6>
                        <strong id="total_general_recursos" class="text-success">$0.00</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator separator-dashed mb-2"></div>
        <!-- Mensaje cuando no hay módulo seleccionado -->
        <div class="alert alert-warning" id="mensaje_sin_modulo" style="display: none;">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h6 class="alert-heading">Seleccione un módulo principal</h6>
                    <p class="mb-0">Para ver los recursos adicionales disponibles, debe seleccionar un módulo principal (Práctico,
                        Control,
                        Contable o Nube) en la pestaña "Datos Licencia".</p>
                </div>
            </div>
        </div>

        <!-- Formularios dinámicos -->
        <div id="formularios_adicionales_container" style="display: none;">
            <div class="row" id="formularios_adicionales_row">
                <!-- Se llenan dinámicamente -->
            </div>
        </div>
    </div>
</div>
@section('script')
    <script>
        // ====================================
        // CONFIGURACIÓN GLOBAL SIMPLIFICADA
        // ====================================

        const AppConfigPC = {
            //  Toda la configuración en una sola variable
            configuracion: @json(config('sistema')),

            // Configuración de formulario
            accion: "{{ $accion }}",

            // Permisos completos
            permisos: {
                editarAdicionales: @json(puede('pc', 'editar_adicionales_' . strtolower($accion))),
            },

            // URLs
            rutas: {
                subcategorias: "{{ route('subcategorias') }}",
                obtenerAdicionales: "{{ route('licencias.obtener-adicionales') }}",
                agregarAdicional: "{{ route('licencias.agregar-adicional') }}"
            }
        };

        // ====================================
        // GESTOR PRINCIPAL DEL FORMULARIO
        // ====================================

        const FormularioLicenciaPC = {
            init() {
                this.inicializarFormulario();
                this.configurarEventos();
                this.inicializarDatepickers();
                this.configurarEnvioFormulario();
            },

            inicializarFormulario() {
                // Mostrar configuración de nube si está activa
                if ($("#nube").prop("checked")) {
                    ConfiguracionNube.mostrar(true);
                }

                GestorPrecios.inicializar();
                GestorAplicativos.inicializar();
            },

            configurarEventos() {
                // Eventos de renovación
                $("#renovarmensual").on('click', () =>
                    this.confirmarAccion('mes', "¿Está seguro de Renovar la Licencia?"));
                $("#renovaranual").on('click', () =>
                    this.confirmarAccion('anual', "¿Está seguro de Renovar la Licencia?"));
                $("#renovaractualizacion").on('click', () =>
                    this.confirmarAccion('actualizacion', "¿Está seguro de Renovar la Licencia?"));

                // Eventos de cambio de período
                $("#periodo").on('change', () => {
                    this.cambiarPeriodo();
                    GestorPrecios.actualizar();
                });

                // Eventos de configuración de nube
                $("#tipo_nube").on('change', function () {
                    ConfiguracionNube.actualizarTipo($(this).val());
                    GestorPrecios.actualizar();
                });

                $("#nivel_nube").on('change', () => {
                    GestorPrecios.actualizar();
                    ConfiguracionNube.actualizarValores();
                });

                // Eventos para campos que afectan recursos adicionales
                $("#numeromoviles, #numerosucursales, #numeroequipos, #usuarios").on('input change', () => {
                    RecursosAdicionalesPC.cargarCantidadesActuales();
                    RecursosAdicionalesPC.actualizarDisplay();
                });

                //  Configurar módulos dinámicamente
                this.configurarEventosModulos();
            },

            //  Configurar eventos de módulos usando configuración
            configurarEventosModulos() {
                const modulosPrincipales = Object.keys(AppConfigPC.configuracion.productos.pc.modulos_principales);
                const modulosAdicionales = Object.keys(AppConfigPC.configuracion.productos.pc.modulos_adicionales);

                // Eventos módulos principales
                modulosPrincipales.forEach(modulo => {
                    $("#" + modulo).on('click', () => {
                        this.toggleModuloPrincipal(modulo);
                    });
                });

                // Eventos módulos adicionales
                modulosAdicionales.forEach(modulo => {
                    $("#" + modulo).on('click', () => {
                        this.toggleModuloAdicional(modulo);
                    });
                });
            },

            //  Mantener lógica original de módulos principales
            toggleModuloPrincipal(modulo) {
                const estado = $("#" + modulo).prop("checked");

                if (estado) {
                    // Desmarcar otros módulos principales
                    const modulosPrincipales = Object.keys(AppConfigPC.configuracion.productos.pc.modulos_principales);
                    modulosPrincipales.forEach(mod => {
                        if (mod !== modulo) {
                            $("#" + mod).prop("checked", false);
                        }
                    });
                    this.limpiarModulosAdicionales();
                    this.aplicarConfiguracionModulo(modulo);
                } else {
                    this.limpiarConfiguracion();
                }

                // Actualizar recursos adicionales
                setTimeout(() => {
                    RecursosAdicionalesPC.actualizarTodo();
                }, 100);
            },

            //  Aplicar configuración específica del módulo usando config dinámico
            aplicarConfiguracionModulo(modulo) {
                const config = AppConfigPC.configuracion.productos.pc.modulos_principales[modulo];
                if (!config) return;

                // Limpiar aplicativos antes de aplicar nueva configuración
                GestorAplicativos.limpiarSeleccion();

                // Configurar recursos
                this.configurarRecursos(config);

                // Activar módulos incluidos
                this.activarModulosIncluidos(config);

                // Activar aplicativos del módulo
                this.activarAplicativos(modulo);

                // Configuración específica de nube
                if (modulo === "nube") {
                    ConfiguracionNube.mostrar(true);
                } else {
                    ConfiguracionNube.mostrar(false);
                }

                // Actualizar precio
                GestorPrecios.actualizar();
            },

            //  Configurar recursos según el módulo usando config
            configurarRecursos(config) {
                if (config.equipos) $("#numeroequipos").val(config.equipos);
                if (config.moviles) $("#numeromoviles").val(config.moviles);
                if (config.sucursales) $("#numerosucursales").val(config.sucursales);
            },

            //  Activar módulos incluidos usando config
            activarModulosIncluidos(config) {
                if (config.incluye_nomina) {
                    $("#nomina").prop("checked", true);
                    this.actualizarCheckboxesPorModulo('nomina', true);
                }
                if (config.incluye_activos) {
                    $("#activos").prop("checked", true);
                    this.actualizarCheckboxesPorModulo('activos', true);
                }
                if (config.incluye_produccion) {
                    $("#produccion").prop("checked", true);
                    this.actualizarCheckboxesPorModulo('produccion', true);
                }
            },

            //  Activar aplicativos del módulo usando config
            activarAplicativos(modulo) {
                this.activarModuloConDependencias(modulo);
            },

            //  Activar módulo con sus dependencias usando config
            activarModuloConDependencias(modulo) {
                const config = AppConfigPC.configuracion.productos.pc.modulos_principales[modulo];
                if (!config) return;

                // Activar aplicativos del módulo actual
                if (config.ids_aplicativos) {
                    config.ids_aplicativos.forEach(id => {
                        $("#" + id).prop("checked", true);
                    });
                }

                // Activar aplicativos de módulos padre si hereda
                if (config.hereda_de) {
                    this.activarModuloConDependencias(config.hereda_de);
                }
            },

            //  Alternar módulo adicional usando config
            toggleModuloAdicional(modulo) {
                const estado = $("#" + modulo).prop("checked");
                this.actualizarCheckboxesPorModulo(modulo, estado);
            },

            //  Actualizar checkboxes por módulo usando config
            actualizarCheckboxesPorModulo(modulo, estado) {
                const config = AppConfigPC.configuracion.productos.pc.modulos_adicionales[modulo];
                if (config && config.ids_aplicativos) {
                    config.ids_aplicativos.forEach(id => {
                        $("#" + id).prop("checked", estado);
                    });
                }
            },

            //  Limpiar configuración
            limpiarConfiguracion() {
                ConfiguracionNube.mostrar(false);
                GestorPrecios.limpiar();
                this.limpiarModulosAdicionales();
            },

            //  Obtener módulo actualmente seleccionado
            obtenerModuloActivo() {
                const modulosPrincipales = Object.keys(AppConfigPC.configuracion.productos.pc.modulos_principales);
                return modulosPrincipales.find(modulo =>
                    $("#" + modulo).prop("checked")
                ) || null;
            },

            //  Limpiar todos los módulos adicionales
            limpiarModulosAdicionales() {
                const modulosAdicionales = Object.keys(AppConfigPC.configuracion.productos.pc.modulos_adicionales);
                modulosAdicionales.forEach(modulo => {
                    $("#" + modulo).prop("checked", false);
                    this.actualizarCheckboxesPorModulo(modulo, false);
                });
            },

            inicializarDatepickers() {
                $("#fechacaduca, #fechaactulizaciones, #fechacaduca_soporte").datepicker({
                    language: "es",
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: {
                        leftArrow: '<i class="la la-angle-left"></i>',
                        rightArrow: '<i class="la la-angle-right"></i>'
                    }
                });
            },

            cambiarPeriodo() {
                const fecha = new Date();
                const periodo = $("#periodo").val();
                const configuracionPeriodo = AppConfigPC.configuracion.productos.pc.periodos[periodo];

                if (configuracionPeriodo) {
                    // Actualizar fecha de caducidad
                    const fechaCaduca = new Date(fecha);
                    fechaCaduca.setMonth(fechaCaduca.getMonth() + configuracionPeriodo.meses);
                    $("#fechacaduca").val(this.formatearFecha(fechaCaduca));

                    // Actualizar fecha de actualizaciones
                    const fechaActualizaciones = new Date(fecha);
                    fechaActualizaciones.setMonth(fechaActualizaciones.getMonth() + configuracionPeriodo.meses_actualizaciones);
                    $("#fechaactulizaciones").val(this.formatearFecha(fechaActualizaciones));
                }
            },

            configurarEnvioFormulario() {
                $('#formulario').on('submit', (e) => {
                    GestorAplicativos.prepararParaEnvio();
                    $(e.target).find('input, select, textarea').removeAttr('disabled');

                    this.mostrarSpinnerGuardar();

                    // Programar actualización de adicionales después del envío
                    setTimeout(() => {
                        RecursosAdicionalesPC.actualizarDespuesDeGuardar();
                    }, 2000);

                    return true;
                });
            },

            mostrarSpinnerGuardar() {
                const btnGuardar = $('button[type="submit"]');
                const textoOriginal = btnGuardar.html() || btnGuardar.val();

                // Guardar texto original y mostrar spinner
                btnGuardar.data('texto-original', textoOriginal);
                btnGuardar.prop('disabled', true);
                btnGuardar.html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

            },

            confirmarAccion(tipo, mensaje) {
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
            },

            formatearFecha(fecha) {
                const date = new Date(fecha);
                return `${("0" + date.getDate()).slice(-2)}-${("0" + (date.getMonth() + 1)).slice(-2)}-${date.getFullYear()}`;
            },

            copiarAlPortapapeles() {
                const textarea = document.getElementById("tokenrespaldo");
                textarea.select();
                textarea.setSelectionRange(0, 99999);
                document.execCommand("copy");

                Swal.fire({
                    title: "Copiado",
                    text: "Token copiado al portapapeles",
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        };

        // ====================================
        // GESTOR DE PRECIOS DINÁMICO
        // ====================================

        const GestorPrecios = {
            inicializar() {
                this.actualizar();
            },

            actualizar() {
                const moduloActivo = FormularioLicenciaPC.obtenerModuloActivo();

                if (!moduloActivo) {
                    this.limpiar();
                    return;
                }

                const config = AppConfigPC.configuracion.productos.pc.modulos_principales[moduloActivo];
                if (!config) {
                    this.limpiar();
                    return;
                }

                let precio = 0;

                if (moduloActivo === "nube") {
                    precio = this.calcularPrecioNube(config);
                } else {
                    precio = this.calcularPrecioModulo(config);
                }

                $("#precio").val(precio);
            },

            calcularPrecioNube(config) {
                const tipoNube = $("#tipo_nube").val() || '1';
                const nivelNube = $("#nivel_nube").val() || '1';

                const tipoNubeNombre = tipoNube === '1' ? 'prime' : 'contaplus';
                const nivelNubeNombre = 'nivel' + nivelNube;

                if (config.precios &&
                    config.precios[tipoNubeNombre] &&
                    config.precios[tipoNubeNombre][nivelNubeNombre]) {
                    return config.precios[tipoNubeNombre][nivelNubeNombre];
                }

                return 0;
            },

            calcularPrecioModulo(config) {
                const periodo = $("#periodo").val() || '2';
                const periodoNombre = this.obtenerNombrePeriodo(periodo);

                if (config.precios && config.precios[periodoNombre]) {
                    return config.precios[periodoNombre];
                }

                return 0;
            },

            obtenerNombrePeriodo(periodo) {
                const mapaPeriodos = {
                    '1': 'mensual',
                    '2': 'anual',
                    '3': 'venta'
                };
                return mapaPeriodos[periodo] || 'anual';
            },

            limpiar() {
                $("#precio").val('0');
            }
        };

        // ====================================
        // GESTOR DE APLICATIVOS (CORREGIDO)
        // ====================================

        const GestorAplicativos = {
            dataTable: null,

            inicializar() {
                this.configurarDataTable();
            },

            //  CORREGIDO: Problema con .api()
            configurarDataTable() {
                this.dataTable = $('#aplicativos').DataTable({
                    responsive: true,
                    serverSide: true,
                    searching: false,
                    paging: false,
                    ajax: AppConfigPC.rutas.subcategorias,
                    drawCallback: (settings) => {
                        this.procesarFilasAgrupadas();
                    },
                    columns: [
                        {
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
                    initComplete: () => {
                        this.cargarPermisosExistentes();
                    }
                });
            },

            //  CORREGIDO: Usar directamente this.dataTable sin .api()
            procesarFilasAgrupadas() {
                const rows = this.dataTable.rows({page: 'current'}).nodes();
                let last = null;

                this.dataTable.column(0, {page: 'current'}).data().each((group, i) => {
                    if (last !== group) {
                        $(rows).eq(i).before(`<tr class="group"><td colspan="3">${group}</td></tr>`);
                        last = group;
                    }
                });
            },

            cargarPermisosExistentes() {
                const permisos = $("#permisos").val();
                if (permisos) {
                    const array = permisos.split(';');
                    array.forEach(id => {
                        if (id) $("#" + id).prop('checked', true);
                    });
                }
            },

            limpiarSeleccion() {
                $("#aplicativos input[type='checkbox']").prop("checked", false);
            },

            prepararParaEnvio() {
                const modulosSeleccionados = [];

                $('#aplicativos input[type="checkbox"]:checked').each(function () {
                    const id = $(this).attr('id');
                    if (id && id !== '') {
                        modulosSeleccionados.push(id);
                    }
                });

                const modulosUnicos = [...new Set(modulosSeleccionados)];
                modulosUnicos.sort((a, b) => parseInt(a) - parseInt(b));

                $('#permisos').val(modulosUnicos.join(';') + ';');
            }
        };

        // ====================================
        // CONFIGURACIÓN DE NUBE
        // ====================================

        const ConfiguracionNube = {
            mostrar(estado) {
                $("#div_nube").toggle(estado);

                if (estado && AppConfigPC.configuracion.productos.pc.configuracion_nube) {
                    this.aplicarConfiguracionInicial();
                }
            },

            aplicarConfiguracionInicial() {
                const usuariosActuales = parseInt($("#usuarios").val()) || 0;

                if (usuariosActuales === 0) {
                    const tipoNube = $("#tipo_nube").val() || '1';
                    const usuarios = AppConfigPC.configuracion.productos.pc.configuracion_nube.usuarios_por_tipo[tipoNube] || 4;
                    $("#usuarios").val(usuarios);
                }

                GestorPrecios.actualizar();
            },

            actualizarTipo(tipoNube) {
                const usuariosActuales = parseInt($("#usuarios").val()) || 0;

                if (usuariosActuales === 0) {
                    const usuarios = AppConfigPC.configuracion.productos.pc.configuracion_nube.usuarios_por_tipo[tipoNube] || 4;
                    $("#usuarios").val(usuarios);
                }

                this.actualizarValores();
            },

            actualizarValores() {
                const moduloActivo = FormularioLicenciaPC.obtenerModuloActivo();
                if (moduloActivo) {
                    RecursosAdicionalesPC.generarFormularios();
                }
            }
        };

        // ====================================
        // RECURSOS ADICIONALES PC (MANTENIENDO FUNCIONALIDAD COMPLETA)
        // ====================================

        const RecursosAdicionalesPC = {
            cantidadesAdicionales: {},
            moduloSeleccionado: null,

            init() {
                this.inicializarCantidades();
                this.configurarEventos();
                this.actualizarTodo();
            },

            inicializarCantidades() {
                const todosLosTipos = new Set();

                Object.values(AppConfigPC.configuracion.productos.pc.modulos_principales).forEach(moduloConfig => {
                    if (moduloConfig.adicionales) {
                        moduloConfig.adicionales.forEach(tipo => todosLosTipos.add(tipo));
                    }
                });

                todosLosTipos.forEach(tipo => {
                    this.cantidadesAdicionales[tipo] = 0;
                });
            },

            configurarEventos() {
                $(document).on('input change', '.cantidad-input', () => {
                    this.actualizarDisplay();
                });

                $(document).on('click', '.btn-agregar-recurso', (e) => {
                    const tipoId = $(e.target).closest('button').data('tipo-id');
                    this.agregarRecurso(tipoId);
                });
            },

            actualizarTodo() {
                this.cargarAdicionalesExistentes();
                this.detectarModuloSeleccionado();
                this.generarFormularios();
            },

            obtenerMapaCampos() {
                const mapaCampos = {};

                Object.keys(AppConfigPC.configuracion.tipos_adicionales).forEach(tipoId => {
                    const tipoConfig = AppConfigPC.configuracion.tipos_adicionales[tipoId];
                    if (tipoConfig.campo_licencia) {
                        mapaCampos[tipoId] = tipoConfig.campo_licencia;
                    }
                });

                return mapaCampos;
            },

            cargarCantidadesActuales() {
                // Mantener funcionalidad original
                const mapasCampos = this.obtenerMapaCampos();

                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    const campo = mapasCampos[tipoId];
                    if (campo) {
                        // No hacer nada aquí, solo mantener compatibilidad
                    }
                });
            },

            cargarAdicionalesExistentes() {
                const numerocontrato = $("#numerocontrato").val();
                if (!numerocontrato) return;

                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    this.cantidadesAdicionales[tipoId] = 0;
                });

                $.ajax({
                    url: AppConfigPC.rutas.obtenerAdicionales,
                    method: 'GET',
                    data: {numerocontrato: numerocontrato},
                    success: (response) => {
                        if (response.success && response.adicionales) {
                            response.adicionales.forEach(adicional => {
                                const tipoId = adicional.tipo_adicional;
                                if (this.cantidadesAdicionales.hasOwnProperty(tipoId)) {
                                    this.cantidadesAdicionales[tipoId] = parseInt(adicional.cantidad) || 0;
                                }
                            });
                            this.actualizarDisplay();
                        }
                    },
                    error: (xhr, status, error) => {
                        console.warn('No se pudieron cargar los adicionales existentes:', error);
                    }
                });
            },

            detectarModuloSeleccionado() {
                this.moduloSeleccionado = FormularioLicenciaPC.obtenerModuloActivo();

                if (this.moduloSeleccionado) {
                    $("#nombre_modulo_adicionales").text(
                        this.moduloSeleccionado.charAt(0).toUpperCase() + this.moduloSeleccionado.slice(1)
                    );
                } else {
                    $("#nombre_modulo_adicionales").text("Seleccione un módulo principal");
                }
            },

            generarFormularios() {
                const container = $("#formularios_adicionales_container");
                const row = $("#formularios_adicionales_row");
                const mensaje = $("#mensaje_sin_modulo");

                if (!this.moduloSeleccionado || !AppConfigPC.configuracion.productos.pc.modulos_principales[this.moduloSeleccionado]) {
                    container.hide();
                    mensaje.show();
                    return;
                }

                mensaje.hide();
                container.show();
                row.empty();

                const moduloConfig = AppConfigPC.configuracion.productos.pc.modulos_principales[this.moduloSeleccionado];
                const adicionalesPermitidos = moduloConfig.adicionales || [];

                if (adicionalesPermitidos.length === 0) {
                    row.append(this.crearMensajeSinAdicionales());
                    return;
                }

                adicionalesPermitidos.forEach(tipoId => {
                    if (AppConfigPC.configuracion.tipos_adicionales[tipoId]) {
                        const tipoConfig = AppConfigPC.configuracion.tipos_adicionales[tipoId];
                        row.append(this.crearFormularioTipo(tipoId, tipoConfig));
                    }
                });

                this.actualizarDisplay();
                this.actualizarTotalGeneral();
            },

            crearMensajeSinAdicionales() {
                return `
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle text-white"></i>
                    Este módulo no tiene recursos adicionales disponibles.
                </div>
            </div>
        `;
            },

            crearFormularioTipo(tipoId, tipoConfig) {
                const cantidades = this.calcularCantidades(tipoId);
                const precios = this.calcularPrecios(tipoId, tipoConfig, cantidades);
                const precioInfo = this.obtenerPrecioInfo(tipoId, tipoConfig);

                return `
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card border border-primary">
                    <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
                        <h6 class="card-title text-primary mb-0">
                            <i class="fa ${tipoConfig.icono} text-primary"></i> ${tipoConfig.nombre}
                        </h6>
                        <div class="text-right">
                            <small class="text-muted d-block">Valor Total</small>
                            <strong class="text-success" id="valor_total_${tipoId}">$${precios.valorTotal.toFixed(2)}</strong>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted font-size-sm mb-3">${tipoConfig.descripcion}</p>

                        ${this.crearSeccionCantidades(tipoId, cantidades)}
                        ${this.crearSeccionInput(tipoId)}
                        ${this.crearSeccionPrecios(tipoId, precioInfo)}
                    </div>
                </div>
            </div>
        `;
            },

            crearSeccionCantidades(tipoId, cantidades) {
                return `
            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted">Adicionales:</small>
                    <div class="font-weight-bold text-warning" id="cantidad_adicional_${tipoId}">${cantidades.adicional}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Total:</small>
                    <div class="font-weight-bold text-primary" id="cantidad_total_${tipoId}">${cantidades.total}</div>
                </div>
            </div>
        `;
            },

            crearSeccionInput(tipoId) {
                const disabled = !AppConfigPC.permisos.editarAdicionales ? 'disabled' : '';

                return `
            <div class="form-group">
                <label class="font-size-sm">Cantidad a Agregar:</label>
                <div class="input-group">
                    <input type="number" class="form-control text-center cantidad-input"
                           id="cantidad_${tipoId}"
                           data-tipo-id="${tipoId}"
                           min="0" max="100" value="0" placeholder="0">
                    <div class="input-group-append">
                        <button type="button"
                                class="btn btn-primary btn-agregar-recurso"
                                data-tipo-id="${tipoId}" ${disabled}>
                            <i class="fa fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        `;
            },

            crearSeccionPrecios(tipoId, precioInfo) {
                return `
            <div class="d-flex justify-content-between align-items-center">
                <div>${precioInfo}</div>
                <div class="text-right">
                    <small class="text-muted">Costo a agregar:</small>
                    <div class="font-weight-bold text-warning" id="costo_agregar_${tipoId}">$0.00</div>
                </div>
            </div>
        `;
            },

            calcularCantidades(tipoId) {
                const cantidadAdicional = this.cantidadesAdicionales[tipoId] || 0;

                return {
                    adicional: cantidadAdicional,
                    total: cantidadAdicional
                };
            },

            calcularPrecios(tipoId, tipoConfig, cantidades) {
                const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                const valorTotal = cantidades.adicional * precioUnitario;

                return {
                    unitario: precioUnitario,
                    valorTotal: valorTotal
                };
            },

            obtenerPrecioUnitario(tipoId, tipoConfig) {
                const periodo = $("#periodo").val() || '2';
                const periodoTexto = periodo == '1' ? 'mensual' : 'anual';

                switch (tipoConfig.precio_strategy) {
                    case 'simple':
                        return tipoConfig.precios.pc[periodoTexto] || 0;

                    case 'nube':
                        const tipoNube = $("#tipo_nube").val() || '1';
                        const nivelNube = $("#nivel_nube").val() || '1';
                        const tipoNubeTexto = tipoNube == '1' ? 'prime' : 'contaplus';
                        const nivelTexto = 'nivel' + nivelNube;

                        return tipoConfig.precios[tipoNubeTexto]?.[nivelTexto]?.[periodoTexto] || 0;

                    default:
                        return 0;
                }
            },

            obtenerPrecioInfo(tipoId, tipoConfig) {
                const precio = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                const periodo = $("#periodo").val() || '2';
                const periodoTexto = periodo == '1' ? 'mensual' : 'anual';

                if (precio === 0) {
                    return '<small class="text-success"><i class="fa fa-check"></i> Sin costo adicional</small>';
                }
                return `<small class="text-muted">Precio: $${precio}/${periodoTexto} c/u</small>`;
            },

            actualizarDisplay() {
                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    const cantidades = this.calcularCantidades(tipoId);
                    const inputCantidad = parseInt($(`#cantidad_${tipoId}`).val()) || 0;
                    const totalConInput = cantidades.adicional + inputCantidad;

                    this.actualizarElementoSiExiste(`cantidad_adicional_${tipoId}`, cantidades.adicional);
                    this.actualizarElementoSiExiste(`cantidad_total_${tipoId}`, totalConInput);

                    this.actualizarColorTotal(tipoId, inputCantidad);
                    this.actualizarPreciosDisplay(tipoId, cantidades, inputCantidad);
                });

                this.actualizarTotalGeneral();
            },

            actualizarElementoSiExiste(elementId, valor) {
                const elemento = $(`#${elementId}`);
                if (elemento.length) {
                    elemento.text(valor);
                }
            },

            actualizarColorTotal(tipoId, inputCantidad) {
                const elemento = $(`#cantidad_total_${tipoId}`);
                if (elemento.length) {
                    if (inputCantidad > 0) {
                        elemento.removeClass('text-primary').addClass('text-success');
                    } else {
                        elemento.removeClass('text-success').addClass('text-primary');
                    }
                }
            },

            actualizarPreciosDisplay(tipoId, cantidades, inputCantidad) {
                const valorElement = $(`#valor_total_${tipoId}`);
                const costoElement = $(`#costo_agregar_${tipoId}`);

                if (valorElement.length && costoElement.length) {
                    const tipoConfig = AppConfigPC.configuracion.tipos_adicionales[tipoId];
                    if (tipoConfig) {
                        const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                        const valorTotalActual = cantidades.adicional * precioUnitario;
                        const costoAgregar = inputCantidad * precioUnitario;

                        valorElement.text(`$${valorTotalActual.toFixed(2)}`);
                        costoElement.text(`$${costoAgregar.toFixed(2)}`);

                        if (inputCantidad > 0) {
                            costoElement.removeClass('text-warning').addClass('text-danger');
                        } else {
                            costoElement.removeClass('text-danger').addClass('text-warning');
                        }
                    }
                }
            },

            actualizarTotalGeneral() {
                let totalGeneral = 0;

                Object.keys(this.cantidadesAdicionales).forEach(tipoId => {
                    if (AppConfigPC.configuracion.tipos_adicionales[tipoId]) {
                        const cantidadAdicional = this.cantidadesAdicionales[tipoId] || 0;
                        const cantidadAgregar = parseInt($(`#cantidad_${tipoId}`).val()) || 0;
                        const totalAdicionales = cantidadAdicional + cantidadAgregar;

                        const tipoConfig = AppConfigPC.configuracion.tipos_adicionales[tipoId];
                        const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                        const valorTotal = totalAdicionales * precioUnitario;

                        totalGeneral += valorTotal;
                    }
                });

                const elemento = $("#total_general_recursos");
                elemento.text(`$${totalGeneral.toFixed(2)}`);

                if (totalGeneral > 0) {
                    elemento.removeClass('text-warning').addClass('text-success');
                } else {
                    elemento.removeClass('text-success').addClass('text-warning');
                }
            },

            agregarRecurso(tipoId) {
                const cantidad = parseInt($(`#cantidad_${tipoId}`).val());

                if (!cantidad || cantidad < 1) {
                    Swal.fire({
                        title: "Error",
                        text: "Debe ingresar una cantidad válida mayor a 0",
                        icon: "error"
                    });
                    return;
                }

                const datosAdicional = this.construirDatosAdicional(tipoId, cantidad);
                const btn = $(`.btn-agregar-recurso[data-tipo-id="${tipoId}"]`);
                const tipoNombre = AppConfigPC.configuracion.tipos_adicionales[tipoId].nombre;

                this.ejecutarAgregarRecurso(datosAdicional, btn, tipoId, cantidad, tipoNombre);
            },

            construirDatosAdicional(tipoId, cantidad) {
                const tipoConfig = AppConfigPC.configuracion.tipos_adicionales[tipoId];
                const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);

                return {
                    numerocontrato: $("#numerocontrato").val(),
                    fechainicia: new Date().toISOString().split('T')[0],
                    fechacaduca: $("#fechacaduca").val(),
                    tipo_adicional: tipoId,
                    tipo_licencia: 1, // PC
                    periodo: $("#periodo").val(),
                    cantidad: cantidad,
                    precio_unitario: precioUnitario,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
            },

            ejecutarAgregarRecurso(datosAdicional, btn, tipoId, cantidad, tipoNombre) {
                $.ajax({
                    url: AppConfigPC.rutas.agregarAdicional,
                    method: 'POST',
                    data: datosAdicional,
                    beforeSend: () => {
                        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
                    },
                    success: (response) => {
                        if (response.success) {
                            this.procesarExitoAgregar(tipoId, cantidad, tipoNombre, response);
                        } else {
                            this.mostrarError(response.message || "Ocurrió un error al agregar el recurso");
                        }
                    },
                    error: (xhr) => {
                        let errorMessage = "Error de comunicación con el servidor";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        this.mostrarError(errorMessage);
                    },
                    complete: () => {
                        btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Agregar');
                    }
                });
            },

            procesarExitoAgregar(tipoId, cantidad, tipoNombre, response) {
                // Actualizar cantidad en memoria
                this.cantidadesAdicionales[tipoId] += cantidad;

                // Actualizar TODOS los campos que fueron modificados
                if (response.licencia_actualizada) {
                    Object.keys(response.licencia_actualizada).forEach(campo => {
                        const elemento = $(`#${campo}`);
                        if (elemento.length) {
                            elemento.val(response.licencia_actualizada[campo]);
                        }
                    });
                }

                $(`#cantidad_${tipoId}`).val(0);
                this.actualizarDisplay();

                const tipoConfig = AppConfigPC.configuracion.tipos_adicionales[tipoId];
                const precioUnitario = this.obtenerPrecioUnitario(tipoId, tipoConfig);
                const valorAgregado = cantidad * precioUnitario;

                Swal.fire({
                    title: "¡Éxito!",
                    html: `
                <div class="text-center">
                    <p>Se agregaron <strong>${cantidad} ${tipoNombre.toLowerCase()}</strong> correctamente</p>
                    <p class="text-muted">Adicionales contratados: <strong>${this.cantidadesAdicionales[tipoId]}</strong></p>
                    <p class="text-success">Valor agregado: <strong>$${valorAgregado.toFixed(2)}</strong></p>
                </div>
            `,
                    icon: "success",
                    timer: 3000,
                    timerProgressBar: true
                });
            },

            mostrarError(mensaje) {
                Swal.fire({
                    title: "Error",
                    text: mensaje,
                    icon: "error"
                });
            },

            actualizarDespuesDeGuardar() {
                setTimeout(() => {
                    this.cargarAdicionalesExistentes();
                    this.actualizarDisplay();
                }, 1000);
            }
        };
        // ====================================
        // FUNCIÓN GLOBAL
        // ====================================

        function copiarAlPortapapeles() {
            FormularioLicenciaPC.copiarAlPortapapeles();
        }

        // ====================================
        // INICIALIZACIÓN
        // ====================================

        $(document).ready(function () {
            FormularioLicenciaPC.init();
            RecursosAdicionalesPC.init();
        });
    </script>
@endsection
