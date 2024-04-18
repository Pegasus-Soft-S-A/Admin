<?php

namespace App\Exports;

use App\Models\Licencias;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LicenciasExport implements FromCollection, WithHeadings, WithColumnFormatting, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        // Paso 1: Obtener la versión más alta normalizando los segmentos para la comparación
        $versionMasAlta = Licencias::select(DB::raw("MAX(CONCAT(LPAD(SUBSTRING_INDEX(SUBSTRING_INDEX(version_ejecutable, '.', 1), '.', -1), 10, '0'), '.', LPAD(SUBSTRING_INDEX(SUBSTRING_INDEX(version_ejecutable, '.', 2), '.', -1), 10, '0'), '.', LPAD(SUBSTRING_INDEX(SUBSTRING_INDEX(version_ejecutable, '.', 3), '.', -1), 10, '0'), '.', LPAD(SUBSTRING_INDEX(SUBSTRING_INDEX(version_ejecutable, '.', 4), '.', -1), 10, '0'))) as version_normalizada"))
            ->value('version_normalizada');

        // Consulta principal para obtener los registros deseados con el filtro de versión aplicado
        $registros = DB::table('sis_licencias')
            ->join('sis_clientes', 'sis_licencias.sis_clientesid', '=', 'sis_clientes.sis_clientesid')
            ->join('sis_distribuidores', 'sis_clientes.sis_distribuidoresid', '=', 'sis_distribuidores.sis_distribuidoresid')
            ->join('sis_revendedores as vendedores', 'sis_clientes.sis_vendedoresid', '=', 'vendedores.sis_revendedoresid')
            ->join('sis_revendedores as revendedores', 'sis_clientes.sis_revendedoresid', '=', 'revendedores.sis_revendedoresid')
            ->select(
                'sis_clientes.identificacion',
                'sis_clientes.nombres',
                'sis_clientes.telefono2',
                'sis_clientes.correos',
                'sis_licencias.fechainicia AS fechainicia',
                'sis_licencias.fechacaduca AS fechacaduca',
                'sis_licencias.fechaactulizaciones AS fechaactulizaciones',
                'sis_licencias.fechaultimopago AS fechaultimopago',
                'sis_licencias.numerocontrato',
                'sis_licencias.version_ejecutable',
                'sis_distribuidores.razonsocial AS distribuidor',
                'vendedores.razonsocial AS vendedor',
                'revendedores.razonsocial AS revendedor',
                'sis_licencias.Identificador',
            )
            ->get();

        $registrosMenores = $registros->filter(function ($registro) use ($versionMasAlta) {
            // Dividir la versión en segmentos y asegurar que siempre hay 4, rellenando con '0' si es necesario
            $segmentos = explode('.', $registro->version_ejecutable) + array_fill(0, 4, '0');

            // Normalizar la versión actual para comparación
            $versionActualNormalizada = sprintf(
                '%010d.%010d.%010d.%010d',
                $segmentos[0],
                $segmentos[1],
                $segmentos[2],
                $segmentos[3]
            );
            $registro->identificacion = "'" . $registro->identificacion; // Anteponer un apóstrofo

            // Comparar con la versión más alta
            return strcmp($versionActualNormalizada, $versionMasAlta) < 0;
        });

        return $registrosMenores;
    }

    public function headings(): array
    {
        // Definir los encabezados de las columnas
        return [
            'Identificación',
            'Nombres',
            'Whastapp',
            'Correos',
            'Fecha Inicia',
            'Fecha Caduca',
            'Fecha Actualizaciones',
            'Fecha Último Pago',
            'Número Contrato',
            'Versión Ejecutable',
            'Distribuidor',
            'Vendedor',
            'Revendedor',
            'Identificador',
        ];
    }

    public function columnFormats(): array
    {
        // Definir el formato de las columnas específicas
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Asume que 'A' es la columna de Identificación
            // Agrega más formatos según sea necesario
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 40,
            'C' => 15,
            'D' => 20,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
        ];
    }
}
