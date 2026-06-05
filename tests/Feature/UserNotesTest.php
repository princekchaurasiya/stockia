<?php

namespace Tests\Feature;

use App\Livewire\Portal\NotesManager;
use App\Models\Batch;
use App\Models\Lecture;
use App\Models\Module;
use App\Models\User;
use App\Models\UserNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UserNotesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_note_without_lecture_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'account_id' => null]);

        Livewire::actingAs($admin)
            ->test(NotesManager::class)
            ->set('view', 'form')
            ->set('title', 'Test note')
            ->set('body', 'Some content')
            ->set('lecture_id', '')
            ->set('is_shared', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('view', 'list');

        $this->assertDatabaseHas('user_notes', [
            'user_id' => $admin->id,
            'title' => 'Test note',
            'is_shared' => true,
            'lecture_id' => null,
        ]);
    }

    public function test_student_can_save_private_note(): void
    {
        $student = User::factory()->create(['role' => 'user']);

        UserNote::create([
            'user_id' => $student->id,
            'title' => 'My strategy',
            'body' => 'Buy on dips.',
            'is_shared' => false,
        ]);

        $this->actingAs($student)
            ->get(route('notes.index'))
            ->assertOk()
            ->assertSee('My strategy');
    }

    public function test_student_sees_admin_shared_note(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin', 'account_id' => null]);

        UserNote::create([
            'user_id' => $admin->id,
            'title' => 'Weekly market plan',
            'body' => 'Focus on Bank Nifty.',
            'is_shared' => true,
        ]);

        $this->actingAs($student)
            ->get(route('notes.index'))
            ->assertOk()
            ->assertSee('Weekly market plan')
            ->assertSee('Shared notes from admin');
    }

    public function test_student_does_not_see_other_students_private_note(): void
    {
        $studentA = User::factory()->create(['role' => 'user']);
        $studentB = User::factory()->create(['role' => 'user']);

        UserNote::create([
            'user_id' => $studentB->id,
            'title' => 'Secret notes',
            'body' => 'Private idea.',
            'is_shared' => false,
        ]);

        $this->actingAs($studentA)
            ->get(route('notes.index'))
            ->assertOk()
            ->assertDontSee('Secret notes');
    }

    public function test_notes_page_requires_auth(): void
    {
        $this->get(route('notes.index'))->assertRedirect(route('login'));
    }

    public function test_user_note_shows_lecture_batch_and_module_context(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $batch = Batch::create(['name' => 'Feb Batch', 'is_active' => true]);
        $module = Module::create(['name' => 'Chart Analysis', 'is_active' => true]);
        $lecture = Lecture::create([
            'batch_id' => $batch->id,
            'module_id' => $module->id,
            'title' => 'Combination Patterns',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        UserNote::create([
            'user_id' => $student->id,
            'lecture_id' => $lecture->id,
            'title' => 'My lecture notes',
            'body' => 'Important patterns.',
            'is_shared' => false,
        ]);

        $this->actingAs($student)
            ->get(route('notes.index'))
            ->assertOk()
            ->assertSee('My lecture notes')
            ->assertSee('Feb Batch')
            ->assertSee('Chart Analysis')
            ->assertSee('Combination Patterns');
    }

    public function test_student_can_upload_example_image_with_note(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->image('chart-example.png');

        Livewire::actingAs($student)
            ->test(NotesManager::class)
            ->set('view', 'form')
            ->set('title', 'Pattern example')
            ->set('body', 'See attached chart.')
            ->set('newImages', [$file])
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('view', 'list');

        $note = UserNote::query()->where('title', 'Pattern example')->first();
        $this->assertNotNull($note);
        $this->assertCount(1, $note->images);
        Storage::disk('public')->assertExists($note->images->first()->file_path);

        $this->actingAs($student)
            ->get(route('notes.index'))
            ->assertOk()
            ->assertSee('Pattern example');
    }

    public function test_admin_shared_note_image_is_visible_to_student(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin', 'account_id' => null]);
        $file = UploadedFile::fake()->image('admin-example.jpg');

        Livewire::actingAs($admin)
            ->test(NotesManager::class)
            ->set('view', 'form')
            ->set('title', 'Admin chart example')
            ->set('body', 'Use this setup.')
            ->set('is_shared', true)
            ->set('newImages', [$file])
            ->call('save')
            ->assertHasNoErrors();

        $note = UserNote::query()->where('title', 'Admin chart example')->firstOrFail();

        Livewire::actingAs($student)
            ->test(NotesManager::class)
            ->call('viewNote', $note->id)
            ->assertSet('view', 'read')
            ->assertSee('Admin chart example')
            ->assertSee('Use this setup');
    }

    public function test_student_can_upload_pdf_with_note(): void
    {
        Storage::fake('public');
        $student = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->create('example.pdf', 100, 'application/pdf');

        Livewire::actingAs($student)
            ->test(NotesManager::class)
            ->set('view', 'form')
            ->set('title', 'PDF example')
            ->set('body', 'See attached PDF.')
            ->set('newImages', [$file])
            ->call('save')
            ->assertHasNoErrors();

        $note = UserNote::query()->where('title', 'PDF example')->firstOrFail();
        $this->assertCount(1, $note->images);
        $this->assertTrue($note->images->first()->isPdf());
    }
}
