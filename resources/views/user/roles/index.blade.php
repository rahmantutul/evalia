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
                          @if(session('user.role.name') !== 'Supervisor')
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                            <i class="fas fa-plus me-1"></i> Create Role
                        </button>
                        @endif
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
                                        <td>
                                            @php
                                                $permissions = isset($role['permissions']) ? (is_array($role['permissions']) ? $role['permissions'] : $role['permissions']->toArray()) : [];
                                            @endphp
                                            @if(count($permissions) > 0)
                                                <div class="permission-tags">
                                                    @foreach(array_slice($permissions, 0, 3) as $permission)
                                                        <span class="badge bg-light text-dark border me-1 mb-1">
                                                            {{ $permission['name'] ?? $permission }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($permissions) > 3)
                                                        <span class="badge bg-secondary">
                                                            +{{ count($permissions) - 3 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No permissions</span>
                                            @endif
                                        </td>
                                        <td>
                                             <div class="btn-group">
                                                   @if(session('user.role.name') !== 'Supervisor')
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                                        data-role-id="{{ $role['id'] }}"
                                                        data-role-name="{{ $role['name'] }}"
                                                        onclick="editRole(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete('{{ $role['id'] }}', '{{ $role['name'] }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @else
                                                <span class="badge bg-light text-dark">View Only</span>
                                                @endif
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
                                                 @if(session('user.role.name') !== 'Supervisor')
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                                                    Create Role
                                                </button>
                                                @endif
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
    <div class="modal-dialog modal-xl">
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
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span>Permissions <span class="text-danger">*</span></span>
                                    <div class="d-flex align-items-center">
                                        <div class="input-group input-group-sm me-2" style="width: 200px;">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" placeholder="Search..." onkeyup="filterPermissions('create', this.value)">
                                        </div>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" id="selectAllCreate" onclick="toggleAllPermissions('create')">
                                            <label class="form-check-label" for="selectAllCreate">Select All</label>
                                        </div>
                                    </div>
                                </label>
                                <div class="permissions-container border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center loading-permissions py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="mt-2 text-muted">Loading permissions...</div>
                                    </div>
                                    <div class="permissions-list d-none row g-2">
                                        <!-- Permissions will be loaded here via JavaScript -->
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Select individual permissions to assign to this role</small>
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
    <div class="modal-dialog modal-xl">
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
                                <p class="form-control-static d-none" id="editRoleId"></p>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span>Permissions <span class="text-danger">*</span></span>
                                    <div class="d-flex align-items-center">
                                        <div class="input-group input-group-sm me-2" style="width: 200px;">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control" placeholder="Search..." onkeyup="filterPermissions('edit', this.value)">
                                        </div>
                                        <div class="form-check form-check-inline mb-0">
                                            <input class="form-check-input" type="checkbox" id="selectAllEdit" onclick="toggleAllPermissions('edit')">
                                            <label class="form-check-label" for="selectAllEdit">Select All</label>
                                        </div>
                                    </div>
                                </label>
                                <div class="permissions-container border rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center loading-permissions py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <div class="mt-2 text-muted">Loading permissions...</div>
                                    </div>
                                    <div class="permissions-list d-none row g-2">
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
function renderPermissions(container, permissions, selectedPermissionNames) {
    if (permissions.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted py-3">No permissions available</div>';
        return;
    }

    let html = '';
    
    const modalType = container.closest('.modal').id.includes('create') ? 'Create' : 'Edit';
    const allNames = permissions.map(p => p.name);
    const selectAllCheckbox = document.getElementById(`selectAll${modalType}`);
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = allNames.length > 0 && allNames.every(name => selectedPermissionNames.includes(name));
    }

    // Sort permissions alphabetically for the list
    const sortedPermissions = [...permissions].sort((a, b) => a.name.localeCompare(b.name));

    sortedPermissions.forEach(permission => {
        const isChecked = selectedPermissionNames.includes(permission.name);
        html += `
            <div class="col-md-6 permission-item border-bottom py-1">
                <div class="form-check d-flex align-items-center mb-0 px-3 py-1">
                    <input class="form-check-input mt-0" type="checkbox" name="permissions[]" 
                           value="${permission.name}" id="perm_${modalType}_${permission.id}" ${isChecked ? 'checked' : ''}
                           onclick="updateSelectAllState('${modalType.toLowerCase()}')">
                    <label class="form-check-label ms-3 cursor-pointer w-100 mb-0" for="perm_${modalType}_${permission.id}">
                        <span class="fs-13 fw-medium">${permission.name}</span>
                    </label>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Toggle all permissions
function toggleAllPermissions(type) {
    const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
    const selectAll = document.getElementById(`selectAll${capitalizedType}`);
    const checkboxes = document.querySelectorAll(`#${type}RoleModal .form-check-input[name="permissions[]"]`);
    
    checkboxes.forEach(cb => {
        if (!cb.closest('.permission-item').classList.contains('d-none')) {
            cb.checked = selectAll.checked;
        }
    });
}

// Update "Select All" checkbox state when individual checkboxes are clicked
function updateSelectAllState(type) {
    const capitalizedType = type.charAt(0).toUpperCase() + type.slice(1);
    const selectAll = document.getElementById(`selectAll${capitalizedType}`);
    const checkboxes = Array.from(document.querySelectorAll(`#${type}RoleModal .form-check-input[name="permissions[]"]`))
        .filter(cb => !cb.closest('.permission-item').classList.contains('d-none'));
    
    if (checkboxes.length === 0) return;
    
    const allChecked = checkboxes.every(cb => cb.checked);
    if (selectAll) selectAll.checked = allChecked;
}

// Filter permissions by search input
function filterPermissions(type, query) {
    const q = query.toLowerCase();
    const items = document.querySelectorAll(`#${type}RoleModal .permission-item`);
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(q)) {
            item.classList.remove('d-none');
        } else {
            item.classList.add('d-none');
        }
    });
    
    updateSelectAllState(type);
}

// Edit role
function editRole(button) {
    const roleId = button.getAttribute('data-role-id');
    const roleName = button.getAttribute('data-role-name');
    const roleDescription = button.getAttribute('data-role-description') || '';

    // Set form values
    document.getElementById('editRoleName').value = roleName;
    document.getElementById('editRoleId').textContent = roleId;
    
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
            const currentPermissions = data.role && data.role.permissions ? 
                data.role.permissions.map(p => p.name) : [];
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
    border: 1px solid #e9ebec !important;
}
.form-check-label {
    cursor: pointer;
}
.fs-11 { font-size: 11px; }
.fs-13 { font-size: 13px; }
.cursor-pointer { cursor: pointer; }
.hover-shadow-sm:hover {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    background-color: #fff !important;
}
.permission-item .card-radio {
    transition: all 0.2s ease;
}
.permission-item .form-check-input:checked + label {
    color: #405189;
}
.permission-group-header h6 {
    letter-spacing: 1px;
}
</style>
@endpush