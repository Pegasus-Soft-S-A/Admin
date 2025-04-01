<?php echo csrf_field(); ?>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Distribuidor:</label>
        <select class="form-control select2" name="sis_distribuidoresid" id="distribuidor">
            <option value="0">Todos</option>
            <?php $__currentLoopData = $distribuidores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribuidor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($distribuidor->sis_distribuidoresid); ?>"
                    <?php echo e($distribuidor->sis_distribuidoresid == $notificaciones->sis_distribuidoresid ? 'Selected' : ''); ?>>
                    <?php echo e($distribuidor->razonsocial); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($errors->has('sis_distribuidoresid')): ?>
            <span class="text-danger"><?php echo e($errors->first('sis_distribuidoresid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Usuarios:</label>
        <select class="form-control select2" id="usuarios" name="usuarios">
            <option value="0" <?php echo e(old('usuarios', $notificaciones->usuarios) == '0' ? 'Selected' : ''); ?>>Todos
            </option>
            <option value="1" <?php echo e(old('usuarios', $notificaciones->usuarios) == '1' ? 'Selected' : ''); ?>>Solo Admin
            </option>
        </select>
        <?php if($errors->has('usuarios')): ?>
            <span class="text-danger"><?php echo e($errors->first('usuarios')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Tipo :</label>
        <select class="form-control select2" id="tipo" name="tipo">
            <option value="0" <?php echo e(old('tipo', $notificaciones->tipo) == '0' ? 'Selected' : ''); ?>>Todos
            </option>
            <option value="1" <?php echo e(old('tipo', $notificaciones->tipo) == '1' ? 'Selected' : ''); ?>>Web
            </option>
            <option value="2" <?php echo e(old('tipo', $notificaciones->tipo) == '2' ? 'Selected' : ''); ?>>PC
            </option>
        </select>
        <?php if($errors->has('tipo')): ?>
            <span class="text-danger"><?php echo e($errors->first('tipo')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Tipo Mensaje:</label>
        <select class="form-control select2" id="tipo_mensaje" name="tipo_mensaje">
            <option value="1" <?php echo e(old('tipo_mensaje', $notificaciones->tipo_mensaje) == '1' ? 'Selected' : ''); ?>>Informativo
            </option>
            <option value="2" <?php echo e(old('tipo_mensaje', $notificaciones->tipo_mensaje) == '2' ? 'Selected' : ''); ?>>Alerta
            </option>
        </select>
        <?php if($errors->has('tipo')): ?>
            <span class="text-danger"><?php echo e($errors->first('tipo')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Tipo Contenido:</label>
        <select class="form-control select2" id="tipo_contenido" name="tipo_contenido">
            <option value="1" <?php echo e(old('tipo_contenido', $notificaciones->tipo_contenido) == '1' ? 'Selected' : ''); ?>>HTML
            </option>
            <option value="2" <?php echo e(old('tipo_contenido', $notificaciones->tipo_contenido) == '2' ? 'Selected' : ''); ?>>URL
            </option>
        </select>
        <?php if($errors->has('tipo')): ?>
            <span class="text-danger"><?php echo e($errors->first('tipo')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Asunto:</label>
        <input type="text" class="form-control <?php echo e($errors->has('asunto') ? 'is-invalid' : ''); ?>" placeholder="Ingrese asunto" name="asunto"
            autocomplete="off" value="<?php echo e(old('asunto', $notificaciones->asunto)); ?>" id="asunto" />
        <?php if($errors->has('asunto')): ?>
            <span class="text-danger"><?php echo e($errors->first('asunto')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Publicacion Desde:</label>
        <input type="text" class="form-control <?php echo e($errors->has('fecha_publicacion_desde') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Fecha Publicacion Desde" name="fecha_publicacion_desde" id="fecha_publicacion_desde" autocomplete="off"
            value="<?php echo e(old('fecha_publicacion_desde', $notificaciones->fecha_publicacion_desde)); ?>" />
        <?php if($errors->has('fecha_publicacion_desde')): ?>
            <span class="text-danger"><?php echo e($errors->first('fecha_publicacion_desde')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Fecha Publicacion Hasta:</label>
        <input type="text" class="form-control <?php echo e($errors->has('fecha_publicacion_hasta') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Fecha Publicacion Hasta" name="fecha_publicacion_hasta" id="fecha_publicacion_hasta" autocomplete="off"
            value="<?php echo e(old('fecha_publicacion_hasta', $notificaciones->fecha_publicacion_hasta)); ?>" />
        <?php if($errors->has('fecha_publicacion_hasta')): ?>
            <span class="text-danger"><?php echo e($errors->first('fecha_publicacion_hasta')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-12">
        <label>Contenido:</label>
        <textarea class="summernote" name="contenido" id="contenido"><?php echo e(old('contenido', $notificaciones->contenido)); ?>

        </textarea>
        <?php if($errors->has('contenido')): ?>
            <span class="text-danger"><?php echo e($errors->first('contenido')); ?></span>
        <?php endif; ?>
    </div>
</div>
<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            //Iniciar fecha
            $('#fecha_publicacion_desde, #fecha_publicacion_hasta').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });
            $('.summernote').summernote({
                height: 400,
                lang: 'es-ES'
            });

            if ("<?php echo e(isset($notificaciones->sis_notificacionesid)); ?>" == false) {
                var fecha = new Date();
                let fechaInicia = ("0" + (fecha.getDate())).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear()
                $('#fecha_publicacion_desde, #fecha_publicacion_hasta').val(fechaInicia);
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php /**PATH C:\laragon\www\admin\resources\views/admin/notificaciones/_form.blade.php ENDPATH**/ ?>