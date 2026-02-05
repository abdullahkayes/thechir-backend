@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Distributor Points Management</h4>
                </div>
                <div class="card-body">
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Single Form to Add Distributor Point -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Distributor Point</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Purpose:</strong> Add a new distributor point with dedicated API keys for shipping calculations from the nearest location.
                                    </div>

                                    <form id="apiKeysForm" method="POST" action="{{ route('admin.update-api-keys') }}">
                                        @csrf
                                        
                                        <!-- API Keys Section -->
                                        <h6 class="text-primary mb-3"><i class="fas fa-key"></i> API Keys</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="google_maps_api_key">Google Maps API Key</label>
                                                    <input type="text" class="form-control" id="google_maps_api_key"
                                                           name="google_maps_api_key"
                                                           placeholder="Enter Google Maps API Key"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="locationiq_api_key">LocationIQ API Key</label>
                                                    <input type="text" class="form-control" id="locationiq_api_key"
                                                           name="locationiq_api_key" 
                                                           placeholder="Enter LocationIQ API Key"
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        <!-- Location Section -->
                                        <h6 class="text-primary mb-3"><i class="fas fa-map-marker-alt"></i> Location Details</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="city">City</label>
                                                    <input type="text" class="form-control" id="city"
                                                           name="city" 
                                                           placeholder="Enter City"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="state">State</label>
                                                    <input type="text" class="form-control" id="state"
                                                           name="state" 
                                                           placeholder="Enter State"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="zip_code">ZIP Code</label>
                                                    <input type="text" class="form-control" id="zip_code"
                                                           name="zip_code" 
                                                           placeholder="Enter ZIP Code"
                                                           required>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add Distributor Point
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Display existing distributor points -->
                    @if(isset($distributorPoints) && !empty($distributorPoints))
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Existing Distributor Points</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Address</th>
                                                    <th>API Index</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($distributorPoints as $point)
                                                <tr>
                                                    <td>{{ $point->id }}</td>
                                                    <td>{{ $point->name }}</td>
                                                    <td>{{ $point->address }}</td>
                                                    <td>{{ $point->id }}</td>
                                                    <td>
                                                        @if($point->status === 'active')
                                                            <span class="badge badge-success">Active</span>
                                                        @else
                                                            <span class="badge badge-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.distributor-points.edit', $point->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('admin.distributor-points.destroy', $point->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this distributor point?')">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
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
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
