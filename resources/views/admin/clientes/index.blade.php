@extends('admin.layouts.app')
@section('contenido')

<style>
    #kt_datatable td {
        padding: 3px;
    }
</style>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->

                    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                        <div class="card-header ">
                            <div class="card-title">
                                <h3 class="card-label">Clientes</h3>

                            </div>
                            <div class="card-toolbar">

                                <a href="#" class="btn btn-primary font-weight-bolder" id="filtrar">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="la la-filter"></i>
                                    </span>Filtrar
                                </a>

                                <div class="dropdown dropdown-inline mr-2 ml-2">
                                    <button type="button"
                                        class="btn btn-md btn-light-primary font-weight-bolder dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                        d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                        fill="#000000" opacity="0.3" />
                                                    <path
                                                        d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                        fill="#000000" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>Exportar</button>
                                    <!--begin::Dropdown Menu-->
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <!--begin::Navigation-->
                                        <ul class="navi flex-column navi-hover py-2">
                                            <li
                                                class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                                Elija una opcion:</li>
                                            <li class="navi-item">
                                                <a href="#" class="navi-link" id="export_print">
                                                    <span class="navi-icon">
                                                        <i class="la la-print"></i>
                                                    </span>
                                                    <span class="navi-text">Imprimir</span>
                                                </a>
                                            </li>
                                            <li class="navi-item">
                                                <a href="#" class="navi-link" id="export_copy">
                                                    <span class="navi-icon">
                                                        <i class="la la-copy"></i>
                                                    </span>
                                                    <span class="navi-text">Copiar</span>
                                                </a>
                                            </li>
                                            <li class="navi-item">
                                                <a href="#" class="navi-link" id="export_excel">
                                                    <span class="navi-icon">
                                                        <i class="la la-file-excel-o"></i>
                                                    </span>
                                                    <span class="navi-text">Excel</span>
                                                </a>
                                            </li>
                                            <li class="navi-item">
                                                <a href="#" class="navi-link" id="export_pdf">
                                                    <span class="navi-icon">
                                                        <i class="la la-file-pdf-o"></i>
                                                    </span>
                                                    <span class="navi-text">PDF</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <!--end::Navigation-->
                                    </div>
                                    <!--end::Dropdown Menu-->
                                </div>

                                @if (Auth::user()->tipo != 4)
                                <a href="{{ route('clientes.crear') }}" class="btn btn-primary font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="flaticon2-plus-1"></i>
                                    </span>Nuevo
                                </a>
                                @endif
                            </div>

                        </div>
                        <div class="card-body">
                            <!--begin: Search Form-->
                            <div class="mb-15" id="filtro" style="display: none;">
                                <div class="row mb-8">
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Tipo Fecha:</label>
                                        <select class="form-control datatable-input" id="tipofecha">
                                            <option value="1">Fecha Inicio</option>
                                            <option value="2">Fecha Caduca</option>
                                            <option value="3">Fecha Actualizacion</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Tipo Licencia:</label>
                                        <select class="form-control datatable-input" id="tipolicencia">
                                            <option value="1">Todos</option>
                                            <option value="2">Web</option>
                                            <option value="3">PC</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Fecha:</label>
                                        <div class="input-group" id='kt_fecha'>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" autocomplete="off"
                                                placeholder="Rango de Fechas" id="fecha">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Periodo:</label>
                                        <select class="form-control datatable-input" id="periodo" name="periodo">
                                            <option id="" value="">Todos</option>
                                            <option id="periodo1" value="1">Mensual</option>
                                            <option id="periodo2" value="2">Anual</option>
                                            <option class="d-none" id="periodo3" value="3">Premium</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-8">
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Producto:</label>
                                        <select class="form-control datatable-input" id="producto" name="producto">
                                            <option value="">Todos</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Distribuidor:</label>
                                        <select class="form-control datatable-input select2" id="distribuidor"
                                            name="distribuidor">
                                            @if (Auth::user()->tipo == 1)
                                            <option value="">Todos</option>
                                            @else
                                            <option value="">Seleccione</option>
                                            @endif
                                            @foreach ($distribuidores as $distribuidor)
                                            <option value="{{ $distribuidor->sis_distribuidoresid }}">
                                                {{ $distribuidor->razonsocial }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Vendedor:</label>
                                        <select class="form-control datatable-input select2" id="vendedor"
                                            name="vendedor">
                                            <option value="">Todos</option>
                                            @foreach ($vendedores as $vendedor)
                                            <option value="{{ $vendedor->sis_revendedoresid }}">
                                                {{ $vendedor->razonsocial }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Provincias:</label>
                                        <select class="form-control datatable-input select2" id="provinciasid"
                                            name="provinciasid">
                                            <option value="">Seleccione una provincia</option>
                                            <option value="01">
                                                Azuay
                                            </option>
                                            <option value="02">
                                                Bolivar
                                            </option>
                                            <option value="03">
                                                Cañar
                                            </option>
                                            <option value="04">
                                                Carchi
                                            </option>
                                            <option value="05">
                                                Chimborazo
                                            </option>
                                            <option value="06">
                                                Cotopaxi
                                            </option>
                                            <option value="07">
                                                El Oro
                                            </option>
                                            <option value="08">
                                                Esmeraldas
                                            </option>
                                            <option value="09">
                                                Guayas
                                            </option>
                                            <option value="20">
                                                Galapagos
                                            </option>
                                            <option value="10">
                                                Imbabura
                                            </option>
                                            <option value="11">
                                                Loja</option>
                                            <option value="12">
                                                Los Rios
                                            </option>
                                            <option value="13">
                                                Manabi
                                            </option>
                                            <option value="14">
                                                Morona
                                                Santiago</option>
                                            <option value="15">
                                                Napo</option>
                                            <option value="22">
                                                Orellana
                                            </option>
                                            <option value="16">
                                                Pastaza
                                            </option>
                                            <option value="17">
                                                Pichincha
                                            </option>
                                            <option value="24">
                                                Santa Elena
                                            </option>
                                            <option value="23">
                                                Santo Domingo
                                                De Los Tsachilas</option>
                                            <option value="21">
                                                Sucumbios
                                            </option>
                                            <option value="18">
                                                Tungurahua
                                            </option>
                                            <option value="19">
                                                Zamora
                                                Chinchipe</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-8">
                                    @if (Auth::user()->tipo == 1)
                                    <div class="col-lg-3 mb-lg-0 mb-6">
                                        <label>Origen:</label>
                                        <select class="form-control datatable-input select2" id="origen" name="origen">
                                            @if (Auth::user()->tipo == 1)
                                            <option value="">Todos</option>
                                            @endif
                                            <option value="1">Perseo</option>
                                            <option value="2">Contafácil</option>
                                            <option value="3">UIO-01</option>
                                            <option value="8">UIO-02</option>
                                            <option value="5">GYE-02</option>
                                            <option value="6">CUE-01</option>
                                            <option value="7">STO-01</option>
                                            <option value="10">CNV-01</option>
                                            <option value="11">MATRIZ</option>
                                            <option value="12">CUE-02</option>
                                            <option value="13">CUE-03</option>
                                            <option value="14">UIO-03</option>
                                            <option value="15">UIO-04</option>
                                            <option value="16">UIO-05</option>
                                        </select>
                                    </div>
                                    @endif
                                </div>

                                <div class="row ">
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary btn-primary--icon" id="kt_search">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Buscar</span>
                                                <input type="hidden" name="buscar_filtro" id="buscar_filtro">
                                            </span>
                                        </button>&#160;&#160;
                                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reiniciar</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--begin: Datatable-->
                            <table class="table table-sm table-bordered table-head-custom table-hover"
                                id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="no-exportar">#</th>
                                        <th>Contrato</th>
                                        <th data-priority="1">Identificacion</th>
                                        <th data-priority="2">Nombres</th>
                                        <th data-priority="3">Distribuidor</th>
                                        <th>Celular</th>
                                        <th style="display:none">Correos</th>
                                        <th data-priority="4">Tipo</th>
                                        <th data-priority="5">Producto</th>
                                        <th data-priority="6">Inicia</th>
                                        <th data-priority="7">Caduca</th>
                                        <th style="display:none">Grupo</th>
                                        <th style="display:none">Dias Hasta Vencer</th>
                                        <th style="display:none">Precio</th>
                                        <th style="display:none">Periodo</th>
                                        <th style="display:none">Producto</th>
                                        <th style="display:none">Fecha Ultimo Pago</th>
                                        <th style="display:none">Fecha Actualizaciones</th>
                                        <th style="display:none">Vendedor</th>
                                        <th style="display:none">Revendedor</th>
                                        <th style="display:none">Origen</th>
                                        <th style="display:none">Provincia</th>
                                        <th style="display:none">Usuarios</th>
                                        <th style="display:none">Empresas</th>
                                        <th style="display:none">Moviles</th>
                                        <th style="display:none">Cantidad Empresas</th>
                                        <th class="no-exportar">Acciones</th>

                                    </tr>
                                </thead>
                            </table>
                            <!--end: Datatable-->

                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')
@endsection

@section('script')

<script>
    $('#tipolicencia').on('change', function(e){
        var distribuidor = e.target.value;
        var tipo= $('#tipolicencia').val();
        $('#producto').empty();
        $.ajax({
            type:"GET",
            url: '/admin/productos/' + tipo ,
            success: function(data){
                $.each(data, function(fetch, producto){
                    for(i = 0; i < producto.length; i++){
                    $('#producto').append('<option value="'+ producto[i].id +'">'+ producto[i].nombre +'</option>');
                    }
                })
            }
        });
    });

    $('#producto').on('change', function(e){
        if($('#producto').val()==12){
            $('#periodo1').html("Inicial");
            $('#periodo2').html("Basico");
            $('#periodo3').html("Premium");
            $('#periodo3').removeClass("d-none");
        }else{
            $('#periodo1').html("Mensual");
            $('#periodo2').html("Anual");
            $('#periodo3').addClass("d-none");
        }
    });
    $(document).ready(function() {

        //Inicializar rango de fechas
        $('#kt_fecha').daterangepicker({
            autoUpdateInput: false,
            format: "DD-MM-YYYY",
            locale: {
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "DE",
                "toLabel": "HASTA",
                "customRangeLabel": "Personalizado",
                "daysOfWeek": [
                    "Dom",
                    "Lun",
                    "Mar",
                    "Mie",
                    "Jue",
                    "Vie",
                    "Sáb"
                ],
                "monthNames": [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
                "firstDay": 1
            },
            ranges: {
            'Hoy': [moment(), moment()],
            'Ultimos 7 días': [moment().subtract(6, 'days'), moment()],
            'Ultimos 30 días ': [moment().subtract(29, 'days'), moment()],
            'Mes Actual': [moment().startOf('month'), moment().endOf('month')],
            'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Año Actual': [moment().startOf('year'), moment().endOf('year')],
            'Año Anterior': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            },
            buttonClasses: ' btn',
            applyClass: 'btn-primary',
            cancelClass: 'btn-secondary',
            alwaysShowCalendars: true,
            showDropdowns: true,
        }, function(start, end, label) {
            $('#kt_fecha .form-control').val( start.format('DD-MM-YYYY') + ' / ' + end.format('DD-MM-YYYY'));
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //inicializar datatable
        var table = $('#kt_datatable').DataTable({
            //Posicion de los elementos de la datatable f:filtering l:length t:table r:processing i:info p:pagination
            dom:"<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            responsive: true,
            processing: true,
            search: {
                return: true,
            },
            //Combo cantidad de registros a mostrar por pantalla
            lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'Todos']],
            //Registros por pagina
            pageLength: 15,
            //Orden inicial
            order: [[ 0, 'desc' ]],
            //Guardar pagina, busqueda, etc
            //stateSave: true,
            //Trabajar del lado del server
            serverSide: true,
            //Peticion ajax que devuelve los registros
            ajax: {
                url: "{{ route('clientes.tabla') }}",
                type: 'POST',
                data: function (d) {
                    //Valores de filtro a enviar a la ruta clientes.index
                    d.tipofecha= $("#tipofecha").val();
                    d.tipolicencia= $("#tipolicencia").val();
                    d.fecha= $("#fecha").val();
                    d.periodo= $("#periodo").val();
                    d.producto= $("#producto").val();
                    d.distribuidor= $("#distribuidor").val();
                    d.vendedor= $("#vendedor").val();
                    d.origen= $("#origen").val();
                    d.provinciasid = $("#provinciasid").val();
                    d.buscar_filtro = $("#buscar_filtro").val();
                }
            },
            //Columnas de la tabla (Debe contener misma cantidad que thead)
            columns: [
                {data: 'sis_clientesid', name: 'sis_clientesid',visible:false, searchable: false},
                {data: 'numerocontrato', name: 'numerocontrato',visible:false},
                {data: 'identificacion', name: 'identificacion'},
                {data: 'nombres', name: 'nombres'},
                {data: 'sis_distribuidoresid', name: 'sis_distribuidoresid', searchable: false},
                {data: 'telefono2', name: 'telefono2', searchable: false},
                {data: 'correos', name: 'correos',visible:false, searchable: false},
                {data: 'tipo_licencia', name: 'tipo_licencia', searchable: false},
                {data: 'producto', name: 'producto', searchable: false},
                {data: 'fechainicia', name: 'fechainicia', searchable: false},
                {data: 'fechacaduca', name: 'fechacaduca', searchable: false},
                {data: 'grupo', name: 'grupo',visible:false, searchable: false},
                {data: 'diasvencer', name: 'diasvencer',visible:false, searchable: false},
                {data: 'precio', name: 'precio',visible:false, searchable: false},
                {data: 'periodo', name: 'periodo',visible:false, searchable: false},
                {data: 'producto', name: 'producto',visible:false, searchable: false},
                {data: 'fechaultimopago', name: 'fechaultimopago',visible:false, searchable: false},
                {data: 'fechaactulizaciones', name: 'fechaactulizaciones',visible:false, searchable: false},
                {data: 'sis_vendedoresid', name: 'sis_vendedoresid',visible:false, searchable: false},
                {data: 'sis_revendedoresid', name: 'sis_revendedoresid',visible:false, searchable: false},
                {data: 'red_origen', name: 'red_origen',visible:false, searchable: false},
                {data: 'provinciasid',name: 'provinciasid',visible: false, searchable: false},

                {data: 'usuarios', name: 'usuarios',visible:false, searchable: false},
                {data: 'empresas', name: 'empresas',visible:false, searchable: false},
                {data: 'numeromoviles', name: 'numeromoviles',visible:false, searchable: false},
                {data: 'cantidadempresas', name: 'cantidadempresas',visible:false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
            ],
            //botones para exportar
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: 'Clientes',
                    exportOptions: {
                        columns: ':not(.no-exportar)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Clientes',
                    exportOptions: {
                        columns: ':not(.no-exportar)'
                    }
                },
            ],

            initComplete: function() {

                self = this.api();
                //Si esta en movil agregar boton buscar
                if ($(window).width() < 768) {
                    var input = $('.dataTables_filter input').unbind(),
                    $searchButton = $('<button>')
                            .text('Buscar')
                            .addClass('btn btn-sm btn-primary ml-1')
                            .click(function() {
                                self.search(input.val()).draw();
                            })

                    $('.dataTables_filter').append($searchButton);
                }else{
                    // //Buscar con enter
                    $('.dataTables_filter input').unbind();
                    $('.dataTables_filter input').bind('keyup', function(e){
                        var code = e.keyCode || e.which;
                        if (code == 13) {
                            table.search(this.value).draw();
                        }
                    });
                }

                //Buscar al borrar y no hay caracteres
                $('.dataTables_filter input').off('.DT').on('keyup.DT', function (e) {
                    if (e.keyCode == 8 && this.value.length == 0) {
                    self.search('').draw();
                    }
                });

                // //Buscar al hacer clic en limpiar
                $('input[type="search"]').on('search', function () {
                    self.search('').draw();
                });

            },
        });

        //Al hacer clic en los botones para exportar
        $('#export_copy').on('click', function(e) {
			e.preventDefault();
			table.button(0).trigger();
		});

		$('#export_excel').on('click', function(e) {
			e.preventDefault();
			table.button(1).trigger();
		});

        //Clic en boton buscar
        $('#kt_search').on('click', function(e) {
			e.preventDefault();
            $("#buscar_filtro").val('1');
            table.draw();
		});

        //Clic en boton resetear
		$('#kt_reset').on('click', function(e) {
            $("#tipofecha").val('');
            $("#tipolicencia").val('');
            $("#fecha").val('');
            $("#periodo").val('');
            $("#producto").val('');
            $("#distribuidor").val('');
            $("#vendedor").val('');
            $("#origen").val('');
            $("#provinciasid").val('');
            $("#buscar_filtro").val('');
            table.draw();
		});

        //Mostrar div de busqueda
        $('#filtrar').on('click', function(e) {
            $("#filtro").toggle(500);
		});

    });

</script>


@endsection