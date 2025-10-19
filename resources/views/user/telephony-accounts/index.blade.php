@extends('user.layouts.app')

@push('styles')
<link href="{{ asset('/') }}assets/css/dashboard.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4 mt-3">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Telephony Accounts</h4>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#telephonyModal" onclick="resetForm()">
                        Add New
                    </button>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <div id="alertContainer"></div>

                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Provider</th>
                                    <th>Username</th>
                                    <th>Base URL</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($telephonyAccounts as $account)
                                <tr id="row-{{ $account->id }}">
                                    <td>{{ $account->id }}</td>
                                    <td>{{ ucfirst($account->provider) }}</td>
                                    <td>{{ $account->username }}</td>
                                    <td>{{ $account->base_url ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning editBtn" 
                                                data-id="{{ $account->id }}" 
                                                data-provider="{{ $account->provider }}" 
                                                data-username="{{ $account->username }}" 
                                                data-base_url="{{ $account->base_url ?? '' }}"
                                                data-bs-toggle="modal" data-bs-target="#telephonyModal">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger deleteBtn" 
                                                data-id="{{ $account->id }}" 
                                                data-username="{{ $account->username }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No telephony accounts found.</td>
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

<!-- Modal for Create/Edit -->
<div class="modal fade" id="telephonyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="telephonyForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="account_id" id="account_id" value="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Telephony Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger d-none"></div>
                    
                    <div class="mb-3">
                        <label for="provider" class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" id="provider" class="form-select" required>
                            <option value="">Select Provider</option>
                            <option value="avaya">Avaya</option>
                            <option value="twilio">Twilio</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <small class="text-muted" id="passwordHelp">Minimum 6 characters</small>
                    </div>
                    <div class="mb-3">
                        <label for="base_url" class="form-label">Base URL (optional)</label>
                        <input type="url" name="base_url" id="base_url" class="form-control" placeholder="https://example.com">
                    </div>
                    <!-- Remove company_id field if not needed -->
                    <input type="hidden" name="company_id" id="company_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Account
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete telephony account for <strong id="deleteUsername"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentAccountId = null;

    // Edit button click handler
    $('.editBtn').on('click', function() {
        const id = $(this).data('id');
        const provider = $(this).data('provider');
        const username = $(this).data('username');
        const base_url = $(this).data('base_url');

        $('#modalTitle').text('Edit Telephony Account');
        $('#telephonyForm').attr('action', "{{ url('telephony-accounts') }}/" + id);
        $('#formMethod').val('PUT');
        $('#account_id').val(id);
        $('#password').removeAttr('required');
        $('#passwordHelp').text('Leave empty if you don\'t want to change password');

        $('#provider').val(provider);
        $('#username').val(username);
        $('#password').val('');
        $('#base_url').val(base_url);
    });

    // Delete button click handler
    $('.deleteBtn').on('click', function() {
        currentAccountId = $(this).data('id');
        const username = $(this).data('username');
        $('#deleteUsername').text(username);
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDelete').on('click', function() {
        if (currentAccountId) {
            $.ajax({
                url: "{{ url('telephony-accounts') }}/" + currentAccountId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        $('#row-' + currentAccountId).remove();
                        $('#deleteModal').modal('hide');
                        
                        // Check if table is empty
                        if ($('tbody tr').length === 0 || ($('tbody tr').length === 1 && $('tbody tr').hasClass('text-center'))) {
                            location.reload();
                        }
                    } else {
                        showAlert(response.message || 'Error deleting account', 'danger');
                    }
                },
                error: function(xhr) {
                    let message = 'Error deleting account';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert(message, 'danger');
                }
            });
        }
    });

    // Form submission
    $('#telephonyForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const url = $(this).attr('action');
        const method = $('#formMethod').val();
        
        console.log('Submitting form to:', url, 'Method:', method, 'Data:', formData);
        
        // Show loading state
        $('#submitBtn').prop('disabled', true);
        $('#submitBtn .spinner-border').removeClass('d-none');

        // Clear previous errors
        $('#formErrors').addClass('d-none').html('');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#telephonyModal').modal('hide');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert(response.message || 'Operation failed', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error response:', xhr.responseJSON);
                console.log('Status:', status);
                console.log('Error:', error);
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorHtml = '<ul class="mb-0">';
                    
                    for (const field in errors) {
                        errorHtml += `<li>${errors[field][0]}</li>`;
                    }
                    errorHtml += '</ul>';
                    
                    $('#formErrors').html(errorHtml).removeClass('d-none');
                } else {
                    let message = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert(message, 'danger');
                }
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false);
                $('#submitBtn .spinner-border').addClass('d-none');
            }
        });
    });

    // Reset modal on close
    $('#telephonyModal').on('hidden.bs.modal', function () {
        resetForm();
    });
});

function resetForm() {
    $('#modalTitle').text('Add Telephony Account');
    $('#telephonyForm').attr('action', "{{ route('user.telephonyAccounts.store') }}");
    $('#formMethod').val('POST');
    $('#account_id').val('');
    $('#telephonyForm')[0].reset();
    $('#formErrors').addClass('d-none').html('');
    $('#password').attr('required', true);
    $('#passwordHelp').text('Password is required');
}

function showAlert(message, type) {
    const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    $('#alertContainer').html(alertHtml);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}
</script>
@endpush