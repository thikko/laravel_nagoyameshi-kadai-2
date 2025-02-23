<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション（レビュー一覧ページ）
    // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
    public function test_guest_cannot_access_index()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_no_plan_user_can_access_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertStatus(200);
    }
    // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
    public function test_plan_user_can_access_index()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
    public function test_admin_cannot_access_index()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('restaurants.reviews.index', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    // createアクション（レビュー投稿ページ）
    // 未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
    public function test_guest_cannot_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
    public function test_no_plan_user_cannot_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
    public function test_plan_user_can_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
    public function test_admin_cannot_access_create()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('restaurants.reviews.create', $restaurant));
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（レビュー投稿機能）
    // 未ログインのユーザーはレビューを投稿できない
    public function test_guest_cannot_store()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->post(route('restaurants.reviews.store', $restaurant), [
            'score' => 5,
            'content' => 'テストのビューコメント'
        ]);
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員はレビューを投稿できない
    public function test_no_plan_user_cannot_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), [
            'score' => 5,
            'content' => 'テストのビューコメント'
        ]);
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員はレビューを投稿できる
    public function test_plan_user_can_store()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant), [
            'score' => 5,
            'content' => 'テストのビューコメント'
        ]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }
    // ログイン済みの管理者はレビューを投稿できない
    public function test_admin_cannot_store()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->post(route('restaurants.reviews.store', $restaurant), [
            'score' => 5,
            'content' => 'テストのビューコメント'
        ]);
        $response->assertRedirect(route('admin.home'));
    }

    // editアクション（レビュー編集ページ）
    // 未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    public function test_guest_cannot_access_edit()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員はレビュー編集ページにアクセスできない
    public function test_no_plan_user_cannot_access_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
    public function test_plan_other_user_cannot_access_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }
    // ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
    public function test_plan_user_can_access_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
    public function test_admin_cannot_access_edit()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($adminUser, 'admin')->get(route('restaurants.reviews.edit', [$restaurant, $review]));
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（レビュー更新機能）
    // 未ログインのユーザーはレビューを更新できない
    public function test_guest_cannot_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->put(route('restaurants.reviews.update', [$restaurant, $review]), [
            'score' => 5,
            'content' => 'テストのレビューコメント'
        ]);
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員はレビューを更新できない
    public function test_no_plan_user_cannot_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]), [
            'score' => 5,
            'content' => 'テストのレビューコメント'
        ]);
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は他人のレビューを更新できない
    public function test_plan_other_user_cannot_update()
    {
        $restaurant = Restaurant::factory()->create();
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);
        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]), [
            'score' => 5,
            'content' => 'テストのレビューコメント'
        ]);
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }
    // ログイン済みの有料会員は自身のレビューを更新できる
    public function test_plan_user_can_update()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $this->actingAs($user);

        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        // 自身のレビューを更新できる
        $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]), [
            'score' => 5,
            'content' => 'テストのレビューコメント'
        ]);
        $response->assertStatus(302);
    }
    // ログイン済みの管理者はレビューを更新できない
    public function test_admin_cannot_update()
    {
        $restaurant = Restaurant::factory()->create();
        $adminUser = User::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->actingAs($adminUser, 'admin')->put(route('restaurants.reviews.update', [$restaurant, $review]), [
            'score' => 5,
            'content' => 'テストのレビューコメント'
        ]);
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（レビュー削除機能）
    // 未ログインのユーザーはレビューを削除できない
    public function test_guest_cannot_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);
        $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員はレビューを削除できない
    public function test_no_plan_user_cannot_destroy()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は他人のレビューを削除できない
    public function test_plan_other_user_cannot_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }
    // ログイン済みの有料会員は自身のレビューを削除できる
    public function test_plan_user_can_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('restaurants.reviews.index', $restaurant->id));
    }
    // ログイン済みの管理者はレビューを削除できない
    public function test_admin_cannot_destroy()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $review = Review::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($adminUser, 'admin')->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
        $response->assertRedirect(route('admin.home'));
    }
}
