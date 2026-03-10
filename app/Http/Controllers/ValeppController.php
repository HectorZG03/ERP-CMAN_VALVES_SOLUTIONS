<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valepp;
use App\Models\ValeppDetalle;
use App\Models\Personal;
use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;



// PARTE PARA EXCEL
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\StreamedResponse;


class ValeppController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $query = Valepp::with('personal', 'user', 'detalles.inventario');
        
        // Aplicar búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_vale', 'like', "%{$search}%")
                  ->orWhereHas('personal', function($pq) use ($search) {
                      $pq->where('nombre_completo', 'like', "%{$search}%")
                        ->orWhere('area', 'like', "%{$search}%");
                  });
            });
        }
        
        $valepp = $query->orderBy('fecha_solicitud', 'desc')->paginate(15);
        
        // Estadística simple
        $totalVales = Valepp::count();
        
        return view('valepp.index', compact(
            'valepp',
            'totalVales',
            'search'
        ));
    }

    public function create()
    {
        // Personal activo e inventario con existencia
        $personalActivo = Personal::activo()
            ->orderBy('nombre_completo')
            ->get();
        
        $inventarios = Inventario::where('existencia', '>', 0)
            ->orderBy('nombre_producto')
            ->get();
        
        // Generar número de vale automáticamente
        $numeroVale = Valepp::generarNumeroVale();
        
        return view('valepp.create', compact('personalActivo', 'inventarios', 'numeroVale'));
    }

// funcion estore q no descuenta del inventario cada q se crea un vale, para usar esta funcion desmarcarla y comentar 
// la otra funcion store que si descuenta del inventario
    public function store(Request $request)
{
    $request->validate([
        'personal_id' => 'required|exists:personal,id',
        'fecha_solicitud' => 'required|date',
        'observaciones' => 'nullable|string',
        'embarcacion' => 'nullable|string',
        'inventario_id' => 'required|array|min:1',
        'inventario_id.*' => 'required|exists:inventarios,id',
        'cantidad' => 'required|array|min:1',
        'cantidad.*' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();
    
    try {
        // Crear el vale
        $valepp = Valepp::create([
            'numero_vale' => Valepp::generarNumeroVale(),
            'personal_id' => $request->personal_id,
            'fecha_solicitud' => $request->fecha_solicitud,
            'observaciones' => $request->observaciones,
            'embarcacion' => $request->embarcacion,
            'user_id' => Auth::id(),
        ]);
        
        // Crear los detalles SIN descontar del inventario
        foreach ($request->inventario_id as $index => $inventario_id) {
            ValeppDetalle::create([
                'valepp_id' => $valepp->id,
                'inventario_id' => $inventario_id,
                'cantidad' => $request->cantidad[$index],
                'fecha_entrega' => now(),
                'observaciones' => $request->observaciones_detalle[$index] ?? null,
                'embarcacion' => $request->embarcacion ?? null,
            ]);
        }
        
        DB::commit();
        
        return redirect()->route('valepp.index')
            ->with('success', 'Vale PP creado exitosamente');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withErrors(['error' => 'Error al crear el vale: ' . $e->getMessage()])
            ->withInput();
    }
}



    // para descontar del inventario  cada q se crea un vale desmarcar esta funcion y comentar el otro estore el cual no descuenta
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'personal_id' => 'required|exists:personal,id',
    //         'fecha_solicitud' => 'required|date',
    //         'observaciones' => 'nullable|string',
    //         'embarcacion' => 'nullable|string',
    //         'inventario_id' => 'required|array|min:1',
    //         'inventario_id.*' => 'required|exists:inventarios,id',
    //         'cantidad' => 'required|array|min:1',
    //         'cantidad.*' => 'required|integer|min:1',
    //     ]);

    //     DB::beginTransaction();
        
    //     try {
    //         // Validar que haya suficiente existencia
    //         foreach ($request->inventario_id as $index => $inventario_id) {
    //             $inventario = Inventario::findOrFail($inventario_id);
    //             $cantidad = $request->cantidad[$index];
                
    //             if ($inventario->existencia < $cantidad) {
    //                 throw new \Exception("No hay suficiente existencia de {$inventario->nombre_producto}. Disponible: {$inventario->existencia}");
    //             }
    //         }
            
    //         // Crear el vale
    //         $valepp = Valepp::create([
    //             'numero_vale' => Valepp::generarNumeroVale(),
    //             'personal_id' => $request->personal_id,
    //             'fecha_solicitud' => $request->fecha_solicitud,
    //             'observaciones' => $request->observaciones,
    //             'embarcacion' => $request->embarcacion,
    //             'user_id' => Auth::id(),
    //         ]);
            
    //         // Crear los detalles y descontar del inventario
    //         foreach ($request->inventario_id as $index => $inventario_id) {
    //             $inventario = Inventario::findOrFail($inventario_id);
    //             $cantidad = $request->cantidad[$index];
                
    //             // Descontar del inventario
    //             $inventario->decrement('existencia', $cantidad);
                
    //             // Actualizar precio total proporcional
    //             $precioPromedio = $inventario->getPrecioPromedio();
    //             $inventario->decrement('precio_total', $precioPromedio * $cantidad);
                
    //             // Crear detalle del vale con fecha de entrega actual
    //             ValeppDetalle::create([
    //                 'valepp_id' => $valepp->id,
    //                 'inventario_id' => $inventario_id,
    //                 'cantidad' => $cantidad,
    //                 'fecha_entrega' => now(), // Se entrega inmediatamente
    //                 'observaciones' => $request->observaciones_detalle[$index] ?? null,
    //                 'embarcacion' => $request->embarcacion ?? null,
    //             ]);
    //         }
            
    //         DB::commit();
            
    //         return redirect()->route('valepp.index')
    //             ->with('success', 'Vale PP creado y materiales entregados exitosamente');
                
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->withErrors(['error' => 'Error al crear el vale: ' . $e->getMessage()])
    //             ->withInput();
    //     }
    // }

    public function show(Valepp $valepp)
    {
        $valepp->load('personal', 'user', 'detalles.inventario');
        
        return view('valepp.show', compact('valepp'));
    }

    public function edit(Valepp $valepp)
    {
        // No permitir edición una vez creado
        return redirect()->route('valepp.index')
            ->withErrors(['error' => 'Los vales no se pueden editar una vez creados']);
    }

    public function update(Request $request, Valepp $valepp)
    {
        return redirect()->route('valepp.index')
            ->withErrors(['error' => 'Los vales no se pueden editar']);
    }

    public function destroy(Valepp $valepp)
    {
        try {
            // No permitir eliminar una vez creado
            return redirect()->route('valepp.index')
                ->withErrors(['error' => 'Los vales no se pueden eliminar una vez creados']);
                
        } catch (\Exception $e) {
            return redirect()->route('valepp.index')
                ->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }



    




    public function exportPDF(Valepp $valepp)
{
    $valepp->load('personal', 'user', 'detalles.inventario');
    
    $data = [
        'valepp' => $valepp,
        'fechaGeneracion' => now()->format('d/m/Y H:i:s')
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('valepp.pdf', $data);
    $pdf->setPaper('A4', 'portrait');
    
    return $pdf->download("vale_pp_{$valepp->numero_vale}.pdf");
}




 // ✅ EXPORTACIÓN A EXCEL CON IMÁGENES CORRECTAS
    public function exportExcel(Valepp $valepp)
    {
        $valepp->load(['personal', 'user', 'detalles.inventario']);

        // Ruta a la plantilla
        $templatePath = storage_path('app/plantillas/Valepp.xlsx');

        // Cargar plantilla existente
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // 🔹 Rellena los datos donde corresponda

        // $sheet->setCellValue('D9', $valepp->user->name);
        // $sheet->setCellValue('F14', $valepp->user->role);
        // $sheet->setCellValue('F20', $valepp->fecha_solicitud);
         $sheet->setCellValue('m9', $valepp->created_at->format('d/m/Y'));
        // $sheet->setCellValue('Q58', $valepp->created_at->format('d/m/Y'));  

        // personal
        $sheet->setCellValue('d9', $valepp->personal->nombre_completo);
        // $sheet->setCellValue('d10', $valepp->personal->area);
        // $sheet->setCellValue('G6', $valepp->personal->departamento);
        $sheet->setCellValue('d10', $valepp->personal->grado);

        // OBSERVACIONES
         $sheet->setCellValue('b27', $valepp->observaciones ?? 'N/A');
         $sheet->setCellValue('j10', $valepp->embarcacion ?? 'N/A');

        

        
        

        


        // Supongamos que tus productos comienzan en la fila 10:
    $row = 15;
    foreach ($valepp->detalles as $detalle) {
        $sheet->setCellValue('B' . $row, $detalle->cantidad);
        // $sheet->setCellValue('k' . $row, $detalle->inventario->medida ?? '-');
        $sheet->setCellValue('c' . $row, $detalle->inventario->nombre_producto ?? 'N/A');
        $sheet->setCellValue('m' . $row, $valepp->created_at->format('d/m/Y'));
        $sheet->setCellValue('k' . $row, $detalle->unidad ?? 'N/A');
        
        
        
        // $sheet->setCellValue('B' . $row, $detalle->inventario->categoria ?? '-');
        // $sheet->setCellValue('E' . $row, $detalle->precio_unitario);
        $row++;
    }



    // ================= FIRMA =================
        if ($valepp->user->signature) {

            $signaturePath = storage_path('app/public/'.$valepp->user->signature);

            if (file_exists($signaturePath)) {

                $drawing = new Drawing();
                $drawing->setPath($signaturePath);
                $drawing->setCoordinates('b37'); // Posición de la firma
                $drawing->setOffsetX(70);// derecha
                $drawing->setOffsetY(-105);// abajo
                $drawing->setHeight(150);// altura en píxeles
                $drawing->setWidth(260);// ancho en píxeles (descomentar si necesitas)
                $drawing->setWorksheet($sheet);
            }
        }

        


        // ✅ INSERTAR FOTO DE PERFIL COMO IMAGEN (OPCIONAL)
        // if ($solicitud->user->profile_photo) {
        //     $photoPath = storage_path('app/public/' . $solicitud->user->profile_photo);
            
        //     if (file_exists($photoPath)) {
        //         $photoDrawing = new Drawing();
        //         $photoDrawing->setName('Foto de Perfil');
        //         $photoDrawing->setDescription('Foto del usuario');
        //         $photoDrawing->setPath($photoPath);
                
        //         // Posicionar donde quieras la foto (por ejemplo F26)
        //         $photoDrawing->setCoordinates('F26');
                
        //         // Ajustar tamaño
        //         $photoDrawing->setHeight(100);
                
        //         // Agregar la imagen a la hoja
        //         $photoDrawing->setWorksheet($sheet);
        //     }
        // }

        // // Productos - comenzando en la fila 27
        // $row = 27;
        // foreach ($solicitud->detalles as $detalle) {
        //     $sheet->setCellValue('D' . $row, $detalle->cantidad_solicitada);
        //     $sheet->setCellValue('E' . $row, $detalle->inventario->medida ?? '-');
        //     $sheet->setCellValue('F' . $row, $detalle->inventario->nombre_producto ?? 'N/A');
        //     $row++;
        // }

        //  // Descargar el archivo final
         $writer = new Xlsx($spreadsheet);
         $filename = 'Valepp_' . $valepp->id . '.xlsx';

         return new StreamedResponse(function() use ($writer) {
             $writer->save('php://output');
         }, 200, [
             'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
             'Content-Disposition' => 'attachment;filename="' . $filename . '"',
             'Cache-Control' => 'max-age=0',
         ]);
    }





}