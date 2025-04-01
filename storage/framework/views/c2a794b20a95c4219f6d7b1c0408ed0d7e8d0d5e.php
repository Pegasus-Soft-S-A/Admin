
<?php $__env->startSection('contenido'); ?>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!--begin::Card-->
                    <form class="form"
                        action="<?php echo e(route('notificaciones.actualizar', $notificaciones->sis_notificacionesid)); ?>"
                        method="POST">
                        <?php echo method_field('PUT'); ?>
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header flex-wrap py-5">
                                <div class="card-title">
                                    <h3 class="card-label">Notificaciones </h3>
                                </div>
                                <div class="card-toolbar">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="">
                                        <div class="btn-group" role="group" aria-label="First group">

                                            <a href="<?php echo e(route('notificaciones.index')); ?>"
                                                class="btn btn-secondary btn-icon" data-toggle="tooltip"
                                                title="Volver"><i class="la la-long-arrow-left"></i></a>

                                            <button type="submit" class="btn btn-success btn-icon" data-toggle="tooltip"
                                                title="Guardar"><i class="la la-save"></i></button>

                                            <a href="<?php echo e(route('notificaciones.crear')); ?>"
                                                class="btn btn-warning btn-icon" data-toggle="tooltip" title="Nuevo"><i
                                                    class="la la-user-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <?php echo $__env->make('admin.notificaciones._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>


                        </div>
                        <!--end::Card-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\admin\resources\views/admin/notificaciones/editar.blade.php ENDPATH**/ ?>