@extends('user.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center m-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('criteria.index') }}" class="btn btn-light btn-sm mr-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="mb-0 text-dark">Create New Criteria</h5>
                            <p class="text-muted mb-0">Add criteria for matching sales people with clients</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('criteria.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-600">Criteria Title</label>
                            <input type="text" class="form-control" name="title" 
                                   placeholder="e.g., Budget Above 10M" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Description</label>
                            <textarea class="form-control" name="description" rows="3"
                                      placeholder="Describe this criteria..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Criteria Type</label>
                            <select class="form-control" name="type" required>
                                <option value="">Select Type</option>
                                <option value="budget">Budget Range</option>
                                <option value="location">Location</option>
                                <option value="property_type">Property Type</option>
                                <option value="client_type">Client Type</option>
                                <option value="requirements">Special Requirements</option>
                            </select>
                        </div>

                        <div class="form-group mb-0 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-check mr-2"></i>Create Criteria
                            </button>
                            <a href="{{ route('criteria.index') }}" class="btn btn-light px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection