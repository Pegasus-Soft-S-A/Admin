<?php // config/tipos_usuario.php
return [
    'tipos' => [
        1 => 'Admin',
        2 => 'Distribuidor',
        3 => 'Soporte distribuidor',
        4 => 'Ventas',
        5 => 'Marketing',
        6 => 'Visor',
        7 => 'Soporte matriz',
        8 => 'Comercial',
        9 => 'Posventa'
    ],

    'permisos' => [

        'clientes' => [
            'crear_clientes' => [1, 2, 3],
            'guardar_clientes' => [1, 2],
        ],

        'web' => [
            'crear_web' => [1, 2],
            'resetear_clave_web' => [1, 2, 3, 5, 6, 7],
        ],

        'pc' => [
            'crear_pc' => [1, 2, 3],
        ],

        'vps' => [
            'crear_vps' => [1, 2,],
        ],
    ]
];
