@extends('admin.layouts.app')

@push('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Plans</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a></li>
                        <li class="breadcrumb-item active">Plans</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="table-responsive">
                        <div class="mb-3 d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.plans.preview') }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i> Preview
                            </a>
                            <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i> Create New Plan
                            </a>
                        </div>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Popular</th>
                                    <th>Active</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plans as $plan)
                                <tr>
                                    <td>{{ $plan->name }}</td>
                                    <td>{{ $plan->description }}</td>
                                    <td>${{ number_format($plan->price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $plan->is_popular ? 'success' : 'secondary' }}">
                                            {{ $plan->is_popular ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                            {{ $plan->is_active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>{{ $plan->sort_order }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.plans.features.edit', $plan) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-cogs"></i>
                                            </a>
                                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

    // Enhanced delete confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush