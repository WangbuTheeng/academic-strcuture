<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SchoolAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test school login page is accessible
     */
    public function test_school_login_page_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('School Login');
    }

    /**
     * Test super admin login page is accessible
     */
    public function test_super_admin_login_page_accessible(): void
    {
        $response = $this->get('/super-admin/login');
        $response->assertStatus(200);
        $response->assertSee('Super Admin Login');
    }

    /**
     * Test school authentication works
     */
    public function test_school_authentication_works(): void
    {
        // Create a test school
        $school = School::create([
            'name' => 'Test School',
            'code' => 'TEST001',
            'password' => Hash::make('testpassword'),
            'email' => 'test@school.edu',
            'status' => 'active'
        ]);

        // Attempt to login
        $response = $this->post('/login', [
            'school_id' => 'TEST001',
            'password' => 'testpassword'
        ]);

        // Should redirect to dashboard
        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /**
     * Test invalid school credentials fail
     */
    public function test_invalid_school_credentials_fail(): void
    {
        $response = $this->post('/login', [
            'school_id' => 'INVALID',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    /**
     * Test super admin authentication works
     */
    public function test_super_admin_authentication_works(): void
    {
        // Create a super admin user
        $superAdmin = User::factory()->create([
            'email' => 'admin@system.com',
            'password' => Hash::make('adminpassword'),
            'school_id' => null
        ]);

        $superAdmin->assignRole('super-admin');

        // Attempt to login
        $response = $this->post('/super-admin/login', [
            'email' => 'admin@system.com',
            'password' => 'adminpassword'
        ]);

        // Should redirect to super admin dashboard
        $response->assertRedirect();
        $this->assertAuthenticated();
    }
}
