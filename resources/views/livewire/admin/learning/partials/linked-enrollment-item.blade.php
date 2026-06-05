@props(['enrollment', 'batchId' => null])

@php
    $enrollmentUrl = route('admin.learning.enrollments.index', array_filter([
        'batch' => $batchId ?? $enrollment->batch_id ?? null,
    ]));
@endphp

<div {{ $attributes->merge(['class' => 'linked-enrollment-item d-flex justify-content-between align-items-start gap-3']) }}>
    <div class="min-w-0">
        <a href="{{ $enrollmentUrl }}" class="fw-medium text-decoration-none">{{ $enrollment->user->name ?? '—' }}</a>
        <div class="small text-muted">{{ $enrollment->user->email ?? '—' }}</div>
        <div class="small text-muted mt-1">Enrolled {{ $enrollment->enrolled_at?->format('M j, Y') ?? '—' }}</div>
    </div>
    <span class="badge flex-shrink-0 {{ $enrollment->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
        {{ $enrollment->is_active ? 'Active' : 'Inactive' }}
    </span>
</div>
