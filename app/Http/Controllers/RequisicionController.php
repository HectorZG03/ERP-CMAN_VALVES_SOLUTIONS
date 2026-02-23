<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\Contrato;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RequisicionController extends Controller
{

    private function obtenerImagenEstatus($estatus, $tipo = 'direccion')
    {
        if ($estatus === 'aprobado') {
            return $tipo === 'finanzas'
                ? public_path('storage/admin/02.png')
                : public_path('storage/admin/01.png');
        }

        if ($estatus === 'denegado') {
            return public_path('storage/admin/03.png');
        }

        return public_path('storage/admin/04.png');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->canApproveFinanzas()) {
            $requisiciones = Requisicion::with(['user', 'detalles', 'aprobadorFinanzas'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->canApproveRequests()) {
            $requisiciones = Requisicion::with(['user', 'detalles', 'aprobadorFinanzas'])
                ->where('estatus_finanzas', 'aprobado')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->canManageInventory()) {
            $requisiciones = Requisicion::with(['user', 'detalles'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            $requisiciones = Requisicion::where('user_id', $user->id)
                ->with(['user', 'detalles'])
                ->orderBy('created_at', 'desc')
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
    // Mensajes personalizados en español
    $messages = [
        'nombre_solicitante.required' => 'El nombre del solicitante es obligatorio',
        'departamento.required' => 'El departamento es obligatorio',
        'tipo_requerimiento.required' => 'Debes seleccionar un tipo de requerimiento',
        'tipo_requerimiento.in' => 'El tipo de requerimiento debe ser interno o externo',
        'comentario.required' => 'Debes proporcionar un comentario o justificación',
        'materiales.required' => 'Debes agregar al menos un material',
        'materiales.min' => 'Debes agregar al menos un material',
        'materiales.*.cantidad.required' => 'La cantidad es obligatoria',
        'materiales.*.cantidad.integer' => 'La cantidad debe ser un número entero',
        'materiales.*.cantidad.min' => 'La cantidad debe ser al menos 1',
        'materiales.*.unidad.required' => 'La unidad es obligatoria',
        'materiales.*.material.required' => 'La descripción del material es obligatoria',
        'contrato_id.required' => 'Debes seleccionar un contrato',
    ];

    $request->validate([
        'nombre_solicitante' => 'required|string|max:255',
        'departamento' => 'required|string|max:255',
        // ✅ PLATAFORMA Y EMBARCACIÓN AHORA SON OPCIONALES (nullable)
        'plataforma' => 'nullable|string|max:255',
        'embarcacion' => 'nullable|string|max:255',
        'tipo_requerimiento' => 'required|in:interno,externo',
        'comentario' => 'required|string',
        'contrato_id' => 'required|exists:contratos,id',
        'materiales' => 'required|array|min:1',
        'materiales.*.cantidad' => 'required|integer|min:1',
        'materiales.*.unidad' => 'required|string|max:255',
        'materiales.*.material' => 'required|string|max:255',
    ], $messages);

    DB::beginTransaction();

    try {
        $requisicion = Requisicion::create([
            'nombre_solicitante' => $request->nombre_solicitante,
            'departamento' => $request->departamento,
            'proyecto' => $request->proyecto ?? 'N/A',
            'sit' => $request->sit ?? 'N/A',
            'partida' => $request->partida ?? 'N/A',
            // ✅ Si plataforma o embarcación vienen vacíos, se guardan como N/A
            'plataforma' => $request->plataforma ?: 'N/A',
            'area' => $request->area ?? 'N/A',
            'activo' => $request->activo ?? 'N/A',
            'contrato_id' => $request->contrato_id,
            'embarcacion' => $request->embarcacion ?: 'N/A',
            'tipo_requerimiento' => $request->tipo_requerimiento,
            'comentario' => $request->comentario,
            'user_id' => auth()->id(),
        ]);

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
        return back()->withErrors(['error' => 'Error al crear la requisición: ' . $e->getMessage()])->withInput();
    }
}

    public function show(Requisicion $requisicion)
    {
        $user = auth()->user();

        if (!($requisicion->user_id == $user->id ||
            $user->canApproveRequests() ||
            $user->canApproveFinanzas() ||
            $user->canManageInventory())) {
            abort(403);
        }

        $requisicion->load(['user', 'detalles', 'contrato', 'aprobadorFinanzas']);
        return view('requisiciones.show', compact('requisicion'));
    }

    public function updateEstatusFinanzas(Request $request, Requisicion $requisicion)
    {
        if (!auth()->user()->canApproveFinanzas()) abort(403);

        $request->validate([
            'estatus_finanzas' => 'required|in:aprobado,denegado',
        ]);

        $requisicion->update([
            'estatus_finanzas' => $request->estatus_finanzas,
            'aprobado_por_finanzas_id' => auth()->id(),
            'fecha_aprobacion_finanzas' => now(),
        ]);

        return back()->with('success', 'Estatus finanzas actualizado');
    }

    public function updateEstatus(Request $request, Requisicion $requisicion)
    {
        if (!auth()->user()->canApproveRequests()) abort(403);

        if ($requisicion->estatus_finanzas !== 'aprobado') {
            return back()->with('error', 'Aún no aprobado por finanzas');
        }

        $request->validate([
            'estatus' => 'required|in:aprobado,denegado',
        ]);

        $requisicion->update(['estatus' => $request->estatus]);

        return back()->with('success', 'Estatus actualizado');
    }


    // ===============================
    //  EXPORTACIÓN A EXCEL 
    // ===============================
    public function exportExcel(Requisicion $requisicion)
    {
        $requisicion->load(['user','contrato','detalles']);

        $templatePath = storage_path('app/plantillas/Requisicion.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('C10', strtoupper($requisicion->user->name));
        $sheet->setCellValue('C11', strtoupper($requisicion->user->role));
        $sheet->setCellValue('G5', $requisicion->created_at->format('d/m/Y'));
        $sheet->setCellValue('A34', $requisicion->comentario ?? 'N/A');
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
        $sheet->setCellValue('G5', $requisicion->created_at->format('d/m/Y)'));
        $sheet->setCellValue('A34', $requisicion->comentario ?? 'N/A');

        // ✅ Materiales (múltiples productos)
        $row = 21;
        foreach ($requisicion->detalles as $detalle) {
            $sheet->setCellValue('A' . $row, $detalle->cantidad);
            $sheet->setCellValue('B' . $row, $detalle->unidad);
            $sheet->setCellValue('C' . $row, $detalle->material);
            $row++;
        }

        // ================= FIRMA =================
        if ($requisicion->user->signature) {

            $signaturePath = storage_path('app/public/'.$requisicion->user->signature);

            if (file_exists($signaturePath)) {

                $drawing = new Drawing();
                $drawing->setPath($signaturePath);
                $drawing->setCoordinates('G35');
                $drawing->setOffsetX(-40);// derecha
                $drawing->setOffsetY(-60);// abajo
                $drawing->setHeight(100);// altura en píxeles
                $drawing->setWidth(220);// ancho en píxeles (descomentar si necesitas)
                $drawing->setWorksheet($sheet);
            }
        }

        // ================= ESTATUS finanzas =================
        $imgFinanzas = $this->obtenerImagenEstatus($requisicion->estatus_finanzas,'finanzas');

        if(file_exists($imgFinanzas)){
            $draw1 = new Drawing();
            $draw1->setPath($imgFinanzas);
            $draw1->setCoordinates('B40');
            $draw1->setOffsetY(-40); // abajo
            $draw1->setOffsetX(-20); // derecha

            $draw1->setHeight(90); // altura en píxeles
            // $draw1->setWidth(100); // ancho en píxeles (descomentar si necesitas)

            $draw1->setWorksheet($sheet);
        }

        // ================= ESTATUS direccion =================
        $imgEstatus = $this->obtenerImagenEstatus($requisicion->estatus,'direccion');

        if(file_exists($imgEstatus)){
            $draw2 = new Drawing();
            $draw2->setPath($imgEstatus);
            $draw2->setCoordinates('G40');
            $draw2->setOffsetY(-30); // abajo
            $draw2->setOffsetX(-30); // derecha
            $draw2->setHeight(90); // altura en píxeles
            // $draw2->setWidth(150); // ancho en píxeles (descomentar si necesitas)
  
            $draw2->setWorksheet($sheet);
        }

        // ================= MATERIALES =================
        $row = 21;
        foreach ($requisicion->detalles as $detalle) {
            $sheet->setCellValue('A'.$row, $detalle->cantidad);
            $sheet->setCellValue('B'.$row, $detalle->unidad);
            $sheet->setCellValue('C'.$row, $detalle->material);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Requisicion_'.$requisicion->id.'.xlsx';

        return new StreamedResponse(function() use ($writer){
            $writer->save('php://output');
        },200,[
            'Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition'=>'attachment;filename="'.$filename.'"',
        ]);
    }
}
