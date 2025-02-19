<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_index(): void
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_index():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertRedirect('admin/login');
    }

    public function test_admin_can_access_admin_index():void
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_show():void
    {
        $response = $this->get('/admin/users/1');
        $response->assertRedirect('admin/login');
    }

    public function test_user_cannot_access_admin_show():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.users.show', $user));
        $response->assertRedirect('admin/login');
    }

    public function test_admin_can_access_admin_show():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $user = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.users.show', $user));
        $response->assertStatus(200);
    }
}
