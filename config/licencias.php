<?php
// config/licencias.php - CONFIGURACIÓN CENTRALIZADA MIGRADA

return [
    // === CONFIGURACIÓN DE FORMULARIOS ===
    'formularios' => [
        'web' => [
            'campos_base' => ['usuarios', 'numeromoviles', 'numerosucursales', 'empresas', 'precio'],
            'mapeo_campos' => [
                'numeromoviles' => 'moviles',
                'numerosucursales' => 'sucursales'
            ],
            'productos_especiales' => [6, 9, 10], // Productos con período fijo
            'producto_defecto' => 2, // Facturación
            'servidor_defecto' => 3,
        ],
        'pc' => [
            'campos_base' => ['equipos', 'moviles', 'sucursales', 'numerocontrato'],
            'modulos_principales' => ['practico', 'control', 'contable', 'nube'],
            'herencia_modulos' => [
                'control' => ['practico'],
                'contable' => ['control', 'practico'],
                'nube' => ['contable', 'control', 'practico']
            ],
            'periodos_disponibles' => [1 => 'Mensual', 2 => 'Anual', 3 => 'Venta']
        ]
    ],

    // === MAPEO DE PERÍODOS ===
    'periodos' => [
        'web' => [
            'normal' => [1 => 'mensual', 2 => 'anual'],
            'facturito' => [1 => 'inicial', 2 => 'basico', 3 => 'premium', 4 => 'gratis']
        ],
        'pc' => [
            1 => ['nombre' => 'Mensual', 'meses' => 1, 'meses_actualizaciones' => 12],
            2 => ['nombre' => 'Anual', 'meses' => 12, 'meses_actualizaciones' => 12],
            3 => ['nombre' => 'Venta', 'meses' => 60, 'meses_actualizaciones' => 12, 'sin_renovacion' => true]
        ],
        'etiquetas' => [
            'facturito' => [
                'inicial' => 'Inicial',
                'basico' => 'Básico',
                'premium' => 'Premium',
                'gratis' => 'Gratis'
            ]
        ]
    ],

    // === CONFIGURACIÓN DE EMAILS (Simplificada) ===
    'emails' => [
        'templates' => [
            'web' => 'emails.licenciaweb',
            'pc' => 'emails.licenciapc',
            'vps' => 'emails.licenciavps',
            'facturito' => 'emails.licenciaweb',
            'credenciales' => 'emails.credenciales'
        ],
        'subjects' => [
            'nuevo' => 'Nueva {producto}',
            'modificado' => '{producto} Modificada',
            'renovacion_mensual' => 'Renovación Mensual {producto}',
            'renovacion_anual' => 'Renovación Anual {producto}',
            'recarga_documentos' => 'Recarga de Documentos {producto}',
            'credenciales' => 'Credenciales de Acceso {producto}',
            'credenciales_simples' => 'Recordatorio Credenciales {producto}'
        ],
        'productos_nombres' => [
            'web' => 'Licencia Web',
            'pc' => 'Licencia PC',
            'vps' => 'Licencia VPS',
            'facturito' => 'Licencia Facturito'
        ],
        'attachments' => [
            'credenciales' => [
                'public_path/assets/media/Procedimiento Ingreso.pdf',
                'public_path/assets/media/Términos y Condiciones.pdf'
            ]
        ]
    ],

    // === VALIDACIONES POR TIPO ===
    'validaciones' => [
        'web' => [
            'producto' => ['required', 'integer', 'in:2,3,4,5,6,8,9,10,11,12'],
            'periodo' => ['required', 'integer', 'min:1', 'max:4'],
            'usuarios' => ['required', 'integer', 'min:1', 'max:1000'],
            'precio' => ['required', 'numeric', 'min:0'],
            'numeromoviles' => ['required', 'integer', 'min:0'],
            'numerosucursales' => ['required', 'integer', 'min:0'],
            'empresas' => ['required', 'integer', 'min:1'],
        ],
        'pc' => [
            'modulo_principal' => ['required', 'in:practico,control,contable,nube'],
            'equipos' => ['required', 'integer', 'min:1', 'max:50'],
            'numerocontrato' => ['required', 'string', 'max:50'],
            'periodo' => ['required', 'integer', 'in:1,2,3'],
        ]
    ],

    // === PRODUCTOS WEB (Migrados y Simplificados) ===
    'productos' => [
        'web' => [
            2 => [ // Facturación
                'nombre' => 'Facturación',
                'mensual' => ['precio' => 17.99, 'meses' => 1],
                'anual' => ['precio' => 173.99, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 1, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => true, 'produccion' => true, 'nomina' => false, 'activos' => false, 'restaurantes' => true, 'talleres' => false, 'garantias' => false],
                'adicionales' => [1, 2, 3]
            ],
            3 => [ // Servicios
                'nombre' => 'Servicios',
                'mensual' => ['precio' => 29.99, 'meses' => 1],
                'anual' => ['precio' => 311.99, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 0, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => false, 'produccion' => false, 'nomina' => true, 'activos' => true, 'restaurantes' => false, 'talleres' => false, 'garantias' => false],
                'adicionales' => [1, 2, 3]
            ],
            4 => [ // Comercial
                'nombre' => 'Comercial',
                'mensual' => [
                    'precio' => 41.99, 'meses' => 1,
                    'modulos' => ['ecommerce' => true, 'produccion' => true, 'nomina' => true, 'activos' => false, 'restaurantes' => false, 'talleres' => true, 'garantias' => true]
                ],
                'anual' => [
                    'precio' => 431.99, 'meses' => 12,
                    'modulos' => ['ecommerce' => true, 'produccion' => true, 'nomina' => true, 'activos' => true, 'restaurantes' => false, 'talleres' => true, 'garantias' => true]
                ],
                'usuarios' => 50, 'moviles' => 2, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'adicionales' => [1, 2, 3]
            ],
            5 => [ // Soy Contador Comercial
                'nombre' => 'Soy Contador Comercial',
                'mensual' => ['precio' => 23.99, 'meses' => 1],
                'anual' => ['precio' => 215.99, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 0, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => true, 'produccion' => true, 'nomina' => true, 'activos' => true, 'restaurantes' => true, 'talleres' => false, 'garantias' => false],
                'adicionales' => [1, 2, 3]
            ],
            6 => [ // Perseo Lite Anterior
                'nombre' => 'Perseo Lite Anterior',
                'anual' => ['precio' => 0, 'meses' => 12],
                'usuarios' => 3, 'moviles' => 1, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => false, 'produccion' => true, 'nomina' => true, 'activos' => true, 'restaurantes' => true, 'talleres' => true, 'garantias' => true],
                'adicionales' => [1, 2, 3]
            ],
            8 => [ // Soy Contador Servicios
                'nombre' => 'Soy Contador Servicios',
                'mensual' => ['precio' => 17.99, 'meses' => 1],
                'anual' => ['precio' => 179.99, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 0, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => false, 'produccion' => false, 'nomina' => false, 'activos' => false, 'restaurantes' => false, 'talleres' => false, 'garantias' => false],
                'adicionales' => [1, 2, 3]
            ],
            9 => [ // Perseo Lite (Demo)
                'nombre' => 'Perseo Lite',
                'mensual' => ['precio' => 0, 'meses' => 0.5],
                'usuarios' => 50, 'moviles' => 1, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => true, 'produccion' => true, 'nomina' => true, 'activos' => true, 'restaurantes' => true, 'talleres' => true, 'garantias' => true],
                'adicionales' => [1, 2, 3],
                'demo' => [
                    'dias_vigencia' => 15,
                    'parametros' => ['Documentos' => "100000", 'Productos' => "100000", 'Almacenes' => "1", 'Nomina' => "3", 'Produccion' => "3", 'Activos' => "3", 'Talleres' => "3", 'Garantias' => "3"]
                ]
            ],
            10 => [ // Emprendedor
                'nombre' => 'Emprendedor',
                'anual' => ['precio' => 24.50, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 0, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => false, 'produccion' => false, 'nomina' => false, 'activos' => false, 'restaurantes' => false, 'talleres' => false, 'garantias' => false],
                'adicionales' => [1, 2, 3]
            ],
            11 => [ // Socio Perseo
                'nombre' => 'Socio Perseo',
                'mensual' => ['precio' => 9.99, 'meses' => 1],
                'anual' => ['precio' => 119.90, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 1, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 3,
                'modulos' => ['ecommerce' => true, 'produccion' => true, 'nomina' => true, 'activos' => true, 'restaurantes' => true, 'talleres' => true, 'garantias' => true],
                'adicionales' => [1, 2, 3]
            ],
            12 => [ // Facturito
                'nombre' => 'Facturito',
                'inicial' => ['precio' => 8.99, 'meses' => 12],
                'basico' => ['precio' => 14.99, 'meses' => 12],
                'premium' => ['precio' => 29.99, 'meses' => 12],
                'gratis' => ['precio' => 4, 'meses' => 12],
                'usuarios' => 50, 'moviles' => 1, 'sucursales' => 0, 'empresas' => 1, 'servidor' => 2,
                'modulos' => ['ecommerce' => false, 'produccion' => false, 'nomina' => false, 'activos' => false, 'restaurantes' => false, 'talleres' => false, 'garantias' => false],
                'adicionales' => [1, 2, 3]
            ]
        ],

        // === PRODUCTOS PC (Reorganizados) ===
        'pc' => [
            'modulos_principales' => [
                'practico' => [
                    'nombre' => 'Práctico',
                    'equipos' => 2, 'moviles' => 0, 'sucursales' => 1,
                    'precios' => ['mensual' => 33, 'anual' => 310, 'venta' => 700],
                    'ids_aplicativos' => ['105', '110', '111', '112', '113', '114', '115', '117', '118', '120', '125', '126', '127', '130', '131', '135', '136', '141', '142', '150', '305', '310', '315', '320', '325', '330', '335', '430', '431', '432', '433', '434', '435', '440', '445', '450', '455', '456', '460', '461', '462', '463', '464', '465', '466', '469', '470', '471', '475', '480', '486', '491', '492', '495', '630', '905', '910', '915', '916', '917', '918', '919', '920', '925', '930', '931', '940', '960', '1105', '1110', '1115', '1120'],
                    'incluye_nomina' => false, 'incluye_activos' => false, 'incluye_produccion' => true, 'adicionales' => [1, 2, 3]
                ],
                'control' => [
                    'nombre' => 'Control',
                    'equipos' => 3, 'moviles' => 0, 'sucursales' => 1,
                    'precios' => ['mensual' => 63, 'anual' => 560, 'venta' => 1400],
                    'ids_aplicativos' => ['200', '142', '201', '205', '210', '215', '225', '230', '505', '510', '515', '516', '517', '462', '463', '485', '490', '116', '140', '605', '630', '635'],
                    'incluye_nomina' => false, 'incluye_activos' => false, 'incluye_produccion' => true, 'hereda_de' => 'practico', 'adicionales' => [1, 2, 3]
                ],
                'contable' => [
                    'nombre' => 'Contable',
                    'equipos' => 4, 'moviles' => 0, 'sucursales' => 1,
                    'precios' => ['mensual' => 77, 'anual' => 660, 'venta' => 2000],
                    'ids_aplicativos' => ['605', '142', '606', '610', '615', '616', '620', '625', '626', '627', '628', '630', '635', '636', '640'],
                    'incluye_nomina' => true, 'incluye_activos' => true, 'incluye_produccion' => true, 'hereda_de' => 'control', 'adicionales' => [1, 2, 3]
                ],
                'nube' => [
                    'nombre' => 'Nube',
                    'equipos' => 4, 'moviles' => 0, 'sucursales' => 1,
                    'precios' => [
                        'prime' => ['nivel1' => 970, 'nivel2' => 1300, 'nivel3' => 1420],
                        'contaplus' => ['nivel1' => 700, 'nivel2' => 950, 'nivel3' => 1200]
                    ],
                    'ids_aplicativos' => [], 'incluye_nomina' => true, 'incluye_activos' => true, 'incluye_produccion' => true,
                    'hereda_de' => 'contable', 'requiere_configuracion_nube' => true, 'adicionales' => [4, 5]
                ]
            ],
            'modulos_adicionales' => [
                'nomina' => ['ids_aplicativos' => ['705', '710', '715', '720', '725', '730', '735', '740', '741', '745']],
                'activos' => ['ids_aplicativos' => ['805', '806', '810', '815', '816', '820']],
                'produccion' => ['ids_aplicativos' => ['1005', '1010', '1015']],
                'tvcable' => ['ids_aplicativos' => ['1200', '1205', '1210', '1215', '1220']],
                'encomiendas' => ['ids_aplicativos' => ['1601', '1610', '1615', '1620', '1625']],
                'crmcartera' => ['ids_aplicativos' => ['220']],
                'ahorros' => ['ids_aplicativos' => ['1705', '1710', '1715', '1716', '1720', '1725']],
                'apiwhatsapp' => ['ids_aplicativos' => ['950']],
                'restaurante' => ['ids_aplicativos' => ['1500', '1505', '1510', '1515', '1520']],
                'garantias' => ['ids_aplicativos' => ['1300', '1305', '1310']],
                'talleres' => ['ids_aplicativos' => ['1400', '1405', '1410']],
                'academico' => ['ids_aplicativos' => ['1805', '1810', '1815', '1820', '1825', '1830']]
            ],
            'configuracion_nube' => [
                'usuarios_por_tipo' => [1 => 4, 2 => 6] // Prime, Contaplus
            ]
        ]
    ],

    // === TIPOS ADICIONALES (Simplificados) ===
    'adicionales' => [
        1 => [ // App Ventas
            'nombre' => 'App Ventas', 'descripcion' => 'Licencias móviles', 'icono' => 'fa-mobile',
            'campo_licencia' => 'numeromoviles2', 'precio_strategy' => 'simple',
            'precios' => ['pc' => ['mensual' => 12, 'anual' => 120], 'web' => ['mensual' => 20.50, 'anual' => 15.00]]
        ],
        2 => [ // Sucursales
            'nombre' => 'Sucursales', 'descripcion' => 'Sucursales del sistema', 'icono' => 'fa-building',
            'campo_licencia' => 'numerosucursales', 'precio_strategy' => 'simple',
            'precios' => ['pc' => ['mensual' => 15, 'anual' => 200], 'web' => ['mensual' => 1.50, 'anual' => 15.00]]
        ],
        3 => [ // Equipos
            'nombre' => 'Equipos', 'descripcion' => 'Equipos para instalación', 'icono' => 'fa-desktop',
            'campo_licencia' => 'numeroequipos', 'precio_strategy' => 'simple', 'campos_relacionados' => ['numeromoviles'],
            'precios' => ['pc' => ['mensual' => 15, 'anual' => 200], 'web' => ['mensual' => 10.50, 'anual' => 15.00]]
        ],
        4 => [ // Usuarios Nube
            'nombre' => 'Usuarios Nube', 'descripcion' => 'Usuarios para licencias de nube', 'icono' => 'fa-user',
            'campo_licencia' => 'usuarios_nube', 'precio_strategy' => 'nube',
            'precios' => [
                'prime' => ['nivel1' => ['mensual' => 15, 'anual' => 150], 'nivel2' => ['mensual' => 18, 'anual' => 180], 'nivel3' => ['mensual' => 20, 'anual' => 200]],
                'contaplus' => ['nivel1' => ['mensual' => 12, 'anual' => 120], 'nivel2' => ['mensual' => 15, 'anual' => 150], 'nivel3' => ['mensual' => 18, 'anual' => 180]]
            ]
        ],
        5 => [ // Empresas Nube
            'nombre' => 'Empresas Nube', 'descripcion' => 'Empresas para licencias de nube', 'icono' => 'fa-user',
            'campo_licencia' => 'empresas', 'precio_strategy' => 'nube',
            'precios' => [
                'prime' => ['nivel1' => ['mensual' => 15, 'anual' => 150], 'nivel2' => ['mensual' => 18, 'anual' => 180], 'nivel3' => ['mensual' => 20, 'anual' => 200]],
                'contaplus' => ['nivel1' => ['mensual' => 12, 'anual' => 120], 'nivel2' => ['mensual' => 15, 'anual' => 150], 'nivel3' => ['mensual' => 18, 'anual' => 180]]
            ]
        ]
    ]
];
