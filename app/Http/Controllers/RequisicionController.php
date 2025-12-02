<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\Contrato;
use Illuminate\Support\Facades\DB;

// PARTE PARA EXCEL
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequisicionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->canApproveRequests() || $user->canManageInventory()) {
            $requisiciones = Requisicion::with(['user', 'detalles'])->paginate(15);
        } else {
            $requisiciones = Requisicion::where('user_id', $user->id)
                                       ->with(['user', 'detalles'])
                                       ->paginate(15);
        }
        
        return view('requisiciones.index', compact('requisiciones'));
    }

    public function create()
    {
        $contratos = Contrato::all();
        return view('requisiciones.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_solicitante' => 'required|string|max:255',
            'departamento' => 'required|string|max:255',
            'plataforma' => 'required|string|max:255',
            'embarcacion' => 'required|string|max:255',
            'tipo_requerimiento' => 'required|in:interno,externo',
            'comentario' => 'required|string',
            'materiales' => 'required|array|min:1',
            'materiales.*.cantidad' => 'required|integer|min:1',
            'materiales.*.unidad' => 'required|string|max:255',
            'materiales.*.material' => 'required|string|max:255',
        ], [
            'materiales.required' => 'Debe agregar al menos un material a la requisición',
            'materiales.*.cantidad.required' => 'La cantidad es obligatoria',
            'materiales.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'materiales.*.unidad.required' => 'La unidad es obligatoria',
            'materiales.*.material.required' => 'La descripción del material es obligatoria',
        ]);

        DB::beginTransaction();
        
        try {
            // Crear la requisición principal
            $requisicion = Requisicion::create([
                'nombre_solicitante' => $request->nombre_solicitante,
                'departamento' => $request->departamento,
                'proyecto' => $request->proyecto ?? 'N/A',
                'sit' => $request->sit ?? 'N/A',
                'partida' => $request->partida ?? 'N/A',
                'plataforma' => $request->plataforma ?? 'N/A',
                'area' => $request->area ?? 'N/A',
                'activo' => $request->activo ?? 'N/A',
                'contrato_id' => $request->contrato_id,
                'embarcacion' => $request->embarcacion,
                'tipo_requerimiento' => $request->tipo_requerimiento,
                'comentario' => $request->comentario,
                'user_id' => auth()->id(),
            ]);

            // Crear los detalles de la requisición
            foreach ($request->materiales as $material) {
                RequisicionDetalle::create([
                    'requisicion_id' => $requisicion->id,
                    'cantidad' => $material['cantidad'],
                    'unidad' => $material['unidad'],
                    'material' => $material['material'],
                ]);
            }

            DB::commit();
            
            return redirect()->route('requisiciones.index')->with('success', 'Requisición enviada correctamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Requisicion $requisicion)
    {
        $user = auth()->user();
        
        if (!($requisicion->user_id == $user->id || 
              $user->canApproveRequests() || 
              $user->canManageInventory())) {
            abort(403, 'No tienes permisos para ver esta requisición');
        }

        $requisicion->load(['user', 'detalles', 'contrato']);
        
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

    // EXPORTACIÓN A EXCEL
    public function exportExcel(Requisicion $requisicion)
    {
        $requisicion->load(['user', 'contrato', 'detalles']);

        $templatePath = storage_path('app/plantillas/Requisicion.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Todo en minúsculas → strtolower()  Todo en MAYUSCULAS → strtoupper()
        // Primera letra de cada palabra en mayúscula → ucwords()
        // Datos generales (convertidos a mayúsculas)
        $sheet->setCellValue('C10', strtoupper($requisicion->user->name));
        $sheet->setCellValue('C11', strtoupper($requisicion->user->role));
        $sheet->setCellValue('C12', ucwords($requisicion->proyecto ?? 'N/A'));
        $sheet->setCellValue('C13', strtoupper($requisicion->sit ?? 'N/A'));
        $sheet->setCellValue('C14', strtoupper($requisicion->partida ?? 'N/A'));
        $sheet->setCellValue('C15', strtoupper($requisicion->plataforma ?? 'N/A'));
        $sheet->setCellValue('C16', strtoupper($requisicion->embarcacion ?? 'N/A'));
        $sheet->setCellValue('C17', strtoupper($requisicion->area ?? 'N/A'));
        $sheet->setCellValue('C18', strtoupper($requisicion->activo ?? 'N/A'));

        $sheet->setCellValue('F36', $requisicion->user->name);
        $sheet->setCellValue('G6', $requisicion->folio);
        $sheet->setCellValue('G7', $requisicion->contrato->contrato ?? 'N/A');
        $sheet->setCellValue('G8', $requisicion->contrato->convenio ?? 'N/A');
        $sheet->setCellValue('G5', $requisicion->created_at->format('d/m/Y'));
        $sheet->setCellValue('A34', $requisicion->comentario ?? 'N/A');

        // ✅ Materiales (múltiples productos)
        $row = 21;
        foreach ($requisicion->detalles as $detalle) {
            $sheet->setCellValue('A' . $row, $detalle->cantidad);
            $sheet->setCellValue('B' . $row, $detalle->unidad);
            $sheet->setCellValue('C' . $row, $detalle->material);
            $row++;
        }

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