<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAjusteInventarioRequest;
use App\Models\AjusteInventario;
use App\Models\Inventario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AjusteInventarioController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'inventario_id' => ['nullable', 'integer', 'exists:inventarios,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'tipo' => ['nullable', Rule::in(['incremento', 'disminucion', 'revaluacion'])],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $ajustes = AjusteInventario::query()
            ->with(['inventario', 'user'])
            ->when($filters['inventario_id'] ?? null, fn ($query, $id) => $query->where('inventario_id', $id))
            ->when($filters['user_id'] ?? null, fn ($query, $id) => $query->where('user_id', $id))
            ->when($filters['tipo'] ?? null, fn ($query, $tipo) => $query->where('tipo', $tipo))
            ->when($filters['fecha_desde'] ?? null, fn ($query, $fecha) => $query->whereDate('created_at', '>=', $fecha))
            ->when($filters['fecha_hasta'] ?? null, fn ($query, $fecha) => $query->whereDate('created_at', '<=', $fecha))
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('producto', 'like', "%{$search}%")
                        ->orWhere('economico', 'like', "%{$search}%")
                        ->orWhere('motivo', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $productos = Inventario::query()
            ->orderBy('nombre_producto')
            ->get(['id', 'nombre_producto', 'economico']);

        $usuarios = User::query()
            ->whereIn('id', AjusteInventario::query()->whereNotNull('user_id')->select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('inventario.ajustes.index', compact('ajustes', 'productos', 'usuarios', 'filters'));
    }

    public function store(StoreAjusteInventarioRequest $request, Inventario $inventario)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $inventario, $request) {
            $producto = Inventario::query()->lockForUpdate()->findOrFail($inventario->id);

            $existenciaAnterior = (int) $producto->existencia;
            $valorAnterior = round((float) $producto->precio_total, 2);
            $costoAnterior = $existenciaAnterior > 0
                ? $valorAnterior / $existenciaAnterior
                : 0;

            if ($data['operacion'] === 'stock') {
                $existenciaNueva = (int) $data['nueva_existencia'];
                $diferencia = $existenciaNueva - $existenciaAnterior;

                if ($diferencia === 0) {
                    throw ValidationException::withMessages([
                        'nueva_existencia' => 'La nueva existencia debe ser diferente de la existencia actual.',
                    ]);
                }

                if ($diferencia > 0) {
                    if (!isset($data['costo_unitario_ajuste']) || (float) $data['costo_unitario_ajuste'] <= 0) {
                        throw ValidationException::withMessages([
                            'costo_unitario_ajuste' => 'Indica el costo unitario de las unidades agregadas.',
                        ]);
                    }

                    $tipo = 'incremento';
                    $costoAjuste = (float) $data['costo_unitario_ajuste'];
                    $valorNuevo = round($valorAnterior + ($diferencia * $costoAjuste), 2);
                } else {
                    $tipo = 'disminucion';
                    $costoAjuste = $costoAnterior;
                    $valorNuevo = $existenciaNueva === 0
                        ? 0
                        : round($costoAnterior * $existenciaNueva, 2);
                }
            } else {
                if ($existenciaAnterior === 0) {
                    throw ValidationException::withMessages([
                        'nuevo_costo_unitario' => 'No se puede revaluar un producto sin existencias.',
                    ]);
                }

                $tipo = 'revaluacion';
                $existenciaNueva = $existenciaAnterior;
                $diferencia = 0;
                $costoAjuste = (float) $data['nuevo_costo_unitario'];
                $valorNuevo = round($existenciaNueva * $costoAjuste, 2);

                if (abs($valorNuevo - $valorAnterior) < 0.01) {
                    throw ValidationException::withMessages([
                        'nuevo_costo_unitario' => 'El nuevo costo debe modificar el valor actual del inventario.',
                    ]);
                }
            }

            if ($valorNuevo > 99999999.99) {
                throw ValidationException::withMessages([
                    'costo_unitario_ajuste' => 'El valor total calculado excede el límite permitido.',
                    'nuevo_costo_unitario' => 'El valor total calculado excede el límite permitido.',
                ]);
            }

            $costoNuevo = $existenciaNueva > 0 ? $valorNuevo / $existenciaNueva : 0;
            $diferenciaValor = round($valorNuevo - $valorAnterior, 2);

            $producto->update([
                'existencia' => $existenciaNueva,
                'precio_total' => $valorNuevo,
            ]);

            AjusteInventario::create([
                'inventario_id' => $producto->id,
                'user_id' => $request->user()->id,
                'producto' => $producto->nombre_producto,
                'economico' => $producto->economico,
                'usuario_nombre' => $request->user()->name,
                'tipo' => $tipo,
                'existencia_anterior' => $existenciaAnterior,
                'existencia_nueva' => $existenciaNueva,
                'diferencia' => $diferencia,
                'costo_promedio_anterior' => round($costoAnterior, 4),
                'costo_unitario_ajuste' => round($costoAjuste, 4),
                'costo_promedio_nuevo' => round($costoNuevo, 4),
                'valor_total_anterior' => $valorAnterior,
                'valor_total_nuevo' => $valorNuevo,
                'diferencia_valor' => $diferenciaValor,
                'motivo' => $data['motivo'],
            ]);
        }, 3);

        return redirect()
            ->route('inventario.edit', $inventario)
            ->with('success', 'El ajuste de inventario se registró correctamente.');
    }
}
