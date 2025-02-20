<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // indexアクション
    public function test_guest_cannot_access_index(): void
    {
        $response = $this->get(route('admin.company.index'));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_access_index() {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.company.index'));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_access_index() {
        $adminUser = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.company.index'));
        $response->assertStatus(200);
    }

    // editアクション
    public function test_guest_cannot_access_edit() {
        $company = Company::factory()->create();
        $response = $this->get(route('admin.company.edit', $company));
        $response->assertRedirect(route('admin.login'));
    } 
    public function test_user_cannot_access_edit() {
        $user = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.edit', $company));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_access_edit() {
        $adminUser = User::factory()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($adminUser, 'admin')->get(route('admin.company.edit', $company));
        $response->assertStatus(200);
    }

    // updateアクション
    public function test_guest_cannot_update() {
        $company = Company::factory()->create();
        $response = $this->patch(route('admin.company.update', $company));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_user_cannot_update() {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($user)->patch(route('admin.company.update', $company));
        $response->assertRedirect(route('admin.login'));
    }
    public function test_admin_can_update() {
        $adminUser = User::factory()->create();
        $company_data = Company::factory()->create();
        $new_company_data = [
            'name' => 'テスト更新',
            'postal_code' => '0000000',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新'
        ];
        $response = $this->actingAs($adminUser, 'admin')->patch(route('admin.company.update', $company_data), $new_company_data);
        $this->assertDatabaseHas('companies', $new_company_data);
        $response->assertRedirect(route('admin.company.index'));
    }
}
