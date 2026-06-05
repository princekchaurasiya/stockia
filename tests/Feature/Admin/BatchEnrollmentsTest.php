<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Learning\EnrollmentFormModal;
use App\Livewire\Admin\Learning\EnrollmentTable;
use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\Lecture;
use App\Models\LectureVideo;
use App\Models\Module;
use App\Models\User;
use App\Models\UserNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BatchEnrollmentsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'account_id' => null]);
    }

    public function test_admin_can_access_students_page(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.learning.enrollments.index'))
            ->assertOk()
            ->assertSee('Students');
    }

    public function test_student_cannot_access_students_page(): void
    {
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($student)
            ->get(route('admin.learning.enrollments.index'))
            ->assertForbidden();
    }

    public function test_admin_can_enroll_student_in_batch(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'user']);
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(EnrollmentFormModal::class)
            ->call('open')
            ->set('batch_id', $batch->id)
            ->set('user_id', $student->id)
            ->set('is_active', true)
            ->call('save')
            ->assertDispatched('enrollmentTableRefresh');

        $this->assertDatabaseHas('batch_enrollments', [
            'batch_id' => $batch->id,
            'user_id' => $student->id,
            'is_active' => true,
        ]);
    }

    public function test_enrollment_table_shows_student_batch_and_status(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'user', 'name' => 'Prince Student']);
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);

        BatchEnrollment::create([
            'batch_id' => $batch->id,
            'user_id' => $student->id,
            'is_active' => false,
            'enrolled_at' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(EnrollmentTable::class)
            ->assertSee('Prince Student')
            ->assertSee('Feb Batch')
            ->assertSee('Inactive');

        $this->actingAs($admin)
            ->get(route('admin.learning.enrollments.index', ['batch' => $batch->id]))
            ->assertOk()
            ->assertSee('Prince Student');
    }

    public function test_batch_table_shows_enrolled_student_count(): void
    {
        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'user']);
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);

        BatchEnrollment::create([
            'batch_id' => $batch->id,
            'user_id' => $student->id,
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Learning\BatchTable::class)
            ->assertSee('1 enrolled');
    }

    public function test_batch_show_page_displays_nested_linked_resources(): void
    {
        Cache::flush();

        Http::fake([
            'www.youtube.com/oembed*' => Http::response(['title' => 'Sample YouTube Video'], 200),
        ]);

        $admin = $this->admin();
        $student = User::factory()->create(['role' => 'user', 'name' => 'Batch Student']);
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);
        $module = Module::create(['name' => 'Lecture 5', 'is_active' => true]);
        $lecture = Lecture::create([
            'batch_id' => $batch->id,
            'module_id' => $module->id,
            'title' => 'chart analysis combination',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        BatchEnrollment::create([
            'batch_id' => $batch->id,
            'user_id' => $student->id,
            'is_active' => true,
            'enrolled_at' => now(),
        ]);

        LectureVideo::create([
            'lecture_id' => $lecture->id,
            'label' => 'Part 1',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        UserNote::create([
            'user_id' => $admin->id,
            'lecture_id' => $lecture->id,
            'title' => 'maarna kam ghaseetna jada',
            'body' => 'Trend note',
            'is_shared' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.learning.batches.show', $batch))
            ->assertOk()
            ->assertSee('Batch overview')
            ->assertSee('Batch Student')
            ->assertSee('Lecture 5')
            ->assertSee('chart analysis combination')
            ->assertSee('Part 1')
            ->assertSee('maarna kam ghaseetna jada')
            ->assertSee('Sample YouTube Video');
    }

    public function test_cannot_enroll_admin_user_as_student(): void
    {
        $admin = $this->admin();
        $otherAdmin = User::factory()->create(['role' => 'admin', 'account_id' => null]);
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(EnrollmentFormModal::class)
            ->call('open')
            ->set('batch_id', $batch->id)
            ->set('user_id', $otherAdmin->id)
            ->set('is_active', true)
            ->call('save')
            ->assertHasErrors(['user_id']);
    }
}
