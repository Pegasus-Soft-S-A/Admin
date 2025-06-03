@php
    $rol = Auth::user()->tipo;
    // Definir constantes de roles
    define('ROL_ADMIN', 1);
    define('ROL_DISTRIBUIDOR', 2);
    define('ROL_SOPORTE_DISTRIBUIDOR', 3);
    define('ROL_SOPORTE_MATRIZ', 7);
    define('ROL_VENTAS', 4);
    define('ROL_POSVENTA', 9);
@endphp
@extends('admin.layouts.app')
@section('contenido')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form class="form" action="{{ route('licencias.Pc.actualizar', $licencia->sis_licenciasid) }}" method="POST" id="formulario">
                            @method('PUT')
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label">Licencia PC </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                            <div class="btn-group" role="group" aria-label="First group">

                                                <a href="{{ route('clientes.editar', $cliente->sis_clientesid) }}" class="btn btn-secondary btn-icon"
                                                    data-toggle="tooltip" title="Volver"><i class="la la-long-arrow-left"></i></a>

                                                @if (puede('pc', 'guardar_pc'))
                                                    <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip" title="Guardar"><i
                                                            class="la la-save"></i></button>
                                                @endif

                                                @if (puede('pc', 'crear_pc'))
                                                    <a href="{{ route('licencias.Pc.crear', $cliente->sis_clientesid) }}" class="btn btn-warning btn-icon"
                                                        data-toggle="tooltip" title="Nuevo"><i class="la la-user-plus"></i></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @include('admin.licencias.PC._form')
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
