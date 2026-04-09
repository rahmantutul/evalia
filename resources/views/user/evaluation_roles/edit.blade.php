@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header bg-white py-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning p-2 rounded-3 me-3">
                            <i class="fas fa-edit text-white shadow-sm"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">Edit Evaluation Role</h4>
                            <p class="text-muted mb-0">Modify criteria for <strong>{{ $role->name }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-5 pb-5">
                    <form action="{{ route('user.evaluation_roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Role Name</label>
                            <input type="text" name="name" class="form-control form-control-lg border-2" 
                                   value="{{ $role->name }}" required
                                   style="border-radius: 12px; border-color: #f1f4f9; background: #f8fafc;">
                        </div>

                        <h6 class="fw-bold text-muted mb-3 text-uppercase small" style="letter-spacing: 1px;">Evaluation Criteria</h6>
                        
                        <div class="row g-3">
                            @php
                                $criteria = [
                                    ['id' => 'eval_kb', 'label' => 'Knowledge Base Analysis', 'desc' => 'Verify agent answers against company KB'],
                                    ['id' => 'eval_policies', 'label' => 'Policy Compliance', 'desc' => 'Check if agent follows company policies'],
                                    ['id' => 'eval_risks', 'label' => 'Risk Assessment', 'desc' => 'Identify potential risks and red flags'],
                                    ['id' => 'eval_extractions', 'label' => 'Data Extractions', 'desc' => 'Extract specific data points from the call'],
                                    ['id' => 'eval_professionalism', 'label' => 'Professionalism', 'desc' => 'Rate tone, politeness, and formality'],
                                    ['id' => 'eval_assessment', 'label' => 'Skills Assessment', 'desc' => 'Evaluate technical skills and problem solving'],
                                    ['id' => 'eval_cooperation', 'label' => 'Cooperation', 'desc' => 'Assess empathy and proactive assistance'],
                                    ['id' => 'eval_linguistic', 'label' => 'Linguistic Analysis', 'desc' => 'Deep analysis of language, formal speech and tone'],
                                ];
                            @endphp

                            @foreach($criteria as $item)
                            <div class="col-md-6">
                                <div class="form-check form-switch p-3 border rounded-4 bg-light d-flex align-items-center justify-content-between h-100">
                                    <div class="me-3">
                                        <label class="form-check-label fw-bold d-block mb-0" for="{{ $item['id'] }}">{{ $item['label'] }}</label>
                                        <small class="text-muted d-block" style="font-size: 11px;">{{ $item['desc'] }}</small>
                                    </div>
                                    <input class="form-check-input ms-0" type="checkbox" name="{{ $item['id'] }}" id="{{ $item['id'] }}" 
                                           {{ $role->{$item['id']} ? 'checked' : '' }} style="width: 40px; height: 20px;">
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-5 d-flex gap-3">
                            <button type="submit" class="btn btn-warning text-white px-5 py-2 fw-bold" style="border-radius: 12px;">
                                Update Role
                            </button>
                            <a href="{{ route('user.evaluation_roles.index') }}" class="btn btn-light px-4 py-2 text-muted fw-bold" style="border-radius: 12px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check-input:checked { background-color: #f59e0b; border-color: #f59e0b; }
    .form-control:focus { background: white !important; border-color: #f59e0b !important; box-shadow: 0 0 0 0.25rem rgba(245,158,11,0.1); }
</style>
@endsection
