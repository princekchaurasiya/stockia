<?php

namespace App\Support;

use App\Models\Batch;
use App\Models\Lecture;
use App\Models\Module;

class LearningAdminNav
{
    public static function sections(): array
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard',
                'route' => 'admin.learning.dashboard',
                'pattern' => 'admin.learning.dashboard',
            ],
            'batches' => [
                'label' => 'Batches',
                'route' => 'admin.learning.batches.index',
                'pattern' => 'admin.learning.batches.*',
            ],
            'enrollments' => [
                'label' => 'Students',
                'route' => 'admin.learning.enrollments.index',
                'pattern' => 'admin.learning.enrollments.*',
            ],
            'modules' => [
                'label' => 'Modules',
                'route' => 'admin.learning.modules.index',
                'pattern' => 'admin.learning.modules.*',
            ],
            'lectures' => [
                'label' => 'Lectures',
                'route' => 'admin.learning.lectures.index',
                'pattern' => 'admin.learning.lectures.*',
            ],
            'videos' => [
                'label' => 'Videos',
                'route' => 'admin.learning.videos.index',
                'pattern' => 'admin.learning.videos.*',
            ],
            'documents' => [
                'label' => 'Documents',
                'route' => 'admin.learning.documents.index',
                'pattern' => 'admin.learning.documents.*',
            ],
        ];
    }

    public static function breadcrumbs(string $section, array $trail = []): array
    {
        $crumbs = [
            'Learning Admin' => route('admin.learning.dashboard'),
        ];

        if ($section === 'dashboard') {
            return array_merge($crumbs, ['Dashboard' => null]);
        }

        $meta = self::sections()[$section] ?? null;
        if ($meta === null) {
            return $crumbs;
        }

        $crumbs[$meta['label']] = route($meta['route']);

        if ($trail === []) {
            $context = self::requestContext($section);
            if ($context !== []) {
                $crumbs = array_merge($crumbs, $context);
            } else {
                $crumbs[$meta['label']] = null;
            }

            return $crumbs;
        }

        foreach ($trail as $label => $url) {
            $crumbs[$label] = $url;
        }

        return $crumbs;
    }

    private static function requestContext(string $section): array
    {
        if (! in_array($section, ['lectures', 'videos', 'documents', 'modules', 'enrollments'], true)) {
            return [];
        }

        $context = [];
        $batchId = request()->integer('batch');
        $moduleId = request()->integer('module');
        $lectureId = request()->integer('lecture');

        if ($batchId > 0) {
            $batch = Batch::query()->find($batchId);
            if ($batch) {
                $context[$batch->name] = ($moduleId > 0 || $lectureId > 0)
                    ? self::filteredRoute($section, ['batch' => $batchId])
                    : null;
            }
        }

        if ($moduleId > 0) {
            $module = Module::query()->find($moduleId);
            if ($module) {
                $context[$module->name] = $lectureId > 0
                    ? self::filteredRoute($section, array_filter([
                        'batch' => $batchId ?: null,
                        'module' => $moduleId,
                    ]))
                    : null;
            }
        }

        if ($lectureId > 0) {
            $lecture = Lecture::query()->find($lectureId);
            if ($lecture) {
                $context[$lecture->title] = null;
            }
        }

        return $context;
    }

    private static function filteredRoute(string $section, array $params): string
    {
        $meta = self::sections()[$section];

        return route($meta['route'], $params);
    }
}
