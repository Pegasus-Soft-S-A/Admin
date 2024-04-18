<?php

namespace App\Exports;

use App\Models\Licencias;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RespaldosExport implements FromCollection, WithHeadings, WithColumnFormatting, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $fechaAyer = now()->subDay()->toDateString(); // Fecha de ayer

        $registros = Licencias::select(
            'sis_clientes.identificacion',
            'sis_clientes.nombres',
            'sis_clientes.telefono2',
            'sis_clientes.correos',
            'sis_licencias.fechainicia',
            'sis_licencias.fechacaduca',
            'sis_licencias.fechaactulizaciones',
            'sis_licencias.fechaultimopago',
            'sis_licencias.fecha_respaldo',
            'sis_licencias.numerocontrato',
            'vendedores.razonsocial AS vendedor',
            'sis_distribuidores.razonsocial AS distribuidor',
            'revendedores.razonsocial AS revendedor',
            'sis_licencias.Identificador'
        )
            ->join('sis_clientes', 'sis_licencias.sis_clientesid', '=', 'sis_clientes.sis_clientesid')
            ->join('sis_distribuidores', 'sis_clientes.sis_distribuidoresid', '=', 'sis_distribuidores.sis_distribuidoresid')
            ->join('sis_revendedores as vendedores', 'sis_clientes.sis_vendedoresid', '=', 'vendedores.sis_revendedoresid')
            ->join('sis_revendedores as revendedores', 'sis_clientes.sis_revendedoresid', '=', 'revendedores.sis_revendedoresid')
            ->whereDate('sis_licencias.fecha_respaldo', '<', $fechaAyer)
            ->get();

        $registros = $registros->map(function ($registro) {
            $registro->identificacion = "'" . $registro->identificacion;
            return $registro;
        });

        return $registros;
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
            'Fecha Respaldo',
            'Número Contrato',
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
            'I' => NumberFormat::FORMAT_TEXT,
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
