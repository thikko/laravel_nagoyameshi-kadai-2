<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Term;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション
    public function test_guest_cannot_access_index(): void
    {
        $response = $this->get(route('admin.terms.index'));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_access_index() {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.terms.index'));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_access_index() {
        $adminUser = User::factory()->create();
        $term =Term::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.terms.index', $term));
        $response->assertStatus(200);
    }

    // editアクション
    public function test_guest_cannot_access_edit() {
        $term = Term::factory()->create();
        $response = $this->get(route('admin.terms.edit', $term));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_access_edit() {
        $user = User::factory()->create();
        $term = Term::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.terms.edit', $term));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_access_edit() {
        $adminUser = User::factory()->create();
        $term = Term::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.terms.edit', $term));
        $response->assertStatus(200);
    }

    // updateアクション
    public function test_guest_cannot_update() {
        $term = Term::factory()->create();
        $response = $this->patch(route('admin.terms.update', $term));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_update() {
        $user = User::factory()->create();
        $term = Term::factory()->create();
        $response = $this->actingAs($user)->patch(route('admin.terms.update', $term));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_update() {
        $adminUser = User::factory()->create();
        $term_data = Term::factory()->create();
        $new_term_data = [
            'content' => 'テスト更新'
        ];
        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.terms.update', $term_data), $new_term_data);
        $this->assertDatabaseHas('terms', $new_term_data);
        $response->assertRedirect(route('admin.terms.index'));
    }
}
