<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
<?php
$rol = Auth::user()->tipo;
$accion = isset($cliente->sis_clientesid) ? 'Modificar' : 'Crear';
$grupos = App\Models\Grupos::get();

?>
<?php echo csrf_field(); ?>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Identificacion:</label>
        <div id="spinner">
            <input type="text" class="form-control <?php echo e($errors->has('identificacion') ? 'is-invalid' : ''); ?>"
                placeholder="Ingrese identificacion" name="identificacion" autocomplete="off"
                value="<?php echo e(old('identificacion', $cliente->identificacion)); ?>" id="identificacion"
                onkeypress="return validarNumero(event)" <?php if($rol !=1 && $accion=='Modificar' ): ?> readonly <?php else: ?>
                onblur="validarIdentificacion()" <?php endif; ?> />
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
            value="<?php echo e(old('nombres', $cliente->nombres)); ?>" id="nombres" <?php if($rol !=1 && $accion=='Modificar' ): ?>
            readonly <?php endif; ?> />
        <?php if($errors->has('nombres')): ?>
        <span class="text-danger"><?php echo e($errors->first('nombres')); ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Dirección:</label>
        <input type="text" class="form-control <?php echo e($errors->has('direccion') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Dirección" name="direccion" autocomplete="off" id="direccion"
            value="<?php echo e(old('direccion', $cliente->direccion)); ?>" <?php if($rol !=1 && $accion=='Modificar' ): ?> readonly
            <?php endif; ?> />
        <?php if($errors->has('direccion')): ?>
        <span class="text-danger"><?php echo e($errors->first('direccion')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Correo:</label>
        <input class="form-control <?php echo e($errors->has('correos') ? 'is-invalid' : ''); ?>" placeholder="Ingrese Correo"
            name="correos" autocomplete="off" value="<?php echo e(old('correos', $cliente->correos)); ?>" id="correos" <?php if($rol !=1
            && $accion=='Modificar' ): ?> readonly <?php endif; ?> />
        <?php if($errors->has('correos')): ?>
        <span class="text-danger"><?php echo e($errors->first('correos')); ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-lg-6">
        <label>Provincia:</label>
        <select class="form-control select2" name="provinciasid" id="provinciasid" onchange="cambiarCiudad(this);      "
            <?php if($rol !=1 && $accion=='Modificar' ): ?> disabled <?php endif; ?>>
            <option value="">Seleccione una provincia</option>
            <option value="01" <?php echo e($cliente->provinciasid == '01' ? 'Selected' : ''); ?>>
                Azuay
            </option>
            <option value="02" <?php echo e($cliente->provinciasid== '02' ? 'Selected' : ''); ?>>
                Bolivar
            </option>
            <option value="03" <?php echo e($cliente->provinciasid == '03' ? 'Selected' : ''); ?>>
                Cañar
            </option>
            <option value="04" <?php echo e($cliente->provinciasid == '04' ? 'Selected' : ''); ?>>
                Carchi
            </option>
            <option value="05" <?php echo e($cliente->provinciasid == '05' ? 'Selected' : ''); ?>>
                Cotopaxi
            </option>
            <option value="06" <?php echo e($cliente->provinciasid == '06' ? 'Selected' : ''); ?>>
                Chimborazo
            </option>
            <option value="07" <?php echo e($cliente->provinciasid == '07' ? 'Selected' : ''); ?>>
                El Oro
            </option>
            <option value="08" <?php echo e($cliente->provinciasid == '08' ? 'Selected' : ''); ?>>
                Esmeraldas
            </option>
            <option value="09" <?php echo e($cliente->provinciasid == '09' ? 'Selected' : ''); ?>>
                Guayas
            </option>
            <option value="20" <?php echo e($cliente->provinciasid == '20' ? 'Selected' : ''); ?>>
                Galapagos
            </option>
            <option value="10" <?php echo e($cliente->provinciasid == '10' ? 'Selected' : ''); ?>>
                Imbabura
            </option>
            <option value="11" <?php echo e($cliente->provinciasid == '11' ? 'Selected' : ''); ?>>
                Loja</option>
            <option value="12" <?php echo e($cliente->provinciasid == '12' ? 'Selected' : ''); ?>>
                Los Rios
            </option>
            <option value="13" <?php echo e($cliente->provinciasid == '13' ? 'Selected' : ''); ?>>
                Manabi
            </option>
            <option value="14" <?php echo e($cliente->provinciasid == '14' ? 'Selected' : ''); ?>>
                Morona
                Santiago</option>
            <option value="15" <?php echo e($cliente->provinciasid == '15' ? 'Selected' : ''); ?>>
                Napo</option>
            <option value="22" <?php echo e($cliente->provinciasid == '22' ? 'Selected' : ''); ?>>
                Orellana
            </option>
            <option value="16" <?php echo e($cliente->provinciasid == '16' ? 'Selected' : ''); ?>>
                Pastaza
            </option>
            <option value="17" <?php echo e($cliente->provinciasid == '17' ? 'Selected' : ''); ?>>
                Pichincha
            </option>
            <option value="24" <?php echo e($cliente->provinciasid == '24' ? 'Selected' : ''); ?>>
                Santa Elena
            </option>
            <option value="23" <?php echo e($cliente->provinciasid== '23' ? 'Selected' : ''); ?>>
                Santo Domingo
                De Los Tsachilas</option>
            <option value="21" <?php echo e($cliente->provinciasid == '21' ? 'Selected' : ''); ?>>
                Sucumbios
            </option>
            <option value="18" <?php echo e($cliente->provinciasid == '18' ? 'Selected' : ''); ?>>
                Tungurahua
            </option>
            <option value="19" <?php echo e($cliente->provinciasid == '19' ? 'Selected' : ''); ?>>
                Zamora
                Chinchipe</option>
        </select>
        <?php if($errors->has('provinciasid')): ?>
        <span class="text-danger"><?php echo e($errors->first('provinciasid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Ciudad:</label>
        <select class="form-control select2" name="ciudadesid" id="ciudadesid">
            <option value="">Seleccione una Ciudad</option>

        </select>

        <?php if($errors->has('ciudadesid')): ?>
        <span class="text-danger"><?php echo e($errors->first('ciudadesid')); ?></span>
        <?php endif; ?>
    </div>

</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Grupo:</label>
        <select class="form-control select2" name="grupo" id="grupo">
            <option value="">Seleccione un Tipo de Negocio</option>
            <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($grupo->gruposid); ?>" <?php echo e(old('grupo', $cliente->grupo) == $grupo->gruposid ? 'selected' :
                ''); ?>>
                <?php echo e($grupo->descripcion); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </select>
        <?php if($errors->has('grupo')): ?>
        <span class="text-danger"><?php echo e($errors->first('grupo')); ?></span>
        <?php endif; ?>

    </div>
    <div class="col-lg-3">
        <label>Convencional:</label>
        <input type="text" class="form-control <?php echo e($errors->has('telefono1') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Numero Convencional" name="telefono1" onkeypress="return validarNumero(event)"
            autocomplete="off" value="<?php echo e(old('telefono1', $cliente->telefono1)); ?>" id="telefono1" <?php if($rol !=1 && $rol
            !=2 && $accion=='Modificar' ): ?> readonly <?php endif; ?> />
        <?php if($errors->has('telefono1')): ?>
        <span class="text-danger"><?php echo e($errors->first('telefono1')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <label>Celular:</label>
        <input type="text" class="form-control <?php echo e($errors->has('telefono2') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Numero Celular" onkeypress="return validarNumero(event)" name="telefono2"
            autocomplete="off" value="<?php echo e(old('telefono2', $cliente->telefono2)); ?>" id="telefono2" <?php if($rol !=1 && $rol
            !=2 && $accion=='Modificar' ): ?> readonly <?php endif; ?> />
        <?php if($errors->has('telefono2')): ?>
        <span class="text-danger"><?php echo e($errors->first('telefono2')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-3">
        <label>Distribuidor:</label>
        <select class="form-control select2 <?php if($rol != 1 && $accion == 'Modificar'): ?> disabled <?php endif; ?>"
            name="sis_distribuidoresid" id="distribuidor" <?php if($rol !=1 && $accion=='Modificar' ): ?> disabled <?php endif; ?>>
            <option value="">Seleccione un distribuidor</option>
            <?php $__currentLoopData = $distribuidores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribuidor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($distribuidor->sis_distribuidoresid); ?>" <?php echo e($distribuidor->sis_distribuidoresid==
                $cliente->sis_distribuidoresid
                ? 'Selected'
                : ''); ?>>
                <?php echo e($distribuidor->razonsocial); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($errors->has('sis_distribuidoresid')): ?>
        <span class="text-danger"><?php echo e($errors->first('sis_distribuidoresid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <label>Vendedor:</label>
        <select class="form-control select2" name="sis_vendedoresid" id="vendedor" <?php if($rol !=1 && $rol !=2 &&
            $accion=='Modificar' ): ?> disabled <?php endif; ?>>
            <option value="">Seleccione un Vendedor</option>
        </select>
        <?php if($errors->has('sis_vendedoresid')): ?>
        <span class="text-danger"><?php echo e($errors->first('sis_vendedoresid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <label>Revendedor:</label>
        <select class="form-control select2" name="sis_revendedoresid" id="revendedor" <?php if($rol !=1 && $rol !=2 &&
            $accion=='Modificar' ): ?> disabled <?php endif; ?>>
            <option value="">Seleccione un Revendedor</option>
        </select>
        <?php if($errors->has('sis_revendedoresid')): ?>
        <span class="text-danger"><?php echo e($errors->first('sis_revendedoresid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-3">
        <label>Origen:</label>
        <select class="form-control select2" id="red_origen" name="red_origen">
            <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($link->sis_linksid); ?>" <?php echo e($link->sis_linksid==
                $cliente->red_origen
                ? 'Selected'
                : ''); ?>>
                <?php echo e($link->codigo); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($errors->has('red_origen')): ?>
        <span class="text-danger"><?php echo e($errors->first('red_origen')); ?></span>
        <?php endif; ?>
    </div>
</div><?php /**PATH C:\laragon\www\admin\resources\views/admin/clientes/_form.blade.php ENDPATH**/ ?>