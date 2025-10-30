@extends('user.layouts.app')
@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">Role Management</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('user.home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Roles</li>
                        </ol>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                            <i class="fas fa-plus me-1"></i> Create Role
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-shield-alt text-primary fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0">{{ $role['name'] }}</h6>
                                                    <small class="text-muted">ID: {{ $role['id'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $role['description'] ?? 'No description' }}</td>
                                        <td>
                                            @if(isset($role['permissions']) && count($role['permissions']) > 0)
                                                <div class="permission-tags">
                                                    @foreach(array_slice($role['permissions'], 0, 3) as $permission)
                                                        <span class="badge bg-light text-dark border me-1 mb-1">
                                                            {{ $permission['name'] ?? $permission }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($role['permissions']) > 3)
                                                        <span class="badge bg-secondary">
                                                            +{{ count($role['permissions']) - 3 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No permissions</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                        data-role-id="{{ $role['id'] }}"
                                                        data-role-name="{{ $role['name'] }}"
                                                        data-role-description="{{ $role['description'] ?? '' }}"
                                                        onclick="editRole(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete('{{ $role['id'] }}', '{{ $role['name'] }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                                <h5>No Roles Found</h5>
                                                <p>Get started by creating your first role.</p>
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                                    Create Role
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRoleForm" action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="roleName" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="roleName" name="name" required 
                                       placeholder="Enter role name (e.g., Content Manager)">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="roleDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="roleDescription" name="description" 
                                          rows="3" placeholder="Enter role description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Permissions</label>
                                <div class="permissions-container border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center loading-permissions">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading permissions...</span>
                                        </div>
                                        <span class="ms-2">Loading permissions...</span>
                                    </div>
                                    <div class="permissions-list d-none">
                                        <!-- Permissions will be loaded here via JavaScript -->
                                    </div>
                                </div>
                                <small class="text-muted">Select permissions to assign to this role</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Edit Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editRoleName" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="editRoleName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role ID</label>
                                <p class="form-control-static" id="editRoleId"></p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="editRoleDescription" class="form-label">Role Description</label>
                                <textarea class="form-control" id="editRoleDescription" name="description" rows="3" placeholder="Enter role description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Permissions</label>
                                <div class="permissions-container border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center loading-permissions">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading permissions...</span>
                                        </div>
                                        <span class="ms-2">Loading permissions...</span>
                                    </div>
                                    <div class="permissions-list d-none">
                                        <!-- Permissions will be loaded here via JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5>Are you sure?</h5>
                    <p>You are about to delete the role: <strong id="deleteRoleName"></strong></p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRoleForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Role</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables
let allPermissions = [];

// Load permissions for create modal
function loadPermissionsForCreate() {
    fetch('{{ route("roles.permissions") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('#createRoleModal .permissions-list');
            const loading = document.querySelector('#createRoleModal .loading-permissions');
            
            loading.classList.add('d-none');
            container.classList.remove('d-none');
            
            allPermissions = data.permissions || [];
            renderPermissions(container, allPermissions, []);
        })
        .catch(error => {
            console.error('Error loading permissions:', error);
            document.querySelector('#createRoleModal .loading-permissions').innerHTML = 
                '<span class="text-danger">Failed to load permissions</span>';
        });
}

// Load permissions for edit modal
function loadPermissionsForEdit(roleId, currentPermissions = []) {
    fetch('{{ route("roles.permissions") }}')
        .then(response => response.json())
        .then(data => {
            const container = document.querySelector('#editRoleModal .permissions-list');
            const loading = document.querySelector('#editRoleModal .loading-permissions');
            
            loading.classList.add('d-none');
            container.classList.remove('d-none');
            
            allPermissions = data.permissions || [];
            renderPermissions(container, allPermissions, currentPermissions);
        })
        .catch(error => {
            console.error('Error loading permissions:', error);
            document.querySelector('#editRoleModal .loading-permissions').innerHTML = 
                '<span class="text-danger">Failed to load permissions</span>';
        });
}

// Render permissions checkboxes
function renderPermissions(container, permissions, selectedPermissionIds) {
    if (permissions.length === 0) {
        container.innerHTML = '<div class="text-center text-muted">No permissions available</div>';
        return;
    }

    let html = '';
    permissions.forEach(permission => {
        const isChecked = selectedPermissionIds.includes(permission.id);
        html += `
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="permission_ids[]" 
                       value="${permission.id}" id="perm_${permission.id}" ${isChecked ? 'checked' : ''}>
                <label class="form-check-label" for="perm_${permission.id}">
                    <strong>${permission.name}</strong>
                    ${permission.description ? `<br><small class="text-muted">${permission.description}</small>` : ''}
                </label>
            </div>
        `;
    });
    container.innerHTML = html;
}

// Edit role
function editRole(button) {
    const roleId = button.getAttribute('data-role-id');
    const roleName = button.getAttribute('data-role-name');
    const roleDescription = button.getAttribute('data-role-description') || '';

    // Set form values
    document.getElementById('editRoleName').value = roleName;
    document.getElementById('editRoleId').textContent = roleId;
    document.getElementById('editRoleDescription').value = roleDescription;
    
    // Set form action using route
    const updateUrl = '{{ route("roles.update", ":id") }}'.replace(':id', roleId);
    document.getElementById('editRoleForm').action = updateUrl;

    // Reset and show loading
    const container = document.querySelector('#editRoleModal .permissions-list');
    const loading = document.querySelector('#editRoleModal .loading-permissions');
    container.classList.add('d-none');
    loading.classList.remove('d-none');

    // Load current role permissions first - Use route with parameter
    const showUrl = '{{ route("roles.show", ":id") }}'.replace(':id', roleId);
    fetch(showUrl + '?ajax=1') // Add ajax parameter to trigger JSON response
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch role details');
            }
            return response.json();
        })
        .then(data => {
            const currentPermissions = data.role?.permissions ? 
                data.role.permissions.map(p => p.id || p) : [];
            loadPermissionsForEdit(roleId, currentPermissions);
        })
        .catch(error => {
            console.error('Error loading role:', error);
            loading.innerHTML = '<span class="text-danger">Failed to load role data</span>';
        });
}

// Confirm delete
function confirmDelete(roleId, roleName) {
    document.getElementById('deleteRoleName').textContent = roleName;
    
    // Set form action using route
    const deleteUrl = '{{ route("roles.destroy", ":id") }}'.replace(':id', roleId);
    document.getElementById('deleteRoleForm').action = deleteUrl;
    
    new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Load permissions when create modal is shown
    const createModal = document.getElementById('createRoleModal');
    createModal.addEventListener('show.bs.modal', loadPermissionsForCreate);

    // Reset create modal when hidden
    createModal.addEventListener('hidden.bs.modal', function() {
        document.getElementById('createRoleForm').reset();
        const container = document.querySelector('#createRoleModal .permissions-list');
        const loading = document.querySelector('#createRoleModal .loading-permissions');
        container.classList.add('d-none');
        loading.classList.remove('d-none');
        loading.innerHTML = `
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading permissions...</span>
            </div>
            <span class="ms-2">Loading permissions...</span>
        `;
    });

    // Reset edit modal when hidden
    const editModal = document.getElementById('editRoleModal');
    editModal.addEventListener('hidden.bs.modal', function() {
        const container = document.querySelector('#editRoleModal .permissions-list');
        const loading = document.querySelector('#editRoleModal .loading-permissions');
        container.classList.add('d-none');
        loading.classList.remove('d-none');
        loading.innerHTML = `
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading permissions...</span>
            </div>
            <span class="ms-2">Loading permissions...</span>
        `;
    });

    // Auto-open modals on page load if needed
    @if(session('open_create_modal'))
        new bootstrap.Modal(document.getElementById('createRoleModal')).show();
    @endif

    @if(session('open_edit_modal'))
        const roleId = '{{ session("open_edit_modal") }}';
        setTimeout(() => {
            const editButton = document.querySelector(`button[data-role-id="${roleId}"]`);
            if (editButton) {
                editButton.click();
            }
        }, 500);
    @endif
});
</script>

<style>
.permission-tags .badge {
    font-size: 0.75em;
}
.permissions-container {
    background-color: #f8f9fa;
}
.form-check-label {
    cursor: pointer;
}
</style>
@endpush