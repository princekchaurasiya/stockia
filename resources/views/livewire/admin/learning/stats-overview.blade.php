<div>
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg">
            <a href="{{ route('admin.learning.batches.index') }}" class="learning-stat-card-link">
                <div class="card border-0 shadow-sm learning-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Batches</div>
                                <div class="display-6 fw-bold mb-0">{{ $stats['batches']['total'] }}</div>
                                <div class="small text-muted">{{ $stats['batches']['active'] }} active</div>
                            </div>
                            <span class="icon-wrap"><i class="bi bi-collection"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg">
            <a href="{{ route('admin.learning.modules.index') }}" class="learning-stat-card-link">
                <div class="card border-0 shadow-sm learning-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Modules</div>
                                <div class="display-6 fw-bold mb-0">{{ $stats['modules']['total'] }}</div>
                                <div class="small text-muted">{{ $stats['modules']['active'] }} active</div>
                            </div>
                            <span class="icon-wrap"><i class="bi bi-folder"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg">
            <a href="{{ route('admin.learning.lectures.index') }}" class="learning-stat-card-link">
                <div class="card border-0 shadow-sm learning-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Lectures</div>
                                <div class="display-6 fw-bold mb-0">{{ $stats['lectures']['total'] }}</div>
                                <div class="small text-muted">{{ $stats['lectures']['active'] }} active</div>
                            </div>
                            <span class="icon-wrap"><i class="bi bi-book"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.learning.videos.index') }}" class="learning-stat-card-link">
                <div class="card border-0 shadow-sm learning-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Videos</div>
                                <div class="display-6 fw-bold mb-0">{{ $stats['videos'] }}</div>
                                <div class="small text-muted">~{{ $stats['avg_videos_per_lecture'] }} per lecture</div>
                            </div>
                            <span class="icon-wrap"><i class="bi bi-play-circle"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-lg">
            <a href="{{ route('admin.learning.documents.index') }}" class="learning-stat-card-link">
                <div class="card border-0 shadow-sm learning-stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Documents</div>
                                <div class="display-6 fw-bold mb-0">{{ $stats['documents'] }}</div>
                                <div class="small text-muted">PDFs &amp; slides</div>
                            </div>
                            <span class="icon-wrap"><i class="bi bi-file-earmark-pdf"></i></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <x-ui.section-title class="mb-3">Recent lectures</x-ui.section-title>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-muted text-uppercase small">Title</th>
                        <th class="text-muted text-uppercase small">Batch</th>
                        <th class="text-muted text-uppercase small">Module</th>
                        <th class="text-muted text-uppercase small">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentLectures as $lecture)
                        <tr>
                            <td>
                                <a href="{{ route('admin.learning.lectures.index', array_filter(['batch' => $lecture->batch_id, 'module' => $lecture->module_id, 'lecture' => $lecture->id])) }}" class="text-decoration-none">
                                    {{ $lecture->title }}
                                </a>
                            </td>
                            <td>{{ $lecture->batch->name ?? '—' }}</td>
                            <td>{{ $lecture->module->name ?? '—' }}</td>
                            <td class="text-muted small">{{ $lecture->created_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No lectures yet. Create your first lecture.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @once
        @push('styles')
            <style>
                .learning-stat-card-link {
                    display: block;
                    color: inherit;
                    text-decoration: none;
                }

                .learning-stat-card-link:hover {
                    color: inherit;
                }

                .learning-stat-card .icon-wrap {
                    width: 42px;
                    height: 42px;
                    border-radius: 10px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    background: rgba(13, 110, 253, 0.1);
                    color: #0d6efd;
                    font-size: 1.1rem;
                    flex-shrink: 0;
                }
            </style>
        @endpush
    @endonce
</div>
