<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisicion;
use App\Models\Contrato;



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

        $contratos = Contrato::all();
        return view('requisiciones.create', compact('contratos'));

        // return view('requisiciones.create');
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
            'proyecto' => $request->proyecto ?? 'N/A', // ✅ Si está vacío, guarda N/A
            'sit' => $request->sit ?? 'N/A',
            'partida' => $request->partida ?? 'N/A',
            'plataforma' => $request->plataforma ?? 'N/A',
            'area' => $request->area ?? 'N/A',
            'activo' => $request->activo ?? 'N/A',
            'contrato_id' => $request->contrato_id, // esta es la ruta a la relación contrato
            
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
    // ✅ Agregar 'contrato' al load()
    $requisicion->load(['user', 'contrato']);

    // Ruta a la plantilla
    $templatePath = storage_path('app/plantillas/Requisicion.xlsx');

    // Cargar plantilla existente
    $spreadsheet = IOFactory::load($templatePath);
    $sheet = $spreadsheet->getActiveSheet();

    // 🔹 Rellena los datos donde corresponda
    $sheet->setCellValue('C10', $requisicion->user->name);
    $sheet->setCellValue('F36', $requisicion->user->name);
    // $sheet->setCellValue('k11', $requisicion->nombre_solicitante);
    $sheet->setCellValue('C11', $requisicion->user->role);
    $sheet->setCellValue('G5', $requisicion->created_at->format('d/m/Y'));
    // $sheet->setCellValue('f1', $requisicion->created_at->format('d/m/Y'));
    $sheet->setCellValue('A34', $requisicion->comentario ?? 'N/A');

    $sheet->setCellValue('C12', $requisicion->proyecto ?? 'N/A');
    $sheet->setCellValue('C13', $requisicion->sit ?? 'N/A');
    $sheet->setCellValue('C14', $requisicion->partida ?? 'N/A');
    $sheet->setCellValue('C15', $requisicion->plataforma ?? 'N/A');
    $sheet->setCellValue('C16', $requisicion->embarcacion ?? 'N/A');
    $sheet->setCellValue('C17', $requisicion->area ?? 'N/A');
    $sheet->setCellValue('C18', $requisicion->activo ?? 'N/A');
    $sheet->setCellValue('G6', $requisicion->folio);
    



    // ✅ Opción 1: Mostrar todos los datos en una sola celda
    // $sheet->setCellValue('p1', 
    //     $requisicion->contrato 
    //         ? $requisicion->contrato->empresa_nombre . ' — ' . 
    //           $requisicion->contrato->contrato . ' — ' . 
    //           $requisicion->contrato->convenio
    //         : 'N/A'
    // );

     //✅ Opción 2: Mostrar cada dato en celdas separadas
    //  $sheet->setCellValue('p1', $requisicion->contrato->empresa_nombre ?? 'N/A');
     $sheet->setCellValue('G7', $requisicion->contrato->contrato ?? 'N/A');
     $sheet->setCellValue('G8', $requisicion->contrato->convenio ?? 'N/A');

    // Supongamos que tus productos comienzan en la fila 10:
    $row = 21;

    foreach ([$requisicion] as $item) {
        $sheet->setCellValue('A' . $row, $item->cantidad ?? '-');
        $sheet->setCellValue('B' . $row, $item->unidad ?? '-');
        $sheet->setCellValue('C' . $row, $item->material ?? '-');
        $row++;
    }   

    // Descargar el archivo final
    $writer = new Xlsx($spreadsheet);
    $filename = 'Requisicion_' . $requisicion->id . '.xlsx';

    return new StreamedResponse(function() use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
    ]);
}
}