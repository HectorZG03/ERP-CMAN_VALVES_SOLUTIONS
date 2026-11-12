<?php

namespace Tests\Unit;

use App\Models\DatosLaborales;
use App\Models\Personal;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserExpedienteTest extends TestCase
{
    public function test_lee_numero_y_foto_del_expediente_sin_cambiar_el_nombre_de_la_cuenta(): void
    {
        $user = $this->cuenta();
        $personal = new Personal(['nombre' => 'Nombre del expediente', 'foto' => 'personal/fotos/foto.jpg']);
        $personal->setRelation('datosLaborales', new DatosLaborales(['employee_id' => 'CMAN-ADM-030']));
        $user->setRelation('personal', $personal);
        $atributosOriginales = $user->getAttributes();

        $this->assertSame('CMAN-ADM-030', $user->numero_empleado_laboral);
        $this->assertSame(asset('storage/personal/fotos/foto.jpg'), $user->profile_photo_url);
        $this->assertSame('Nombre de cuenta ERP', $user->name);
        $this->assertSame($atributosOriginales, $user->getAttributes());
    }

    public function test_cuenta_sin_expediente_no_reutiliza_datos_heredados(): void
    {
        $user = $this->cuenta();
        $user->setRelation('personal', null);

        $this->assertNull($user->numero_empleado_laboral);
        $this->assertSame(asset('images/default-avatar.svg'), $user->profile_photo_url);
        $this->assertFileExists(public_path('images/default-avatar.svg'));
    }

    public function test_expediente_sin_datos_laborales_conserva_su_foto(): void
    {
        $user = $this->cuenta();
        $personal = new Personal(['foto' => 'personal/fotos/foto.jpg']);
        $personal->setRelation('datosLaborales', null);
        $user->setRelation('personal', $personal);

        $this->assertNull($user->numero_empleado_laboral);
        $this->assertSame(asset('storage/personal/fotos/foto.jpg'), $user->profile_photo_url);
    }

    #[DataProvider('valoresAusentes')]
    public function test_datos_ausentes_no_muestran_el_numero_o_foto_antiguos(?string $valor): void
    {
        $user = $this->cuenta();
        $personal = new Personal(['foto' => $valor]);
        $personal->setRelation('datosLaborales', new DatosLaborales(['employee_id' => $valor]));
        $user->setRelation('personal', $personal);

        $this->assertNull($user->numero_empleado_laboral);
        $this->assertSame(asset('images/default-avatar.svg'), $user->profile_photo_url);
    }

    public static function valoresAusentes(): array
    {
        return [
            'nulo' => [null],
            'vacio' => [''],
            'espacios' => ['   '],
            'sin dato' => ['N/A'],
            'sin dato con espacios' => [' n/a '],
        ];
    }

    private function cuenta(): User
    {
        // Simula una cuenta hidratada de la estructura anterior para comprobar
        // que esos atributos ya no participan en la lectura del expediente.
        return (new User(['name' => 'Nombre de cuenta ERP']))->forceFill([
            'num_empleado' => 'NUMERO-ANTIGUO',
            'profile_photo' => 'profile_photos/foto-antigua.jpg',
        ]);
    }
}
