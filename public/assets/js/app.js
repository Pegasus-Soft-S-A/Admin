$(document).ready(function () {

    //Iniciar select2
    $('.select2').select2({
        width: '100%'
    });

    //Inicializar Datepicker en Español
    $.fn.datepicker.dates['es'] = {
        days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
        daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
        daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        today: "Hoy",
        monthsTitle: "Meses",
        clear: "Borrar",
        weekStart: 1,
        format: "dd-mm-yyyy"
    };

    //Inicializar Lenguaje Español Datatables
    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "processing": "<button type='button' class='btn btn-primary spinner spinner-white spinner-right'>Cargando</button>",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "searchPlaceholder": "Término de búsqueda",
            "infoThousands": ",",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad",
                "collection": "Colección",
                "colvisRestore": "Restaurar visibilidad",
                "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                "copySuccess": {
                    "1": "Copiada 1 fila al portapapeles",
                    "_": "Copiadas %d fila al portapapeles"
                },
                "copyTitle": "Copiar al portapapeles",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                    "-1": "Mostrar todas las filas",
                    "_": "Mostrar %d filas"
                },
                "pdf": "PDF",
                "print": "Imprimir"
            },
            "autoFill": {
                "cancel": "Cancelar",
                "fill": "Rellene todas las celdas con <i>%d<\/i>",
                "fillHorizontal": "Rellenar celdas horizontalmente",
                "fillVertical": "Rellenar celdas verticalmentemente"
            },
            "decimal": ",",
            "searchBuilder": {
                "add": "Añadir condición",
                "button": {
                    "0": "Constructor de búsqueda",
                    "_": "Constructor de búsqueda (%d)"
                },
                "clearAll": "Borrar todo",
                "condition": "Condición",
                "conditions": {
                    "date": {
                        "after": "Despues",
                        "before": "Antes",
                        "between": "Entre",
                        "empty": "Vacío",
                        "equals": "Igual a",
                        "notBetween": "No entre",
                        "notEmpty": "No Vacio",
                        "not": "Diferente de"
                    },
                    "number": {
                        "between": "Entre",
                        "empty": "Vacio",
                        "equals": "Igual a",
                        "gt": "Mayor a",
                        "gte": "Mayor o igual a",
                        "lt": "Menor que",
                        "lte": "Menor o igual que",
                        "notBetween": "No entre",
                        "notEmpty": "No vacío",
                        "not": "Diferente de"
                    },
                    "string": {
                        "contains": "Contiene",
                        "empty": "Vacío",
                        "endsWith": "Termina en",
                        "equals": "Igual a",
                        "notEmpty": "No Vacio",
                        "startsWith": "Empieza con",
                        "not": "Diferente de"
                    },
                    "array": {
                        "not": "Diferente de",
                        "equals": "Igual",
                        "empty": "Vacío",
                        "contains": "Contiene",
                        "notEmpty": "No Vacío",
                        "without": "Sin"
                    }
                },
                "data": "Data",
                "deleteTitle": "Eliminar regla de filtrado",
                "leftTitle": "Criterios anulados",
                "logicAnd": "Y",
                "logicOr": "O",
                "rightTitle": "Criterios de sangría",
                "title": {
                    "0": "Constructor de búsqueda",
                    "_": "Constructor de búsqueda (%d)"
                },
                "value": "Valor"
            },
            "searchPanes": {
                "clearMessage": "Borrar todo",
                "collapse": {
                    "0": "Paneles de búsqueda",
                    "_": "Paneles de búsqueda (%d)"
                },
                "count": "{total}",
                "countFiltered": "{shown} ({total})",
                "emptyPanes": "Sin paneles de búsqueda",
                "loadMessage": "Cargando paneles de búsqueda",
                "title": "Filtros Activos - %d"
            },
            "select": {
                "cells": {
                    "1": "1 celda seleccionada",
                    "_": "$d celdas seleccionadas"
                },
                "columns": {
                    "1": "1 columna seleccionada",
                    "_": "%d columnas seleccionadas"
                },
                "rows": {
                    "1": "1 fila seleccionada",
                    "_": "%d filas seleccionadas"
                }
            },
            "thousands": ".",
            "datetime": {
                "previous": "Anterior",
                "next": "Proximo",
                "hours": "Horas",
                "minutes": "Minutos",
                "seconds": "Segundos",
                "unknown": "-",
                "amPm": [
                    "AM",
                    "PM"
                ],
                "months": {
                    "0": "Enero",
                    "1": "Febrero",
                    "10": "Noviembre",
                    "11": "Diciembre",
                    "2": "Marzo",
                    "3": "Abril",
                    "4": "Mayo",
                    "5": "Junio",
                    "6": "Julio",
                    "7": "Agosto",
                    "8": "Septiembre",
                    "9": "Octubre"
                },
                "weekdays": [
                    "Dom",
                    "Lun",
                    "Mar",
                    "Mie",
                    "Jue",
                    "Vie",
                    "Sab"
                ]
            },
            "editor": {
                "close": "Cerrar",
                "create": {
                    "button": "Nuevo",
                    "title": "Crear Nuevo Registro",
                    "submit": "Crear"
                },
                "edit": {
                    "button": "Editar",
                    "title": "Editar Registro",
                    "submit": "Actualizar"
                },
                "remove": {
                    "button": "Eliminar",
                    "title": "Eliminar Registro",
                    "submit": "Eliminar",
                    "confirm": {
                        "_": "¿Está seguro que desea eliminar %d filas?",
                        "1": "¿Está seguro que desea eliminar 1 fila?"
                    }
                },
                "error": {
                    "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
                },
                "multi": {
                    "title": "Múltiples Valores",
                    "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
                    "restore": "Deshacer Cambios",
                    "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
                }
            },
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros"
        }
    });

    //Inicializar Lenguaje Español Summernote
    $.extend($.summernote.lang, {
        'es-ES': {
            font: {
                bold: 'Negrita',
                italic: 'Cursiva',
                underline: 'Subrayado',
                clear: 'Eliminar estilo de letra',
                height: 'Altura de línea',
                name: 'Tipo de letra',
                strikethrough: 'Tachado',
                subscript: 'Subíndice',
                superscript: 'Superíndice',
                size: 'Tamaño de la fuente',
                sizeunit: 'Unidad del tamaño de letra',
            },
            image: {
                image: 'Imagen',
                insert: 'Insertar imagen',
                resizeFull: 'Redimensionar a tamaño completo',
                resizeHalf: 'Redimensionar a la mitad',
                resizeQuarter: 'Redimensionar a un cuarto',
                resizeNone: 'Tamaño original',
                floatLeft: 'Flotar a la izquierda',
                floatRight: 'Flotar a la derecha',
                floatNone: 'No flotar',
                shapeRounded: 'Forma: Redondeado',
                shapeCircle: 'Forma: Círculo',
                shapeThumbnail: 'Forma: Miniatura',
                shapeNone: 'Forma: Ninguna',
                dragImageHere: 'Arrastre una imagen o texto aquí',
                dropImage: 'Suelte una imagen o texto',
                selectFromFiles: 'Seleccione un fichero',
                maximumFileSize: 'Tamaño máximo del fichero',
                maximumFileSizeError: 'Superado el tamaño máximo de fichero.',
                url: 'URL de la imagen',
                remove: 'Eliminar la imagen',
                original: 'Original',
            },
            video: {
                video: 'Vídeo',
                videoLink: 'Enlace del vídeo',
                insert: 'Insertar un vídeo',
                url: 'URL del vídeo',
                providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion o Youku)',
            },
            link: {
                link: 'Enlace',
                insert: 'Insertar un enlace',
                unlink: 'Quitar el enlace',
                edit: 'Editar',
                textToDisplay: 'Texto a mostrar',
                url: '¿A qué URL lleva este enlace?',
                openInNewWindow: 'Abrir en una nueva ventana',
                useProtocol: 'Usar el protocolo predefinido',
            },
            table: {
                table: 'Tabla',
                addRowAbove: 'Añadir una fila encima',
                addRowBelow: 'Añadir una fila debajo',
                addColLeft: 'Añadir una columna a la izquierda',
                addColRight: 'Añadir una columna a la derecha',
                delRow: 'Borrar la fila',
                delCol: 'Borrar la columna',
                delTable: 'Borrar la tabla',
            },
            hr: {
                insert: 'Insertar una línea horizontal',
            },
            style: {
                style: 'Estilo',
                p: 'Normal',
                blockquote: 'Cita',
                pre: 'Código',
                h1: 'Título 1',
                h2: 'Título 2',
                h3: 'Título 3',
                h4: 'Título 4',
                h5: 'Título 5',
                h6: 'Título 6',
            },
            lists: {
                unordered: 'Lista',
                ordered: 'Lista numerada',
            },
            options: {
                help: 'Ayuda',
                fullscreen: 'Pantalla completa',
                codeview: 'Ver el código fuente',
            },
            paragraph: {
                paragraph: 'Párrafo',
                outdent: 'Reducir la sangría',
                indent: 'Aumentar la sangría',
                left: 'Alinear a la izquierda',
                center: 'Centrar',
                right: 'Alinear a la derecha',
                justify: 'Justificar',
            },
            color: {
                recent: 'Último color',
                more: 'Más colores',
                background: 'Color de fondo',
                foreground: 'Color del texto',
                transparent: 'Transparente',
                setTransparent: 'Establecer transparente',
                reset: 'Restablecer',
                resetToDefault: 'Restablecer a los valores predefinidos',
                cpSelect: 'Seleccionar',
            },
            shortcut: {
                shortcuts: 'Atajos de teclado',
                close: 'Cerrar',
                textFormatting: 'Formato de texto',
                action: 'Acción',
                paragraphFormatting: 'Formato de párrafo',
                documentStyle: 'Estilo de documento',
                extraKeys: 'Teclas adicionales',
            },
            help: {
                insertParagraph: 'Insertar un párrafo',
                undo: 'Deshacer la última acción',
                redo: 'Rehacer la última acción',
                tab: 'Tabular',
                untab: 'Eliminar tabulación',
                bold: 'Establecer estilo negrita',
                italic: 'Establecer estilo cursiva',
                underline: 'Establecer estilo subrayado',
                strikethrough: 'Establecer estilo tachado',
                removeFormat: 'Limpiar estilo',
                justifyLeft: 'Alinear a la izquierda',
                justifyCenter: 'Alinear al centro',
                justifyRight: 'Alinear a la derecha',
                justifyFull: 'Justificar',
                insertUnorderedList: 'Insertar lista',
                insertOrderedList: 'Insertar lista numerada',
                outdent: 'Reducir sangría del párrafo',
                indent: 'Aumentar sangría del párrafo',
                formatPara: 'Cambiar el formato del bloque actual a párrafo (etiqueta P)',
                formatH1: 'Cambiar el formato del bloque actual a H1',
                formatH2: 'Cambiar el formato del bloque actual a H2',
                formatH3: 'Cambiar el formato del bloque actual a H3',
                formatH4: 'Cambiar el formato del bloque actual a H4',
                formatH5: 'Cambiar el formato del bloque actual a H5',
                formatH6: 'Cambiar el formato del bloque actual a H6',
                insertHorizontalRule: 'Insertar una línea horizontal',
                'linkDialog.show': 'Mostrar el panel de enlaces',
            },
            history: {
                undo: 'Deshacer',
                redo: 'Rehacer',
            },
            specialChar: {
                specialChar: 'CARACTERES ESPECIALES',
                select: 'Seleccionar caracteres especiales',
            },
            output: {
                noSelection: '¡No ha seleccionado nada!',
            },
        },
    });
});



$(document).on('click', '.confirm-delete', function (e) {
    e.preventDefault();
    var url = $(this).data("href");
    $("#delete-modal").modal("show");
    $("#delete-link").attr("action", url);
});

$(document).on('click', '.actividad', function (e) {
    e.preventDefault();
    var url = $(this).data("href");

    $.ajax({
        type: "GET",
        url: url,
        success: function (data) {
            $.each(data, function (fetch, actividades) {
                $('#actividad-modal tbody tr').remove();
                var table = $('#actividad-modal').find('.table tbody');
                if (actividades.length == 0) {
                    table.append('<tr><td colspan="5">No se encontraron datos</td></tr>');
                }
                for (i = 0; i < actividades.length; i++) {
                    table.append('<tr><td>' + actividades[i].descripcionsubcategoria + '</td><td>' + actividades[i].accion + '</td><td>' + actividades[i].fecha + '</td><td>' + actividades[i].nombrecorto + '</td><td>' + actividades[i].nombrecomercial + '</td></tr>');
                }
            })
            $("#actividad-modal").modal("show");
        }
    });
});


function validarNumero(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8) return true;
    patron = /[0-9]/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

function validarIdentificacion() {
    var cad = document.getElementById('identificacion').value.trim();
    var total = 0;
    var longitud = cad.length;
    var longcheck = longitud - 1;
    var digitos = cad.split('').map(Number);
    var codigo_provincia = digitos[0] * 10 + digitos[1];
    if (cad !== "" && longitud === 10) {

        if (cad != '2222222222' && codigo_provincia >= 1 && (codigo_provincia <= 24 || codigo_provincia == 30)) {
            for (i = 0; i < longcheck; i++) {
                if (i % 2 === 0) {
                    var aux = cad.charAt(i) * 2;
                    if (aux > 9) aux -= 9;
                    total += aux;
                } else {
                    total += parseInt(cad.charAt(i));
                }
            }
            total = total % 10 ? 10 - total % 10 : 0;

            if (cad.charAt(longitud - 1) == total) {
                recuperarInformacion(cad);
                $('#mensajeBandera').addClass("d-none");
                $('#identificacion').removeClass("is-invalid");


            } else {
                $('#identificacion').focus();
                $('#mensajeBandera').removeClass("d-none");
                $('#identificacion').addClass("is-invalid");

                camposvacios();
            }
        } else {
            $('#identificacion').focus();
            $('#mensajeBandera').removeClass("d-none");
            $('#identificacion').addClass("is-invalid");

            camposvacios();

        }
    } else
        if (longitud == 13 && cad !== "") {
            var extraer = cad.substr(10, 3);
            if (extraer == "001") {
                recuperarInformacion(cad);
                $('#mensajeBandera').addClass("d-none");
                $('#identificacion').removeClass("is-invalid");
            } else {
                $('#identificacion').focus();
                $('#mensajeBandera').removeClass("d-none");
                $('#identificacion').addClass("is-invalid");
                camposvacios();
            }


        } else
            if (cad !== "") {
                $('#identificacion').focus();
                $('#mensajeBandera').removeClass("d-none");
                $('#identificacion').addClass("is-invalid");
                camposvacios();
            }

}

function camposvacios() {
    $("#razonsocial").val(" ");
    $("#nombrecomercial").val(" ");
    $("#direccion").val(" ");
    $("#correo").val(" ");
    $("#ciudad").val(" ");
    $("#telefono1").val(" ");
    $("#telefono2").val(" ");
    $("#numero_empresas").val(" ");
    $("#precio").val(" ");
    $("#clave").val(null);
    $("#provinciasid").val("");
    $('#provinciasid').change();

}
