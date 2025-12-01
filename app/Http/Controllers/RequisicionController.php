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
    $sheet->setCellValue('a1', $requisicion->user->name);
    $sheet->setCellValue('d1', $requisicion->nombre_solicitante);
    $sheet->setCellValue('e1', $requisicion->created_at->format('d/m/Y'));
    $sheet->setCellValue('f1', $requisicion->created_at->format('d/m/Y'));
    $sheet->setCellValue('h1', $requisicion->comentario ?? 'N/A');

    $sheet->setCellValue('i1', $requisicion->proyecto ?? 'N/A');
    $sheet->setCellValue('j1', $requisicion->sit ?? 'N/A');
    $sheet->setCellValue('k1', $requisicion->partida ?? 'N/A');
    $sheet->setCellValue('l1', $requisicion->plataforma ?? 'N/A');
    $sheet->setCellValue('m1', $requisicion->embarcacion ?? 'N/A');
    $sheet->setCellValue('n1', $requisicion->area ?? 'N/A');
    $sheet->setCellValue('o1', $requisicion->activo ?? 'N/A');

    // ✅ Opción 1: Mostrar todos los datos en una sola celda
    // $sheet->setCellValue('p1', 
    //     $requisicion->contrato 
    //         ? $requisicion->contrato->empresa_nombre . ' — ' . 
    //           $requisicion->contrato->contrato . ' — ' . 
    //           $requisicion->contrato->convenio
    //         : 'N/A'
    // );

     //✅ Opción 2: Mostrar cada dato en celdas separadas
     $sheet->setCellValue('p1', $requisicion->contrato->empresa_nombre ?? 'N/A');
     $sheet->setCellValue('q1', $requisicion->contrato->contrato ?? 'N/A');
     $sheet->setCellValue('r1', $requisicion->contrato->convenio ?? 'N/A');

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
    $filename = 'Requisicion_' . $requisicion->id . '.xlsx';

    return new StreamedResponse(function() use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
    ]);
}
}