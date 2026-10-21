<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Departamento;
use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogoRhController extends Controller
{
    public function index()
    {
        $areas = Area::query()
            ->withCount('departamentos')
            ->orderBy('nombre')
            ->get();

        $departamentos = Departamento::query()
            ->with('area:id,nombre')
            ->withCount('puestos')
            ->orderBy('area_id')
            ->orderBy('nombre')
            ->get();

        $puestos = Puesto::query()
            ->with('departamento.area')
            ->withCount('datosLaborales')
            ->orderBy('nombre')
            ->get();

        return view('rh.catalogos.index', compact(
            'areas',
            'departamentos',
            'puestos'
        ));
    }

    public function storeArea(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100', Rule::unique('areas', 'nombre')],
        ]);

        $area = Area::create([
            'nombre' => trim($datos['nombre']),
        ]);

        return response()->json([
            'message' => 'Área creada correctamente.',
            'data' => $area->loadCount('departamentos'),
        ], 201);
    }

    public function updateArea(Request $request, Area $area)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('areas', 'nombre')->ignore($area->id),
            ],
        ]);

        $area->update([
            'nombre' => trim($datos['nombre']),
        ]);

        return response()->json([
            'message' => 'Área actualizada correctamente.',
            'data' => $area->loadCount('departamentos'),
        ]);
    }

    public function destroyArea(Area $area)
    {
        if ($area->departamentos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el área porque tiene departamentos relacionados.',
            ], 422);
        }

        $id = $area->id;
        $area->delete();

        return response()->json([
            'message' => 'Área eliminada correctamente.',
            'id' => $id,
        ]);
    }

    public function storeDepartamento(Request $request)
    {
        $datos = $request->validate([
            'area_id' => ['required', 'integer', Rule::exists('areas', 'id')],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('departamentos', 'nombre')
                    ->where(fn ($query) => $query->where('area_id', $request->input('area_id'))),
            ],
        ]);

        $departamento = Departamento::create([
            'area_id' => $datos['area_id'],
            'nombre' => trim($datos['nombre']),
        ]);

        return response()->json([
            'message' => 'Departamento creado correctamente.',
            'data' => $departamento
                ->load('area:id,nombre')
                ->loadCount('puestos'),
        ], 201);
    }

    public function updateDepartamento(Request $request, Departamento $departamento)
    {
        $datos = $request->validate([
            'area_id' => ['required', 'integer', Rule::exists('areas', 'id')],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('departamentos', 'nombre')
                    ->where(fn ($query) => $query->where('area_id', $request->input('area_id')))
                    ->ignore($departamento->id),
            ],
        ]);

        $departamento->update([
            'area_id' => $datos['area_id'],
            'nombre' => trim($datos['nombre']),
        ]);

        return response()->json([
            'message' => 'Departamento actualizado correctamente.',
            'data' => $departamento
                ->load('area:id,nombre')
                ->loadCount('puestos'),
        ]);
    }

    public function destroyDepartamento(Departamento $departamento)
    {
        if ($departamento->puestos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el departamento porque tiene puestos relacionados.',
            ], 422);
        }

        $id = $departamento->id;
        $departamento->delete();

        return response()->json([
            'message' => 'Departamento eliminado correctamente.',
            'id' => $id,
        ]);
    }

    public function storePuesto(Request $request)
    {
        $datos = $request->validate([
            'departamento_id' => ['required', 'integer', Rule::exists('departamentos', 'id')],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('puestos', 'nombre')
                    ->where(fn ($query) => $query->where(
                        'departamento_id',
                        $request->input('departamento_id')
                    )),
            ],
        ]);

        $puesto = Puesto::create([
            'departamento_id' => $datos['departamento_id'],
            'nombre' => trim($datos['nombre']),
        ]);

        return response()->json([
            'message' => 'Puesto creado correctamente.',
            'data' => $puesto
                ->load('departamento.area')
                ->loadCount('datosLaborales'),
        ], 201);
    }

    public function updatePuesto(Request $request, Puesto $puesto)
    {
        $datos = $request->validate([
            'departamento_id' => ['required', 'integer', Rule::exists('departamentos', 'id')],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('puestos', 'nombre')
                    ->where(fn ($query) => $query->where(
                        'departamento_id',
                        $request->input('departamento_id')
                    ))
                    ->ignore($puesto->id),
            ],
        ]);

        $puesto->update([
            'departamento_id' => $datos['departamento_id'],
            'nombre' => trim($datos['nombre']),
        ]);

        return response()->json([
            'message' => 'Puesto actualizado correctamente.',
            'data' => $puesto
                ->load('departamento.area')
                ->loadCount('datosLaborales'),
        ]);
    }

    public function destroyPuesto(Puesto $puesto)
    {
        if ($puesto->datosLaborales()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el puesto porque está asignado a personal.',
            ], 422);
        }

        $id = $puesto->id;
        $puesto->delete();

        return response()->json([
            'message' => 'Puesto eliminado correctamente.',
            'id' => $id,
        ]);
    }
}