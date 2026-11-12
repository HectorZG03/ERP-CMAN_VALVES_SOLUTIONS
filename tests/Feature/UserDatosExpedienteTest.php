<?php

namespace Tests\Feature;

use App\Models\DatosLaborales;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserDatosExpedienteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Base aislada: estas pruebas no ejecutan migraciones históricas ni
        // se conectan a la base local o de producción.
        config([
            'database.default' => 'usuarios_prueba',
            'database.connections.usuarios_prueba' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
            'session.driver' => 'array',
            'cache.default' => 'array',
            'hashing.bcrypt.rounds' => 4,
            'app.key' => 'base64:'.base64_encode(str_repeat('t', 32)),
        ]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->unsignedBigInteger('personal_id')->nullable()->unique();
            $table->string('signature')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('personal', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ape_pat')->nullable();
            $table->string('ape_mat')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
        Schema::create('datos_laborales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personal_id')->unique();
            $table->unsignedBigInteger('puesto_id')->nullable();
            $table->unsignedBigInteger('estado_laboral_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->timestamps();
        });
        foreach (['solicitud_materiales', 'requisiciones'] as $tabla) {
            Schema::create($tabla, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
            });
        }
    }

    public function test_crea_cuenta_sin_numero_ni_foto_independientes_y_abre_su_detalle(): void
    {
        $actor = $this->cuenta('ti');
        $personal = $this->expediente();

        $response = $this->actingAs($actor)->post(route('users.store'), [
            'name' => 'Cuenta nueva',
            'email' => 'cuenta.nueva@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'operaciones',
            // Estos campos no deben poder establecerse desde este formulario.
            'num_empleado' => 'NUMERO-INYECTADO',
            'profile_photo' => 'foto-inyectada.jpg',
            'personal_id' => $personal->id,
        ]);

        $user = User::where('email', 'cuenta.nueva@example.test')->firstOrFail();
        $response->assertSessionHasNoErrors()->assertRedirect(route('users.show', $user));
        $this->assertSame('Cuenta nueva', $user->name);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertArrayNotHasKey('num_empleado', $user->getAttributes());
        $this->assertArrayNotHasKey('profile_photo', $user->getAttributes());
        $this->assertNull($user->personal_id);
    }

    public function test_actualiza_cuenta_sin_sobrescribir_expediente_vinculo_o_archivos_antiguos(): void
    {
        Storage::fake('public');
        $actor = $this->cuenta('ti');
        $personal = $this->expediente();
        $user = $this->cuenta('operaciones', $personal);
        Storage::disk('public')->put('profile_photos/antigua.jpg', 'archivo conservado');

        $this->actingAs($actor)->patch(route('users.update', $user), [
            'name' => 'Nombre actualizado por TI',
            'num_empleado' => 'NUMERO-INYECTADO',
            'profile_photo' => UploadedFile::fake()->create('reemplazo.png', 1, 'image/png'),
            'personal_id' => null,
        ])->assertSessionHasNoErrors()->assertRedirect(route('users.show', $user));

        $user->refresh();
        $this->assertSame('Nombre actualizado por TI', $user->name);
        $this->assertArrayNotHasKey('num_empleado', $user->getAttributes());
        $this->assertArrayNotHasKey('profile_photo', $user->getAttributes());
        $this->assertEquals($personal->id, $user->personal_id);
        $this->assertSame('CMAN-ADM-030', $user->numero_empleado_laboral);
        $this->assertSame('personal/fotos/expediente.jpg', $personal->fresh()->foto);
        Storage::disk('public')->assertExists('profile_photos/antigua.jpg');
        $this->assertCount(1, Storage::disk('public')->allFiles('profile_photos'));
    }

    public function test_listado_y_detalle_muestran_datos_del_expediente_y_conservan_nombre_de_cuenta(): void
    {
        $actor = $this->cuenta('ti');
        $user = $this->cuenta('operaciones', $this->expediente());

        foreach (['users.index', 'users.show'] as $route) {
            $url = $route === 'users.show' ? route($route, $user) : route($route);
            $response = $this->actingAs($actor)->get($url);
            $response->assertOk()
                ->assertSee($user->name)
                ->assertSee('CMAN-ADM-030')
                ->assertSee(asset('storage/personal/fotos/expediente.jpg'), false)
                ->assertDontSee('NUMERO-ANTIGUO')
                ->assertDontSee('profile_photos/antigua.jpg')
                ->assertDontSee('name="num_empleado"', false)
                ->assertDontSee('name="profile_photo"', false);
        }
    }

    public function test_formulario_crear_pide_datos_de_cuenta_y_firma_sin_campos_duplicados(): void
    {
        $this->actingAs($this->cuenta('ti'))->get(route('users.create'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="signature"', false)
            ->assertDontSee('name="num_empleado"', false)
            ->assertDontSee('name="profile_photo"', false);
    }

    public function test_quitar_y_reasignar_vinculo_actualiza_datos_mostrados_sin_cambiar_nombre(): void
    {
        $actor = $this->cuenta('ti');
        $personal = $this->expediente();
        $user = $this->cuenta('operaciones', $personal);
        $nombreCuenta = $user->name;

        $this->actingAs($actor)->patch(route('users.personal.update', $user), ['personal_id' => null])
            ->assertSessionHasNoErrors()->assertRedirect(route('users.show', $user));
        $this->get(route('users.show', $user))->assertOk()
            ->assertSee('Sin expediente vinculado')
            ->assertSee(asset('images/default-avatar.svg'), false)
            ->assertDontSee('storage/personal/fotos/expediente.jpg');
        $this->assertNull($user->fresh()->numero_empleado_laboral);

        $this->patch(route('users.personal.update', $user), ['personal_id' => $personal->id])
            ->assertSessionHasNoErrors()->assertRedirect(route('users.show', $user));
        $this->assertSame('CMAN-ADM-030', $user->fresh()->numero_empleado_laboral);
        $this->assertSame($nombreCuenta, $user->fresh()->name);
    }

    public function test_direccion_no_puede_cambiar_vinculaciones(): void
    {
        $user = $this->cuenta('operaciones');
        $personal = $this->expediente();
        $this->actingAs($this->cuenta('direccion'))->patch(route('users.personal.update', $user), [
            'personal_id' => $personal->id,
        ])->assertForbidden();
        $this->assertNull($user->fresh()->personal_id);
    }

    private function cuenta(string $role, ?Personal $personal = null): User
    {
        return User::create([
            'name' => 'Nombre de cuenta ERP',
            'email' => uniqid('cuenta', true).'@example.test',
            'password' => 'password123',
            'role' => $role,
            'personal_id' => $personal?->id,
        ]);
    }

    private function expediente(): Personal
    {
        $personal = Personal::create(['nombre' => 'Nombre distinto del expediente', 'foto' => 'personal/fotos/expediente.jpg']);
        DatosLaborales::create(['personal_id' => $personal->id, 'employee_id' => 'CMAN-ADM-030']);

        return $personal;
    }
}
