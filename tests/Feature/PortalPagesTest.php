<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_today_live_class_widget(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        LiveClass::create([
            'title' => 'Morning Session',
            'meeting_url' => 'https://zoom.us/j/123',
            'scheduled_at' => now()->setHour(10)->setMinute(0),
            'status' => 'scheduled',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Morning Session');
        $response->assertSee('Join');
    }

    public function test_dashboard_shows_pinned_announcement(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        Announcement::create([
            'title' => 'Market holiday notice',
            'body' => 'Markets closed tomorrow.',
            'type' => 'market_alert',
            'is_pinned' => true,
            'published_at' => now(),
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Market holiday notice');
        $response->assertSee('Pinned');
    }

    public function test_student_does_not_see_research_moderation_tools(): void
    {
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($student)
            ->get(route('research.index'))
            ->assertOk()
            ->assertDontSee('Admin Management')
            ->assertDontSee('Confirm reject');
    }
}
