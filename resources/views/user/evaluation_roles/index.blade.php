@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">

                {{-- Header --}}
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-info p-2 rounded-3 me-3 shadow-sm">
                            <i class="fas fa-user-shield text-white fa-lg"></i>
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold text-dark">Evaluation Roles</h4>
                            <p class="text-muted mb-0 small">Define evaluation criteria for different agent levels</p>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary d-flex align-items-center px-4 py-2" 
                                style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#roleModal"
                                onclick="openCreateModal()">
                            <i class="fas fa-plus me-2"></i> Create New Role
                        </button>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                <div class="alert alert-success border-0 m-3 rounded-3">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger border-0 m-3 rounded-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted">Role Name</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">KB</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Policies</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Risks</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Extractions</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Pro</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Skills</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Coop</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Ling</th>
                                    <th class="text-end pe-4 py-3 text-uppercase small fw-bold text-muted">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr class="border-bottom">
                                    <td class="ps-4 fw-bold text-dark">{{ $role->name }}</td>
                                    <td class="text-center">{!! $role->eval_kb ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_policies ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_risks ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_extractions ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_professionalism ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_assessment ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_cooperation ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td class="text-center">{!! $role->eval_linguistic ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' !!}</td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2 pe-3">
                                            <button type="button" 
                                               class="btn btn-sm btn-icon btn-light border shadow-sm edit-role-btn"
                                               data-bs-toggle="modal" data-bs-target="#roleModal"
                                               data-role="{{ json_encode($role) }}">
                                                <i class="fas fa-edit text-warning"></i>
                                            </button>
                                            <form action="{{ route('user.evaluation_roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-light border shadow-sm"
                                                        data-bs-toggle="tooltip" title="Delete"
                                                        onclick="return confirm('Delete this role?')">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fas fa-user-shield fa-4x text-light mb-3 d-block"></i>
                                        <h5 class="fw-bold">No Evaluation Roles Found</h5>
                                        <p class="text-muted">Create roles to restrict evaluation depth for certain agents.</p>
                                        <button type="button" class="btn btn-primary px-4 mt-2 create-role-btn" style="border-radius: 10px;"
                                                data-bs-toggle="modal" data-bs-target="#roleModal">
                                            <i class="fas fa-plus me-1"></i> Create Your First Role
                                        </button>
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

<!-- Evaluation Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 bg-dark text-white rounded-top" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <div>
                    <h5 class="modal-title fw-bold fs-4" id="roleModalLabel" style="color: white !important;">Evaluation Role</h5>
                    <p class="text-white-50 small mb-2">Customize which AI checks to perform for this role</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="roleForm" method="POST">
                @csrf
                <div id="method_field"></div>
                <div class="modal-body p-4">
                    <!-- Role Name Input -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold text-dark mb-2">Role Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                                <i class="fas fa-tag text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 py-2" id="name" name="name" 
                                   placeholder="e.g. Senior Customer Success Agent" required 
                                   style="border-radius: 0 12px 12px 0; font-size: 1rem;">
                        </div>
                    </div>

                    <!-- Evaluation Segments -->
                    <div class="d-flex align-items-center mb-3 mt-4">
                        <hr class="flex-grow-1 text-muted opacity-25">
                        <span class="mx-3 text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Evaluation Criteria</span>
                        <hr class="flex-grow-1 text-muted opacity-25">
                    </div>

                    <div class="row g-3">
                        @foreach([
                            'eval_kb' => ['Knowledge Base', 'Checks if agent answers correctly based on your data.', 'book-reader', 'info'],
                            'eval_policies' => ['Policy Compliance', 'Ensures agent follows company-specific protocols.', 'file-contract', 'primary'],
                            'eval_risks' => ['Risk Assessment', 'Detects potential threats, legal risks, or churn signs.', 'exclamation-triangle', 'danger'],
                            'eval_extractions' => ['Data Extraction', 'Pulls specific entities (IDs, Emails, etc.) from transcript.', 'database', 'warning'],
                            'eval_professionalism' => ['Professionalism', 'Analyzes tone, politeness, and ethical standards.', 'award', 'success'],
                            'eval_assessment' => ['Skills Assessment', 'Evaluates communication and problem-solving skills.', 'clipboard-check', 'indigo'],
                            'eval_cooperation' => ['Cooperation', 'Measures responsiveness and proactive assistance.', 'handshake', 'secondary'],
                            'eval_linguistic' => ['Linguistic Analysis', 'Analyzes language usage, formal speech, and tone depth.', 'language', 'dark']
                        ] as $field => $info)
                        <div class="col-md-6">
                            <div class="form-check form-switch card-check p-3 border rounded-4 h-100 shadow-sm transition-all position-relative">
                                <div class="d-flex align-items-start">
                                    <div class="icon-box rounded-3 bg-{{ $info[3] }} bg-opacity-10 p-2 me-3">
                                        <i class="fas fa-{{ $info[2] }} text-{{ $info[3] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form-check-label d-block fw-bold text-dark mb-0 cursor-pointer" for="{{ $field }}">
                                            {{ $info[0] }}
                                        </label>
                                        <p class="text-muted small mb-0 mt-1" style="line-height: 1.2; font-size: 0.75rem;">
                                            {{ $info[1] }}
                                        </p>
                                    </div>
                                    <div class="ms-2">
                                        <input class="form-check-input mt-1" type="checkbox" role="switch" id="{{ $field }}" name="{{ $field }}" value="1" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-2">
                    <button type="button" class="btn btn-link text-muted fw-semibold text-decoration-none px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" id="submitBtn" 
                            style="border-radius: 12px; font-weight: 600;">Save Role Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-icon { width:32px; height:32px; display:flex; align-items:center; justify-content:center; padding:0; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.02); }
    .card-check { cursor: pointer; transition: all 0.2s; position: relative; border: 1.5px solid #edf2f7 !important; }
    .card-check:hover { border-color: #cbd5e1 !important; background-color: #fbfcfd; }
    .card-check:has(.form-check-input:checked) { border-color: #0d6efd !important; background-color: #f0f7ff; }
    .icon-box { width: 38px; height: 38px; display: flex; align-items:center; justify-content:center; font-size: 1.1rem; }
    .form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }
    .transition-all { transition: all 0.2s ease-in-out; }
    .cursor-pointer { cursor: pointer; }
    .bg-indigo { background-color: #6610f2; }
    .text-indigo { color: #6610f2; }
    .bg-indigo.bg-opacity-10 { background-color: rgba(102, 16, 242, 0.1) !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleForm = document.getElementById('roleForm');
        const methodField = document.getElementById('method_field');
        const submitBtn = document.getElementById('submitBtn');
        const modalLabel = document.getElementById('roleModalLabel');
        const nameInput = document.getElementById('name');
        const checkboxes = [
            'eval_kb', 'eval_policies', 'eval_risks', 'eval_extractions', 
            'eval_professionalism', 'eval_assessment', 'eval_cooperation', 'eval_linguistic'
        ];

        // Handle Create Button
        document.querySelectorAll('.create-role-btn, #openCreateBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                modalLabel.innerText = 'Create New Evaluation Role';
                roleForm.action = "{{ route('user.evaluation_roles.store') }}";
                methodField.innerHTML = '';
                nameInput.value = '';
                checkboxes.forEach(c => document.getElementById(c).checked = true);
                submitBtn.innerText = 'Create Role';
            });
        });

        // Handle Edit Button
        document.querySelectorAll('.edit-role-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const role = JSON.parse(this.getAttribute('data-role'));
                modalLabel.innerText = 'Edit Evaluation Role: ' + role.name;
                roleForm.action = "/user/evaluation-roles/" + role.id;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                nameInput.value = role.name;
                
                checkboxes.forEach(c => {
                    document.getElementById(c).checked = !!role[c];
                });
                
                submitBtn.innerText = 'Update Role';
            });
        });
        
        // Make the whole card clickable for the switch
        document.querySelectorAll('.card-check').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const input = this.querySelector('input');
                    input.checked = !input.checked;
                }
            });
        });
    });
</script>
@endsection

