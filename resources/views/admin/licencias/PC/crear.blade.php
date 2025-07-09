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
                                        <h3 class="card-label font-weight-bold text-dark">
                                            <i class="fas fa-desktop text-primary mr-3"></i>Nueva Licencia PC
                                            <small class="text-muted d-block font-size-sm">
                                                Cliente: {{ $cliente->nombres }} ({{ $cliente->identificacion }})
                                            </small>
                                        </h3>
                                    </div>

                                    <div class="card-toolbar">
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

                                            <!-- Botón Guardar -->
                                            <button type="submit"
                                                    class="btn btn-success font-weight-bold"
                                                    data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="Guardar la licencia PC"
                                                    id="btn-guardar">
                                                <i class="fas fa-save mr-2"></i>
                                                <span class="d-none d-sm-inline">Guardar Licencia</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @include('admin.licencias.PC._form')
                                </div>

                                <!--end::Card-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
