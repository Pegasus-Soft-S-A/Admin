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
                        <form class="form" action="{{ route('licencias.Pc.guardar') }}" method="POST" id="formulario">
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label">Licencia PC </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                            <div class="btn-group" role="group" aria-label="">
                                                <a href="{{ route('clientes.editar', $cliente->sis_clientesid) }}" class="btn btn-secondary btn-icon"
                                                    data-toggle="tooltip" title="Volver"><i class="la la-long-arrow-left"></i></a>

                                                <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip" title="Guardar"><i
                                                        class="la la-save"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">

                                    @include('admin.licencias.PC._form')

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
