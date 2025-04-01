<?php echo csrf_field(); ?>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control <?php echo e($errors->has('identificacion') ? 'is-invalid' : ''); ?>"
                placeholder="Ingrese identificacion" name="identificacion" autocomplete="off"
                value="<?php echo e(old('identificacion', $distribuidor->identificacion)); ?>" id="identificacion"
                onkeypress="return validarNumero(event)" onblur="validarIdentificacion()" />
        </div>
        <span class="text-danger d-none" id="mensajeBandera">La cédula o Ruc no es válido</span>
        <?php if($errors->has('identificacion')): ?>
        <span class="text-danger"><?php echo e($errors->first('identificacion')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Razon Social:</label>
        <input type="text" class="form-control <?php echo e($errors->has('razonsocial') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Razon Social" name="razonsocial" autocomplete="off"
            value="<?php echo e(old('razonsocial', $distribuidor->razonsocial)); ?>" id="razonsocial" />
        <?php if($errors->has('razonsocial')): ?>
        <span class="text-danger"><?php echo e($errors->first('razonsocial')); ?></span>
        <?php endif; ?>
    </div>

</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Nombre Comercial:</label>
        <input type="text" class="form-control <?php echo e($errors->has('nombrecomercial') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Nombre Comercial" name="nombrecomercial" autocomplete="off" id="nombrecomercial"
            value="<?php echo e(old('nombrecomercial', $distribuidor->nombrecomercial)); ?>" />
        <?php if($errors->has('nombrecomercial')): ?>
        <span class="text-danger"><?php echo e($errors->first('nombrecomercial')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Correo(s):</label>
        <input class="form-control <?php echo e($errors->has('correos') ? 'is-invalid' : ''); ?>" placeholder="Ingrese Correo"
            name="correos" autocomplete="off" value="<?php echo e(old('correos', $distribuidor->correos)); ?>" id="correo" />
        <?php if($errors->has('correos')): ?>
        <span class="text-danger"><?php echo e($errors->first('correos')); ?></span>
        <?php endif; ?>
    </div>
</div><?php /**PATH C:\laragon\www\admin\resources\views/admin/distribuidores/_form.blade.php ENDPATH**/ ?>