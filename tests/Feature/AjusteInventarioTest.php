<?php

namespace Tests\Feature;

use App\Models\Inventario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AjusteInventarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_almacen_puede_incrementar_stock_y_se_calcula_el_nuevo_promedio(): void
    {
        $user = $this->usuario('almacen');
        $inventario = $this->inventario(10, 1000);

        $response = $this->actingAs($user)->post(route('inventario.ajustes.store', $inventario), [
            'operacion' => 'stock',
            'nueva_existencia' => 15,
            'costo_unitario_ajuste' => 120,
            'motivo' => 'Diferencia encontrada durante el conteo físico.',
        ]);

        $response->assertRedirect(route('inventario.edit', $inventario));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inventarios', [
            'id' => $inventario->id,
            'existencia' => 15,
            'precio_total' => 1600,
        ]);
        $this->assertDatabaseHas('ajustes_inventario', [
            'inventario_id' => $inventario->id,
            'user_id' => $user->id,
            'tipo' => 'incremento',
            'existencia_anterior' => 10,
            'existencia_nueva' => 15,
            'diferencia' => 5,
            'valor_total_anterior' => 1000,
            'valor_total_nuevo' => 1600,
            'diferencia_valor' => 600,
        ]);
    }

    public function test_disminucion_utiliza_el_costo_promedio_actual(): void
    {
        $user = $this->usuario('aux_almacen');
        $inventario = $this->inventario(15, 1600);

        $this->actingAs($user)->post(route('inventario.ajustes.store', $inventario), [
            'operacion' => 'stock',
            'nueva_existencia' => 13,
            'motivo' => 'Material dañado durante el almacenamiento.',
        ])->assertRedirect(route('inventario.edit', $inventario));

        $this->assertDatabaseHas('inventarios', [
            'id' => $inventario->id,
            'existencia' => 13,
            'precio_total' => 1386.67,
        ]);
        $this->assertDatabaseHas('ajustes_inventario', [
            'inventario_id' => $inventario->id,
            'tipo' => 'disminucion',
            'diferencia' => -2,
            'valor_total_nuevo' => 1386.67,
            'diferencia_valor' => -213.33,
        ]);
    }

    public function test_almacen_puede_revaluar_sin_modificar_existencia(): void
    {
        $user = $this->usuario('almacen');
        $inventario = $this->inventario(8, 800);

        $this->actingAs($user)->post(route('inventario.ajustes.store', $inventario), [
            'operacion' => 'revaluacion',
            'nuevo_costo_unitario' => 125,
            'motivo' => 'Actualización autorizada del costo del producto.',
        ])->assertRedirect(route('inventario.edit', $inventario));

        $this->assertDatabaseHas('inventarios', [
            'id' => $inventario->id,
            'existencia' => 8,
            'precio_total' => 1000,
        ]);
        $this->assertDatabaseHas('ajustes_inventario', [
            'inventario_id' => $inventario->id,
            'tipo' => 'revaluacion',
            'existencia_anterior' => 8,
            'existencia_nueva' => 8,
            'diferencia' => 0,
            'costo_promedio_nuevo' => 125,
            'valor_total_nuevo' => 1000,
        ]);
    }

    public function test_usuario_sin_rol_de_almacen_no_puede_registrar_ajustes(): void
    {
        $user = $this->usuario('direccion');
        $inventario = $this->inventario(10, 1000);

        $this->actingAs($user)->post(route('inventario.ajustes.store', $inventario), [
            'operacion' => 'stock',
            'nueva_existencia' => 12,
            'costo_unitario_ajuste' => 100,
            'motivo' => 'Intento de ajuste sin autorización.',
        ])->assertForbidden();

        $this->assertDatabaseHas('inventarios', [
            'id' => $inventario->id,
            'existencia' => 10,
            'precio_total' => 1000,
        ]);
        $this->assertDatabaseCount('ajustes_inventario', 0);
    }

    public function test_no_se_registra_un_ajuste_si_la_existencia_no_cambia(): void
    {
        $user = $this->usuario('almacen');
        $inventario = $this->inventario(10, 1000);

        $this->actingAs($user)->from(route('inventario.edit', $inventario))
            ->post(route('inventario.ajustes.store', $inventario), [
                'operacion' => 'stock',
                'nueva_existencia' => 10,
                'costo_unitario_ajuste' => 100,
                'motivo' => 'Conteo físico sin diferencia registrada.',
            ])
            ->assertRedirect(route('inventario.edit', $inventario))
            ->assertSessionHasErrors('nueva_existencia');

        $this->assertDatabaseCount('ajustes_inventario', 0);
    }

    public function test_no_se_puede_eliminar_un_producto_con_historial_de_ajustes(): void
    {
        $user = $this->usuario('almacen');
        $inventario = $this->inventario(10, 1000);

        $this->actingAs($user)->post(route('inventario.ajustes.store', $inventario), [
            'operacion' => 'stock',
            'nueva_existencia' => 11,
            'costo_unitario_ajuste' => 100,
            'motivo' => 'Unidad localizada durante el conteo físico.',
        ]);

        $this->actingAs($user)->delete(route('inventario.destroy', $inventario))
            ->assertRedirect(route('inventario.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('inventarios', ['id' => $inventario->id]);
        $this->assertDatabaseCount('ajustes_inventario', 1);
    }

    private function usuario(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function inventario(int $existencia, float $precioTotal): Inventario
    {
        return Inventario::create([
            'categoria' => 'Válvulas',
            'nombre_producto' => 'Válvula de prueba',
            'economico' => 'TEST-001',
            'medida' => 'Pieza',
            'ubicacion' => 'A-01',
            'existencia' => $existencia,
            'precio_total' => $precioTotal,
        ]);
    }
}
