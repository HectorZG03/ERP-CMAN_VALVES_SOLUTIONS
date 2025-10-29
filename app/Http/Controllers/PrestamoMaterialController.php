<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrestamoMaterial;
use App\Models\PrestamoMaterialDetalle;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


// PARTE PARA EXCEL
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;



class PrestamoMaterialController extends Controller
{
    public function index()
{
    $user = auth()->user();
    
    // Solo administradores y personal de almacén ven todos los préstamos
    if ($user->canApproveRequests() || $user->canManageInventory()) {
        $prestamos = PrestamoMaterial::with(['detalles.inventario', 'user'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(15);
    } else {
        // Los demás usuarios solo ven sus propios préstamos
        $prestamos = PrestamoMaterial::where('user_id', $user->id)
                                    ->with(['detalles.inventario', 'user'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(15);
    }
        
        // Contar préstamos por estado para estadísticas
        $estadisticas = [
            'vencidos' => PrestamoMaterial::vencidos()->count(),
            'proximos' => PrestamoMaterial::proximos()->count(),
            'prestados' => PrestamoMaterial::prestados()->count(),
            'pendientes' => PrestamoMaterial::pendientes()->count(),
        ];
        
        return view('prestamos.index', compact('prestamos', 'estadisticas'));
    }

    public function create()
    {
        return view('prestamos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_prestamo' => 'required|date|after_or_equal:today',
            'fecha_devolucion_esperada' => 'required|date|after:fecha_prestamo',
            'destino' => 'required|string|max:255',
            'comentario' => 'nullable|string',
            'productos' => 'required|array|min:1',
            'productos.*.inventario_id' => 'required|exists:inventarios,id',
            'productos.*.cantidad_prestada' => 'required|integer|min:1',
        ], [
            'fecha_prestamo.after_or_equal' => 'La fecha de préstamo debe ser hoy o posterior',
            'fecha_devolucion_esperada.after' => 'La fecha de devolución debe ser posterior a la fecha de préstamo',
            'productos.required' => 'Debe agregar al menos un producto al préstamo',
            'productos.*.inventario_id.required' => 'Debe seleccionar un producto válido',
            'productos.*.cantidad_prestada.required' => 'La cantidad es obligatoria',
            'productos.*.cantidad_prestada.min' => 'La cantidad debe ser mayor a 0',
        ]);

        DB::beginTransaction();
        
        try {
            // Verificar disponibilidad de todos los productos antes de procesar
            foreach ($request->productos as $producto) {
                $inventario = Inventario::find($producto['inventario_id']);
                
                if (!$inventario) {
                    throw new \Exception("El producto con ID {$producto['inventario_id']} no existe");
                }
                
                if ($inventario->existencia < $producto['cantidad_prestada']) {
                    throw new \Exception("No hay suficiente stock de '{$inventario->nombre_producto}'. Disponible: {$inventario->existencia}, Solicitado: {$producto['cantidad_prestada']}");
                }
            }

            // Crear el préstamo principal
            $prestamo = PrestamoMaterial::create([
                'user_id' => auth()->id(),
                'fecha_prestamo' => $request->fecha_prestamo,
                'fecha_devolucion_esperada' => $request->fecha_devolucion_esperada,
                'destino' => $request->destino,
                'comentario' => $request->comentario,
                'estatus' => 'pendiente',
            ]);

            // Crear los detalles del préstamo
            foreach ($request->productos as $producto) {
                $inventario = Inventario::find($producto['inventario_id']);
                
                PrestamoMaterialDetalle::create([
                    'prestamo_material_id' => $prestamo->id,
                    'inventario_id' => $producto['inventario_id'],
                    'cantidad_prestada' => $producto['cantidad_prestada'],
                    'precio_unitario' => $inventario->getPrecioPromedio(), // Guardar precio al momento del préstamo
                    'estado_devolucion' => 'pendiente',
                ]);
            }

            DB::commit();
            
            return redirect()->route('prestamos.index')->with('success', 'Solicitud de préstamo enviada correctamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(PrestamoMaterial $prestamo)
    {
        $user = auth()->user();
        
        // Verificar permisos
        if (!($prestamo->user_id == $user->id || 
              $user->canApproveRequests() || 
              $user->canManageInventory())) {
            abort(403, 'No tienes permisos para ver este préstamo');
        }

        // Cargar relaciones
        $prestamo->load(['detalles.inventario', 'user']);
        
        return view('prestamos.show', compact('prestamo'));
    }

    public function updateEstatus(Request $request, PrestamoMaterial $prestamo)
    {
        if (!auth()->user()->canApproveRequests()) {
            abort(403);
        }

        $request->validate([
            'estatus' => 'required|in:aprobado,denegado',
        ]);

        DB::beginTransaction();
        
        try {
            $estatusAnterior = $prestamo->estatus;
            $nuevoEstatus = $request->estatus;

            // Si se aprueba el préstamo, reducir existencia del inventario
            if ($nuevoEstatus === 'aprobado' && $estatusAnterior !== 'aprobado') {
                // Cambiar estado a "prestado" cuando se aprueba
                $prestamo->update([
                    'estatus' => 'prestado',
                    'fecha_prestamo' => $prestamo->fecha_prestamo ?? Carbon::now(),
                ]);

                foreach ($prestamo->detalles as $detalle) {
                    $inventario = $detalle->inventario;
                    
                    if (!$inventario) {
                        throw new \Exception("No se puede procesar el préstamo porque uno de los productos no existe");
                    }
                    
                    if ($inventario->existencia < $detalle->cantidad_prestada) {
                        throw new \Exception("No hay suficiente existencia de '{$inventario->nombre_producto}' para aprobar este préstamo");
                    }
                    
                    // Reducir existencia
                    $inventario->decrement('existencia', $detalle->cantidad_prestada);
                    
                    // Actualizar estado del detalle
                    $detalle->update(['estado_devolucion' => 'prestado']);
                }
            } else if ($nuevoEstatus === 'denegado') {
                $prestamo->update(['estatus' => 'denegado']);
            }

            DB::commit();
            
            return back()->with('success', 'Estado del préstamo actualizado correctamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function devolucion(PrestamoMaterial $prestamo)
    {
        if (!auth()->user()->canManageInventory()) {
            abort(403);
        }

        // Verificar que el préstamo esté en estado prestado
        if ($prestamo->estatus !== 'prestado') {
            return back()->withErrors(['error' => 'Este préstamo no está activo']);
        }

        $prestamo->load(['detalles.inventario', 'user']);
        
        return view('prestamos.devolucion', compact('prestamo'));
    }

    public function procesarDevolucion(Request $request, PrestamoMaterial $prestamo)
    {
        if (!auth()->user()->canManageInventory()) {
            abort(403);
        }

        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.detalle_id' => 'required|exists:prestamo_material_detalles,id',
            'productos.*.cantidad_devuelta' => 'required|integer|min:0',
            'productos.*.condicion' => 'required|in:bueno,dañado,perdido',
            'observaciones_devolucion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $todosDevueltos = true;

            foreach ($request->productos as $productoData) {
                $detalle = PrestamoMaterialDetalle::find($productoData['detalle_id']);
                
                if ($detalle->prestamo_material_id !== $prestamo->id) {
                    throw new \Exception("El detalle no pertenece a este préstamo");
                }

                $nuevaCantidadDevuelta = $detalle->cantidad_devuelta + $productoData['cantidad_devuelta'];
                
                if ($nuevaCantidadDevuelta > $detalle->cantidad_prestada) {
                    throw new \Exception("No se puede devolver más de lo prestado para '{$detalle->inventario->nombre_producto}'");
                }

                // Actualizar inventario solo si el producto está en buenas condiciones
                if ($productoData['condicion'] === 'bueno') {
                    $detalle->inventario->increment('existencia', $productoData['cantidad_devuelta']);
                }

                // Actualizar el detalle
                $detalle->update([
                    'cantidad_devuelta' => $nuevaCantidadDevuelta,
                    'condicion_devolucion' => $productoData['condicion'],
                    'estado_devolucion' => $nuevaCantidadDevuelta >= $detalle->cantidad_prestada ? 'devuelto_completo' : 'devuelto_parcial',
                ]);

                if ($nuevaCantidadDevuelta < $detalle->cantidad_prestada) {
                    $todosDevueltos = false;
                }
            }

            // Actualizar el préstamo principal
            if ($todosDevueltos) {
                $prestamo->update([
                    'estatus' => 'devuelto',
                    'fecha_devolucion_real' => Carbon::now(),
                    'observaciones_devolucion' => $request->observaciones_devolucion,
                ]);
            } else {
                $prestamo->update([
                    'observaciones_devolucion' => $request->observaciones_devolucion,
                ]);
            }

            DB::commit();
            
            $mensaje = $todosDevueltos ? 
                'Devolución completa procesada correctamente' : 
                'Devolución parcial procesada correctamente';
            
            return redirect()->route('prestamos.show', $prestamo)->with('success', $mensaje);
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Método para búsqueda de productos via AJAX (reutilizando el del SolicitudController)
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

    // Dashboard de préstamos vencidos y próximos a vencer
    public function dashboard()
    {
        $prestamosVencidos = PrestamoMaterial::vencidos()
            ->with(['user', 'detalles.inventario'])
            ->get();

        $prestamosProximos = PrestamoMaterial::proximos()
            ->with(['user', 'detalles.inventario'])
            ->get();

        $estadisticas = [
            'total_prestados' => PrestamoMaterial::prestados()->count(),
            'total_vencidos' => $prestamosVencidos->count(),
            'total_proximos' => $prestamosProximos->count(),
            'valor_prestado' => PrestamoMaterial::prestados()->with('detalles')->get()->sum(function($p) {
                return $p->detalles->sum('subtotal');
            }),
        ];

        return view('prestamos.dashboard', compact('prestamosVencidos', 'prestamosProximos', 'estadisticas'));
    }







    // ESTO ES LA PARTE DE LA EXPORTACION A EXCEL


    public function exportExcel(PrestamoMaterial $prestamo)
{
    $prestamo->load(['detalles.inventario', 'user']);

    // Ruta a la plantilla
    $templatePath = storage_path('app/plantillas/Resguardo_de_Equipos_y_Herramientas.xlsx');

    // Cargar plantilla existente
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    // 🔹 Rellena los datos donde corresponda
    $sheet->setCellValue('H13', $prestamo->user->name);
    $sheet->setCellValue('B46', $prestamo->user->name);
    $sheet->setCellValue('B13', $prestamo->user->role);
    $sheet->setCellValue('M13', $prestamo->destino);
    // $sheet->setCellValue('F22', $prestamo->created_at->format('d/m/Y'));
    $sheet->setCellValue('P44', $prestamo->created_at->format('d/m/Y'));
    $sheet->setCellValue('P45', $prestamo->estatus);
    $sheet->setCellValue('F33', $prestamo->comentario ?? 'N/A');
   

    

    // Supongamos que tus productos comienzan en la fila 10:
    $row = 16;
    foreach ($prestamo->detalles as $detalle) {
        $sheet->setCellValue('E' . $row, $detalle->cantidad_prestada);
        $sheet->setCellValue('F' . $row, $detalle->inventario->medida ?? '-');
        $sheet->setCellValue('G' . $row, $detalle->inventario->economico ?? 'N/A');
        $sheet->setCellValue('I' . $row, $detalle->inventario->nombre_producto ?? 'N/A');
        
        
        
        // $sheet->setCellValue('B' . $row, $detalle->inventario->categoria ?? '-');
        // $sheet->setCellValue('E' . $row, $detalle->precio_unitario);
        $row++;
    }

    // Descargar el archivo final
    $writer = new Xlsx($spreadsheet);
    $filename = 'Prestamo_Materiales' . $prestamo->id . '.xlsx';

    return new StreamedResponse(function() use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
    ]);
}




}