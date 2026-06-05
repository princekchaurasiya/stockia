<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAdminCrudAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_crud_on_announcements_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'account_id' => null]);

        $this->actingAs($admin)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Admin Management')
            ->assertSee('Create announcement')
            ->assertSee('Student View');
    }

    public function test_admin_sees_crud_on_portal_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'account_id' => null]);

        foreach (['live_classes.index', 'calendar.index', 'charts.index', 'research.index'] as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk()
                ->assertSee('Admin Management');
        }
    }

    public function test_student_does_not_see_admin_crud_on_announcements_page(): void
    {
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($student)
            ->get(route('announcements.index'))
            ->assertOk()
            ->assertDontSee('Create announcement')
            ->assertDontSee('Admin Management')
            ->assertDontSee('Student View');
    }
}
