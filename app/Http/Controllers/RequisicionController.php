<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisicion;

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
}