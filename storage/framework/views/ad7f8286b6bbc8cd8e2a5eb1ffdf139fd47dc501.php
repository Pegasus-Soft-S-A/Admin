<?php
$listadoDistribuidor = App\Models\Distribuidores::select('sis_distribuidoresid', 'razonsocial')->get();
?>
<?php echo csrf_field(); ?>
<div class="form-group row">

    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control <?php echo e($errors->has('identificacion') ? 'is-invalid' : ''); ?>"
                placeholder="Ingrese identificacion" name="identificacion" autocomplete="off"
                value="<?php echo e(old('identificacion', $usuarios->identificacion)); ?>" id="identificacion"
                onkeypress="return validarNumero(event)" onblur="validarIdentificacion()" />
        </div>
        <span class="text-danger d-none" id="mensajeBandera">La cédula o Ruc no es válido</span>
        <?php if($errors->has('identificacion')): ?>
        <span class="text-danger"><?php echo e($errors->first('identificacion')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Nombres:</label>
        <input type="text" class="form-control <?php echo e($errors->has('nombres') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Nombres" name="nombres" autocomplete="off"
            value="<?php echo e(old('nombres', $usuarios->nombres)); ?>" id="razonsocial" />
        <?php if($errors->has('nombres')): ?>
        <span class="text-danger"><?php echo e($errors->first('nombres')); ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group row">


    <div class="col-lg-6">
        <label>Correo:</label>
        <input type="email" class="form-control <?php echo e($errors->has('correo') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Correo" id="correo" name="correo" autocomplete="off"
            value="<?php echo e(old('correo', $usuarios->correo)); ?>" />
        <?php if($errors->has('correo')): ?>
        <span class="text-danger"><?php echo e($errors->first('correo')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Clave:</label>
        <input type="password" class="form-control <?php echo e($errors->has('contrasena') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Clave" name="contrasena" value="<?php echo e(old('contrasena')); ?>" />
        <?php if($usuarios->contrasena): ?>
        <span class="form-text text-muted">La clave se modificará solo si se llena el campo</span>
        <?php endif; ?>
        <?php if($errors->has('contrasena')): ?>
        <span class="text-danger"><?php echo e($errors->first('contrasena')); ?></span>
        <?php endif; ?>
    </div>

</div>

<div class="form-group row">

    <div class="col-lg-6">
        <label>Distribuidor:</label>
        <select class="form-control select2 " id="sis_distribuidoresid" name="sis_distribuidoresid">
            <?php if(count($listadoDistribuidor) > 0): ?>
            <option value="">
                Escoja un distribuidor
            </option>
            <?php $__currentLoopData = $listadoDistribuidor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribuidorL): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($distribuidorL->sis_distribuidoresid); ?>" <?php echo e($distribuidorL->sis_distribuidoresid ==
                $usuarios->sis_distribuidoresid ? 'selected' : ''); ?>>
                <?php echo e($distribuidorL->razonsocial); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <option value="">
                No existe un distribuidor
            </option>
            <?php endif; ?>
        </select>
        <?php if($errors->has('sis_distribuidoresid')): ?>
        <span class="text-danger"><?php echo e($errors->first('sis_distribuidoresid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Tipo:</label>
        <select class="form-control  <?php echo e($errors->has('tipo') ? 'is-invalid' : ''); ?> " name="tipo" id="tipo">
            <option value="">
                Escoja un tipo
            </option>
            <option value="1" <?php echo e(old('tipo', $usuarios->tipo) == '1' ? 'Selected' : ''); ?>>
                Admin
            </option>
            <option value="2" <?php echo e(old('tipo', $usuarios->tipo) == '2' ? 'Selected' : ''); ?>>
                Distribuidor
            </option>
            <option value="3" <?php echo e(old('tipo', $usuarios->tipo) == '3' ? 'Selected' : ''); ?>>
                Soporte distribuidor
            </option>
            <option value="7" <?php echo e(old('tipo', $usuarios->tipo) == '7' ? 'Selected' : ''); ?>>
                Soporte matriz
            </option>
            <option value="4" <?php echo e(old('tipo', $usuarios->tipo) == '4' ? 'Selected' : ''); ?>>
                Ventas
            </option>
            <option value="5" <?php echo e(old('tipo', $usuarios->tipo) == '5' ? 'Selected' : ''); ?>>
                Marketing
            </option>
            <option value="6" <?php echo e(old('tipo', $usuarios->tipo) == '6' ? 'Selected' : ''); ?>>
                Visor
            </option>
        </select>
        <?php if($errors->has('tipo')): ?>
        <span class="text-danger"><?php echo e($errors->first('tipo')); ?></span>
        <?php endif; ?>
    </div>


</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Activo:</label>
        <span class="switch switch-outline switch-icon switch-primary">
            <label>
                <input type="checkbox" name="estado" id="estado" <?php if($usuarios->estado == 1): ?> checked <?php endif; ?> />
                <span></span>
            </label>
        </span>
    </div>
</div><?php /**PATH C:\laragon\www\admin\resources\views/admin/usuarios/_form.blade.php ENDPATH**/ ?>