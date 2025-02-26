<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Reservation;


class ReservationTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（予約一覧ページ）
    // 未ログインのユーザーは会員側の予約一覧ページにアクセスできない
    public function test_guest_cannot_access_index()
    {
        $response = $this->get(route('reservations.index'));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
    public function test_no_plan_user_cannot_access_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('reservations.index'));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
    public function test_plan_user_can_access_index()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('reservations.index'));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
    public function test_admin_cannot_access_index()
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('reservations.index'));
        $response->assertRedirect(route('admin.home'));
    }

    // createアクション（予約ページ）
    // 未ログインのユーザーは会員側の予約ページにアクセスできない
    public function test_guest_cannot_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は会員側の予約ページにアクセスできない
    public function test_no_plan_user_cannot_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は会員側の予約ページにアクセスできる
    public function test_plan_user_can_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('restaurants.reservations.create', $restaurant));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者は会員側の予約ページにアクセスできない
    public function test_admin_cannot_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('restaurants.reservations.create', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（予約機能）
    // 未ログインのユーザーは予約できない
    public function test_guest_cannot_store()
    {
        $restaurant = Restaurant::factory()->create();
        $reservationData = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];
        $response = $this->post(route('restaurants.reservations.store', $restaurant), $reservationData);
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は予約できない
    public function test_no_plan_user_cannot_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $reservationData = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];
        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservationData);
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は予約できる
    public function test_plan_user_can_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $reservationData = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];
        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservationData);
        $response->assertRedirect(route('reservations.index'));
    }
    // ログイン済みの管理者は予約できない
    public function test_admin_cannot_store()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $reservationData = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];
        $response = $this->actingAs($adminUser, 'admin')->post(route('restaurants.reservations.store', $restaurant), $reservationData);
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（予約キャンセル機能）
    // 未ログインのユーザーは予約をキャンセルできない
    public function test_guest_cannot_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は予約をキャンセルできない
    public function test_no_plan_user_cannot_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は他人の予約をキャンセルできない
    public function test_other_plan_user_cannot_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');

        $otherUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);
        $response = $this->actingAs($user)->delete(route('reservations.destroy',$reservation));
        $response->assertRedirect(route('reservations.index'));
    }
    // ログイン済みの有料会員は自身の予約をキャンセルできる
    public function test_plan_user_can_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('reservations.index'));
    }
    // ログイン済みの管理者は予約をキャンセルできない
    public function test_admin_cannot_destroy()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($adminUser, 'admin')->delete(route('reservations.destroy', $reservation));
        $response->assertRedirect(route('admin.home'));
    }
}
