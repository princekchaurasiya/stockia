<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Lecture;
use App\Models\LectureDocument;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_loads(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_lecture_documents_table_has_composite_index(): void
    {
        $indexes = Schema::getIndexes('lecture_documents');
        $hasComposite = collect($indexes)->contains(function ($index) {
            $cols = $index['columns'] ?? [];

            return $cols === ['lecture_id', 'is_active', 'sort_order'];
        });

        $this->assertTrue($hasComposite, 'Expected composite index on lecture_documents (lecture_id, is_active, sort_order)');
    }

    public function test_lecture_documents_relationship(): void
    {
        $batch = Batch::create(['name' => 'Test Batch', 'is_active' => true]);
        $module = Module::create(['name' => 'Test Module', 'is_active' => true]);
        $lecture = Lecture::create([
            'batch_id' => $batch->id,
            'module_id' => $module->id,
            'title' => 'Test Lecture',
            'is_active' => true,
        ]);

        LectureDocument::create([
            'lecture_id' => $lecture->id,
            'title' => 'Slides',
            'file_path' => 'lecture-documents/test.pdf',
            'file_type' => 'pdf',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $lecture->fresh()->documents
        );
        $this->assertCount(1, $lecture->documents);
    }
}
