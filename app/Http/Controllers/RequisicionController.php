<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisicion;



// PARTE PARA EXCEL
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequisicionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Los que pueden aprobar y el personal de inventario ven todas las requisiciones
        if ($user->canApproveRequests() || $user->canManageInventory()) {
            $requisiciones = Requisicion::with('user')->paginate(15);
        } else {
            // Los demás usuarios solo ven sus propias requisiciones
            $requisiciones = Requisicion::where('user_id', $user->id)->paginate(15);
        }
        
        return view('requisiciones.index', compact('requisiciones'));
    }

    public function create()
    {
        return view('requisiciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_solicitante' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'plataforma' => 'required|string|max:255',
            'embarcacion' => 'required|string|max:255',
            'cantidad' => 'required|integer|min:1',
            'unidad' => 'required|string|max:255',
            'material' => 'required|string|max:255',
            'tipo_requerimiento' => 'required|in:interno,externo',
            'comentario' => 'required|string',
        ]);

        Requisicion::create([
            'nombre_solicitante' => $request->nombre_solicitante,
            'departamento' => $request->departamento,
            'plataforma' => $request->plataforma,
            'embarcacion' => $request->embarcacion,
            'cantidad' => $request->cantidad,
            'unidad' => $request->unidad,
            'material' => $request->material,
            'tipo_requerimiento' => $request->tipo_requerimiento,
            'comentario' => $request->comentario,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('requisiciones.index')->with('success', 'Requisición enviada correctamente');
    }

    // Método para ver los detalles de una requisición
    public function show(Requisicion $requisicion)
    {
        $user = auth()->user();
        
        // Verificar permisos: puede ver si es el solicitante, puede aprobar, o maneja inventario
        if (!($requisicion->user_id == $user->id || 
              $user->canApproveRequests() || 
              $user->canManageInventory())) {
            abort(403, 'No tienes permisos para ver esta requisición');
        }

        $requisicion->load('user');
        
        return view('requisiciones.show', compact('requisicion'));
    }

    public function updateEstatus(Request $request, Requisicion $requisicion)
    {
        if (!auth()->user()->canApproveRequests()) {
            abort(403);
        }

        $request->validate([
            'estatus' => 'required|in:aprobado,denegado',
        ]);

        $requisicion->update(['estatus' => $request->estatus]);

        return back()->with('success', 'Estatus de requisición actualizado');
    }







    // ESTO ES LA PARTE DE LA EXPORTACION A EXCEL


    public function exportExcel(Requisicion $requisicion)
{
    $requisicion->load(['user']);

    // Ruta a la plantilla
    $templatePath = storage_path('app/plantillas/Requisicion.xlsx');

    // Cargar plantilla existente
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    // 🔹 Rellena los datos donde corresponda
    $sheet->setCellValue('H13', $requisicion->user->name);
    $sheet->setCellValue('B46', $requisicion->user->name);
    $sheet->setCellValue('B13', $requisicion->user->role);
    $sheet->setCellValue('M13', $requisicion->nombre_solicitante);
    $sheet->setCellValue('F22', $requisicion->created_at->format('d/m/Y'));
    $sheet->setCellValue('P44', $requisicion->created_at->format('d/m/Y'));
    $sheet->setCellValue('P45', $requisicion->estatus);
    $sheet->setCellValue('F33', $requisicion->comentario ?? 'N/A');

   

    

    // Supongamos que tus productos comienzan en la fila 10:
    $row = 16;

    foreach ([$requisicion] as $item) {
    $sheet->setCellValue('E' . $row, $item->cantidad ?? '-');
    $sheet->setCellValue('F' . $row, $item->unidad ?? '-');
    $sheet->setCellValue('G' . $row, $item->material ?? '-');
    $row++;
        }   

    // Descargar el archivo final
    $writer = new Xlsx($spreadsheet);
    $filename = 'Requisicion.xlsx' . $requisicion->id . '.xlsx';

    return new StreamedResponse(function() use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
    ]);
}
}