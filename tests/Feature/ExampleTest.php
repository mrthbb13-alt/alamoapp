<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\IncidentReportSeeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba el flujo de login inicial y navegación protegida
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(IncidentReportSeeder::class);

        // 1. Visitante no autenticado es redirigido a login
        $response = $this->get('/');
        $response->assertRedirect(route('login'));

        // 2. Pantalla de login y registro disponibles
        $this->get(route('login'))->assertStatus(200);
        $this->get(route('register'))->assertStatus(200);

        // Registro falla si falta el teléfono (campo obligatorio)
        $invalidReg = $this->post(route('register.post'), [
            'name'                  => 'Mariana Valdez',
            'username'              => 'mariana',
            'phone'                 => '',
            'colonia'               => 'Zona Centro',
            'password'              => 'marianapass',
            'password_confirmation' => 'marianapass',
        ]);
        $invalidReg->assertSessionHasErrors('phone');

        // Registro falla si falta el documento INE (campo obligatorio)
        $missingIne = $this->post(route('register.post'), [
            'name'                  => 'Mariana Valdez',
            'username'              => 'mariana',
            'phone'                 => '7651122334',
            'colonia'               => 'Zona Centro',
            'password'              => 'Valdez@2026',
            'password_confirmation' => 'Valdez@2026',
        ]);
        $missingIne->assertSessionHasErrors('ine_document');

        // Registro falla si la contraseña no cumple requisitos (sin símbolo o mayúscula)
        $weakPwd = $this->post(route('register.post'), [
            'name'                  => 'Mariana Valdez',
            'username'              => 'mariana',
            'phone'                 => '7651122334',
            'colonia'               => 'Zona Centro',
            'password'              => 'simple123',
            'password_confirmation' => 'simple123',
            'ine_document'          => \Illuminate\Http\UploadedFile::fake()->create('ine.jpg', 120, 'image/jpeg'),
        ]);
        $weakPwd->assertSessionHasErrors('password');

        // Registro exitoso con contraseña robusta (8+ caracteres, mayúscula, símbolo) y documento INE
        $fakeIne = \Illuminate\Http\UploadedFile::fake()->create('ine_frontal.jpg', 150, 'image/jpeg');
        $regResponse = $this->post(route('register.post'), [
            'name'                  => 'Mariana Valdez',
            'username'              => 'mariana',
            'phone'                 => '7651122334',
            'colonia'               => 'Zona Centro',
            'password'              => 'Valdez@2026',
            'password_confirmation' => 'Valdez@2026',
            'ine_document'          => $fakeIne,
        ]);
        $regResponse->assertRedirect(route('citizen.home'));
        $this->assertDatabaseHas('users', ['username' => 'mariana']);

        // Cerrar sesión para probar login
        $this->post(route('logout'));

        // 3. Intento con contraseña incorrecta
        $failLogin = $this->post(route('login.post'), [
            'username' => 'user',
            'password' => 'clave_incorrecta',
        ]);
        $failLogin->assertSessionHasErrors('login_error');

        // 4. Iniciar sesión con user y 123 (local fija garantizada)
        $loginResponse = $this->post(route('login.post'), [
            'username' => 'user',
            'password' => '123',
        ]);
        $loginResponse->assertRedirect(route('citizen.home'));
        $user = User::where('username', 'user')->first();

        // 5. Visitar pantallas autenticadas
        $this->actingAs($user)->get(route('citizen.home'))->assertStatus(200);
        $this->actingAs($user)->get(route('citizen.feed'))->assertStatus(200);
        $this->actingAs($user)->get(route('citizen.track'))->assertStatus(200);
        $this->actingAs($user)->get(route('citizen.profile'))->assertStatus(200);
        $this->actingAs($user)->get(route('citizen.directory'))
            ->assertStatus(200)
            ->assertSee('Directorio Municipal')
            ->assertSee('Regidores')
            ->assertSee('Directores')
            ->assertSee('DIF Municipal');
        $this->get(route('municipal.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get('/reportar/api/folio/ALM-2026-0001')->assertStatus(200);
    }

    /**
     * Prueba el ciclo de creación de reporte ciudadano y perfil
     */
    public function test_citizen_can_create_report_and_interact(): void
    {
        $user = User::create([
            'name'     => 'Ciudadano Álamo',
            'username' => 'user',
            'email'    => 'user@alamo.gob.mx',
            'phone'    => '765 123 4567',
            'colonia'  => 'Zona Centro',
            'password' => Hash::make('123'),
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('bache.jpg', 100, 'image/jpeg');

        // 1. Guardar reporte estando autenticado
        $storeResponse = $this->actingAs($user)->post(route('citizen.store'), [
            'category'          => 'bache',
            'title'             => 'Bache enorme en calle Juárez',
            'description'       => 'Peligroso por la noche',
            'colonia'           => 'Zona Centro',
            'address_reference' => 'Cerca de correos',
            'latitude'          => 20.9120,
            'longitude'         => -97.6740,
            'citizen_name'      => 'Ciudadano Álamo',
            'citizen_phone'     => '7651234567',
            'evidence_initial'  => $file,
        ]);

        $storeResponse->assertStatus(200);
        $storeResponse->assertJson(['success' => true]);
        $reportData = $storeResponse->json('report');

        // Test reporte sin subir archivo (se guarda null sin imágenes mock)
        $noFileReport = $this->actingAs($user)->post(route('citizen.store'), [
            'category'          => 'accidente',
            'title'             => 'Accidente vial en crucero',
            'colonia'           => 'Zona Centro',
            'latitude'          => 20.9125,
            'longitude'         => -97.6745,
        ]);
        $noFileReport->assertStatus(200);
        $noFileReport->assertJson(['success' => true]);
        $this->assertNull($noFileReport->json('report.evidence_initial'));

        // 2. Dar +1 Me afecta
        $upvoteResponse = $this->actingAs($user)->post(route('citizen.upvote', ['id' => $reportData['id']]));
        $upvoteResponse->assertStatus(200);
        $upvoteResponse->assertJson(['success' => true, 'upvotes' => 2]);

        // 3. Consultar duplicados cercanos
        $nearbyResponse = $this->actingAs($user)->get(route('citizen.nearby', [
            'lat'      => 20.9120,
            'lng'      => -97.6740,
            'category' => 'bache',
        ]));
        $nearbyResponse->assertStatus(200);
        $nearbyResponse->assertJson(['found' => true]);

        // 4. Actualizar perfil
        $profileResponse = $this->actingAs($user)->post(route('citizen.profile.update'), [
            'name'    => 'Ciudadano Álamo Actualizado',
            'phone'   => '7659998888',
            'colonia' => 'Col. Pantepec',
        ]);
        $profileResponse->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'Ciudadano Álamo Actualizado',
            'phone' => '7659998888',
        ]);

        // 5. Cerrar sesión
        $logoutResponse = $this->actingAs($user)->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
