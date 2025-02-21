<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;



class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    // createアクション
    // 未ログインのユーザーは有料プラン登録ページにアクセスできない
    public function test_guest_cannot_access_subscription_create()
    {
        $response = $this->get(route('subscription.create'));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は有料プラン登録ページにアクセスできる
    public function test_no_plan_user_can_access_subscription_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertStatus(200);
    }
    // ログイン済みの有料会員は有料プラン登録ページにアクセスできない
    public function test_plan_user_cannot_access_subscription_create()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('subscription.create'));
        $response->assertRedirect(route('subscription.edit'));
    }
    // ログイン済みの管理者は有料プラン登録ページにアクセスできない
    public function test_admin_cannot_access_subscription_create()
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('subscription.create'));
        $response->assertRedirect(route('admin.home'));
    }

    // storeアクション（有料プラン登録機能）
//     storeアクションではpaymentMethodIdパラメータ（支払い方法のID）が必要になるので、以下のデータをpost()メソッドの第2引数に渡して送信してください。
// $request_parameter = [
//     'paymentMethodId' => 'pm_card_visa'
// ];

// また、有料プランに登録できたことを検証するにはassertTrue()メソッドを使い、引数に$user->subscribed('premium_plan')を指定すればOKです。

// // assertTrue()は引数がtrueであることを検証するメソッドなので、$user->subscribed('premium_plan')がtrueを返せばテストに成功します。
    // 未ログインのユーザーは有料プランに登録できない
    public function test_guest_cannot_store()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];

        $response = $this->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は有料プランに登録できる
    public function test_no_plan_user_can_store()
    {
        $user = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('home'));

        $user->refresh();
        $this->assertTrue($user->subscribed('premium_plan'));
    }
    // ログイン済みの有料会員は有料プランに登録できない
    public function test_plan_user_cannot_store()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user)->post(route('subscription.store'), $request_parameter);
        $response->assertRedirect(route('subscription.edit'));
    }
    // ログイン済みの管理者は有料プランに登録できない
    public function test_admin_cannot_store()
    {
        $adminUser = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($adminUser, 'admin')->post(route('subscription.store'));
        $response->assertRedirect(route('admin.home'));
    }
    // editアクション（お支払い方法編集ページ）
    // 未ログインのユーザーはお支払い方法編集ページにアクセスできない
    public function test_guest_cannot_access_subscription_edit()
    {
        $response = $this->get(route('subscription.edit'));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
    public function test_no_plan_user_can_access_subscription_edit()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
    public function test_plan_user_can_access_subscription_edit()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('subscription.edit'));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者はお支払い方法編集ページにアクセスできない
    public function test_admin_cannot_access_subscription_edit()
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('subscription.edit'));
        $response->assertRedirect(route('admin.home'));
    }

    // updateアクション（お支払い方法更新機能）
    // storeアクションと同様に、updateアクションでもpaymentMethodIdパラメータ（支払い方法のID）が必要になります。今回は更新なので、以下のように登録時とは異なるテスト用カード（Mastercard）を指定。
    // $request_parameter = [
    //     'paymentMethodId' => 'pm_card_mastercard'
    // ];
//     また、お支払い方法を更新できたことを検証するにはassertNotEquals()メソッドを使い、「元のデフォルトの支払い方法のID」と「更新後のデフォルトの支払い方法のID」を比較する方法が有効です。

// デフォルトの支払い方法のIDを取得するには、以下のようにdefaultPaymentMethod()メソッドを使って支払い情報を格納したオブジェクトを取得し、そのidプロパティにアクセスすればOKです。

// $default_payment_method_id = $user->defaultPaymentMethod()->id;
    // 未ログインのユーザーはお支払い方法を更新できない
    public function test_guest_cannot_update()
    {
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $response = $this->patch(route('subscription.update'), $request_parameter);

        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員はお支払い方法を更新できない
    public function test_no_plan_user_cannot_update()
    {
        $user = User::factory()->create();

        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];

        $response = $this->actingAs($user)->patch(route('subscription.update'), $request_parameter);

        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員はお支払い方法を更新できる
    public function test_plan_user_can_update()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');

        $original_payment_method_id = $user->defaultPaymentMethod()->id;

        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $response = $this->actingAs($user)->patch(route('subscription.update'), $request_parameter);

        $response->assertRedirect(route('home'));

        $user->refresh();
        $this->assertNotEquals($original_payment_method_id, $user->defaultPaymentMethod()->id);
    }
    // ログイン済みの管理者はお支払い方法を更新できない
    public function test_admin_cannot_update()
    {
        $adminUser = User::factory()->create();
        $request_parameter = [
            'paymentMethodId' => 'pm_card_mastercard'
        ];
        $response = $this->actingAs($adminUser, 'admin')->patch(route('subscription.update'), $request_parameter);
        $response->assertRedirect(route('admin.home'));
    }
    // cancelアクション（有料プラン解約ページ）
    // 未ログインのユーザーは有料プラン解約ページにアクセスできない
    public function test_guest_cannot_access_subscription_cancel()
    {
        $response = $this->get(route('subscription.cancel'));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は有料プラン解約ページにアクセスできない
    public function test_no_plan_user_cannot_access_subscription_cancel()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は有料プラン解約ページにアクセスできる
    public function test_plan_user_can_access_subscription_cancel()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');
        $response = $this->actingAs($user)->get(route('subscription.cancel'));
        $response->assertStatus(200);
    }
    // ログイン済みの管理者は有料プラン解約ページにアクセスできない
    public function test_admin_cannot_access_subscription_cancel()
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->get(route('subscription.cancel'));
        $response->assertRedirect(route('admin.home'));
    }

    // destroyアクション（有料プラン解約機能）
//     有料プランを解約できたことを検証するにはassertFalse()メソッドを使い、引数に$user->subscribed('premium_plan')を指定すればOKです。
// assertFalse()は引数がfalseであることを検証するメソッドなので、$user->subscribed('premium_plan')がfalseを返せばテストに成功します。
    // 未ログインのユーザーは有料プランを解約できない
    public function test_guest_cannot_destroy()
    {
        $response = $this->delete(route('subscription.destroy'));
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの無料会員は有料プランを解約できない
    public function test_no_plan_user_cannot_destroy()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete(route('subscription.destroy'));
        $response->assertRedirect(route('subscription.create'));
    }
    // ログイン済みの有料会員は有料プランを解約できる
    public function test_plan_user_can_destroy()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QtCOmFQM2HB5al7kSxoKiFL')->create('pm_card_visa');

        $response = $this->actingAs($user)->delete(route('subscription.destroy'));
        $response->assertRedirect(route('home'));

        $user->refresh();
        $this->assertFalse($user->subscribed('premium_plan'));
    }
    // ログイン済みの管理者は有料プランを解約できない
    public function test_admin_cannot_destroy()
    {
        $adminUser = User::factory()->create();
        $response = $this->actingAs($adminUser, 'admin')->delete(route('subscription.destroy'));
        $response->assertRedirect(route('admin.home'));
    }
}
