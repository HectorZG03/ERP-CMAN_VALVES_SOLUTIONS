<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class RetirarCamposUsuarioTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'limpieza_usuarios_prueba',
            'database.connections.limpieza_usuarios_prueba' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
            ],
        ]);
        DB::unprepared(<<<'SQL'
            CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password TEXT, role TEXT, personal_id INTEGER, num_empleado TEXT, profile_photo TEXT, signature TEXT);
            CREATE TABLE personal (id INTEGER PRIMARY KEY, nombre TEXT, ape_pat TEXT, ape_mat TEXT, foto TEXT);
            CREATE TABLE datos_laborales (id INTEGER PRIMARY KEY, personal_id INTEGER, employee_id TEXT);
            SQL);
        DB::table('personal')->insert(['id' => 10, 'nombre' => 'Nombre del expediente', 'foto' => 'personal/fotos/actual.jpg']);
        DB::table('datos_laborales')->insert(['id' => 20, 'personal_id' => 10, 'employee_id' => 'CMAN-ADM-030']);
        DB::table('users')->insert([
            'id' => 1, 'name' => 'Nombre de cuenta ERP', 'email' => 'cuenta@example.test',
            'password' => 'hash-conservado', 'role' => 'ti', 'personal_id' => 10,
            'num_empleado' => 'CMAN-ADM-030', 'profile_photo' => 'profile_photos/anterior.jpg',
            'signature' => 'signatures/firma.png',
        ]);
    }

    public function test_retira_columnas_sin_modificar_cuenta_expediente_o_datos_laborales(): void
    {
        $cuentaAntes = (array) DB::table('users')->first();
        $personalAntes = (array) DB::table('personal')->first();
        $laboralesAntes = (array) DB::table('datos_laborales')->first();
        $this->migracion()->up();

        $this->assertFalse(Schema::hasColumn('users', 'num_empleado'));
        $this->assertFalse(Schema::hasColumn('users', 'profile_photo'));
        unset($cuentaAntes['num_empleado'], $cuentaAntes['profile_photo']);
        $this->assertSame($cuentaAntes, (array) DB::table('users')->first());
        $this->assertSame($personalAntes, (array) DB::table('personal')->first());
        $this->assertSame($laboralesAntes, (array) DB::table('datos_laborales')->first());

        $user = User::firstOrFail();
        $this->assertSame('Nombre de cuenta ERP', $user->name);
        $this->assertSame('CMAN-ADM-030', $user->numero_empleado_laboral);
        $this->assertSame(asset('storage/personal/fotos/actual.jpg'), $user->profile_photo_url);
        $this->assertFalse($user->isFillable('num_empleado'));
        $this->assertFalse($user->isFillable('profile_photo'));
    }

    public function test_admite_cuentas_nuevas_sin_vinculo_y_sin_datos_antiguos(): void
    {
        DB::table('users')->insert(['id' => 2, 'name' => 'Cuenta por vincular']);
        $this->migracion()->up();

        $this->assertSame(2, DB::table('users')->count());
        $user = User::findOrFail(2);
        $this->assertNull($user->personal_id);
        $this->assertNull($user->numero_empleado_laboral);
        $this->assertSame(asset('images/default-avatar.svg'), $user->profile_photo_url);
    }

    #[DataProvider('inconsistencias')]
    public function test_no_retira_columnas_si_falta_validar_datos(string $caso): void
    {
        match ($caso) {
            'sin_vinculo' => DB::table('users')->where('id', 1)->update(['personal_id' => null]),
            'vinculo_inexistente' => DB::table('users')->where('id', 1)->update(['personal_id' => 99]),
            'sin_datos_laborales' => DB::table('datos_laborales')->delete(),
            'sin_foto_en_expediente' => DB::table('personal')->update(['foto' => 'N/A']),
        };
        $cuentaAntes = (array) DB::table('users')->first();

        try {
            $this->migracion()->up();
            $this->fail('La migración debía detenerse antes de eliminar columnas.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('No se retiraron los campos antiguos', $e->getMessage());
            $this->assertStringContainsString('Usuario 1', $e->getMessage());
        }

        $this->assertTrue(Schema::hasColumns('users', ['num_empleado', 'profile_photo']));
        $this->assertSame($cuentaAntes, (array) DB::table('users')->first());
    }

    public function test_rollback_recupera_estructura_nullable_y_conserva_firma_y_nombre(): void
    {
        $migracion = $this->migracion();
        $migracion->up();
        $migracion->down();

        $this->assertTrue(Schema::hasColumns('users', ['num_empleado', 'profile_photo']));
        $cuenta = DB::table('users')->first();
        $this->assertNull($cuenta->num_empleado);
        $this->assertNull($cuenta->profile_photo);
        $this->assertSame('Nombre de cuenta ERP', $cuenta->name);
        $this->assertSame('signatures/firma.png', $cuenta->signature);
    }

    public function test_adopta_numero_del_expediente_aunque_el_numero_antiguo_sea_distinto(): void
    {
        DB::table('users')->where('id', 1)->update(['num_empleado' => 'NUMERO-ANTIGUO-DISTINTO']);
        $this->migracion()->up();

        $this->assertFalse(Schema::hasColumn('users', 'num_empleado'));
        $this->assertSame('CMAN-ADM-030', User::firstOrFail()->numero_empleado_laboral);
        $this->assertSame('Nombre de cuenta ERP', User::firstOrFail()->name);
        $this->assertSame('CMAN-ADM-030', DB::table('datos_laborales')->value('employee_id'));
    }

    public static function inconsistencias(): array
    {
        return [
            ['sin_vinculo'], ['vinculo_inexistente'],
            ['sin_datos_laborales'], ['sin_foto_en_expediente'],
        ];
    }

    private function migracion(): \Illuminate\Database\Migrations\Migration
    {
        return require database_path('migrations/2026_11_12_140000_remove_legacy_employee_number_and_photo_from_users_table.php');
    }
}
