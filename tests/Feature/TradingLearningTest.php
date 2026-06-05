<?php

namespace Tests\Feature;

use App\Livewire\Learning\BatchList;
use App\Livewire\Learning\LectureList;
use App\Livewire\Learning\LectureView;
use App\Models\Batch;
use App\Models\Lecture;
use App\Models\Module;
use App\Models\User;
use App\Models\UserNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TradingLearningTest extends TestCase
{
    use RefreshDatabase;

    private function seedLecture(): array
    {
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);
        $module = Module::create(['name' => 'Lecture 5', 'is_active' => true]);
        $lecture = Lecture::create([
            'batch_id' => $batch->id,
            'module_id' => $module->id,
            'title' => 'Chart analysis combination',
            'notes' => 'Instructor summary for this session.',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        return compact('batch', 'module', 'lecture');
    }

    public function test_lecture_view_shows_linked_user_note_for_same_lecture(): void
    {
        $data = $this->seedLecture();
        $student = User::factory()->create(['role' => 'user']);

        UserNote::create([
            'user_id' => $student->id,
            'lecture_id' => $data['lecture']->id,
            'title' => 'My chart patterns',
            'body' => 'Watch for combination setups.',
            'is_shared' => false,
        ]);

        Livewire::actingAs($student)
            ->test(LectureView::class)
            ->call('loadLecture', $data['lecture']->id)
            ->assertSee('Linked notes')
            ->assertSee('My chart patterns')
            ->assertSee('Watch for combination setups')
            ->assertSee('Your note');
    }

    public function test_lecture_view_shows_shared_admin_note_linked_to_lecture(): void
    {
        $data = $this->seedLecture();
        $student = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin', 'account_id' => null]);

        UserNote::create([
            'user_id' => $admin->id,
            'lecture_id' => $data['lecture']->id,
            'title' => 'Weekly focus',
            'body' => 'Review combination charts.',
            'is_shared' => true,
        ]);

        Livewire::actingAs($student)
            ->test(LectureView::class)
            ->call('loadLecture', $data['lecture']->id)
            ->assertSee('Weekly focus')
            ->assertSee('Shared by');
    }

    public function test_lecture_view_hides_other_students_private_linked_note(): void
    {
        $data = $this->seedLecture();
        $studentA = User::factory()->create(['role' => 'user']);
        $studentB = User::factory()->create(['role' => 'user']);

        UserNote::create([
            'user_id' => $studentB->id,
            'lecture_id' => $data['lecture']->id,
            'title' => 'Secret linked note',
            'body' => 'Private idea.',
            'is_shared' => false,
        ]);

        Livewire::actingAs($studentA)
            ->test(LectureView::class)
            ->call('loadLecture', $data['lecture']->id)
            ->assertDontSee('Secret linked note');
    }

    public function test_trading_learning_page_loads_for_authenticated_user(): void
    {
        $data = $this->seedLecture();
        $student = User::factory()->create(['role' => 'user']);

        $this->actingAs($student)
            ->get(route('learning.index'))
            ->assertOk()
            ->assertSee('Trading Learning')
            ->assertSee('Curriculum')
            ->assertSee('Timeframe categories')
            ->assertSee('Exit rule')
            ->assertSee('Intraday');
    }

    public function test_selecting_batch_loads_first_lecture_content(): void
    {
        $data = $this->seedLecture();
        $student = User::factory()->create(['role' => 'user']);

        Livewire::actingAs($student)
            ->test(BatchList::class)
            ->call('selectBatch', $data['batch']->id);

        Livewire::actingAs($student)
            ->test(LectureList::class)
            ->call('onBatchSelected', $data['batch']->id)
            ->assertSet('selectedLectureId', $data['lecture']->id);
    }
}
