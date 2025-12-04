<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudMaterial;
use App\Models\SolicitudMaterialDetalle;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;


// PARTE PARA EXCEL
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudMaterialController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Los que pueden aprobar y el personal de inventario ven todas las solicitudes
        if ($user->canApproveRequests() || $user->canManageInventory()) {
            $solicitudes = SolicitudMaterial::with(['detalles.inventario', 'user'])->paginate(15);
        } else {
            // Los demás usuarios solo ven sus propias solicitudes
            $solicitudes = SolicitudMaterial::where('user_id', $user->id)
                                          ->with(['detalles.inventario', 'user'])
                                          ->paginate(15);
        }
        
        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        return view('solicitudes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'comentario' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.inventario_id' => 'required|exists:inventarios,id',
            'productos.*.cantidad_solicitada' => 'required|integer|min:1',
        ], [
            'productos.required' => 'Debe agregar al menos un producto a la solicitud',
            'productos.*.inventario_id.required' => 'Debe seleccionar un producto válido',
            'productos.*.cantidad_solicitada.required' => 'La cantidad es obligatoria',
            'productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor a 0',
        ]);

        DB::beginTransaction();
        
        try {
            // Verificar disponibilidad de todos los productos antes de procesar
            foreach ($request->productos as $producto) {
                $inventario = Inventario::find($producto['inventario_id']);
                
                if (!$inventario) {
                    throw new \Exception("El producto con ID {$producto['inventario_id']} no existe");
                }
                
                if ($inventario->existencia < $producto['cantidad_solicitada']) {
                    throw new \Exception("No hay suficiente stock de '{$inventario->nombre_producto}'. Disponible: {$inventario->existencia}, Solicitado: {$producto['cantidad_solicitada']}");
                }
            }

            // Crear la solicitud principal
            $solicitud = SolicitudMaterial::create([
                'user_id' => auth()->id(),
                'destino' => $request->destino,
                'comentario' => $request->comentario,
                'operador' => $request->operador ?? 'N/A',
                'categoria' => $request->categoria ?? 'N/A',
                'estatus' => 'pendiente',
            ]);

            // Crear los detalles de la solicitud
            foreach ($request->productos as $producto) {
                $inventario = Inventario::find($producto['inventario_id']);
                
                SolicitudMaterialDetalle::create([
                    'solicitud_material_id' => $solicitud->id,
                    'inventario_id' => $producto['inventario_id'],
                    'cantidad_solicitada' => $producto['cantidad_solicitada'],
                    'precio_unitario' => $inventario->getPrecioPromedio(), // Guardar precio al momento de la solicitud
                ]);
            }

            DB::commit();
            
            return redirect()->route('solicitudes.index')->with('success', 'Solicitud enviada correctamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(SolicitudMaterial $solicitud)
    {
        $user = auth()->user();
        
        // Verificar permisos
        if (!($solicitud->user_id == $user->id || 
              $user->canApproveRequests() || 
              $user->canManageInventory())) {
            abort(403, 'No tienes permisos para ver esta solicitud');
        }

        // Cargar relaciones
        $solicitud->load(['detalles.inventario', 'user']);
        
        return view('solicitudes.show', compact('solicitud'));
    }



    // este no descuenta el inventario
     public function updateEstatus(Request $request, SolicitudMaterial $solicitud)
     {
         if (!auth()->user()->canApproveRequests()) {
             abort(403);
         }

         $request->validate([
             'estatus' => 'required|in:aprobado,denegado',
         ]);

         $solicitud->update(['estatus' => $request->estatus]);

         return back()->with('success', 'Estatus actualizado');
     }



    // CON ESTE  SI DESCUESTA EL INVENTARIO
    // public function updateEstatus(Request $request, SolicitudMaterial $solicitud)
    // {
    //     if (!auth()->user()->canApproveRequests()) {
    //         abort(403);
    //     }

    //     $request->validate([
    //         'estatus' => 'required|in:aprobado,denegado',
    //     ]);

    //     DB::beginTransaction();
        
    //     try {
    //         $estatusAnterior = $solicitud->estatus;
    //         $nuevoEstatus = $request->estatus;

    //         // Si se aprueba la solicitud, reducir existencia del inventario
    //         if ($nuevoEstatus === 'aprobado' && $estatusAnterior !== 'aprobado') {
    //             foreach ($solicitud->detalles as $detalle) {
    //                 $inventario = $detalle->inventario;
                    
    //                 if (!$inventario) {
    //                     throw new \Exception("No se puede procesar la solicitud porque uno de los productos no existe");
    //                 }
                    
    //                 if ($inventario->existencia < $detalle->cantidad_solicitada) {
    //                     throw new \Exception("No hay suficiente existencia de '{$inventario->nombre_producto}' para aprobar esta solicitud");
    //                 }
                    
    //                 $inventario->decrement('existencia', $detalle->cantidad_solicitada);
    //             }
    //         }
            
    //         // Si se deniega una solicitud previamente aprobada, restaurar existencia
    //         if ($nuevoEstatus === 'denegado' && $estatusAnterior === 'aprobado') {
    //             foreach ($solicitud->detalles as $detalle) {
    //                 $detalle->inventario->increment('existencia', $detalle->cantidad_solicitada);
    //             }
    //         }

    //         $solicitud->update(['estatus' => $nuevoEstatus]);

    //         DB::commit();
            
    //         return back()->with('success', 'Estatus actualizado correctamente');
            
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return back()->withErrors(['error' => $e->getMessage()]);
    //     }
    // }



    // Buscar solicitudes por número de folio, destino o estatus
    public function search(Request $request)
    {
        $query = SolicitudMaterial::query();

        if ($request->filled('term')) {
            $term = $request->get('term');
            $query->where(function ($q) use ($term) {
                $q->where('id', $term)
                  ->orWhere('destino', 'like', "%{$term}%")
                  ->orWhere('estatus', 'like', "%{$term}%");
            });
        }

        // Opcional: limitar resultados y cargar relaciones
        $solicitudes = $query->with(['user'])
                             ->orderByDesc('created_at')
                             ->limit(20)
                             ->get();

        return response()->json($solicitudes);
    }

    // Método para búsqueda de productos via AJAX
    public function buscarProductos(Request $request)
    {
        $search = $request->get('q', '');
        
        $productos = Inventario::where('existencia', '>', 0)
            ->where(function($query) use ($search) {
                $query->where('nombre_producto', 'LIKE', "%{$search}%")
                      ->orWhere('categoria', 'LIKE', "%{$search}%")
                      ->orWhere('medida', 'LIKE', "%{$search}%");
            })
            ->select('id', 'nombre_producto', 'categoria', 'medida', 'existencia')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }

    // Método para obtener detalles de un producto específico
    public function obtenerProducto(Request $request, $id)
    {
        $producto = Inventario::where('id', $id)
            ->where('existencia', '>', 0)
            ->first();

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado o sin stock'], 404);
        }

        return response()->json([
            'id' => $producto->id,
            'nombre_producto' => $producto->nombre_producto,
            'categoria' => $producto->categoria,
            'medida' => $producto->medida,
            'existencia' => $producto->existencia,
            'precio_promedio' => $producto->getPrecioPromedio(),
        ]);
    }






    // ESTO ES LA PARTE DE LA EXPORTACION A EXCEL


    public function exportExcel(SolicitudMaterial $solicitud)
{
    $solicitud->load(['detalles.inventario', 'user']);

    // Ruta a la plantilla
    $templatePath = storage_path('app/plantillas/SolicitudMateriales365.xlsx');

    // Cargar plantilla existente
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    // 🔹 Rellena los datos donde corresponda
    $sheet->setCellValue('F16', $solicitud->user->name);
    $sheet->setCellValue('F14', $solicitud->user->role);
    $sheet->setCellValue('F20', $solicitud->destino);
    $sheet->setCellValue('F22', $solicitud->created_at->format('d/m/Y'));
    $sheet->setCellValue('Q58', $solicitud->created_at->format('d/m/Y'));
    $sheet->setCellValue('E47', $solicitud->comentario ?? 'N/A');
    $sheet->setCellValue('F18', $solicitud->user->num_empleado ?? 'N/A');

    $sheet->setCellValue('M14', $solicitud->operador ?? 'N/A');
    $sheet->setCellValue('M16', $solicitud->categoria ?? 'N/A');

    // Supongamos que tus productos comienzan en la fila 10:
    $row = 27;
    foreach ($solicitud->detalles as $detalle) {
        $sheet->setCellValue('D' . $row, $detalle->cantidad_solicitada);
        $sheet->setCellValue('E' . $row, $detalle->inventario->medida ?? '-');
        $sheet->setCellValue('F' . $row, $detalle->inventario->nombre_producto ?? 'N/A');
        
        
        
        // $sheet->setCellValue('B' . $row, $detalle->inventario->categoria ?? '-');
        // $sheet->setCellValue('E' . $row, $detalle->precio_unitario);
        $row++;
    }

    // Descargar el archivo final
    $writer = new Xlsx($spreadsheet);
    $filename = 'Solicitud_Materiales' . $solicitud->id . '.xlsx';

    return new StreamedResponse(function() use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
    ]);
}



}

  