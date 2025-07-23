@extends('admin.layouts.app')
@section('contenido')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form class="form" action="{{ route('licencias.Vps.actualizar', [$licencia->sis_licenciasid]) }}" method="POST"
                              id="formulario">
                            @method('PUT')
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bold text-dark">
                                            <i class="fas fa-server text-primary mr-3"></i>Editar Licencia PC
                                            <small class="text-dark d-block font-size-sm">
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
                                            @if (puede('vps', 'crear_vps'))
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
                                                <a href="{{ route('licencias.Vps.crear', $cliente->sis_clientesid) }}"
                                                   class="btn btn-primary font-weight-bold"
                                                   data-toggle="tooltip"
                                                   data-placement="bottom"
                                                   title="Crear nueva licencia VPS">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    <span class="d-none d-lg-inline">Nueva</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">
                                    @include('admin.licencias.Vps._form')
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
