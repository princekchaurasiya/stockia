<?php

namespace Tests\Feature;

use App\Models\ResearchUpload;
use App\Models\User;
use App\Services\ResearchUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResearchModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_student_cannot_see_other_users_pending_uploads_in_approved_list(): void
    {
        $studentA = User::factory()->create(['role' => 'user']);
        $studentB = User::factory()->create(['role' => 'user']);

        ResearchUpload::create([
            'user_id' => $studentB->id,
            'category' => 'fii',
            'title' => 'Secret pending',
            'file_path' => 'research-uploads/test.pdf',
            'file_type' => 'pdf',
            'original_name' => 'test.pdf',
            'status' => 'pending',
        ]);

        ResearchUpload::create([
            'user_id' => $studentB->id,
            'category' => 'fii',
            'title' => 'Public approved',
            'file_path' => 'research-uploads/public.pdf',
            'file_type' => 'pdf',
            'original_name' => 'public.pdf',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($studentA)->get(route('research.index'));

        $response->assertOk();
        $response->assertSee('Public approved');
        $response->assertDontSee('Secret pending');
    }

    public function test_admin_can_approve_pending_upload(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user']);

        $upload = ResearchUpload::create([
            'user_id' => $student->id,
            'category' => 'dii',
            'title' => 'Awaiting review',
            'file_path' => 'research-uploads/review.pdf',
            'file_type' => 'pdf',
            'original_name' => 'review.pdf',
            'status' => 'pending',
        ]);

        app(ResearchUploadService::class)->approve($upload, $admin);

        $upload->refresh();
        $this->assertSame('approved', $upload->status);
        $this->assertSame($admin->id, $upload->reviewed_by);
        $this->assertNotNull($upload->reviewed_at);
    }

    public function test_admin_can_reject_pending_upload(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'user']);

        $upload = ResearchUpload::create([
            'user_id' => $student->id,
            'category' => 'sector',
            'title' => 'Bad data',
            'file_path' => 'research-uploads/bad.pdf',
            'file_type' => 'pdf',
            'original_name' => 'bad.pdf',
            'status' => 'pending',
        ]);

        app(ResearchUploadService::class)->reject($upload, $admin, 'Incomplete data');

        $upload->refresh();
        $this->assertSame('rejected', $upload->status);
        $this->assertSame('Incomplete data', $upload->rejection_reason);
    }

    public function test_student_upload_creates_pending_record(): void
    {
        $student = User::factory()->create(['role' => 'user']);
        $file = UploadedFile::fake()->create('fii-report.pdf', 100, 'application/pdf');

        $upload = app(ResearchUploadService::class)->store($file, $student, [
            'category' => 'fii',
            'title' => 'FII Flow March',
            'report_date' => '2026-03-01',
        ]);

        $this->assertSame('pending', $upload->status);
        $this->assertSame($student->id, $upload->user_id);
        Storage::disk('public')->assertExists($upload->file_path);
    }

    public function test_announcements_page_requires_auth(): void
    {
        $this->get(route('announcements.index'))->assertRedirect(route('login'));
    }

    public function test_live_classes_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('live_classes.index'))->assertOk();
    }
}
