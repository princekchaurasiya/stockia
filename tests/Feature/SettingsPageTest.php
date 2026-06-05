<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('settings.index'))
            ->assertRedirect(route('login'));
    }

    public function test_student_sees_account_and_learning_sections(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee(__('stockia.settings.profile'))
            ->assertSee(__('stockia.settings.my_notes'))
            ->assertDontSee(__('stockia.settings.learning_admin_title'));
    }

    public function test_admin_sees_learning_admin_and_administration_sections(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee(__('stockia.settings.learning_admin_title'))
            ->assertSee(__('stockia.settings.administration_title'))
            ->assertSee(__('stockia.data_source.title'));
    }
}
