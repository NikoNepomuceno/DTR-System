<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\FailedLoginAttempt;
use App\Models\AccountLockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeAuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test employee
        $this->employee = User::create([
            'name' => 'Test Employee',
            'email' => 'employee@test.com',
            'password' => Hash::make('password123'),
            'employee_id' => 'EMP001',
            'department' => 'IT',
            'position' => 'Developer',
            'role' => 'employee',
        ]);
    }

    /** @test */
    public function employee_can_login_with_valid_credentials()
    {
        $response = $this->post('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/employee/dashboard');
        $this->assertAuthenticatedAs($this->employee);
        
        // Check session variables
        $this->assertEquals($this->employee->id, session('user_id'));
        $this->assertEquals('employee', session('user_role'));
        $this->assertEquals($this->employee->id, session('employee_user_id'));
        
        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'login',
            'status' => 'success',
            'user_id' => $this->employee->id,
            'email' => $this->employee->email,
        ]);
    }

    /** @test */
    public function employee_cannot_login_with_invalid_credentials()
    {
        $response = $this->post('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        
        // Check failed login attempt recorded
        $this->assertDatabaseHas('failed_login_attempts', [
            'email' => 'employee@test.com',
            'type' => 'employee',
        ]);
        
        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'failed_login',
            'status' => 'failure',
            'email' => 'employee@test.com',
        ]);
    }

    /** @test */
    public function employee_login_is_rate_limited_after_multiple_failures()
    {
        // Make 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/employee/login', [
                'email' => 'employee@test.com',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should be blocked
        $response = $this->post('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        
        // Check account lockout
        $this->assertTrue(AccountLockout::isEmailLocked('employee@test.com'));
    }

    /** @test */
    public function employee_can_register_with_valid_data()
    {
        $response = $this->post('/employee/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@test.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'department' => 'HR',
            'position' => 'Manager',
        ]);

        $response->assertRedirect('/employee/dashboard');
        
        // Check user was created
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@test.com',
            'role' => 'employee',
        ]);
        
        // Check auto-login
        $user = User::where('email', 'john.doe@test.com')->first();
        $this->assertEquals($user->id, session('user_id'));
        
        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'registration',
            'status' => 'success',
            'email' => 'john.doe@test.com',
        ]);
    }

    /** @test */
    public function employee_registration_validates_input()
    {
        $response = $this->post('/employee/register', [
            'first_name' => '',
            'last_name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
            'department' => '',
            'position' => '',
        ]);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'password',
            'department',
            'position',
        ]);
    }

    /** @test */
    public function employee_can_logout()
    {
        $this->actingAs($this->employee);
        
        $response = $this->get('/logout');
        
        $response->assertRedirect('/employee/login');
        $this->assertGuest();
        
        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'logout',
            'status' => 'success',
            'user_id' => $this->employee->id,
        ]);
    }

    /** @test */
    public function employee_dashboard_requires_authentication()
    {
        $response = $this->get('/employee/dashboard');
        
        $response->assertRedirect('/employee/login');
    }

    /** @test */
    public function employee_cannot_access_admin_routes()
    {
        $this->actingAs($this->employee);
        
        $response = $this->get('/admin/dashboard');
        
        $response->assertRedirect('/employee/login');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function session_security_detects_user_agent_change()
    {
        $this->actingAs($this->employee);
        
        // Set initial user agent in session
        session(['_user_agent' => 'Mozilla/5.0 (Original Browser)']);
        
        // Make request with different user agent
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Different Browser)'
        ])->get('/employee/dashboard');
        
        $response->assertRedirect('/employee/login');
        $response->assertSessionHas('error');
    }

    /** @test */
    public function password_strength_requirements_are_enforced()
    {
        $weakPasswords = [
            'password',      // Too common
            '12345678',      // Only numbers
            'abcdefgh',      // Only lowercase
            'ABCDEFGH',      // Only uppercase
            'Pass123',       // Too short
        ];

        foreach ($weakPasswords as $password) {
            $response = $this->post('/employee/register', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => $this->faker->unique()->email,
                'password' => $password,
                'password_confirmation' => $password,
                'department' => 'IT',
                'position' => 'Developer',
            ]);

            $response->assertSessionHasErrors(['password']);
        }
    }

    /** @test */
    public function audit_logs_are_created_for_security_events()
    {
        // Test failed login
        $this->post('/employee/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'failed_login',
            'email' => 'nonexistent@test.com',
            'risk_level' => 'medium',
        ]);

        // Test successful login
        $this->post('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'login',
            'user_id' => $this->employee->id,
            'risk_level' => 'low',
        ]);
    }

    /** @test */
    public function failed_login_attempts_are_tracked()
    {
        $this->post('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertDatabaseHas('failed_login_attempts', [
            'email' => 'employee@test.com',
            'type' => 'employee',
        ]);

        $attempts = FailedLoginAttempt::getRecentAttemptsByEmail('employee@test.com');
        $this->assertEquals(1, $attempts);
    }

    /** @test */
    public function account_lockout_prevents_further_attempts()
    {
        AccountLockout::lockEmail('employee@test.com', 30, 5);

        $response = $this->post('/employee/login', [
            'email' => 'employee@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContains('Too many login attempts', session('errors')->first('email'));
    }
}
