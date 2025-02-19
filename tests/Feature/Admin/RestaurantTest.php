<?php

namespace Tests\Feature\Admin;

use App\Models\Restaurant;
use App\Models\User;
use App\Models\Category;
use App\Models\RegularHoliday;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_index(): void
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('admin/login');
    }
    public function test_user_cannot_access_admin_index():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_index():void
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_show():void
    {
        $response = $this->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_show():void
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.show', $restaurant));
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_show():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($adminUser,'admin')->get(route('admin.restaurants.show', $restaurant));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_create():void
    {
        $response = $this->get('/admin/restaurants/create');
        $response->assertRedirect('/admin/login');
    }
    public function test_user_cannot_access_admin_create():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/create');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_create():void
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get('/admin/restaurants/create');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_create_store():void
    {
        $response = $this->get(route('admin.restaurants.store'));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_create_store():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.store'));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_create_store():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();

        $regular_holidays = RegularHoliday::factory()->create();
        $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();

        $restaurantData = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
            'category_ids' => $category_ids,
            'regular_holiday_ids' => $regular_holiday_ids
        ];

        $response = $this->actingAs($adminUser, 'admin')->post(route('admin.restaurants.store'), $restaurantData);
        unset($restaurantData['category_ids']);
        unset($restaurantData['regular_holiday_ids']);
        $this->assertDatabaseHas('restaurants', $restaurantData);

        $restaurant = Restaurant::latest('id')->first();

        foreach ($category_ids as $category_id) {
            $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
        }
        foreach ($regular_holiday_ids as $regular_holiday_id) {
            $this->assertDatabaseHas('regular_holiday_restaurant', ['restaurant_id' => $restaurant->id, 'regular_holiday_id' => $regular_holiday_id]);
        }

        $response->assertRedirect(route('admin.restaurants.index'));
    }

    public function test_guest_cannot_access_admin_edit():void
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_access_admin_edit():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.restaurants.edit', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_access_admin_edit():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.restaurants.edit', $restaurant));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_update():void
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->patch(route('admin.restaurants.update', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_update():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_update():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $old_restaurant = Restaurant::factory()->create();
        
        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();

        $regular_holidays = RegularHoliday::factory()->create();
        $regular_holiday_ids = $regular_holidays->pluck('id')->toArray();

        $new_restaurant = [
            'name' => 'テスト更新',
            'description' => 'テスト更新',
            'lowest_price' => 5000,
            'highest_price' => 10000,
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'opening_time' => '13:00:00',
            'closing_time' => '23:00:00',
            'seating_capacity' => 100,
            'category_ids' => $category_ids,
            'regular_holiday_ids' => $regular_holiday_ids
        ];

        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);
        unset($new_restaurant['category_ids']);
        unset($new_restaurant['regular_holiday_ids']);
        $this->assertDatabaseHas('restaurants', $new_restaurant);

        $restaurant = Restaurant::latest('id')->first();

        foreach ($category_ids as $category_id) {
            $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
        }
        foreach ($regular_holiday_ids as $regular_holiday_id) {
            $this->assertDatabaseHas('regular_holiday_restaurant', ['restaurant_id' => $restaurant->id, 'regular_holiday_id' => $regular_holiday_id]);
        }

        $response->assertRedirect(route('admin.restaurants.show', $old_restaurant));
    }

    public function test_guest_cannot_destroy():void
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->delete(route('admin.restaurants.destroy', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_destroy():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_destroy():void
    {
        $adminUser = User::factory()->create(['email' => 'admin@example.com']);
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
        $response->assertRedirect(route('admin.restaurants.index'));
    }
}
