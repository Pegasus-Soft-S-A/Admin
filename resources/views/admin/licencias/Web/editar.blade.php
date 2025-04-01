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
                                        <h3 class="card-label">Licencia Web </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                            <div class="btn-group" role="group" aria-label="First group">

                                                <a href="{{ route('clientes.editar', $cliente->sis_clientesid) }}" class="btn btn-secondary btn-icon"
                                                    data-toggle="tooltip" title="Volver"><i class="la la-long-arrow-left"></i></a>

                                                @if (Auth::user()->tipo == 1 || Auth::user()->tipo == 2)
                                                    <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip" title="Guardar"><i
                                                            class="la la-save"></i></button>

                                                    <a href="{{ route('licencias.Web.crear', $cliente->sis_clientesid) }}"
                                                        class="btn btn-warning btn-icon" data-toggle="tooltip" title="Nuevo"><i
                                                            class="la la-user-plus"></i></a>
                                                @endif

                                                @if (($licencia->producto == 9 || $licencia->producto == 6 || $licencia->producto == 10) && Auth::user()->tipo == 1)
                                                    <a id="recargar" href="#" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                        title="Recargar 120 Documentos"><i class="la la-file-medical"></i></a>
                                                @endif

                                                @if ($licencia->producto == 10 && Auth::user()->tipo == 1)
                                                    <a id="recargar240" href="#" class="btn btn-warning btn-icon" data-toggle="tooltip"
                                                        title="Recargar 240 Documentos"><i class="la la-file-medical"></i></a>
                                                @endif

                                                @if (Auth::user()->tipo != 4 && Auth::user()->tipo != 8)
                                                    <a href="{{ route('licencias.Web.enviaremail', [$cliente->sis_clientesid, $licencia->producto]) }}"
                                                        class="btn btn-primary btn-icon" data-toggle="tooltip" title="Enviar Email"><i
                                                            class="socicon-mail"></i></a>

                                                    <a id="resetear" href="#" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                        title="Resetear Clave"><i class="la la-user-lock"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('admin.licencias.Web._form')
                                </div>

                                <div class="card-footer pt-2 pb-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="font-size-sm font-weight-bolder text-dark ml-2">Auditoría</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="font-size-sm font-weight-bolder text-dark ml-2">Creación</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $licencia->usuariocreacion }}</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $licencia->fechacreacion }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="font-size-sm font-weight-bolder text-dark ml-2">Modificación</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $licencia->usuariomodificacion }}</span>
                                            <span class="font-size-sm text-primary ml-2">{{ $licencia->fechamodificacion }}</span>
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
