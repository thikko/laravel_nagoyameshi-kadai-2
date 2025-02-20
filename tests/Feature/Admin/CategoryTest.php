<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_cannot_access_admin_category_index(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_category_index():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_access_admin_category_index():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.categories.index'));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_store_admin_category():void
    {
        $categoryData = Category::factory()->create()->toArray();
        $response = $this->post(route('admin.categories.store'), $categoryData);
        $this->assertDatabaseMissing('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_store_admin_category():void
    {
        $user = User::factory()->create();
        $categoryData = Category::factory()->create()->toArray();
        $response = $this->actingAs($user)->post(route('admin.categories.store'), $categoryData);
        $this->assertDatabaseMissing('categories', $categoryData);
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_store_admin_category():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $categoryData = Category::factory()->create()->toArray();
        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.categories.store'), $categoryData);
        $this->assertDatabaseHas('categories', ['name' => 'テスト']);
        $response->assertRedirect(route('admin.categories.index'));
    }

    public function test_guest_cannot_update_admin_category():void
    {
        $category = Category::factory()->create();
        $response = $this->patch(route('admin.categories.update', $category));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_update_admin_category():void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $response = $this->actingAs($user)->patch(route('admin.categories.update', $category));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_update_admin_category():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $old_category = Category::factory()->create();
        $new_category = ['name' => 'テスト更新'];
        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.categories.update', $old_category), $new_category);
        $this->assertDatabaseHas('categories', $new_category);
        $response->assertRedirect(route('admin.categories.index'));
    }

    public function test_guest_cannot_destroy_admin_category():void
    {
        $category = Category::factory()->create();
        $response = $this->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_destroy_admin_category():void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_destroy_admin_category():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $category = Category::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.categories.destroy', $category));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.categories.index'));
    }
}
