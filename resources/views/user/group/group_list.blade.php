@extends('user.layouts.app')
@push('styles')
    <link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-group {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            color: #6c757d;
            background: #e8eff5;
            transition: all 0.2s ease;
            text-decoration: none;
            position: relative;
        }

        .btn-icon:hover {
            background: #e9ecef;
            color: #495057;
            transform: translateY(-1px);
        }

        .btn-delete:hover {
            background: #dc3545;
            color: white;
        }

        .keyword-badge {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 11px;
            margin-right: 4px;
            margin-bottom: 4px;
            display: inline-block;
        }

        .keyword-set-item {
            border-left: 3px solid #007bff;
            padding-left: 10px;
            margin-bottom: 15px;
        }

        .keyword-set-name {
            font-weight: 600;
            font-size: 13px;
            color: #334155;
            margin-bottom: 5px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Group List Section -->
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col d-flex justify-content-between align-items-center">                      
                            <h4 class="card-title mb-0">Groups Management</h4>
                             @if(session('user.role.name') !== 'Supervisor')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                                <i class="bi bi-plus-lg me-1"></i>Create New Group
                            </button>
                            @endif
                        </div>
                    </div>                                 
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="max-width: 400px;">Group Name</th>
                                    <th>Keyword Sets</th>
                                    <th></th>
                                    <th style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($paginatedGroups) > 0)
                                    @foreach($paginatedGroups as $group)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark group-name-text">{{ $group['group_name'] }}</div>
                                                <small class="text-muted">{{ $group['description'] }}</small>
                                            </td> 
                                            <td>
                                                @if(isset($group['keyword_sets']))
                                                    @foreach($group['keyword_sets'] as $set)
                                                        <div class="keyword-set-item">
                                                            <div class="keyword-set-name">{{ $set['name'] }}</div>
                                                            <div>
                                                                @foreach($set['keywords'] as $keyword)
                                                                    <span class="keyword-badge">{{ $keyword }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </td> 
                                            <td>

                                                     @if(session('user.role.name') !== 'Supervisor')
                                                <button type="button" class="btn btn-xs btn-outline-primary mt-1 add-keyword-set-btn" data-group-name="{{ $group['group_name'] }}">
                                                    <i class="bi bi-plus-circle me-1"></i>Add Keyword Set
                                                </button>
                                                @endif
                                            </td>
                                            <td>
                                                 <div class="btn-group" role="group">
                                                    @if(session('user.role.name') !== 'Supervisor')
                                                    <button type="button" class="btn btn-icon edit-group-btn" 
                                                        data-group-id="{{ $group['group_id'] }}" 
                                                        data-group-name="{{ $group['group_name'] }}" 
                                                        title="Settings">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-icon btn-delete demo-restricted" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    @else
                                                    <span class="badge bg-light text-dark">View Only</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center py-4">No groups found.</td>
                                    </tr>
                                @endif         
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="demo-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text" class="form-control" name="group_name" placeholder="Enter group name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Group: <span id="edit-modal-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="demo-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text" id="edit-group-name-input" class="form-control" name="group_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Keyword Set Modal (Demo Placeholder) -->
<div class="modal fade" id="addKeywordSetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Keyword Set to <span id="modal-group-name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="demo-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Set Name</label>
                        <input type="text" class="form-control" placeholder="e.g. Closing Phrases">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keywords</label>
                        <input type="text" id="keyword-input" class="form-control" placeholder="Type and press Enter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Set</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit Group Logic
            document.querySelectorAll('.edit-group-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const groupName = this.getAttribute('data-group-name');
                    document.getElementById('edit-modal-title').textContent = groupName;
                    document.getElementById('edit-group-name-input').value = groupName;
                    const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
                    modal.show();
                });
            });

            // Add Keyword Set Logic
            document.querySelectorAll('.add-keyword-set-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const groupName = this.getAttribute('data-group-name');
                    document.getElementById('modal-group-name').textContent = groupName;
                    const modal = new bootstrap.Modal(document.getElementById('addKeywordSetModal'));
                    modal.show();
                    
                    // Re-init Tagify if needed
                    const input = document.getElementById('keyword-input');
                    if (input && !input.tagify) {
                        new Tagify(input);
                    }
                });
            });

            // Global demo alert for specific forms is handled by app.blade.php
            // but we can add a helper for restricted buttons
            document.querySelectorAll('.demo-restricted').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Demo Account',
                        text: 'This action is restricted in the demo account.',
                        icon: 'info',
                        confirmButtonText: 'I Understand',
                        confirmButtonColor: '#0a66c2'
                    });
                });
            });
        });
    </script>
@endpush