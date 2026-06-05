<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Learning\BatchFormModal;
use App\Livewire\Admin\Learning\BatchTable;
use App\Livewire\Admin\Learning\LectureFormModal;
use App\Livewire\Admin\Learning\LectureTable;
use App\Livewire\Admin\Learning\ModuleTable;
use App\Models\Batch;
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

class LearningResourcesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'account_id' => null]);
    }

    private function seedLearningGraph(): array
    {
        Cache::flush();

        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Sample YouTube Video',
            ], 200),
        ]);

        $batch = Batch::create(['name' => 'Batch Alpha', 'is_active' => true]);
        $module = Module::create(['name' => 'Module One', 'is_active' => true]);
        $lecture = Lecture::create([
            'batch_id' => $batch->id,
            'module_id' => $module->id,
            'title' => 'Intro to Markets',
            'notes' => 'Overview lecture',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LectureVideo::create([
            'lecture_id' => $lecture->id,
            'label' => 'Part 1',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_type' => 'Main',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return compact('batch', 'module', 'lecture');
    }

    public function test_admin_can_access_learning_dashboard_and_resource_pages(): void
    {
        $admin = $this->admin();

        $routes = [
            'admin.learning.dashboard',
            'admin.learning.batches.index',
            'admin.learning.modules.index',
            'admin.learning.lectures.index',
            'admin.learning.videos.index',
            'admin.learning.documents.index',
            'admin.learning.enrollments.index',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_student_cannot_access_learning_admin_pages(): void
    {
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($student)
            ->get(route('admin.learning.dashboard'))
            ->assertForbidden();
    }

    public function test_legacy_trading_learning_url_redirects_to_dashboard(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/trading-learning')
            ->assertRedirect('/admin/learning');
    }

    public function test_batch_table_search_filters_results(): void
    {
        $this->actingAs($this->admin());
        Batch::create(['name' => 'Visible Batch', 'is_active' => true]);
        Batch::create(['name' => 'Hidden Cohort', 'is_active' => true]);

        Livewire::test(BatchTable::class)
            ->set('search', 'Visible')
            ->assertSee('Visible Batch')
            ->assertDontSee('Hidden Cohort');
    }

    public function test_batch_form_modal_creates_batch(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(BatchFormModal::class)
            ->call('open')
            ->set('name', 'New Batch')
            ->set('is_active', true)
            ->call('save')
            ->assertDispatched('batchTableRefresh');

        $this->assertDatabaseHas('batches', ['name' => 'New Batch', 'is_active' => 1]);
    }

    public function test_batch_table_bulk_activate_and_delete(): void
    {
        $this->actingAs($this->admin());
        $batchA = Batch::create(['name' => 'Bulk A', 'is_active' => false]);
        $batchB = Batch::create(['name' => 'Bulk B', 'is_active' => false]);

        Livewire::test(BatchTable::class)
            ->set('selectedIds', [$batchA->id, $batchB->id])
            ->call('bulkActivate');

        $this->assertTrue((bool) Batch::find($batchA->id)->is_active);
        $this->assertTrue((bool) Batch::find($batchB->id)->is_active);

        Livewire::test(BatchTable::class)
            ->set('selectedIds', [$batchA->id])
            ->call('bulkDelete');

        $this->assertDatabaseMissing('batches', ['id' => $batchA->id]);
        $this->assertDatabaseHas('batches', ['id' => $batchB->id]);
    }

    public function test_lecture_table_filters_by_batch_and_module(): void
    {
        $this->actingAs($this->admin());

        $batchA = Batch::create(['name' => 'Batch A', 'is_active' => true]);
        $batchB = Batch::create(['name' => 'Batch B', 'is_active' => true]);
        $moduleA = Module::create(['name' => 'Module A', 'is_active' => true]);
        $moduleB = Module::create(['name' => 'Module B', 'is_active' => true]);

        Lecture::create([
            'batch_id' => $batchA->id,
            'module_id' => $moduleA->id,
            'title' => 'Lecture AA',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        Lecture::create([
            'batch_id' => $batchB->id,
            'module_id' => $moduleB->id,
            'title' => 'Lecture BB',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        Livewire::test(LectureTable::class)
            ->set('batchFilter', $batchA->id)
            ->assertSee('Lecture AA')
            ->assertDontSee('Lecture BB');
    }

    public function test_module_table_shows_linked_batches(): void
    {
        $data = $this->seedLearningGraph();
        $this->actingAs($this->admin());

        Livewire::test(ModuleTable::class)
            ->assertSee('Module One')
            ->assertSee('Batch Alpha')
            ->set('batchFilter', $data['batch']->id)
            ->assertSee('Module One');
    }

    public function test_module_table_orders_by_sort_order_ascending(): void
    {
        $this->actingAs($this->admin());

        Module::create(['name' => 'lecture 1', 'sort_order' => 1, 'is_active' => true]);
        Module::create(['name' => 'lecture 5', 'sort_order' => 0, 'is_active' => true]);

        Livewire::test(ModuleTable::class)
            ->assertSeeInOrder(['lecture 5', 'lecture 1']);
    }

    public function test_lecture_table_shows_video_and_document_counts(): void
    {
        $data = $this->seedLearningGraph();
        $this->actingAs($this->admin());

        Livewire::test(LectureTable::class)
            ->assertSee('Intro to Markets')
            ->assertSee('1 video')
            ->assertSee('0 docs');
    }

    public function test_videos_page_filters_by_lecture_query_param(): void
    {
        $data = $this->seedLearningGraph();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.learning.videos.index', ['lecture' => $data['lecture']->id]))
            ->assertOk()
            ->assertSee('Part 1')
            ->assertSee('Intro to Markets')
            ->assertSee('Overview lecture')
            ->assertSee('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg')
            ->assertSee('Sample YouTube Video');
    }

    public function test_batch_table_shows_lecture_notes_and_video_links(): void
    {
        $data = $this->seedLearningGraph();
        $this->actingAs($this->admin());

        Livewire::test(BatchTable::class)
            ->assertSee('Batch Alpha')
            ->assertSee('1 lecture')
            ->assertSee('1 video');
    }

    public function test_batch_module_and_lecture_tables_show_linked_user_notes(): void
    {
        $data = $this->seedLearningGraph();
        $admin = $this->admin();

        UserNote::create([
            'user_id' => $admin->id,
            'lecture_id' => $data['lecture']->id,
            'title' => 'maarna kam ghaseetna jada',
            'body' => 'Market trend note linked to lecture.',
            'is_shared' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(BatchTable::class)
            ->assertSee('1 linked note');

        Livewire::actingAs($admin)
            ->test(ModuleTable::class)
            ->assertSee('1 linked note');

        Livewire::actingAs($admin)
            ->test(LectureTable::class)
            ->assertSee('maarna kam ghaseetna jada')
            ->assertSee('Shared');

        Livewire::actingAs($admin)
            ->test(LectureFormModal::class)
            ->call('open', $data['lecture']->id)
            ->assertSee('Linked notes (1)')
            ->assertSee('maarna kam ghaseetna jada');
    }

    public function test_lectures_page_filters_by_batch_query_param(): void
    {
        $data = $this->seedLearningGraph();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.learning.lectures.index', ['batch' => $data['batch']->id]))
            ->assertOk()
            ->assertSee('Intro to Markets')
            ->assertSee('Overview lecture');
    }

    public function test_videos_page_filters_by_batch_query_param(): void
    {
        $data = $this->seedLearningGraph();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.learning.videos.index', ['batch' => $data['batch']->id]))
            ->assertOk()
            ->assertSee('Part 1')
            ->assertSee('Overview lecture');
    }

    public function test_lecture_table_shows_notes_preview(): void
    {
        $this->seedLearningGraph();
        $this->actingAs($this->admin());

        Livewire::test(LectureTable::class)
            ->assertSee('Overview lecture');
    }

    public function test_lecture_form_modal_creates_lecture(): void
    {
        $data = $this->seedLearningGraph();
        $this->actingAs($this->admin());

        Livewire::test(LectureFormModal::class)
            ->call('open')
            ->set('batch_id', $data['batch']->id)
            ->set('module_id', $data['module']->id)
            ->set('title', 'Risk Management')
            ->set('notes', 'Basics')
            ->set('sort_order', 2)
            ->set('is_active', true)
            ->call('save')
            ->assertDispatched('lectureTableRefresh');

        $this->assertDatabaseHas('lectures', [
            'title' => 'Risk Management',
            'batch_id' => $data['batch']->id,
            'module_id' => $data['module']->id,
        ]);
    }

    public function test_lecture_form_modal_shows_linked_video_preview(): void
    {
        $data = $this->seedLearningGraph();
        $this->actingAs($this->admin());

        Livewire::test(LectureFormModal::class)
            ->call('open', $data['lecture']->id)
            ->assertSee('Linked videos (1)')
            ->assertSee('Part 1')
            ->assertSee('Sample YouTube Video')
            ->assertSee('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg')
            ->assertSee('Manage videos');
    }

    public function test_dashboard_shows_learning_stats(): void
    {
        $this->seedLearningGraph();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.learning.dashboard'))
            ->assertOk()
            ->assertSee('Learning Admin')
            ->assertSee('Recent lectures')
            ->assertSee('Intro to Markets');
    }
}
