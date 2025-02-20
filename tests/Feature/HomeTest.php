<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_user_toppage() {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }
    public function test_user_can_access_user_toppage() {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200);
    }
    public function test_admin_cannot_access_user_toppage() {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('home'));
        $response->assertRedirect(route('admin.home'));
    }
}
