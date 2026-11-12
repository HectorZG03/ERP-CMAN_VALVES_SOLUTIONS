<?php

namespace Tests\Feature;

use App\Models\DatosLaborales;
use App\Models\Personal;
use App\Models\Salida;
use App\Models\SolicitudMaterial;
use App\Models\User;
use App\Services\SolicitudSalidaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SolicitudSalidaNumeroLaboralTest extends TestCase
{
    private User $solicitante;
    private User $almacen;
    private SolicitudMaterial $solicitud;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'solicitudes_prueba',
            'database.connections.solicitudes_prueba' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'session.driver' => 'array',
            'cache.default' => 'array',
            'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('t', 32)),
        ]);

        // Equivalente de CONCAT_WS de MySQL para las búsquedas existentes.
        DB::connection()->getPdo()->sqliteCreateFunction('CONCAT_WS', fn ($separator, ...$values) => implode($separator, array_filter($values, fn ($value) => $value !== null)));
        DB::unprepared(<<<'SQL'
            CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, password TEXT, role TEXT, personal_id INTEGER, signature TEXT, remember_token TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE personal (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, ape_pat TEXT, ape_mat TEXT, foto TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE datos_laborales (id INTEGER PRIMARY KEY AUTOINCREMENT, personal_id INTEGER, employee_id TEXT, puesto_id INTEGER, created_at TEXT, updated_at TEXT);
            CREATE TABLE solicitud_materiales (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, personal_id INTEGER, destino TEXT, estatus TEXT, comentario TEXT, tipo_solicitud TEXT, operador TEXT, categoria TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE solicitud_material_detalles (id INTEGER PRIMARY KEY AUTOINCREMENT, solicitud_material_id INTEGER, inventario_id INTEGER, cantidad_solicitada INTEGER, precio_unitario REAL, created_at TEXT, updated_at TEXT);
            CREATE TABLE salidas (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, solicitud_material_id INTEGER, cliente_id INTEGER, numero_factura TEXT, fecha_salida TEXT, observaciones TEXT, cantidad_total INTEGER, precio_unitario_promedio REAL, precio_total REAL, iva REAL, total_con_iva REAL, created_at TEXT, updated_at TEXT);
            CREATE TABLE salida_detalles (id INTEGER PRIMARY KEY AUTOINCREMENT, salida_id INTEGER, inventario_id INTEGER, cantidad INTEGER, precio_unitario REAL, precio_total REAL, iva REAL, total_con_iva REAL, created_at TEXT, updated_at TEXT);
            CREATE TABLE inventarios (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre_producto TEXT, economico TEXT, categoria TEXT, medida TEXT, existencia INTEGER, precio_total REAL);
            CREATE TABLE clientes (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, area TEXT, email TEXT);
            SQL);

        $personal = Personal::create(['nombre' => 'Nombre del expediente', 'ape_pat' => 'Solicitante']);
        DatosLaborales::create(['personal_id' => $personal->id, 'employee_id' => 'LABORAL-ACTUAL-030']);
        $operador = Personal::create(['nombre' => 'Operador', 'ape_pat' => 'Asignado']);
        DatosLaborales::create(['personal_id' => $operador->id, 'employee_id' => 'OPERADOR-999']);
        $this->solicitante = User::create([
            'name' => 'Nombre de la cuenta ERP', 'email' => 'solicitante@example.test', 'password' => 'password123',
            'role' => 'operaciones', 'personal_id' => $personal->id,
        ]);
        $this->almacen = User::create(['name' => 'Almacen', 'email' => 'almacen@example.test', 'password' => 'password123', 'role' => 'almacen']);
        $this->solicitud = SolicitudMaterial::create([
            'user_id' => $this->solicitante->id, 'personal_id' => $operador->id,
            'destino' => 'Destino de prueba', 'estatus' => 'aprobado', 'tipo_solicitud' => 'estandar',
        ]);
        DB::table('inventarios')->insert(['id' => 1, 'nombre_producto' => 'Producto de prueba', 'economico' => 'PROD-001', 'categoria' => 'Equipo', 'medida' => 'PZA', 'existencia' => 10, 'precio_total' => 100]);
        DB::table('solicitud_material_detalles')->insert(['solicitud_material_id' => $this->solicitud->id, 'inventario_id' => 1, 'cantidad_solicitada' => 5, 'precio_unitario' => 10]);
    }

    public function test_busca_por_numero_laboral_y_descarta_el_numero_heredado(): void
    {
        $this->actingAs($this->almacen)->getJson(route('salidas.buscar-solicitudes', ['q' => 'LABORAL-ACTUAL-030']))
            ->assertOk()->assertJsonCount(1)->assertJsonPath('0.numero_empleado', 'LABORAL-ACTUAL-030')
            ->assertJsonPath('0.solicitante', $this->solicitante->name);
        $this->getJson(route('salidas.buscar-solicitudes', ['q' => 'NUMERO-ANTIGUO']))->assertOk()->assertExactJson([]);
    }

    public function test_distingue_numero_del_solicitante_y_operador_y_conserva_pendientes(): void
    {
        $salida = $this->salida();
        DB::table('salida_detalles')->insert(['salida_id' => $salida->id, 'inventario_id' => 1, 'cantidad' => 3]);

        $this->actingAs($this->almacen)->getJson(route('salidas.solicitudes.show', $this->solicitud))
            ->assertOk()->assertJsonPath('solicitante.numero_empleado', 'LABORAL-ACTUAL-030')
            ->assertJsonPath('solicitante.nombre', $this->solicitante->name)
            ->assertJsonPath('operador.numero_empleado', 'OPERADOR-999')
            ->assertJsonPath('productos.0.cantidad_pendiente', 2);
        $resultados = app(SolicitudSalidaService::class)->buscarSolicitudes('OPERADOR-999');
        $this->assertCount(1, $resultados);
        $this->assertSame('LABORAL-ACTUAL-030', $resultados[0]['numero_empleado']);
    }

    public function test_solicitante_sin_vinculo_no_recupera_numero_antiguo(): void
    {
        $this->solicitante->update(['personal_id' => null]);
        $this->actingAs($this->almacen)->getJson(route('salidas.solicitudes.show', $this->solicitud))
            ->assertOk()->assertJsonPath('solicitante.numero_empleado', 'N/A');
        $this->getJson(route('salidas.buscar-solicitudes', ['q' => 'NUMERO-ANTIGUO']))->assertOk()->assertExactJson([]);
    }

    public function test_filtro_de_salidas_utiliza_numero_laboral_y_respeta_acceso_por_rol(): void
    {
        $salida = $this->salida();
        $response = $this->actingAs($this->almacen)->get(route('salidas.index', ['buscar' => 'LABORAL-ACTUAL-030']));
        $response->assertOk()->assertViewHas('salidas', fn ($salidas) => $salidas->total() === 1 && $salidas->first()->id === $salida->id);

        $response = $this->get(route('salidas.index', ['buscar' => 'NUMERO-ANTIGUO']));
        $response->assertOk()->assertViewHas('salidas', fn ($salidas) => $salidas->total() === 0);

        // Operaciones solo puede consultar salidas cuyo solicitante sea de Logística.
        $this->actingAs($this->solicitante)->get(route('salidas.index', ['buscar' => 'LABORAL-ACTUAL-030']))
            ->assertOk()->assertViewHas('salidas', fn ($salidas) => $salidas->total() === 0);
    }

    public function test_formatos_de_solicitud_estandar_y_epp_muestran_numero_actual(): void
    {
        foreach (['estandar', 'epp'] as $tipo) {
            $this->solicitud->update(['tipo_solicitud' => $tipo]);
            $this->actingAs($this->almacen)->get(route('solicitudes.pdf', $this->solicitud))
                ->assertOk()->assertSee('LABORAL-ACTUAL-030')->assertSee($this->solicitante->name)
                ->assertDontSee('NUMERO-ANTIGUO');
        }
    }

    public function test_formatos_de_solicitud_sin_expediente_no_muestran_numero_antiguo(): void
    {
        $this->solicitante->update(['personal_id' => null]);
        foreach (['estandar', 'epp'] as $tipo) {
            $this->solicitud->update(['tipo_solicitud' => $tipo]);
            $this->actingAs($this->almacen)->get(route('solicitudes.pdf', $this->solicitud))
                ->assertOk()->assertDontSee('NUMERO-ANTIGUO')->assertDontSee('LABORAL-ACTUAL-030');
        }
    }

    #[DataProvider('vinculaciones')]
    public function test_pdf_de_salida_carga_expediente_y_renderiza_numero_actual(bool $vinculado): void
    {
        if (! $vinculado) {
            $this->solicitante->update(['personal_id' => null]);
        }
        $salida = $this->salida();
        $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        Pdf::shouldReceive('loadView')->once()->withArgs(function ($vista, $datos) use ($vinculado) {
            $user = $datos['salida']->solicitudMaterial->user;
            $this->assertTrue($user->relationLoaded('personal'));
            if ($vinculado) {
                $this->assertTrue($user->personal->relationLoaded('datosLaborales'));
            } else {
                $this->assertNull($user->personal);
            }
            $html = view($vista, $datos)->render();
            $this->assertStringContainsString($vinculado ? 'LABORAL-ACTUAL-030' : 'ID SOLICITANTE:</span> N/A', $html);
            $this->assertStringContainsString(mb_strtoupper($this->solicitante->name), $html);
            $this->assertStringNotContainsString('NUMERO-ANTIGUO', $html);

            return true;
        })->andReturn($pdf);
        $pdf->shouldReceive('setPaper')->once()->with('letter', 'portrait')->andReturnSelf();
        $pdf->shouldReceive('download')->once()->andReturn(response('pdf-prueba', 200, ['Content-Type' => 'application/pdf']));
        $this->actingAs($this->almacen)->get(route('salidas.pdf', $salida))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public static function vinculaciones(): array
    {
        return ['con expediente' => [true], 'sin expediente' => [false]];
    }

    private function salida(): Salida
    {
        // Insertar el historial de prueba sin disparar movimientos de inventario.
        $id = DB::table('salidas')->insertGetId([
            'user_id' => $this->almacen->id, 'solicitud_material_id' => $this->solicitud->id,
            'numero_factura' => 'SAL-PRUEBA', 'fecha_salida' => '2026-11-12',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return Salida::findOrFail($id);
    }
}
