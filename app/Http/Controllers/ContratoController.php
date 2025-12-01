<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::all();
        return view('contratos.index', compact('contratos'));
    }

    public function create()
    {
        return view('contratos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_nombre' => 'required',
            'contrato' => 'required',
            'convenio' => 'required',
        ]);

        Contrato::create($request->all());

        return redirect()->route('contratos.index')->with('success', 'Contrato creado correctamente.');
    }
}
