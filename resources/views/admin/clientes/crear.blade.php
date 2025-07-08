@extends('admin.layouts.app')
@section('contenido')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form class="form" action="{{ route('clientes.guardar') }}" method="POST">
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label font-weight-bold text-dark">
                                            <i class="fas fa-user-plus text-primary mr-3"></i>Nuevo Cliente
                                        </h3>
                                    </div>

                                    <div class="card-toolbar">
                                        <div class="btn-group" role="group">
                                            <!-- Botón Volver -->
                                            <a href="{{ route('clientes.index') }}"
                                               class="btn btn-secondary font-weight-bold"
                                               data-toggle="tooltip"
                                               data-placement="bottom"
                                               title="Volver al listado de clientes">
                                                <i class="fas fa-arrow-left mr-2"></i>
                                                <span class="d-none d-sm-inline">Volver</span>
                                            </a>

                                            <!-- Botón Guardar -->
                                            <button type="submit"
                                                    class="btn btn-success font-weight-bold"
                                                    data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="Guardar la información del cliente"
                                                    id="btn-guardar">
                                                <i class="fas fa-save mr-2"></i>
                                                <span class="d-none d-sm-inline">Guardar Cliente</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">

                                    @include('admin.clientes._form')

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
