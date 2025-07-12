<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'employee_id' => 'EMP0001',
            'department' => 'IT',
            'position' => 'Administrator',
        ]);

        User::create([
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_id' => 'EMP0002',
            'department' => 'HR',
            'position' => 'Staff',
        ]);
    }

    /** @test */
    public function admin_can_login_with_valid_credentials()
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => '/dtr'
                ]);

        $this->assertNotNull(session('admin_user_id'));
    }

    /** @test */
    public function admin_can_login_with_remember_me()
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => '/dtr'
                ]);

        $user = User::where('email', 'admin@test.com')->first();
        $this->assertNotNull($user->remember_token);
        $this->assertNotNull(session('admin_user_id'));
    }

    /** @test */
    public function employee_can_login_with_valid_credentials()
    {
        $response = $this->postJson('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => '/employee/dashboard'
                ]);

        $this->assertNotNull(session('employee_user_id'));
    }

    /** @test */
    public function employee_can_login_with_remember_me()
    {
        $response = $this->postJson('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => '/employee/dashboard'
                ]);

        $user = User::where('email', 'employee@test.com')->first();
        $this->assertNotNull($user->remember_token);
        $this->assertNotNull(session('employee_user_id'));
    }

    /** @test */
    public function logout_clears_session_and_remember_token()
    {
        // Login first
        $user = User::where('email', 'admin@test.com')->first();
        $user->setRememberToken('test-token');
        $user->save();
        
        session(['admin_user_id' => $user->id]);
        auth()->login($user, true);

        // Logout
        $response = $this->get('/logout');

        $response->assertRedirect('/login');
        $this->assertNull(session('admin_user_id'));
        
        $user->refresh();
        $this->assertNull($user->remember_token);
    }

    /** @test */
    public function invalid_credentials_are_rejected()
    {
        $response = $this->postJson('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials.'
                ]);

        $this->assertNull(session('admin_user_id'));
    }

    /** @test */
    public function admin_cannot_access_employee_routes()
    {
        $user = User::where('email', 'admin@test.com')->first();
        session(['admin_user_id' => $user->id]);

        $response = $this->get('/employee/dashboard');
        $response->assertRedirect('/employee/login');
    }

    /** @test */
    public function employee_cannot_access_admin_routes()
    {
        $user = User::where('email', 'employee@test.com')->first();
        session(['employee_user_id' => $user->id]);

        $response = $this->get('/dtr');
        $response->assertRedirect('/login');
    }
}
