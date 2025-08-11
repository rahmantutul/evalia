@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Plan Details: {{ $plan->name }}</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Name</th>
                    <td>{{ $plan->name }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $plan->description }}</td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>{{ number_format($plan->price, 2) }}</td>
                </tr>
                <tr>
                    <th>Popular</th>
                    <td>{{ $plan->is_popular ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <th>Active</th>
                    <td>{{ $plan->is_active ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <th>Sort Order</th>
                    <td>{{ $plan->sort_order }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('admin.plans.features.edit', $plan) }}" class="btn btn-info">Manage Features</a>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection