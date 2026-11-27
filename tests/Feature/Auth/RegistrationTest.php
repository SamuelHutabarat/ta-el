<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_members_can_register_with_name_and_phone_number(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Member',
            'no_telp' => '0812-3456-7000',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('no_telp', '081234567000')->firstOrFail();
        $this->assertSame('pelanggan', $user->role);
        $this->assertNull($user->email);
    }
}
