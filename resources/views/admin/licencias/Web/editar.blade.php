@extends('admin.layouts.app')
@section('contenido')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form class="form" action="{{ route('licencias.Web.actualizar', [$licencia->sis_servidoresid, $licencia->sis_licenciasid]) }}"
                              method="POST" id="formulario">
                            @method('PUT')
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bold text-dark">
                                            <i class="fas fa-cloud text-primary mr-3"></i>Editar Licencia Web
                                            <small class="text-muted d-block font-size-sm">
                                                Cliente: {{ $cliente->nombres }} ({{ $cliente->identificacion }})
                                            </small>
                                        </h3>
                                    </div>

                                    <div class="card-toolbar">
                                        <!-- Grupo principal de botones -->
                                        <div class="btn-group" role="group">
                                            <!-- Botón Volver -->
                                            <a href="{{ route('clientes.editar', $cliente->sis_clientesid) }}"
                                               class="btn btn-secondary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="bottom"
                                               title="Volver al cliente">
                                                <i class="fas fa-arrow-left mr-2"></i>
                                                <span class="d-none d-sm-inline">Volver</span>
                                            </a>
                                        </div>

                                        <!-- Grupo de acciones principales -->
                                        <div class="btn-group ml-2" role="group">
                                            @if (puede('web', 'crear_web'))
                                                <!-- Botón Guardar -->
                                                <button type="submit"
                                                        class="btn btn-success font-weight-bold"
                                                        data-toggle="tooltip"
                                                        data-placement="bottom"
                                                        title="Guardar los cambios realizados"
                                                        id="btn-guardar">
                                                    <i class="fas fa-save mr-2"></i>
                                                    <span class="d-none d-sm-inline">Guardar Cambios</span>
                                                </button>

                                                <!-- Botón Nueva Licencia -->
                                                <a href="{{ route('licencias.Web.crear', $cliente->sis_clientesid) }}"
                                                   class="btn btn-primary font-weight-bold"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom"
                                                   title="Crear nueva licencia web">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    <span class="d-none d-lg-inline">Nueva</span>
                                                </a>
                                            @endif
                                        </div>

                                        @php
                                            $tienePermisosResetear = puede('web', 'resetear_clave_web');
                                            $puedeRecargar = (($licencia->producto == 9 || $licencia->producto == 6 || $licencia->producto == 10) && Auth::user()->tipo == 1);
                                            $mostrarDropdown = $tienePermisosResetear || $puedeRecargar;
                                        @endphp

                                        @if ($mostrarDropdown)
                                            <!-- Grupo de acciones específicas de Web -->
                                            <div class="btn-group ml-2" role="group">
                                                <!-- Dropdown de Acciones Web -->
                                                <div class="btn-group" role="group">
                                                    <button type="button"
                                                            class="btn btn-light-primary font-weight-bold dropdown-toggle"
                                                            data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false"
                                                            data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="Acciones específicas de licencia web">
                                                        <i class="fas fa-cogs mr-2"></i>
                                                        <span class="d-none d-lg-inline">Acciones Web</span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right shadow-lg">
                                                        <!-- Sección: Comunicación -->
                                                        <h6 class="dropdown-header">
                                                            <i class="fas fa-envelope mr-2 text-primary"></i>Comunicación
                                                        </h6>

                                                        @if ($tienePermisosResetear)
                                                            <a class="dropdown-item d-flex align-items-center"
                                                               href="{{ route('licencias.Web.enviarEmail', [$cliente->sis_clientesid, $licencia->producto]) }}">
                                                                <i class="fas fa-paper-plane text-info mr-3"></i>Enviar Credenciales por Email
                                                            </a>

                                                            <a class="dropdown-item d-flex align-items-center"
                                                               href="#" id="resetear">
                                                                <i class="fas fa-key text-success mr-3"></i>Resetear Clave de Usuario
                                                            </a>
                                                        @endif

                                                        <!-- Sección: Recargas (solo para productos específicos) -->
                                                        @if ($puedeRecargar)
                                                            <div class="dropdown-divider"></div>
                                                            <h6 class="dropdown-header">
                                                                <i class="fas fa-plus-circle mr-2 text-warning"></i>Recargas de Documentos
                                                            </h6>

                                                            <a class="dropdown-item d-flex align-items-center"
                                                               href="#" id="recargar">
                                                                <i class="fas fa-file-medical text-success mr-3"></i>Recargar 120 Documentos
                                                            </a>

                                                            @if ($licencia->producto == 10)
                                                                <a class="dropdown-item d-flex align-items-center"
                                                                   href="#" id="recargar240">
                                                                    <i class="fas fa-file-medical text-warning mr-3"></i>Recargar 240 Documentos
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body">
                                    @include('admin.licencias.Web._form')
                                </div>

                                <!-- Footer con información de auditoría -->
                                <div class="card-footer bg-light py-2">
                                    <div class="row align-items-center mb-0">
                                        <div class="col-md-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-history text-primary mr-2"></i>
                                                <small class="font-weight-bold text-dark mb-0">Auditoría</small>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-plus text-success mr-2"></i>
                                                <div>
                                                    <small class="font-weight-bold text-dark mb-0">Creado:</small>
                                                    <small class="text-muted ml-1">
                                                        {{ $licencia->usuariocreacion }}
                                                        - {{ date('d/m/Y H:i', strtotime($licencia->fechacreacion)) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-edit text-warning mr-2"></i>
                                                <div>
                                                    <small class="font-weight-bold text-dark mb-0">Modificado:</small>
                                                    <small class="text-muted ml-1">
                                                        {{ $licencia->usuariomodificacion ?: 'Sin modificaciones' }}
                                                        @if($licencia->fechamodificacion)
                                                            - {{ date('d/m/Y H:i', strtotime($licencia->fechamodificacion)) }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
