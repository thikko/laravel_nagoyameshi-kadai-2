<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class HomeTest extends TestCase
{
    use RefreshDatabase;
  
    // 未ログインのユーザーは管理者側のトップページにアクセスできない
    public function test_guest_cannot_access_index()
    {
        $response = $this->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }
    // ログイン済みの一般ユーザーは管理者側のトップページにアクセスできない
    public function test_user_cannot_access_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }
    // ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_admin_can_access_index()
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.home'));
        $response->assertStatus(200);
    }
}