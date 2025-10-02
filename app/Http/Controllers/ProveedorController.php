<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::paginate(15);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'economico' => 'nullable|string|max:255',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor agregado correctamente');
    }

    public function show(Proveedor $proveedor)
    {
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'proveedor' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'economico' => 'nullable|string|max:255',
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado');
    }

    public function destroy(Proveedor $proveedor)
{
    try {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $proveedor->delete();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado');
    } catch (\Exception $e) {
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return redirect()->route('proveedores.index')
            ->with('error', 'Error al eliminar: ' . $e->getMessage());
    }
}
}