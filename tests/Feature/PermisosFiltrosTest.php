<?php

namespace Tests\Feature;

use App\Models\DatosLaborales;
use App\Models\PermisoPersonal;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PermisosFiltrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'permisos_filtros_prueba',
            'database.connections.permisos_filtros_prueba' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'session.driver' => 'array',
            'cache.default' => 'array',
            'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('t', 32)),
        ]);
        DB::unprepared(<<<'SQL'
            CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, password TEXT, role TEXT, personal_id INTEGER UNIQUE, signature TEXT, remember_token TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE personal (id INTEGER PRIMARY KEY AUTOINCREMENT, nombre TEXT, ape_pat TEXT, ape_mat TEXT, foto TEXT, created_at TEXT, updated_at TEXT);
            CREATE TABLE datos_laborales (id INTEGER PRIMARY KEY AUTOINCREMENT, personal_id INTEGER, employee_id TEXT, puesto_id INTEGER, estado_laboral_id INTEGER, created_at TEXT, updated_at TEXT);
            CREATE TABLE permisos_personal (id INTEGER PRIMARY KEY AUTOINCREMENT, personal_id INTEGER, registrado_por_user_id INTEGER, revisado_por_user_id INTEGER, resuelto_por_user_id INTEGER, fecha_permiso TEXT, horas_solicitadas REAL, motivo TEXT, estatus TEXT, goce_sueldo INTEGER, revisado_at TEXT, comentario_revision TEXT, resuelto_at TEXT, comentario_resolucion TEXT, created_at TEXT, updated_at TEXT);
            SQL);
    }

    public function test_rh_separa_propios_por_beneficiario_y_no_por_quien_registro(): void
    {
        $rh = $this->cuenta('rh', 'Cuenta RH');
        $otroRh = $this->cuenta('aux_rh', 'Otra RH');
        $empleado = $this->cuenta('operaciones', 'Colaborador');
        $this->permiso($rh, $otroRh, 'PERMISO-PROPIO');
        $this->permiso($empleado, $rh, 'PERMISO-DEL-COLABORADOR');
        $this->permiso($otroRh, $otroRh, 'PERMISO-OTRA-RH');

        $response = $this->ajax($rh, ['alcance' => 'mios']);
        $response->assertOk()->assertJsonPath('total', 1);
        $this->assertStringContainsString('PERMISO-PROPIO', $response->json('html'));
        $this->assertStringNotContainsString('PERMISO-DEL-COLABORADOR', $response->json('html'));

        $response = $this->ajax($rh, ['alcance' => 'otros']);
        $response->assertOk()->assertJsonPath('total', 2);
        $this->assertStringNotContainsString('PERMISO-PROPIO', $response->json('html'));
        $this->assertStringContainsString('PERMISO-DEL-COLABORADOR', $response->json('html'));
        $this->assertStringContainsString('PERMISO-OTRA-RH', $response->json('html'));
        $this->ajax($rh, ['alcance' => 'todos'])->assertJsonPath('total', 3);
    }

    public function test_busca_nombre_apellidos_numero_laboral_motivo_y_nombre_de_cuenta(): void
    {
        $rh = $this->cuenta('rh', 'Cuenta RH');
        $empleado = $this->cuenta('operaciones', 'Cuenta administrativa');
        $empleado->personal->update(['nombre' => 'Maria Elena', 'ape_pat' => 'Perez', 'ape_mat' => 'Lopez']);
        $this->permiso($empleado, $rh, 'Tramite escolar');
        $this->permiso($rh, $rh, 'Consulta medica');

        foreach (['Lopez Maria', 'EMP-'.$empleado->personal_id, 'Tramite escolar', 'Cuenta administrativa'] as $buscar) {
            $response = $this->ajax($rh, ['buscar' => $buscar]);
            $response->assertOk()->assertJsonPath('total', 1);
            $this->assertStringContainsString('Tramite escolar', $response->json('html'));
        }
        $this->ajax($rh, ['buscar' => 'Sin coincidencias'])->assertOk()->assertJsonPath('total', 0);
    }

    public function test_paginacion_ajax_conserva_filtros_y_tamano_sin_incluir_modales(): void
    {
        $rh = $this->cuenta('rh', 'Cuenta RH');
        $empleado = $this->cuenta('operaciones', 'Colaborador');
        for ($i = 0; $i < 36; $i++) $this->permiso($empleado, $rh, 'Consulta '.$i);
        $this->permiso($rh, $rh, 'Consulta propia');

        $response = $this->ajax($rh, ['buscar' => 'Consulta', 'alcance' => 'otros', 'page' => 2, 'per_page' => 15]);
        $response->assertOk()->assertJsonPath('total', 36)->assertJsonPath('page', 2);
        $html = $response->json('html');
        $this->assertSame(15, preg_match_all('/<button\b[^>]*\bdata-permiso-open\b/', $html));
        foreach (['buscar=Consulta', 'alcance=otros', 'per_page=15', 'page=3', 'Mostrando 16–30 de 36'] as $text) {
            $this->assertStringContainsString($text, $html);
        }
        $this->assertStringNotContainsString('modal-info-permiso', $html);
        $this->assertStringNotContainsString('modal-crear-permiso', $html);

        foreach ([30, 50] as $size) {
            $response = $this->ajax($rh, ['alcance' => 'otros', 'per_page' => $size]);
            $response->assertOk()->assertJsonPath('total', 36);
            $this->assertSame(min($size, 36), preg_match_all('/<button\b[^>]*\bdata-permiso-open\b/', $response->json('html')));
        }
    }

    public function test_usuario_normal_no_amplia_acceso_manipulando_filtros_o_busqueda(): void
    {
        $empleado = $this->cuenta('operaciones', 'Cuenta propia');
        $otro = $this->cuenta('logistica', 'Otra cuenta');
        $this->permiso($empleado, $empleado, 'Motivo comun PROPIO');
        $this->permiso($otro, $otro, 'Motivo comun AJENO');

        foreach (['todos', 'otros', 'mios'] as $alcance) {
            $response = $this->ajax($empleado, ['buscar' => 'Motivo comun', 'alcance' => $alcance]);
            $response->assertOk()->assertJsonPath('total', 1);
            $this->assertStringContainsString('PROPIO', $response->json('html'));
            $this->assertStringNotContainsString('AJENO', $response->json('html'));
        }
        $this->ajax($empleado, ['buscar' => 'AJENO'])->assertJsonPath('total', 0);
        $this->actingAs($empleado)->withHeaders(['X-Requested-With' => ''])->get(route('permisos.index'))
            ->assertOk()->assertSee('data-permisos-filter-form', false)->assertDontSee('name="alcance"', false);
    }

    public function test_finanzas_conserva_solo_permisos_rh_y_direccion_ve_todos(): void
    {
        $rh = $this->cuenta('rh', 'RH');
        $auxRh = $this->cuenta('aux_rh', 'Aux RH');
        $empleado = $this->cuenta('operaciones', 'Colaborador');
        foreach ([$rh, $auxRh, $empleado] as $user) $this->permiso($user, $user, 'Motivo comun');

        $this->ajax($this->cuenta('finanzas', 'Finanzas'), ['buscar' => 'Motivo comun', 'alcance' => 'otros'])
            ->assertOk()->assertJsonPath('total', 2);
        $this->ajax($this->cuenta('direccion', 'Direccion'), ['buscar' => 'Motivo comun'])
            ->assertOk()->assertJsonPath('total', 3);
    }

    public function test_sin_expediente_no_falla_y_no_expone_permisos_ajenos(): void
    {
        $rh = $this->cuenta('rh', 'RH sin expediente', false);
        $empleado = $this->cuenta('operaciones', 'Colaborador');
        $this->permiso($empleado, $rh, 'Permiso del colaborador');
        $this->ajax($rh, ['alcance' => 'mios'])->assertOk()->assertJsonPath('total', 0);
        $this->ajax($rh, ['alcance' => 'otros'])->assertOk()->assertJsonPath('total', 1);
        $this->ajax($this->cuenta('operaciones', 'Sin vinculo', false), ['buscar' => 'Permiso'])
            ->assertOk()->assertJsonPath('total', 0);
    }

    public function test_validacion_limita_tamano_de_pagina_y_tipo_de_busqueda(): void
    {
        $rh = $this->cuenta('rh', 'RH');
        $this->ajax($rh, ['per_page' => 10000])->assertUnprocessable()->assertJsonValidationErrors('per_page');
        $this->ajax($rh, ['buscar' => ['valor']])->assertUnprocessable()->assertJsonValidationErrors('buscar');
        $this->ajax($rh, ['alcance' => 'invalido'])->assertUnprocessable()->assertJsonValidationErrors('alcance');
        $this->ajax($rh, ['page' => 0])->assertUnprocessable()->assertJsonValidationErrors('page');
    }

    public function test_carga_inicial_incluye_filtros_y_modales_fuera_del_listado(): void
    {
        $this->actingAs($this->cuenta('rh', 'RH'))->get(route('permisos.index'))->assertOk()
            ->assertSee('data-permisos-filter-form', false)->assertSee('name="alcance"', false)
            ->assertSee('data-permisos-results', false)->assertSee('modal-info-permiso', false)
            ->assertSee('modal-crear-permiso', false)->assertSee('js/permisos-filtros.js', false);
    }

    private function ajax(User $usuario, array $filtros): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($usuario)->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->getJson(route('permisos.index', $filtros));
    }

    private function cuenta(string $rol, string $nombre, bool $vinculado = true): User
    {
        $personal = $vinculado ? Personal::create(['nombre' => $nombre, 'ape_pat' => 'Perez', 'ape_mat' => 'Lopez']) : null;
        if ($personal) DatosLaborales::create(['personal_id' => $personal->id, 'employee_id' => 'EMP-'.$personal->id]);

        return User::create(['name' => $nombre, 'email' => uniqid('cuenta', true).'@example.test', 'password' => 'password123', 'role' => $rol, 'personal_id' => $personal?->id]);
    }

    private function permiso(User $beneficiario, User $registradoPor, string $motivo): PermisoPersonal
    {
        return PermisoPersonal::create(['personal_id' => $beneficiario->personal_id, 'registrado_por_user_id' => $registradoPor->id,
            'fecha_permiso' => '2026-11-12', 'horas_solicitadas' => 3, 'motivo' => $motivo, 'estatus' => 'pendiente']);
    }
}
