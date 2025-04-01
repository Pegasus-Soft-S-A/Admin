<style>
    .disabled {
        pointer-events: none;
        opacity: 1;
        background-color: #F3F6F9;
    }
</style>
<?php
    $rol = Auth::user()->tipo;
    $accion = isset($licencia->sis_licenciasid) ? 'Modificar' : 'Crear';
    $servidoresid = isset($licencia->sis_licenciasid) ? $licencia->sis_servidoresid : 0;
    $licenciasid = isset($licencia->sis_licenciasid) ? $licencia->sis_licenciasid : 0;

    // Centralizamos la lógica de permisos basada en roles
    $permisos = [
        'editar_producto' => $rol == 1, // Solo admin (rol 1) puede editar producto en modo modificación
        'editar_periodo' => $rol == 1 || $accion == 'Crear', // Todos pueden editar periodo en creación
        'editar_campos_numericos' => $rol == 1, // Solo admin puede editar valores numéricos
        'editar_fechas' => $rol == 1, // Solo admin puede editar fechas
        'editar_modulos' => $rol == 1, // Solo admin puede editar módulos
        'mostrar_renovar' => $rol == 1 || $rol == 2, // Ciertos roles no pueden renovar
    ];

?>
<?php echo csrf_field(); ?>
<div class="form-group row">
    <div class="col-lg-6">
        <input type="hidden" name="sis_distribuidoresid" value="<?php echo e($licencia->sis_distribuidoresid); ?>">
        <input type="hidden" name="tipo" id="tipo">
        <input type="hidden" value="<?php echo e($cliente->sis_clientesid); ?>" name="sis_clientesid">
        <label>Numero Contrato:</label>
        <input type="text" class="form-control <?php echo e($errors->has('numerocontrato') ? 'is-invalid' : ''); ?>" placeholder="Contrato"
            name="numerocontrato" autocomplete="off" id="numerocontrato" value="<?php echo e(old('numerocontrato', $licencia->numerocontrato)); ?>" readonly />
        <?php if($errors->has('numerocontrato')): ?>
            <span class="text-danger"><?php echo e($errors->first('numerocontrato')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Producto:</label>
        <select class="form-control <?php echo e(!$permisos['editar_producto'] && $accion == 'Modificar' ? 'disabled' : ''); ?>" name="producto" id="producto">
            <?php if($accion == 'Modificar' && $licencia->producto == '12'): ?>
                <option value="12" <?php echo e(old('producto', $licencia->producto) == '12' ? 'Selected' : ''); ?>>Facturito</option>
            <?php else: ?>
                <option value="2" <?php echo e(old('producto', $licencia->producto) == '2' ? 'Selected' : ''); ?>>Facturación</option>
                <option value="3" <?php echo e(old('producto', $licencia->producto) == '3' ? 'Selected' : ''); ?>>Servicios</option>
                <option value="4" <?php echo e(old('producto', $licencia->producto) == '4' ? 'Selected' : ''); ?>>Comercial</option>
                <option value="5" <?php echo e(old('producto', $licencia->producto) == '5' ? 'Selected' : ''); ?>>Soy Contador Comercial</option>
                <option value="8" <?php echo e(old('producto', $licencia->producto) == '8' ? 'Selected' : ''); ?>>Soy Contador Servicios</option>
                <?php if($accion == 'Modificar' && $licencia->producto == '6'): ?>
                    <option value="6" <?php echo e(old('producto', $licencia->producto) == '6' ? 'Selected' : ''); ?>>Perseo Lite Anterior</option>
                <?php endif; ?>
                <option value="9" <?php echo e(old('producto', $licencia->producto) == '9' ? 'Selected' : ''); ?>>Perseo Lite</option>
                <?php if($accion == 'Modificar' && $licencia->producto == '10'): ?>
                    <option value="10" <?php echo e(old('producto', $licencia->producto) == '10' ? 'Selected' : ''); ?>>Emprendedor</option>
                <?php endif; ?>
                <option value="11" <?php echo e(old('producto', $licencia->producto) == '11' ? 'Selected' : ''); ?>>Socio Perseo</option>
                <?php if($accion == 'Crear'): ?>
                    <option value="12" <?php echo e(old('producto', $licencia->producto) == '12' ? 'Selected' : ''); ?>>Facturito</option>
                <?php endif; ?>
            <?php endif; ?>
        </select>
        <?php if($errors->has('producto')): ?>
            <span class="text-danger"><?php echo e($errors->first('producto')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Periodo:</label>
        <div class="input-group">
            <select class="form-control <?php echo e(!$permisos['editar_periodo'] ? 'disabled' : ''); ?>" name="periodo" id="periodo">
                <option id="periodo1" value="1" <?php echo e(old('periodo', $licencia->periodo) == '1' ? 'Selected' : ''); ?>>Mensual</option>
                <option id="periodo2" value="2" <?php echo e(old('periodo', $licencia->periodo) == '2' ? 'Selected' : ''); ?>>Anual</option>
                <option id="periodo3" value="3" <?php echo e(old('periodo', $licencia->periodo) == '3' ? 'Selected' : ''); ?>>Premium</option>
                <option id="periodo4" value="4" <?php echo e(old('periodo', $licencia->periodo) == '4' ? 'Selected' : ''); ?>>Gratis</option>
            </select>
            <?php if(isset($licencia->sis_licenciasid) && $licencia->producto != 6 && $licencia->producto != 9): ?>
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        <?php echo e(!$permisos['mostrar_renovar'] ? 'disabled' : ''); ?>>
                        Renovar
                    </button>
                    <div class="dropdown-menu">
                        <?php if($licencia->producto != 10 && $licencia->producto != 12): ?>
                            <a class="dropdown-item" href="#" id="renovarmensual">Renovar Mensual</a>
                        <?php endif; ?>
                        <a class="dropdown-item" href="#" id="renovaranual">Renovar Anual</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-6">
        <label>Precio:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_campos_numericos'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('precio') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Precio" id="precio" name="precio" autocomplete="off" value="<?php echo e(old('precio', $licencia->precio)); ?>" />
        <?php if($errors->has('precio')): ?>
            <span class="text-danger"><?php echo e($errors->first('precio')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Fecha Inicia:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_fechas'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('fechainicia') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Fecha Inicio" name="fechainicia" id="fechainicia" autocomplete="off"
            value="<?php echo e(old('fechainicia', $licencia->fechainicia)); ?>" />
        <?php if($errors->has('fechainicia')): ?>
            <span class="text-danger"><?php echo e($errors->first('fechainicia')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Fecha Caduca:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_fechas'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('fechacaduca') ? 'is-invalid' : ''); ?>"
            placeholder="Ingrese Fecha Caducidad" name="fechacaduca" id="fechacaduca" autocomplete="off"
            value="<?php echo e(old('fechacaduca', $licencia->fechacaduca)); ?>" />
        <?php if($errors->has('fechacaduca')): ?>
            <span class="text-danger"><?php echo e($errors->first('fechacaduca')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Empresas:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_campos_numericos'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('empresas') ? 'is-invalid' : ''); ?>"
            placeholder="N° Empresas" name="empresas" autocomplete="off" id="empresas" value="<?php echo e(old('empresas', $licencia->empresas)); ?>" />
        <?php if($errors->has('empresas')): ?>
            <span class="text-danger"><?php echo e($errors->first('empresas')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>N° Usuarios:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_campos_numericos'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('usuarios') ? 'is-invalid' : ''); ?>"
            placeholder="N° Usuarios" name="usuarios" autocomplete="off" id="usuarios" value="<?php echo e(old('usuarios', $licencia->usuarios)); ?>" />
        <?php if($errors->has('usuarios')): ?>
            <span class="text-danger"><?php echo e($errors->first('usuarios')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>N° Móviles:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_campos_numericos'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('numeromoviles') ? 'is-invalid' : ''); ?>"
            placeholder="N° Móviles" name="numeromoviles" autocomplete="off" id="numeromoviles"
            value="<?php echo e(old('numeromoviles', $licencia->numeromoviles)); ?>" />
        <?php if($errors->has('numeromoviles')): ?>
            <span class="text-danger"><?php echo e($errors->first('numeromoviles')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>N° Sucursales:</label>
        <input type="text"
            class="form-control <?php echo e(!$permisos['editar_campos_numericos'] ? 'disabled' : ''); ?> 
                <?php echo e($errors->has('numerosucursales') ? 'is-invalid' : ''); ?>"
            placeholder="N° Sucursales" name="numerosucursales" autocomplete="off" id="numerosucursales"
            value="<?php echo e(old('numerosucursales', $licencia->numerosucursales)); ?>" />
        <?php if($errors->has('numerosucursales')): ?>
            <span class="text-danger"><?php echo e($errors->first('numerosucursales')); ?></span>
        <?php endif; ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-lg-6">
        <label>Servidor:</label>
        <select class="form-control disabled" name="sis_servidoresid" id="sis_servidoresid">
            <?php $__currentLoopData = $servidores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servidor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($servidor->sis_servidoresid); ?>"
                    <?php echo e($servidor->sis_servidoresid == $licencia->sis_servidoresid ? 'selected' : ''); ?>>
                    <?php echo e($servidor->descripcion); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($errors->has('sis_servidoresid')): ?>
            <span class="text-danger"><?php echo e($errors->first('sis_servidoresid')); ?></span>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <label>Agrupados:</label>
        <select class="form-control select2" name="sis_agrupadosid" id="sis_agrupadosid"
            <?php echo e(!$permisos['editar_campos_numericos'] ? 'disabled' : ''); ?>>
            <option value="0">Sin grupo</option>
            <?php $__currentLoopData = $agrupados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agrupado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($agrupado->sis_agrupadosid); ?>" <?php echo e($agrupado->sis_agrupadosid == $licencia->sis_agrupadosid ? 'selected' : ''); ?>>
                    <?php echo e($agrupado->codigo); ?>-<?php echo e($agrupado->nombres); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php if($errors->has('sis_servidoresid')): ?>
            <span class="text-danger"><?php echo e($errors->first('sis_servidoresid')); ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- Módulos con switch - simplificada la repetición de código -->
<?php
    $modulos = [
        ['name' => 'nomina', 'label' => 'Nómina', 'value' => $modulos->nomina],
        ['name' => 'activos', 'label' => 'Activos Fijos', 'value' => $modulos->activos],
        ['name' => 'produccion', 'label' => 'Producción', 'value' => $modulos->produccion],
        ['name' => 'restaurantes', 'label' => 'Restaurantes', 'value' => $modulos->restaurantes],
        ['name' => 'talleres', 'label' => 'Talleres', 'value' => $modulos->talleres],
        ['name' => 'garantias', 'label' => 'Garantías', 'value' => $modulos->garantias],
        ['name' => 'ecommerce', 'label' => 'Ecommerce', 'value' => $modulos->ecommerce],
    ];
?>

<?php $__currentLoopData = array_chunk($modulos, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduloRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="form-group row">
        <?php $__currentLoopData = $moduloRow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="col-4 col-form-label"><?php echo e($modulo['label']); ?></label>
            <div class="col-2">
                <span class="switch switch-outline switch-icon switch-primary switch-sm">
                    <label>
                        <input <?php if($modulo['value'] == 1): ?> checked="checked" <?php endif; ?> type="checkbox" name="<?php echo e($modulo['name']); ?>"
                            id="<?php echo e($modulo['name']); ?>" <?php if(!$permisos['editar_modulos']): ?> class="deshabilitar" <?php endif; ?> />
                        <span></span>
                    </label>
                </span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $(document).ready(function() {
            inicializarFormulario();

            // Eventos de los botones
            $("#renovarmensual").click(() => confirmarAccion('mes', "Está seguro de Renovar la Licencia?"));
            $("#renovaranual").click(() => confirmarAccion('anual', "Está seguro de Renovar la Licencia?"));
            $("#recargar").click(() => confirmarAccion('recargar', "¿Está seguro de Recargar 120 Documentos Adicionales a la Licencia?"));
            $("#recargar240").click(() => confirmarAccion('recargar240', "¿Está seguro de Recargar 240 Documentos Adicionales a la Licencia?"));
            $("#resetear").click(confirmarResetearClave);

            // Eventos de cambio
            $('#periodo, #producto').change(cambiarComboWeb);

            // Deshabilitar clics en elementos deshabilitados
            $('.deshabilitar').click(() => false);

        });

        function inicializarFormulario() {
            const fecha = new Date();
            const fechaInicia = formatearFecha(fecha);

            if (!"<?php echo e(isset($licencia->sis_licenciasid)); ?>") {
                configurarFormularioNuevo(fechaInicia);
            } else {
                configurarFormularioExistente();
            }

            inicializarInputNumerico();
            inicializarDatepicker();
        }

        function configurarFormularioNuevo(fechaInicia) {
            $('#fechainicia').val(fechaInicia);
            $('#periodo1').html("Mensual");
            $('#periodo2').html("Anual");
            $('#periodo3, #periodo4').addClass("d-none");
            $('#periodo').removeClass("disabled");
            $('#precio').val('11.69');
            $('#usuarios').val('6');
            $('#numeromoviles').val('1');
            $('#sis_servidoresid').val('4');
            $('#ecommerce').prop('checked', false);
            $('#produccion').prop('checked', true);
            $('#nomina').prop('checked', false);
            $('#activos').prop('checked', false);
            $('#restaurantes').prop('checked', true);
            $('#talleres').prop('checked', false);
            $('#garantias').prop('checked', false);

            cambiarComboWeb();
        }

        function configurarFormularioExistente() {
            const producto = "<?php echo e($licencia->producto); ?>";
            if (producto == 12) {
                llenarComboPeriodoProducto12();
            } else if ([6, 9, 10].includes(parseInt(producto))) {
                $('#periodo1').html("Mensual");
                $('#periodo2').html("Anual");
                $('#periodo3, #periodo4').addClass("d-none");
                $('#periodo').addClass("disabled");
            } else {
                $('#periodo1').html("Mensual");
                $('#periodo2').html("Anual");
                $('#periodo3, #periodo4').addClass("d-none");
            }

            cambiarComboWeb();
        }

        function inicializarInputNumerico() {
            $('#precio').TouchSpin({
                buttondown_class: 'btn btn-secondary <?php echo e($rol != 1 ? 'disabled' : ''); ?>',
                buttonup_class: 'btn btn-secondary <?php echo e($rol != 1 ? 'disabled' : ''); ?>',
                min: 0,
                max: 10000000,
                step: 1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
                forcestepdivisibility: 'none'
            });
        }

        function inicializarDatepicker() {
            $('#fechainicia, #fechacaduca').datepicker({
                language: "es",
                todayHighlight: true,
                orientation: "bottom left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            });
        }

        function llenarComboPeriodoProducto12() {
            $('#periodo1').html("Inicial");
            $('#periodo2').html("Básico");
            $('#periodo3').html("Premium");
            $('#periodo4').html("Gratis");
            $('#periodo3, #periodo4').removeClass("d-none");
        }

        function cambiarComboWeb() {
            const producto = $('#producto').val();
            let periodo = $('#periodo').val();
            const esEdicion = "<?php echo e(isset($licencia->sis_licenciasid)); ?>";
            const productoAnterior = "<?php echo e($licencia->producto); ?>";
            const periodoAnterior = "<?php echo e($licencia->periodo); ?>";
            const fecha = esEdicion ?
                new Date('<?php echo e(isset($licencia->fechacaduca) ? date('Y-m-d', strtotime($licencia->fechacaduca)) : ''); ?>') :
                new Date();

            // Configurar período según el producto
            if (producto == 12) {
                llenarComboPeriodoProducto12();
            } else {
                $('#periodo1').html("Mensual");
                $('#periodo2').html("Anual");
                $('#periodo3, #periodo4').addClass("d-none");

                if (periodo > 2) {
                    $('#periodo').val(1);
                    periodo = 1;
                }
            }

            // Manejar estado disabled del período
            if ([6, 9, 10].includes(parseInt(producto))) {
                $('#periodo').addClass("disabled");
            } else {
                const rol = "<?php echo e($rol); ?>";
                const accion = "<?php echo e($accion); ?>";

                if (rol == 1 || accion == 'Crear') {
                    $('#periodo').removeClass("disabled");
                }
            }

            const configuraciones = obtenerConfiguraciones(producto, periodo);
            // Si estamos editando y el producto cambió, actualizamos los módulos
            const debeActualizarModulos = esEdicion && producto != productoAnterior;
            aplicarConfiguraciones(configuraciones, fecha, esEdicion, debeActualizarModulos);
        }

        function aplicarConfiguraciones(config, fecha, esEdicion, debeActualizarModulos) {
            if (config) {
                $('#precio').val(config.precio);
                $('#usuarios').val(config.usuarios);
                $('#numeromoviles').val(config.moviles);

                // Solo aplicar configuración del servidor en modo creación
                if ("<?php echo e($accion); ?>" == "Crear") {
                    $('#sis_servidoresid').val(config.servidor);
                }

                // Actualizar módulos si:
                // 1. Es una nueva licencia
                // 2. Estamos editando y el producto cambió
                if (!esEdicion || debeActualizarModulos) {
                    $('#ecommerce').prop('checked', config.modulos[0]);
                    $('#produccion').prop('checked', config.modulos[1]);
                    $('#nomina').prop('checked', config.modulos[2]);
                    $('#activos').prop('checked', config.modulos[3]);
                    $('#restaurantes').prop('checked', config.modulos[4]);
                    $('#talleres').prop('checked', config.modulos[5]);
                    $('#garantias').prop('checked', config.modulos[6]);
                }

                // Calcular fecha de caducidad solo en creación
                if (!esEdicion) {
                    const fechaCaducidad = new Date(fecha);
                    fechaCaducidad.setMonth(fechaCaducidad.getMonth() + config.meses);
                    $('#fechacaduca').val(formatearFecha(fechaCaducidad));
                }
            }
        }

        function obtenerConfiguraciones(producto, periodo) {
            return {
                2: {
                    precio: periodo == 1 ? '11.69' : '113.09',
                    usuarios: 6,
                    moviles: 1,
                    servidor: 4,
                    modulos: [true, true, false, false, true, false, false],
                    meses: periodo == 1 ? 1 : 12
                },
                3: {
                    precio: periodo == 1 ? '19.49' : '202.79',
                    usuarios: 6,
                    moviles: 0,
                    servidor: 4,
                    modulos: [false, false, true, true, false, false, false],
                    meses: periodo == 1 ? 1 : 12
                },
                4: {
                    precio: periodo == 1 ? '27.29' : '280.79',
                    usuarios: 6,
                    moviles: 2,
                    servidor: 4,
                    modulos: [true, true, true, periodo == 2, false, true, true],
                    meses: periodo == 1 ? 1 : 12
                },
                5: {
                    precio: periodo == 1 ? '15.59' : '140.39',
                    usuarios: 6,
                    moviles: 0,
                    servidor: 4,
                    modulos: [true, true, true, true, true, false, false],
                    meses: periodo == 1 ? 1 : 12
                },
                6: {
                    precio: '0',
                    usuarios: 3,
                    moviles: 1,
                    servidor: 3,
                    modulos: [false, true, true, true, true, true, true],
                    meses: 12
                },
                8: {
                    precio: periodo == 1 ? '11.69' : '116.99',
                    usuarios: 6,
                    moviles: 0,
                    servidor: 4,
                    modulos: [false, false, false, false, false, false, false],
                    meses: periodo == 1 ? 1 : 12
                },
                9: {
                    precio: '0',
                    usuarios: 6,
                    moviles: 1,
                    servidor: 3,
                    modulos: [true, true, true, true, true, true, true],
                    meses: 1
                },
                10: {
                    precio: '24.50',
                    usuarios: 6,
                    moviles: 0,
                    servidor: 4,
                    modulos: [false, false, false, false, false, false, false],
                    meses: 12
                },
                11: {
                    precio: periodo == 1 ? '6.49' : '77.94',
                    usuarios: 1,
                    moviles: 1,
                    servidor: 4,
                    modulos: [true, true, true, true, true, true, true],
                    meses: periodo == 1 ? 1 : 12
                },
                12: {
                    precio: periodo == 1 ? '5.40' : periodo == 2 ? '8.99' : periodo == 3 ? '17.99' : '4',
                    usuarios: 50,
                    moviles: 1,
                    servidor: 2,
                    modulos: [false, false, false, false, false, false, false],
                    meses: 12
                }
            } [producto];
        }

        function confirmarAccion(tipo, mensaje) {
            Swal.fire({
                title: "Advertencia",
                text: mensaje,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#tipo').val(tipo);
                    $("#formulario").submit();
                }
            });
        }

        function confirmarResetearClave() {
            Swal.fire({
                title: "Advertencia",
                text: '¿Está seguro de resetear la clave del usuario?',
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: "Cancelar",
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get('<?php echo e(route('editar_clave', [$cliente->sis_clientesid, $servidoresid, $licenciasid])); ?>', function(data) {
                        $.notify({
                            message: data.mensaje,
                        }, {
                            showProgressbar: true,
                            delay: 2500,
                            mouse_over: "pause",
                            placement: {
                                from: "top",
                                align: "right"
                            },
                            animate: {
                                enter: "animated fadeInUp",
                                exit: "animated fadeOutDown"
                            },
                            type: data.tipo,
                        });
                    });
                }
            });
        }

        function formatearFecha(fecha) {
            return ("0" + fecha.getDate()).slice(-2) + "-" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "-" + fecha.getFullYear();
        }
    </script>
<?php $__env->stopSection(); ?>
<?php /**PATH C:\laragon\www\admin\resources\views/admin/licencias/Web/_form.blade.php ENDPATH**/ ?>