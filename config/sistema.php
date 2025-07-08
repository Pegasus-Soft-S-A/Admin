<?php // config/sistema.php
return [
    'tipos_roles' => [
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

    'tipos_productos' => [
        1 => 'Web',
        2 => 'PC',
        3 => 'VPS'
    ],

    'tipos_venta_adicionales' => [
        1 => 'Usuarios',
        2 => 'Moviles',
        3 => 'Sucursales',
        4 => 'Estaciones',
    ],

    'permisos' => [

        'clientes' => [
            'crear_clientes' => [1, 2, 3],
            'guardar_clientes' => [1, 2],
            'eliminar_clientes' => [1],
        ],

        'web' => [
            'crear_web' => [1, 2],
            'resetear_clave_web' => [1, 2, 3, 5, 6, 7],

            // Permisos de licencias web
            'editar_producto_modificar' => [1], // Solo admin puede editar producto en modo modificación
            'editar_periodo_crear' => [1, 2, 3, 4, 5, 6, 7, 8, 9], // Todos pueden editar periodo en creación
            'editar_periodo_modificar' => [1], // Solo admin puede editar periodo en modificación
            'editar_campos_numericos' => [1], // Solo admin puede editar valores numéricos (precio, usuarios, empresas, etc.)
            'editar_fechas' => [1], // Solo admin puede editar fechas de inicio y caducidad
            'editar_modulos' => [1], // Solo admin puede editar módulos (checkboxes)
            'mostrar_renovar' => [1, 2, 9], // Admin, Distribuidor y Posventa pueden renovar
            'editar_agrupados' => [1], // Solo admin puede editar agrupados
            'editar_adicionales_crear' => [1],
            'editar_adicionales_modificar' => [1],
        ],

        'pc' => [
            'crear_pc' => [1, 2, 3],
            'guardar_pc' => [1, 2, 3, 7],
            'renovar_licencia' => [1, 2, 3, 9],
            'ver_adicionales' => [1, 8, 9],

            // === PERMISOS PARA CREAR ===
            'editar_configuracion_tecnica_crear' => [1, 2, 3, 7], // Identificador, IPs
            'editar_numeros_configuracion_crear' => [1, 2, 3, 7], // Equipos, móviles, sucursales
            'editar_bd_crear' => [1], // Puertos, Usuario BD, Clave BD
            'editar_puerto_bd_crear' => [1], // Puertos, Usuario BD, Clave BD
            'editar_puerto_movil_crear' => [1, 2, 3, 7], // Puertos, Usuario BD, Clave BD
            'editar_correos_crear' => [1, 2, 3, 7],
            'editar_modulos_principales_crear' => [1, 2, 3, 7], // Practico, Control, Contable, Nube
            'editar_modulos_adicionales_crear' => [1], // Solo admin puede módulos adicionales
            'editar_periodo_crear' => [1, 2, 3, 7],
            'editar_nube_crear' => [1, 2, 3, 7],
            'editar_avanzados_crear' => [1], // Estado, fechas, tokens, etc.

            // === PERMISOS PARA MODIFICAR ===
            'editar_configuracion_tecnica_modificar' => [1, 2, 3, 7], // Identificador, IPs - SOPORTE MATRIZ SÍ puede
            'editar_numeros_configuracion_modificar' => [1, 2], // Equipos, móviles, sucursales - Soporte matriz NO
            'editar_bd_modificar' => [1], // Puertos, Usuario BD, Clave BD - Solo admin y soporte matriz
            'editar_puerto_bd_modificar' => [1, 7], // Puertos, Usuario BD, Clave BD - Solo admin y soporte matriz
            'editar_puerto_movil_modificar' => [1, 3, 7], // Puertos, Usuario BD, Clave BD - Solo admin y soporte matriz
            'editar_correos_modificar' => [1], // Solo admin
            'editar_modulos_principales_modificar' => [1], // Solo admin
            'editar_modulos_adicionales_modificar' => [1], // Solo admin
            'editar_periodo_modificar' => [1], // Solo admin
            'editar_nube_modificar' => [1], // Solo admin
            'editar_avanzados_modificar' => [1], // Estado, fechas, tokens, etc.
            'editar_adicionales_modificar' => [1],
        ],

        'vps' => [
            'crear_vps' => [1, 2],
        ],
    ],

    'tipos_adicionales' => [
        1 => [
            'nombre' => 'Móviles',
            'descripcion' => 'Licencias móviles',
            'icono' => 'fa-mobile',
            'campo_licencia' => 'numeromoviles',
            'precios' => [
                'pc' => [
                    'mensual' => 12,
                    'anual' => 120
                ],
                'web' => [
                    'mensual' => 20.50,
                    'anual' => 15.00
                ],
            ]
        ],
        2 => [
            'nombre' => 'Sucursales',
            'descripcion' => 'Sucursales del sistema',
            'icono' => 'fa-building',
            'campo_licencia' => 'numerosucursales',
            'precios' => [
                'pc' => [
                    'mensual' => 15,
                    'anual' => 200
                ],
                'web' => [
                    'mensual' => 1.50,
                    'anual' => 15.00
                ],
            ]
        ],
        3 => [
            'nombre' => 'Equipos',
            'descripcion' => 'Equipos para instalación',
            'icono' => 'fa-desktop',
            'campo_licencia' => 'numeroequipos',
            'precios' => [
                'pc' => [
                    'mensual' => 15,
                    'anual' => 200
                ],
                'web' => [
                    'mensual' => 10.50,
                    'anual' => 15.00
                ],
            ]
        ],
        4 => [
            'nombre' => 'Usuarios Nube',
            'descripcion' => 'Usuarios para licencias de nube',
            'icono' => 'fa-user',
            'campo_licencia' => 'usuarios_nube',
            'precios' => [
                'prime' => [
                    'nivel1' => ['mensual' => 15, 'anual' => 150],
                    'nivel2' => ['mensual' => 18, 'anual' => 180],
                    'nivel3' => ['mensual' => 20, 'anual' => 200]
                ],
                'contaplus' => [
                    'nivel1' => ['mensual' => 12, 'anual' => 120],
                    'nivel2' => ['mensual' => 15, 'anual' => 150],
                    'nivel3' => ['mensual' => 18, 'anual' => 180]
                ]
            ]
        ]
    ],

    'productos' => [
        'web' => [
            2 => [ // Facturación
                'mensual' => [
                    'precio' => '17.99',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '173.99',
                    'meses' => 12,
                ],
                'usuarios' => 6,
                'moviles' => 1,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => true,
                    'produccion' => true,
                    'nomina' => false,
                    'activos' => false,
                    'restaurantes' => true,
                    'talleres' => false,
                    'garantias' => false,
                ],
                'adicionales' => [1, 2, 3]
            ],
            3 => [ // Servicios
                'mensual' => [
                    'precio' => '29.99',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '311.99',
                    'meses' => 12,
                ],
                'usuarios' => 6,
                'moviles' => 0,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => false,
                    'produccion' => false,
                    'nomina' => true,
                    'activos' => true,
                    'restaurantes' => false,
                    'talleres' => false,
                    'garantias' => false,
                ],
                'adicionales' => [1, 2, 3]
            ],
            4 => [ // Comercial
                'mensual' => [
                    'precio' => '41.99',
                    'meses' => 1,
                    'modulos' => [
                        'ecommerce' => true,
                        'produccion' => true,
                        'nomina' => true,
                        'activos' => false, // Sin activos en mensual
                        'restaurantes' => false,
                        'talleres' => true,
                        'garantias' => true,
                    ]
                ],
                'anual' => [
                    'precio' => '431.99',
                    'meses' => 12,
                    'modulos' => [
                        'ecommerce' => true,
                        'produccion' => true,
                        'nomina' => true,
                        'activos' => true, // Con activos en anual
                        'restaurantes' => false,
                        'talleres' => true,
                        'garantias' => true,
                    ]
                ],
                'adicionales' => [1, 2, 3],
                'usuarios' => 6,
                'moviles' => 2,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
            ],
            5 => [ // Soy Contador Comercial
                'mensual' => [
                    'precio' => '23.99',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '215.99',
                    'meses' => 12,
                ],
                'usuarios' => 6,
                'moviles' => 0,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => true,
                    'produccion' => true,
                    'nomina' => true,
                    'activos' => true,
                    'restaurantes' => true,
                    'talleres' => false,
                    'garantias' => false,
                ],
                'adicionales' => [1, 2, 3]
            ],
            6 => [ // Perseo Lite Anterior
                'anual' => [
                    'precio' => '0',
                    'meses' => 12,
                ],
                'usuarios' => 3,
                'moviles' => 1,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => false,
                    'produccion' => true,
                    'nomina' => true,
                    'activos' => true,
                    'restaurantes' => true,
                    'talleres' => true,
                    'garantias' => true,
                ],
                'adicionales' => [1, 2, 3]
            ],
            8 => [ // Soy Contador Servicios
                'mensual' => [
                    'precio' => '17.99',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '179.99',
                    'meses' => 12,
                ],
                'usuarios' => 6,
                'moviles' => 0,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => false,
                    'produccion' => false,
                    'nomina' => false,
                    'activos' => false,
                    'restaurantes' => false,
                    'talleres' => false,
                    'garantias' => false,
                ],
                'adicionales' => [1, 2, 3]
            ],
            9 => [ // Perseo Lite
                'mensual' => [
                    'precio' => '0',
                    'meses' => 1,
                ],
                'usuarios' => 6,
                'moviles' => 1,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => true,
                    'produccion' => true,
                    'nomina' => true,
                    'activos' => true,
                    'restaurantes' => true,
                    'talleres' => true,
                    'garantias' => true,
                ],
                'adicionales' => [1, 2, 3]
            ],
            10 => [ // Emprendedor
                'anual' => [
                    'precio' => '24.50',
                    'meses' => 12,
                ],
                'usuarios' => 6,
                'moviles' => 0,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => false,
                    'produccion' => false,
                    'nomina' => false,
                    'activos' => false,
                    'restaurantes' => false,
                    'talleres' => false,
                    'garantias' => false,
                ],
                'adicionales' => [1, 2, 3]
            ],
            11 => [ // Socio Perseo
                'mensual' => [
                    'precio' => '9.99',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '119.90',
                    'meses' => 12,
                ],
                'usuarios' => 1,
                'moviles' => 1,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
                'modulos' => [
                    'ecommerce' => true,
                    'produccion' => true,
                    'nomina' => true,
                    'activos' => true,
                    'restaurantes' => true,
                    'talleres' => true,
                    'garantias' => true,
                ],
                'adicionales' => [1, 2, 3]
            ],
            12 => [ // Facturito
                'inicial' => [
                    'precio' => '8.99',
                    'meses' => 12,
                ],
                'basico' => [
                    'precio' => '14.99',
                    'meses' => 12,
                ],
                'premium' => [
                    'precio' => '29.99',
                    'meses' => 12,
                ],
                'gratis' => [
                    'precio' => '4',
                    'meses' => 12,
                ],
                'usuarios' => 50,
                'moviles' => 1,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 2,
                'modulos' => [
                    'ecommerce' => false,
                    'produccion' => false,
                    'nomina' => false,
                    'activos' => false,
                    'restaurantes' => false,
                    'talleres' => false,
                    'garantias' => false,
                ],
                'adicionales' => [1, 2, 3]
            ],
        ],

        'pc' => [
            // Configuraciones base por módulo principal
            'modulos_principales' => [
                'practico' => [
                    'equipos' => 2,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'precios' => [
                        'mensual' => 33,
                        'anual' => 310,
                        'venta' => 700
                    ],
                    'ids_aplicativos' => ['105', '110', '111', '112', '113', '114', '115', '117', '118', '120', '125', '126', '127', '130', '131', '135', '136', '141', '142', '150', '305', '310', '315', '320', '325', '330', '335', '430', '431', '432', '433', '434', '435', '440', '445', '450', '455', '456', '460', '461', '462', '463', '464', '465', '466', '469', '470', '471', '475', '480', '491', '492', '495', '630', '905', '910', '915', '916', '917', '918', '919', '920', '925', '930', '931', '940', '960', '1105', '1110', '1115', '1120'],
                    'incluye_nomina' => false,
                    'incluye_activos' => false,
                    'adicionales' => [1, 2, 3]
                ],
                'control' => [
                    'equipos' => 3,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'precios' => [
                        'mensual' => 63,
                        'anual' => 560,
                        'venta' => 1400
                    ],
                    'ids_aplicativos' => ['200', '142', '201', '205', '210', '215', '225', '230', '505', '510', '515', '516', '517', '462', '463', '485', '490', '116', '140', '605', '630', '635'],
                    'incluye_nomina' => false,
                    'incluye_activos' => false,
                    'hereda_de' => 'practico', // Incluye lo de práctico
                    'adicionales' => [1, 2, 3]
                ],
                'contable' => [
                    'equipos' => 4,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'precios' => [
                        'mensual' => 77,
                        'anual' => 660,
                        'venta' => 2000
                    ],
                    'ids_aplicativos' => ['605', '142', '606', '610', '615', '616', '620', '625', '626', '627', '628', '630', '635', '636', '640'],
                    'incluye_nomina' => true,
                    'incluye_activos' => true,
                    'hereda_de' => 'control', // Incluye lo de control + práctico
                    'adicionales' => [1, 2, 3]
                ],
                'nube' => [
                    'equipos' => 4,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'precios' => [
                        'prime' => [
                            'nivel1' => 970,
                            'nivel2' => 1300,
                            'nivel3' => 1420
                        ],
                        'contaplus' => [
                            'nivel1' => 700,
                            'nivel2' => 950,
                            'nivel3' => 1200
                        ]
                    ],
                    'ids_aplicativos' => [], // Hereda de contable
                    'incluye_nomina' => true,
                    'incluye_activos' => true,
                    'hereda_de' => 'contable', // Incluye lo de contable + control + práctico
                    'requiere_configuracion_nube' => true,
                    'adicionales' => [1, 2, 3, 4]
                ]
            ],

            // Módulos adicionales
            'modulos_adicionales' => [
                'nomina' => [
                    'ids_aplicativos' => ['705', '710', '715', '720', '725', '730', '735', '740', '741', '745']
                ],
                'activos' => [
                    'ids_aplicativos' => ['805', '806', '810', '815', '816', '820']
                ],
                'produccion' => [
                    'ids_aplicativos' => ['1005', '1010', '1015']
                ],
                'tvcable' => [
                    'ids_aplicativos' => ['1200', '1205', '1210', '1215', '1220']
                ],
                'encomiendas' => [
                    'ids_aplicativos' => ['1601', '1610', '1615', '1620', '1625']
                ],
                'crmcartera' => [
                    'ids_aplicativos' => ['220']
                ],
                'ahorros' => [
                    'ids_aplicativos' => ['1705', '1710', '1715', '1716', '1720', '1725']
                ],
                'apiwhatsapp' => [
                    'ids_aplicativos' => ['950']
                ],
                'hybrid' => [
                    'ids_aplicativos' => ['950']
                ],
                'woocomerce' => [
                    'ids_aplicativos' => ['950']
                ],
                'tienda' => [
                    'ids_aplicativos' => ['950']
                ],
                'restaurante' => [
                    'ids_aplicativos' => ['1500', '1505', '1510', '1515', '1520']
                ],
                'garantias' => [
                    'ids_aplicativos' => ['1300', '1305', '1310']
                ],
                'talleres' => [
                    'ids_aplicativos' => ['1400', '1405', '1410']
                ],
                'academico' => [
                    'ids_aplicativos' => ['1805', '1810', '1815', '1820', '1825', '1830']
                ],
                'perseo_contador' => [
                    'ids_aplicativos' => []
                ],
                'api_urbano' => [
                    'ids_aplicativos' => []
                ],
                'integraciones' => [
                    'ids_aplicativos' => []
                ],
                'cashmanager' => [
                    'ids_aplicativos' => []
                ],
                'cashdebito' => [
                    'ids_aplicativos' => []
                ],
                'equifax' => [
                    'ids_aplicativos' => []
                ]
            ],

            // Períodos disponibles
            'periodos' => [
                '1' => [
                    'nombre' => 'Mensual',
                    'meses' => 1,
                    'meses_actualizaciones' => 12 // Actualizaciones siempre anuales
                ],
                '2' => [
                    'nombre' => 'Anual',
                    'meses' => 12,
                    'meses_actualizaciones' => 12
                ],
                '3' => [
                    'nombre' => 'Venta',
                    'meses' => 60, // 5 años
                    'meses_actualizaciones' => 12,
                    'sin_renovacion' => true // No se puede renovar, solo actualizaciones
                ]
            ],

            // Configuración específica de nube
            'configuracion_nube' => [
                'usuarios_por_tipo' => [
                    1 => 4, // Prime
                    2 => 6  // Contaplus
                ]
            ]
        ]
    ]
];
