<?php

namespace App\Exports;

use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InventarioExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithTitle, ShouldAutoSize
{
    /**
     * Obtener la colección de datos
     */
    public function collection()
    {
        return Inventario::all();
    }

    /**
     * Definir los encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre del Producto',
            'Categoría',
            'Unidad de Medida',
            'Existencia',
            'Precio Unitario',
            'Valor Total del Stock',
            'Estado del Stock',
            'Fecha de Creación',
            'Última Actualización'
        ];
    }

    /**
     * Mapear los datos de cada fila
     */
    public function map($inventario): array
    {
        // Determinar el estado del stock
        $estadoStock = '';
        if ($inventario->existencia > 10) {
            $estadoStock = 'STOCK NORMAL';
        } elseif ($inventario->existencia > 0) {
            $estadoStock = 'STOCK BAJO';
        } else {
            $estadoStock = 'AGOTADO';
        }

        return [
            $inventario->id,
            $inventario->nombre_producto,
            $inventario->categoria,
            $inventario->medida,
            $inventario->existencia,
            $inventario->getPrecioPromedio(),
            $inventario->precio_total,
            $estadoStock,
            $inventario->created_at ? $inventario->created_at->format('d/m/Y H:i') : 'N/A',
            $inventario->updated_at ? $inventario->updated_at->format('d/m/Y H:i') : 'N/A'
        ];
    }

    /**
     * Aplicar estilos a la hoja
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '3B82F6'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    /**
     * Formatear columnas específicas
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,  // Precio Unitario
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,  // Valor Total
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Inventario Completo';
    }
}

// NOTA: Para crear esta clase, ejecuta el siguiente comando en tu terminal:
// php artisan make:export InventarioExport --model=Inventario

// INSTRUCCIONES DE INSTALACIÓN:
// Si no tienes el paquete Laravel Excel instalado, ejecuta:
// composer require maatwebsite/excel
// php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config