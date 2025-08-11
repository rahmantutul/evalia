
@extends('admin.layouts.app')
@push('styles')


@endpush

@section('content')
<div class="container-fluid">
   <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box d-md-flex justify-content-md-between align-items-center">
                <h4 class="page-title">Features</h4>
                <div class="">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Creative AI</a>
                        </li><!--end nav-item-->
                        <li class="breadcrumb-item active">Features</li>
                    </ol>
                </div>                            
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.features.create') }}" class="btn-sm btn btn-primary mb-3 float-end">Create New Feature</a>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Key</th>
                            <th>Type</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($features as $feature)
                        <tr>
                            <td>{{ $feature->name }}</td>
                            <td>{{ $feature->key }}</td>
                            <td>{{ $feature->type }}</td>
                            <td>{{ $feature->sort_order }}</td>
                            <td>
                                <a href="{{ route('admin.features.edit', $feature) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.features.destroy', $feature) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
