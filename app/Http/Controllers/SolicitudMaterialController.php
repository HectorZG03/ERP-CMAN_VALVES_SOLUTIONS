<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudMaterial;
use App\Models\SolicitudMaterialDetalle;
use App\Models\Inventario;
use App\Models\Embarcacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// PARTE PARA EXCEL
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudMaterialController extends Controller
{
    


    private function obtenerImagenEstatusSolicitud($estatus)
    {
        $base = storage_path('app/public/admin/');

        return match(strtolower($estatus)) {
            'aprobado' => $base.'01.png',
            'pendiente' => $base.'04.png',
            'denegado' => $base.'03.png',
            default => $base.'04.png',
        };
    }



    public function index(Request $request)
    {
        $user = auth()->user();
        $filterStatus = $request->get('status', 'all');

        // Base query según permisos
        $baseQuery = ($user->canApproveRequests() || $user->canManageInventory())
            ? SolicitudMaterial::with(['detalles.inventario', 'user'])
            : SolicitudMaterial::where('user_id', $user->id)->with(['detalles.inventario', 'user']);

        // Conteos reales desde la BD (sin importar paginación)
        $countQuery = ($user->canApproveRequests() || $user->canManageInventory())
            ? SolicitudMaterial::query()
            : SolicitudMaterial::where('user_id', $user->id);

        $counts = [
            'all'      => (clone $countQuery)->count(),
            'pendiente' => (clone $countQuery)->where('estatus', 'pendiente')->count(),
            'aprobado'  => (clone $countQuery)->where('estatus', 'aprobado')->count(),
            'denegado'  => (clone $countQuery)->where('estatus', 'denegado')->count(),
        ];

        // Si hay filtro activo → sin paginación, todos los registros del estado
        if ($filterStatus !== 'all') {
            $solicitudes = (clone $baseQuery)
                ->where('estatus', $filterStatus)
                ->orderByDesc('created_at')
                ->get(); // <- Collection, sin paginar
            $isPaginated = false;
        } else {
            $solicitudes = (clone $baseQuery)
                ->orderByDesc('created_at')
                ->paginate(15);
            $isPaginated = true;
        }

        return view('solicitudes.index', compact('solicitudes', 'counts', 'filterStatus', 'isPaginated'));
    }

public function create()
{
    $personal = \App\Models\Personal::activo()
        ->orderBy('nombre_completo')
        ->get([
            'id',
            'nombre_completo',
            'employee_id',
            'area',
        ]);

    $embarcaciones = Embarcacion::orderBy('nombre')
        ->get([
            'id',
            'nombre',
        ]);

    return view('solicitudes.create', compact(
        'personal',
        'embarcaciones'
    ));
}
public function store(Request $request)
{
    $request->validate([
        'personal_id' => 'nullable|exists:personal,id',

        // Embarcación seleccionada desde el nuevo catálogo
        'embarcacion_id' => 'required|exists:embarcaciones,id',

        'comentario' => 'nullable|string',

        'productos' => 'required|array|min:1',
        'productos.*.inventario_id' => 'required|exists:inventarios,id',
        'productos.*.cantidad_solicitada' => 'required|integer|min:1',
    ], [
        'embarcacion_id.required' => 'Debes seleccionar una embarcación',
        'embarcacion_id.exists' => 'La embarcación seleccionada no es válida',

        'productos.required' => 'Debe agregar al menos un producto a la solicitud',
        'productos.*.inventario_id.required' => 'Debe seleccionar un producto válido',
        'productos.*.cantidad_solicitada.required' => 'La cantidad es obligatoria',
        'productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor a 0',
    ]);

    DB::beginTransaction();

    try {
        /*
         * Verificar que todos los productos existan
         * y tengan suficiente disponibilidad.
         */
        foreach ($request->productos as $producto) {
            $inventario = Inventario::find(
                $producto['inventario_id']
            );

            if (!$inventario) {
                throw new \Exception(
                    "El producto con ID {$producto['inventario_id']} no existe"
                );
            }

            if (
                $inventario->existencia
                < $producto['cantidad_solicitada']
            ) {
                throw new \Exception(
                    "No hay suficiente stock de "
                    . "'{$inventario->nombre_producto}'. "
                    . "Disponible: {$inventario->existencia}, "
                    . "Solicitado: {$producto['cantidad_solicitada']}"
                );
            }
        }

        /*
         * Obtener la embarcación seleccionada.
         * La validación anterior garantiza que existe.
         */
        $embarcacion = Embarcacion::findOrFail(
            $request->embarcacion_id
        );

        /*
         * Guardar la solicitud.
         *
         * embarcacion_id: relación con el nuevo catálogo.
         * destino: nombre histórico para mantener compatibles
         * las vistas, PDF y Excel actuales.
         */
        $solicitud = SolicitudMaterial::create([
            'user_id' => auth()->id(),
            'personal_id' => $request->personal_id ?: null,

            'embarcacion_id' => $embarcacion->id,
            'destino' => $embarcacion->nombre,

            'comentario' => $request->comentario,
            'operador' => $request->operador ?? 'N/A',
            'categoria' => $request->categoria ?? 'N/A',
            'estatus' => 'pendiente',
        ]);

        /*
         * Guardar los productos de la solicitud.
         */
        foreach ($request->productos as $producto) {
            $inventario = Inventario::findOrFail(
                $producto['inventario_id']
            );

            SolicitudMaterialDetalle::create([
                'solicitud_material_id' => $solicitud->id,
                'inventario_id' => $producto['inventario_id'],
                'cantidad_solicitada' => $producto['cantidad_solicitada'],
                'precio_unitario' => $inventario->getPrecioPromedio(),
            ]);
        }

        DB::commit();

        return redirect()
            ->route('solicitudes.index')
            ->with(
                'success',
                'Solicitud enviada correctamente'
            );

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withErrors([
                'error' => 'Error al crear la solicitud: '
                    . $e->getMessage(),
            ])
            ->withInput();
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


    // pdf

   public function pdf(SolicitudMaterial $solicitud)
{
    $solicitud->load(['detalles.inventario', 'user']);
    
    // Firma del director/admin según estatus
    $firmaAdminPath = $this->obtenerImagenEstatusSolicitud($solicitud->estatus);
    $firmaAdminBase64 = file_exists($firmaAdminPath) 
        ? base64_encode(file_get_contents($firmaAdminPath)) 
        : null;

    // Firma del solicitante
    $firmaUserBase64 = null;
    if ($solicitud->user && $solicitud->user->signature) {
        $signaturePath = storage_path('app/public/' . $solicitud->user->signature);
        if (file_exists($signaturePath)) {
            $firmaUserBase64 = base64_encode(file_get_contents($signaturePath));
        }
    }

    return view('solicitudes.pdf', compact('solicitud', 'firmaAdminBase64', 'firmaUserBase64'));
}





    // ✅ EXPORTACIÓN A EXCEL CON IMÁGENES CORRECTAS
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

        // $sheet->setCellValue('M14', $solicitud->operador ?? 'N/A');
        // $sheet->setCellValue('M16', $solicitud->categoria ?? 'N/A');
        
         $sheet->setCellValue('M14', $solicitud->operadorPersonal->nombre_completo ?? 'N/A');
         $sheet->setCellValue('M16', $solicitud->operadorPersonal->grado ?? 'N/A');


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




    // ================= FIRMA =================
       if ($solicitud->user && $solicitud->user->signature) {


            $signaturePath = storage_path('app/public/'.$solicitud->user->signature);

            if (file_exists($signaturePath)) {

                $drawing = new Drawing();
                $drawing->setPath($signaturePath);
                $drawing->setCoordinates('o57'); // Posición de la firma
                $drawing->setOffsetX(70);// derecha
                $drawing->setOffsetY(-208);// abajo
                $drawing->setHeight(150);// altura en píxeles
                $drawing->setWidth(260);// ancho en píxeles (descomentar si necesitas)
                $drawing->setWorksheet($sheet);
            }
        }




        // ================= ESTATUS SOLICITUD =================
            $imgEstatus = $this->obtenerImagenEstatusSolicitud($solicitud->estatus);

            if(file_exists($imgEstatus)){

                $drawEstatus = new Drawing();
                $drawEstatus->setName('Estatus Solicitud');
                $drawEstatus->setDescription('Estatus de solicitud');
                $drawEstatus->setPath($imgEstatus);

                // 👇 AQUÍ PONES TU POSICIÓN EXACTA
                $drawEstatus->setCoordinates('b53');

                // Ajuste fino (igual que hicimos antes)
                $drawEstatus->setOffsetX(40);
                $drawEstatus->setOffsetY(-60);

                $drawEstatus->setHeight(100);
                $drawEstatus->setWidth(220);

                $drawEstatus->setWorksheet($sheet);
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
         $filename = 'Solicitud_Materiales_' . $solicitud->id . '.xlsx';

         return new StreamedResponse(function() use ($writer) {
             $writer->save('php://output');
         }, 200, [
             'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
             'Content-Disposition' => 'attachment;filename="' . $filename . '"',
             'Cache-Control' => 'max-age=0',
         ]);
    }
}