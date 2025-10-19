@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary p-2 rounded me-3">
                            <i class="fas fa-database text-white"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold text-dark">Knowledge Base</h4>
                            <p class="text-muted mb-0 small"> knowledge resources</p>
                        </div>
                    </div>
                    <a href="{{ route('user.knowledgeBase.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i> New Entry
                    </a>
                </div>
                <!-- Data Table -->
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="ps-4">ID</th>
                                    <th scope="col">Notebook</th>
                                    <th scope="col">File Name</th>
                                    <th scope="col">Topics</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Created</th>
                                    <th scope="col" class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($knowledgeBase as $entry)
                                    <tr class="border-bottom">
                                        <td class="ps-4 fw-semibold text-muted">#{{ $entry['id'] ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-2">
                                                    <i class="fas fa-book text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $entry['notebook_name'] ?? '-' }}</div>
                                                    <small class="text-muted">Company ID: {{ $entry['company_id'] ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt text-secondary me-2"></i>
                                                <span class="text-truncate" style="max-width: 150px;" title="{{ $entry['content_file_name'] ?? '' }}">
                                                    {{ $entry['content_file_name'] ?? '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @if(isset($entry['topics']) && count($entry['topics']) > 0)
                                                @foreach(array_slice($entry['topics'], 0, 2) as $topic)
                                                    <span class="badge bg-light text-dark mb-1">{{ $topic }}</span>
                                                @endforeach
                                                @if(count($entry['topics']) > 2)
                                                    <span class="badge bg-light text-dark">+{{ count($entry['topics']) - 2 }} more</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $entry['description'] ?? '' }}">
                                                {{ $entry['description'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ isset($entry['created_at']) ? date('d M Y', strtotime($entry['created_at'])) : '-' }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2 pe-2">
                                               
                                                <a href="{{ route('user.knowledgeBase.edit', $entry['id']) }}" class="btn btn-sm btn-light border" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('user.knowledgeBase.delete', ['id' => $entry['notebook_id'], 'company_id' => $entry['company_id']]) }}" 
                                                    class="btn btn-sm btn-icon btn-outline-secondary" 
                                                    data-bs-toggle="tooltip" title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this entry?')">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-5">
                                                <i class="fas fa-database fa-2x text-muted mb-3"></i>
                                                <p class="text-muted">No knowledge base entries found.</p>
                                                <a href="{{ route('user.knowledgeBase.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus me-1"></i> Create Your First Entry
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($knowledgeBase->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Showing {{ $knowledgeBase->firstItem() }} to {{ $knowledgeBase->lastItem() }} of {{ $knowledgeBase->total() }} entries
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $knowledgeBase->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush