@extends('user.layouts.app')
@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row mb-4 mt-4">
        <div class="col">
            <h4 class="page-title mb-0">
                <i class="fas fa-lock text-primary me-2"></i> Roles &amp; Permissions
            </h4>
            <ol class="breadcrumb mt-1">
                <li class="breadcrumb-item"><a href="{{ route('user.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </div>
        <div class="col-auto d-flex align-items-center">
            @can('roles.create')
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                <i class="fas fa-plus me-1"></i> New Role
            </button>
            @endcan
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Role Cards Grid --}}
    <div class="row g-4">
        @forelse($roles as $role)
        @php
            $badgeColors = ['Admin'=>'danger','Accounts'=>'primary','HR'=>'success'];
            $badgeColor  = $badgeColors[$role->name] ?? 'secondary';
            $perms       = $role->permissions;
            $permGroups  = [];
            foreach ($perms as $p) {
                $cat = explode('.', $p->name)[0];
                $permGroups[$cat][] = $p->name;
            }
        @endphp
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100 role-card">
                <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center gap-3">
                            <div class="role-avatar bg-{{ $badgeColor }} bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-shield-alt text-{{ $badgeColor }} fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">{{ $role->name }}</h5>
                                <small class="text-muted">{{ $perms->count() }} permission{{ $perms->count() !== 1 ? 's' : '' }}</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                @can('roles.edit')
                                <li>
                                    <a class="dropdown-item" href="#"
                                       data-bs-toggle="modal" data-bs-target="#editRoleModal"
                                       data-role-id="{{ $role->id }}"
                                       data-role-name="{{ $role->name }}"
                                       data-role-permissions="{{ $perms->pluck('name')->toJson() }}"
                                       onclick="openEditModal(this)">
                                        <i class="fas fa-edit me-2 text-primary"></i> Edit Permissions
                                    </a>
                                </li>
                                @endcan
                                @can('roles.delete')
                                @if($role->name !== 'Admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#"
                                       onclick="confirmDelete('{{ $role->id }}', '{{ $role->name }}')">
                                        <i class="fas fa-trash me-2"></i> Delete Role
                                    </a>
                                </li>
                                @endif
                                @endcan
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-1 px-4 pb-4">
                    {{-- Permission groups summary --}}
                    @if(count($permGroups) > 0)
                        <div class="mt-3">
                            @foreach($permGroups as $cat => $catPerms)
                            @php
                                $catIcons = [
                                    'dashboard'=>'tachometer-alt','roles'=>'lock','users'=>'users',
                                    'companies'=>'building','agents'=>'headset',
                                    'knowledgebase'=>'brain','tasks'=>'phone-alt','reports'=>'chart-bar'
                                ];
                                $catColors = [
                                    'dashboard'=>'primary','roles'=>'danger','users'=>'info',
                                    'companies'=>'warning','agents'=>'success',
                                    'knowledgebase'=>'purple','tasks'=>'secondary','reports'=>'dark'
                                ];
                                $icon  = $catIcons[$cat]  ?? 'key';
                                $color = $catColors[$cat] ?? 'secondary';
                            @endphp
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} p-2 rounded-2">
                                        <i class="fas fa-{{ $icon }}" style="font-size:11px;"></i>
                                    </span>
                                    <span class="fw-semibold text-dark" style="font-size:0.85rem;">{{ ucfirst($cat) }}</span>
                                </div>
                                <div class="d-flex flex-wrap gap-1 justify-content-end" style="max-width: 55%;">
                                    @foreach($catPerms as $p)
                                    @php $action = explode('.', $p)[1] ?? $p; @endphp
                                    <span class="badge bg-light text-dark border" style="font-size:10px;">{{ $action }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-ban d-block mb-1 opacity-25 fs-4"></i>
                            <small>No permissions assigned</small>
                        </div>
                    @endif
                </div>
                @can('roles.edit')
                <div class="card-footer bg-transparent border-0 px-4 pb-3 pt-0">
                    <button class="btn btn-outline-primary btn-sm w-100 rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#editRoleModal"
                        data-role-id="{{ $role->id }}"
                        data-role-name="{{ $role->name }}"
                        data-role-permissions="{{ $perms->pluck('name')->toJson() }}"
                        onclick="openEditModal(this)">
                        <i class="fas fa-sliders-h me-1"></i> Manage Permissions
                    </button>
                </div>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shield-alt fa-3x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">No Roles Found</h5>
                    <p class="text-muted mb-3">Get started by creating your first role.</p>
                    @can('roles.create')
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="fas fa-plus me-1"></i> Create First Role
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @endforelse
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- CREATE MODAL --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('roles.store') }}" method="POST" id="createRoleForm" class="m-0">
                @csrf
                <div class="modal-header bg-white border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-plus-circle text-primary me-2"></i> Create New Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name"
                               placeholder="e.g. Content Manager, QA Lead…" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Permissions</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                    onclick="toggleAll('create', true)">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                    onclick="toggleAll('create', false)">Clear All</button>
                        </div>
                    </div>

                    <div id="createPermissionsContainer" class="row g-3 pb-5">
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-primary"></div>
                            <p class="text-muted mt-2">Loading permissions…</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- EDIT MODAL --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form id="editRoleForm" method="POST" class="m-0">
                @csrf
                @method('PUT')
                <div class="modal-header bg-white border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-sliders-h text-primary me-2"></i>
                        Edit Role: <span id="editRoleDisplayName" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editRoleName" name="name" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Permissions</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                    onclick="toggleAll('edit', true)">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                    onclick="toggleAll('edit', false)">Clear All</button>
                        </div>
                    </div>

                    <div id="editPermissionsContainer" class="row g-3 pb-5">
                        <div class="col-12 text-center py-4">
                            <div class="spinner-border text-primary"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- DELETE MODAL --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> Delete Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger opacity-50 mb-3"></i>
                <p class="mb-1">You are about to permanently delete the role:</p>
                <h5 class="fw-bold" id="deleteRoleName"></h5>
                <p class="text-danger small">This action cannot be undone and will remove this role from all users.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRoleForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-trash me-1"></i> Delete Role
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Config ─────────────────────────────────────────────────────────────────
const PERMISSIONS_URL = '{{ route("roles.permissions") }}';
const ROLES_SHOW_URL  = '{{ route("roles.show", ":id") }}';
const ROLES_UPDATE_URL= '{{ route("roles.update", ":id") }}';
const ROLES_DELETE_URL= '{{ route("roles.destroy", ":id") }}';

const CAT_META = {
    dashboard:     { label: 'Dashboard',          icon: 'tachometer-alt', color: 'primary'   },
    roles:         { label: 'Roles & Permissions', icon: 'lock',           color: 'danger'    },
    users:         { label: 'Users',              icon: 'users',           color: 'info'      },
    companies:     { label: 'Companies',          icon: 'building',        color: 'warning'   },
    agents:        { label: 'Agents',             icon: 'headset',         color: 'success'   },
    knowledgebase: { label: 'Knowledge Base',     icon: 'brain',           color: 'purple'    },
    tasks:         { label: 'Tasks / Calls',      icon: 'phone-alt',       color: 'secondary' },
    reports:       { label: 'Reports',            icon: 'chart-bar',       color: 'dark'      },
};

let cachedGrouped = null;

// ── Fetch grouped permissions (cached) ─────────────────────────────────────
async function fetchGrouped() {
    if (cachedGrouped) return cachedGrouped;
    const res  = await fetch(PERMISSIONS_URL);
    const data = await res.json();
    cachedGrouped = data.grouped;
    return cachedGrouped;
}

// ── Render grouped permission checkboxes ───────────────────────────────────
function renderGrouped(containerId, grouped, selected = []) {
    const container = document.getElementById(containerId);
    let html = '';

    for (const [cat, group] of Object.entries(grouped)) {
        const meta   = CAT_META[cat] || { label: cat, icon: 'key', color: 'secondary' };
        const perms  = group.permissions || [];
        const allIn  = perms.every(p => selected.includes(p.name));

        html += `
        <div class="col-md-6 col-xl-4">
            <div class="card border rounded-3 h-100">
                <div class="card-header bg-${meta.color} bg-opacity-10 border-0 py-2 px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-${meta.icon} text-${meta.color}"></i>
                        <span class="fw-bold text-dark" style="font-size:0.85rem;">${meta.label}</span>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="cat_${containerId}_${cat}"
                               ${allIn ? 'checked' : ''}
                               onchange="toggleCategory('${containerId}', '${cat}', this.checked)">
                        <label class="form-check-label small text-muted" for="cat_${containerId}_${cat}">All</label>
                    </div>
                </div>
                <div class="card-body py-2 px-3">
                    ${perms.map(p => {
                        const action  = p.name.split('.')[1] ?? p.name;
                        const checked = selected.includes(p.name) ? 'checked' : '';
                        return `
                        <div class="form-check py-1 border-bottom last-no-border perm-item" data-category="${cat}">
                            <input class="form-check-input perm-cb" type="checkbox" name="permissions[]"
                                   value="${p.name}" id="p_${containerId}_${p.id}" ${checked}
                                   onchange="syncCatToggle('${containerId}', '${cat}')">
                            <label class="form-check-label w-100 cursor-pointer" for="p_${containerId}_${p.id}">
                                <span class="fw-medium text-dark" style="font-size:0.82rem;">${action}</span>
                                <span class="d-block text-muted" style="font-size:0.72rem;">${p.name}</span>
                            </label>
                        </div>`;
                    }).join('')}
                </div>
            </div>
        </div>`;
    }

    container.innerHTML = html;
}

// ── Toggle entire category ─────────────────────────────────────────────────
function toggleCategory(containerId, cat, state) {
    document.querySelectorAll(`#${containerId} .perm-item[data-category="${cat}"] .perm-cb`)
        .forEach(cb => cb.checked = state);
}

// ── Sync the "All" toggle when individual checkboxes change ───────────────
function syncCatToggle(containerId, cat) {
    const boxes  = [...document.querySelectorAll(`#${containerId} .perm-item[data-category="${cat}"] .perm-cb`)];
    const allChk = document.getElementById(`cat_${containerId}_${cat}`);
    if (allChk) allChk.checked = boxes.length > 0 && boxes.every(b => b.checked);
}

// ── Select / Clear All ────────────────────────────────────────────────────
function toggleAll(type, state) {
    const id = type === 'create' ? 'createPermissionsContainer' : 'editPermissionsContainer';
    document.querySelectorAll(`#${id} .perm-cb`).forEach(cb => cb.checked = state);
    document.querySelectorAll(`#${id} [id^="cat_"]`).forEach(cb => cb.checked = state);
}

// ── Open CREATE modal ─────────────────────────────────────────────────────
document.getElementById('createRoleModal').addEventListener('show.bs.modal', async () => {
    const container = document.getElementById('createPermissionsContainer');
    container.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary"></div></div>';
    const grouped = await fetchGrouped();
    renderGrouped('createPermissionsContainer', grouped, []);
    document.getElementById('createRoleForm').reset();
});

// ── Open EDIT modal ───────────────────────────────────────────────────────
function openEditModal(btn) {
    const roleId   = btn.getAttribute('data-role-id');
    const roleName = btn.getAttribute('data-role-name');
    const selected = JSON.parse(btn.getAttribute('data-role-permissions') || '[]');

    document.getElementById('editRoleDisplayName').textContent = roleName;
    document.getElementById('editRoleName').value = roleName;
    document.getElementById('editRoleForm').action = ROLES_UPDATE_URL.replace(':id', roleId);

    const container = document.getElementById('editPermissionsContainer');
    container.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary"></div></div>';

    fetchGrouped().then(grouped => {
        renderGrouped('editPermissionsContainer', grouped, selected);
    });
}

// ── Confirm DELETE ────────────────────────────────────────────────────────
function confirmDelete(roleId, roleName) {
    document.getElementById('deleteRoleName').textContent = roleName;
    document.getElementById('deleteRoleForm').action = ROLES_DELETE_URL.replace(':id', roleId);
    new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
}
</script>

<style>
.role-card { transition: box-shadow 0.2s; }
.role-card:hover { box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08) !important; }
.role-avatar { width: 52px; height: 52px; display: flex; align-items: center; justify-content: center; }
.cursor-pointer { cursor: pointer; }
.last-no-border:last-child { border-bottom: none !important; }
.text-purple { color: #000 !important; }
.bg-purple   { background-color: #eee7f8ff !important; }
</style>
@endpush