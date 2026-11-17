<?php

namespace Tests\Feature;

use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\SolicitudMaterial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreacionTextosMayusculasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mayusculas_prueba',
            'database.connections.mayusculas_prueba' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'session.driver' => 'array', 'cache.default' => 'array', 'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('t', 32)),
        ]);
        DB::unprepared(<<<'SQL'
            CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, password TEXT, role TEXT, personal_id INTEGER, signature TEXT, remember_token TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE personal (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, ape_pat TEXT, ape_mat TEXT, foto TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE contratos (id INTEGER PRIMARY KEY, empresa_nombre TEXT, contrato TEXT, convenio TEXT);
            CREATE TABLE requisiciones (id INTEGER PRIMARY KEY AUTOINCREMENT, folio TEXT, nombre_solicitante TEXT, departamento TEXT, proyecto TEXT, sit TEXT, partida TEXT, plataforma TEXT, area TEXT, activo TEXT, contrato_id INTEGER, destino TEXT, tipo_requerimiento TEXT, comentario TEXT, user_id INTEGER, estatus TEXT, estatus_finanzas TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE requisicion_detalles (id INTEGER PRIMARY KEY AUTOINCREMENT, requisicion_id INTEGER, cantidad INTEGER, unidad TEXT, material TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE inventarios (id INTEGER PRIMARY KEY, nombre_producto TEXT, economico TEXT, categoria TEXT, medida TEXT, ubicacion TEXT, existencia INTEGER, precio_total REAL, created_at TEXT, updated_at TEXT);
            CREATE TABLE solicitud_materiales (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, personal_id INTEGER, destino TEXT, comentario TEXT, operador TEXT, categoria TEXT, tipo_solicitud TEXT, estatus TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE solicitud_material_detalles (id INTEGER PRIMARY KEY AUTOINCREMENT, solicitud_material_id INTEGER, inventario_id INTEGER, cantidad_solicitada INTEGER, precio_unitario REAL, created_at TEXT, updated_at TEXT);
            SQL);
        DB::table('contratos')->insert(['id' => 1, 'empresa_nombre' => 'Empresa origen', 'contrato' => 'abc-01', 'convenio' => 'Convenio']);
        DB::table('inventarios')->insert([
            ['id' => 1, 'nombre_producto' => 'Producto de catálogo', 'economico' => 'eq-01', 'categoria' => 'Herramienta', 'medida' => 'Pza', 'existencia' => 10, 'precio_total' => 100],
            ['id' => 2, 'nombre_producto' => 'Guantes de protección', 'economico' => 'epp-01', 'categoria' => 'SEGURIDAD', 'medida' => 'Par', 'existencia' => 10, 'precio_total' => 100],
        ]);
        $this->actingAs(User::create(['name' => 'Nombre de cuenta ángel', 'email' => 'usuario@example.test', 'password' => 'password123', 'role' => 'operaciones']));
    }

    public function test_requisicion_guarda_textos_y_materiales_en_mayusculas_sin_javascript(): void
    {
        $payload = $this->requisicion();
        DB::table('requisiciones')->insert(['nombre_solicitante' => 'Nombre histórico']);
        $this->post(route('requisiciones.store'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('requisiciones.index'));
        $registro = Requisicion::latest('id')->firstOrFail();

        foreach (['nombre_solicitante', 'departamento', 'proyecto', 'sit', 'partida', 'plataforma', 'area', 'activo', 'destino', 'comentario'] as $campo) {
            $this->assertSame(mb_strtoupper($payload[$campo], 'UTF-8'), $registro->$campo);
        }
        $this->assertSame('interno', $registro->tipo_requerimiento);
        $this->assertSame('pendiente', $registro->estatus);
        $this->assertSame('pendiente', $registro->estatus_finanzas);
        $this->assertEquals(1, $registro->contrato_id);
        $detalles = RequisicionDetalle::where('requisicion_id', $registro->id)->orderBy('id')->get();
        $this->assertCount(2, $detalles);
        $this->assertSame('LÁMINA PARA ÁREA HÚMEDA', $detalles[0]->material);
        $this->assertSame('PIEZAS', $detalles[0]->unidad);
        $this->assertSame(3, $detalles[0]->cantidad);
        $this->assertSame('REVISIÓN DE CONEXIÓN Ñ', $detalles[1]->material);
        $this->assertSame('KG', $detalles[1]->unidad);
        $this->assertSame('Nombre histórico', DB::table('requisiciones')->where('id', 1)->value('nombre_solicitante'));
        $this->assertSame('Nombre de cuenta ángel', User::firstOrFail()->name);
        $this->assertSame('abc-01', DB::table('contratos')->value('contrato'));
    }

    public function test_solicitud_estandar_guarda_textos_sin_modificar_catalogos_o_cantidades(): void
    {
        $this->post(route('solicitudes.store'), [
            'destino' => 'muelle número 2', 'comentario' => "revisión de conexión\nmañana",
            'operador' => 'operador de apoyo', 'categoria' => 'consumibles', 'tipo_solicitud' => 'estandar',
            'productos' => [['inventario_id' => 1, 'cantidad_solicitada' => 2]],
        ])->assertSessionHasNoErrors()->assertRedirect(route('solicitudes.index'));

        $solicitud = SolicitudMaterial::firstOrFail();
        $this->assertSame('MUELLE NÚMERO 2', $solicitud->destino);
        $this->assertSame("REVISIÓN DE CONEXIÓN\nMAÑANA", $solicitud->comentario);
        $this->assertSame('OPERADOR DE APOYO', $solicitud->operador);
        $this->assertSame('CONSUMIBLES', $solicitud->categoria);
        $this->assertSame('estandar', $solicitud->tipo_solicitud);
        $this->assertSame('pendiente', $solicitud->estatus);
        $this->assertDatabaseHas('solicitud_material_detalles', ['inventario_id' => 1, 'cantidad_solicitada' => 2]);
        $this->assertDatabaseHas('inventarios', ['id' => 1, 'nombre_producto' => 'Producto de catálogo', 'existencia' => 10, 'economico' => 'eq-01']);
    }

    public function test_solicitud_epp_conserva_tipo_y_control_de_acceso(): void
    {
        $payload = ['destino' => 'área de operación', 'comentario' => 'protección personal', 'tipo_solicitud' => 'epp',
            'productos' => [['inventario_id' => 2, 'cantidad_solicitada' => 1]]];
        $this->postJson(route('solicitudes.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('tipo_solicitud');
        $this->assertDatabaseCount('solicitud_materiales', 0);

        User::firstOrFail()->update(['role' => 'hse']);
        $this->actingAs(User::firstOrFail())->post(route('solicitudes.store'), $payload)
            ->assertSessionHasNoErrors()->assertRedirect(route('solicitudes.index'));
        $this->assertDatabaseHas('solicitud_materiales', ['destino' => 'ÁREA DE OPERACIÓN', 'comentario' => 'PROTECCIÓN PERSONAL', 'tipo_solicitud' => 'epp']);
        $this->assertDatabaseHas('inventarios', ['id' => 2, 'nombre_producto' => 'Guantes de protección', 'existencia' => 10]);
    }

    public function test_valida_longitud_despues_de_convertir_a_mayusculas(): void
    {
        $payload = $this->requisicion();
        $payload['nombre_solicitante'] = str_repeat('ß', 128); // 256 letras al convertir a SS.
        $this->postJson(route('requisiciones.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('nombre_solicitante');
        $payload = $this->requisicion();
        $payload['proyecto'] = str_repeat('a', 256);
        $this->postJson(route('requisiciones.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors('proyecto');
        $this->assertDatabaseCount('requisiciones', 0);
    }

    public function test_payloads_con_tipos_invalidos_producen_validacion_y_no_error_de_servidor(): void
    {
        $payload = $this->requisicion();
        $payload['proyecto'] = ['texto'];
        $payload['materiales'][0]['unidad'] = ['texto'];
        $this->postJson(route('requisiciones.store'), $payload)->assertUnprocessable()->assertJsonValidationErrors(['proyecto', 'materiales.0.unidad']);
        $this->postJson(route('solicitudes.store'), ['destino' => ['texto'], 'productos' => [['inventario_id' => 1, 'cantidad_solicitada' => 1]]])
            ->assertUnprocessable()->assertJsonValidationErrors('destino');
    }

    public function test_campos_opcionales_conservan_nulos_y_valores_por_defecto(): void
    {
        $payload = $this->requisicion();
        foreach (['proyecto', 'sit', 'partida', 'area', 'activo', 'plataforma', 'destino'] as $campo) unset($payload[$campo]);
        $this->post(route('requisiciones.store'), $payload)->assertSessionHasNoErrors();
        $registro = Requisicion::firstOrFail();
        foreach (['proyecto', 'sit', 'partida', 'area', 'activo', 'plataforma'] as $campo) $this->assertSame('N/A', $registro->$campo);
        $this->assertNull($registro->destino);

        $this->post(route('solicitudes.store'), ['destino' => 'base', 'comentario' => null, 'productos' => [['inventario_id' => 1, 'cantidad_solicitada' => 1]]])->assertSessionHasNoErrors();
        $registro = SolicitudMaterial::firstOrFail();
        $this->assertNull($registro->comentario);
        $this->assertSame('N/A', $registro->operador);
        $this->assertSame('estandar', $registro->tipo_solicitud);
    }

    public function test_formularios_incluyen_conversion_compartida_y_marcadores_de_textos(): void
    {
        foreach (['requisiciones.create', 'solicitudes.create'] as $ruta) {
            $response = $this->get(route($ruta))->assertOk()->assertSee('data-uppercase-form', false)
                ->assertSee('js/forms/uppercase.js', false);
            $dom = new \DOMDocument();
            $dom->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new \DOMXPath($dom);
            foreach (['destino', 'comentario'] as $campo) {
                $this->assertSame(1, $xpath->query('//*[@name="'.$campo.'" and @data-uppercase]')->length);
            }
            $this->assertSame(0, $xpath->query('//input[(@type="number" or @type="search" or @type="hidden") and @data-uppercase]')->length);
        }
    }

    private function requisicion(): array
    {
        return ['nombre_solicitante' => 'Ángel pérez ñúñez', 'departamento' => 'aux_ti', 'proyecto' => 'ampliación área norte',
            'sit' => 'sit-ñ-04', 'partida' => 'a-2', 'plataforma' => 'océano', 'area' => 'taller', 'activo' => 'eq-09',
            'destino' => 'muelle número 2', 'tipo_requerimiento' => 'interno', 'comentario' => "trabajo en área húmeda\nrevisión mañana",
            'contrato_id' => 1, 'materiales' => [
                ['cantidad' => 3, 'unidad' => 'piezas', 'material' => 'lámina para área húmeda'],
                ['cantidad' => 1, 'unidad' => 'kg', 'material' => 'revisión de conexión ñ'],
            ]];
    }
}
