<?php // config/sistema.php
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

    // Distribuidores especiales con reglas específicas para la visibilidad de clientes 
    'distribuidores_especiales' => [
        1  => 'ALFA',
        6  => 'MATRIZ',
        15 => 'SIGMA',
        12 => 'SOCIO'
    ],

    'permisos' => [

        'clientes' => [
            'crear_clientes' => [1, 2, 3],
            'guardar_clientes' => [1, 2],
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
        ],

        'pc' => [
            'crear_pc' => [1, 2, 3],
            'guardar_pc' => [1, 2, 3, 7],
            'renovar_licencia' => [1, 2, 3, 9],

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

        ],

        'vps' => [
            'crear_vps' => [1, 2],
        ],
    ],

    'productos' => [

        'web' => [
            2 => [ // Facturación
                'mensual' => [
                    'precio' => '11.69',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '113.09',
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
                ]
            ],
            3 => [ // Servicios
                'mensual' => [
                    'precio' => '19.49',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '202.79',
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
                ]
            ],
            4 => [ // Comercial
                'mensual' => [
                    'precio' => '27.29',
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
                    'precio' => '280.79',
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
                'usuarios' => 6,
                'moviles' => 2,
                'sucursales' => 0,
                'empresas' => 1,
                'servidor' => 3,
            ],
            5 => [ // Soy Contador Comercial
                'mensual' => [
                    'precio' => '15.59',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '140.39',
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
                ]
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
                ]
            ],
            8 => [ // Soy Contador Servicios
                'mensual' => [
                    'precio' => '11.69',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '116.99',
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
                ]
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
                ]
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
                ]
            ],
            11 => [ // Socio Perseo
                'mensual' => [
                    'precio' => '6.49',
                    'meses' => 1,
                ],
                'anual' => [
                    'precio' => '77.94',
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
                ]
            ],
            12 => [ // Facturito
                'inicial' => [
                    'precio' => '5.40',
                    'meses' => 12,
                ],
                'basico' => [
                    'precio' => '8.99',
                    'meses' => 12,
                ],
                'premium' => [
                    'precio' => '17.99',
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
                ]
            ],
        ],

        'pc' => [
            // Configuraciones base por módulo principal
            'modulos_principales' => [
                'practico' => [
                    'equipos' => 2,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'ids_aplicativos' => ['105', '110', '111', '112', '113', '114', '115', '117', '118', '120', '125', '126', '127', '130', '131', '135', '136', '141', '142', '150', '305', '310', '315', '320', '325', '330', '335', '430', '431', '432', '433', '434', '435', '440', '445', '450', '455', '456', '460', '461', '462', '463', '464', '465', '466', '469', '470', '471', '475', '480', '491', '492', '495', '630', '905', '910', '915', '916', '917', '918', '919', '920', '925', '930', '931', '940', '960', '1105', '1110', '1115', '1120'],
                    'incluye_nomina' => false,
                    'incluye_activos' => false
                ],
                'control' => [
                    'equipos' => 3,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'ids_aplicativos' => ['200', '142', '201', '205', '210', '215', '225', '230', '505', '510', '515', '516', '517', '462', '463', '485', '490', '116', '140', '605', '630', '635'],
                    'incluye_nomina' => false,
                    'incluye_activos' => false,
                    'hereda_de' => 'practico' // Incluye todo lo de práctico
                ],
                'contable' => [
                    'equipos' => 4,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'ids_aplicativos' => ['605', '142', '606', '610', '615', '616', '620', '625', '626', '627', '628', '630', '635', '636', '640'],
                    'incluye_nomina' => true,
                    'incluye_activos' => true,
                    'hereda_de' => 'control' // Incluye todo lo de control + práctico
                ],
                'nube' => [
                    'equipos' => 4,
                    'moviles' => 0,
                    'sucursales' => 1,
                    'ids_aplicativos' => [], // Hereda todo de contable
                    'incluye_nomina' => true,
                    'incluye_activos' => true,
                    'hereda_de' => 'contable', // Incluye todo lo de contable + control + práctico
                    'requiere_configuracion_nube' => true
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
                'usuarios_defecto' => 1
            ]
        ]
    ]
];
