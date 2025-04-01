
<?php $__env->startSection('contenido'); ?>

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
                                <h3 class="card-label">Distribuidores</h3>

                            </div>
                            <div class="card-toolbar">
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

                                <a href="<?php echo e(route('distribuidores.crear')); ?>" class="btn btn-primary font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="flaticon2-plus-1"></i>
                                    </span>Nuevo
                                </a>

                            </div>

                        </div>
                        <div class="card-body">
                            <!--begin: Datatable-->
                            <table class="table table-sm table-bordered table-head-custom table-hover"
                                id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="no-exportar">#</th>
                                        <th data-priority="1">Identificación</th>
                                        <th data-priority="2">Razon Social</th>
                                        <th>Nombre Comercial</th>
                                        <th>Correo</th>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
<?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<script>
    $(document).ready(function() {

        //inicializar datatable
        var table = $('#kt_datatable').DataTable({
            //Posicion de los elementos de la datatable f:filtering l:length t:table r:processing i:info p:pagination
            dom:"<'row'<'col-sm-12 col-md-6'f><'col-sm-12 col-md-6'l>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            responsive: true,
            processing: true,
            //Combo cantidad de registros a mostrar por pantalla
            lengthMenu: [[15, 25, 50, -1], [15, 25, 50, 'Todos']],
            //Registros por pagina
            pageLength: 15,
            //Orden inicial
            order: [[ 0, 'desc' ]],
            //Guardar pagina, busqueda, etc
            stateSave: true,
            //Trabajar del lado del server
            serverSide: true,
            //Peticion ajax que devuelve los registros
            ajax: "<?php echo e(route('distribuidores.index')); ?>",
            //Columnas de la tabla (Debe contener misma cantidad que thead)
            columns: [
                {data: 'sis_distribuidoresid', name: 'sis_distribuidoresid',visible:false},
                {data: 'identificacion', name: 'identificacion'},
                {data: 'razonsocial', name: 'razonsocial'},
                {data: 'nombrecomercial', name: 'nombrecomercial'},
                {data: 'correos', name: 'correos'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
            ],
            
            //botones para exportar
            buttons: [
                {
                    extend: 'print',
                    title: 'Distribuidores',
                    exportOptions: {
                        columns: ':not(.no-exportar)'
                    }
                },
                {
                    extend: 'copyHtml5',
                    title: 'Distribuidores',
                    exportOptions: {
                        columns: ':not(.no-exportar)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Distribuidores',
                    exportOptions: {
                        columns: ':not(.no-exportar)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Distribuidores',
                    exportOptions: {
                        columns: ':not(.no-exportar)'
                    }
                },
            ],
        });

        //Al hacer clic en los botones para exportar 
        $('#export_print').on('click', function(e) {
			e.preventDefault();
			table.button(0).trigger();
		});

		$('#export_copy').on('click', function(e) {
			e.preventDefault();
			table.button(1).trigger();
		});

		$('#export_excel').on('click', function(e) {
			e.preventDefault();
			table.button(2).trigger();
		});

		$('#export_pdf').on('click', function(e) {
			e.preventDefault();
			table.button(3).trigger();
		});

        //Mostrar div de busqueda
        $('#filtrar').on('click', function(e) {
        $("#filtro").toggle(500);
        });

    });
 
</script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\admin\resources\views/admin/distribuidores/index.blade.php ENDPATH**/ ?>