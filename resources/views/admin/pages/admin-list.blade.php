@extends('admin.layouts.app')
@push('styles')
    <style>
        .thumb-md {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
   <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Contacts</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item active">Admin List</li>
                    </ol>
                </div>                                
            </div><!--end page-title-box-->
        </div><!--end col-->
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">                      
                            <h4 class="card-title">Admin Users</h4>                      
                        </div><!--end col-->
                        <div class="col-auto">
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-outline-primary">Add New Admin</a>
                        </div>
                    </div>  <!--end row-->                                  
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ( $dataList as $dataInfo)
                                    <tr>
                                        <td>
                                            @if($dataInfo->image)
                                                <img src="{{ asset('storage/'.$dataInfo->image) }}" alt="" class="rounded-circle thumb-md me-1 d-inline">
                                            @else
                                                <img src="{{ asset('assets/images/default-avatar.jpg') }}" alt="" class="rounded-circle thumb-md me-1 d-inline">
                                            @endif
                                            {{ $dataInfo->name }}
                                        </td>
                                        <td>{{ $dataInfo->email }}</td>
                                        <td> @if($dataInfo->roleId == 1) Admin @else Sub admin @endif</td>
                                        <td class="text-end">                                                       
                                            <a class="btn btn-warning btn-sm" href="{{ route('admin.admins.edit', $dataInfo->id) }}"><i class="las la-pen text-white"></i></a>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $dataInfo->id }}"><i class="las la-trash-alt text-white"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No Data Found</td>
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

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            
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
                    var deleteForm = $('#delete-form');
                    deleteForm.attr('action', "{{ route('admin.admins.index') }}/" + id);
                    deleteForm.submit();
                }
            });
        });

        // Show success/error messages
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @elseif(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
@endpush