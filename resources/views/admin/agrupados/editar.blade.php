@extends('admin.layouts.app')
@section('contenido')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="d-flex flex-column-fluid">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <!--begin::Card-->
                        <form class="form"
                            action="{{ route('agrupados.actualizar', $agrupados->sis_agrupadosid) }}" method="POST">
                            @method('PUT')
                            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                                <div class="card-header flex-wrap py-5">
                                    <div class="card-title">
                                        <h3 class="card-label">Agrupados </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                            <div class="btn-group" role="group" aria-label="First group">

                                                <a href="{{ route('agrupados.index') }}"
                                                    class="btn btn-secondary btn-icon" data-toggle="tooltip"
                                                    title="Volver"><i class="la la-long-arrow-left"></i></a>

                                                <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                    title="Guardar"><i class="la la-save"></i></button>

                                                <a href="{{ route('agrupados.crear') }}" class="btn btn-warning btn-icon"
                                                    data-toggle="tooltip" title="Nuevo"><i class="la la-user-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    @include('admin.agrupados._form')
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
