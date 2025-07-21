@extends('admin.layouts.app')
@section('contenido')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form class="form"
                              action="{{ route('notificaciones.actualizar', $notificaciones->sis_notificacionesid) }}"
                              method="POST">
                            @method('PUT')
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label"><i class="fas fa-bell text-primary mr-3"></i>Editar Notificación
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <!-- Grupo principal de botones -->
                                        <div class="btn-group" role="group">
                                            <!-- Botón Volver -->
                                            <a href="{{ route('notificaciones.index') }}"
                                               class="btn btn-secondary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="bottom"
                                               title="Volver a las notificaciones">
                                                <i class="fas fa-arrow-left mr-2"></i>
                                                <span class="d-none d-sm-inline">Volver</span>
                                            </a>
                                        </div>

                                        <!-- Grupo de acciones principales -->
                                        <div class="btn-group ml-2" role="group">
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
                                            <a href="{{ route('notificaciones.crear') }}"
                                               class="btn btn-primary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="bottom"
                                               title="Crear nueva notificación">
                                                <i class="fas fa-plus mr-2"></i>
                                                <span class="d-none d-lg-inline">Nueva</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @include('admin.notificaciones._form')
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
