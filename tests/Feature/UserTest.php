<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション
    public function test_guest_cannot_access_user_index() {
        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('login'));
    }
    public function test_user_can_access_user_index() {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('user.index'));
        $response->assertStatus(200);
    }
    public function test_admin_cannot_access_user_index() {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('user.index'));
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション
    public function test_guest_cannot_access_user_edit() {
        $response = $this->get(route('user.edit', ['user' => 1]));
        $response->assertRedirect(route('login'));
    }
    public function test_other_user_cannot_access_user_edit() {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        $response = $this->actingAs($other_user)->get(route('user.edit', ['user' => $user->id]));
        $response = $this->actingAs($user)->get(route('user.edit', $other_user));
        $response->assertRedirect(route('user.index'));
    }
    public function test_user_can_access_user_edit() {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('user.edit', ['user' => $user->id]));
        $response->assertStatus(200);
    }
    public function test_admin_cannot_access_user_edit() {
        $adminUser = User::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('user.edit', $user));
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション
    public function test_guest_cannot_update() {
        $user_data = User::factory()->create();
        $new_user_data = [
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'test.update@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'phone_number' => '0123456789',
            'birthday' => '220150319',
            'occupation' => 'テスト更新'
        ];
        $response = $this->patch(route('user.update', $user_data), $new_user_data);
        $this->assertDatabaseMissing('users', $new_user_data);
        $response->assertRedirect(route('login'));
    }
    public function test_other_user_cannot_update() {
        $user = User::factory()->create();
        $old_other_user = User::factory()->create();

        $new_other_user_data = [
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'test.update@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト更新'
        ];

        $response = $this->actingAs($user)->patch(route('user.update', $old_other_user), $new_other_user_data);

        $this->assertDatabaseMissing('users', $new_other_user_data);
        $response->assertRedirect(route('user.index'));
    }
    public function test_user_can_update() {
        $old_user = User::factory()->create();
        $new_user_data = [
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'test.update@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト更新'
        ];
        $response = $this->actingAs($old_user)->patch(route('user.update', $old_user), $new_user_data);
        $this->assertDatabaseHas('users', $new_user_data);
        $response->assertRedirect(route('user.index'));
    }
    public function test_admin_cannot_update() {
        $adminUser = User::factory()->create();

        $user_data = User::factory()->create();
        $new_user_data = [
            'name' => 'テスト更新',
            'kana' => 'テストコウシン',
            'email' => 'test.update@example.com',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'phone_number' => '0123456789',
            'birthday' => '20150319',
            'occupation' => 'テスト更新'
        ];
        $response = $this->actingAs($adminUser, 'admin')->patch(route('user.update', $user_data), $new_user_data);
        $this->assertDatabaseMissing('users', $new_user_data);
        $response->assertRedirect(route('admin.home'));
    }


}
