<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Usuario Prueba',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => 3 // Registrado
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('app_users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function a_user_can_login()
    {
        $user = \App\Models\AppUser::factory()->create([
            'password' => bcrypt($password = 'password123')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/user/dashboard'); // Ajusta según el rol
        $this->assertAuthenticatedAs($user);
    }
}
