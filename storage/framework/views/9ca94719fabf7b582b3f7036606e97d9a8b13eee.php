<div id="kt_header" class="header header-fixed">

    <div class="container-fluid d-flex align-items-stretch justify-content-between">

        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">

        </div>

        <div class="topbar">
            
            
            <div class="topbar-item">
                <?php echo $__env->make('admin.partials.usuarios', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo $__env->make('admin.layouts.user_panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </div>
        </div>

    </div>

</div>

<?php /**PATH C:\laragon\www\admin\resources\views/admin/inc/navBar.blade.php ENDPATH**/ ?>