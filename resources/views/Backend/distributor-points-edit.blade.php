@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Distributor Point</h4>
                    <a href="{{ route('admin.distributor-points.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="editForm" method="POST" action="{{ route('admin.distributor-points.update', $distributorPoint) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name *</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ $distributorPoint->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                           value="{{ $distributorPoint->city }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State *</label>
                                    <input type="text" class="form-control" id="state" name="state"
                                           value="{{ $distributorPoint->state }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zip_code">ZIP Code *</label>
                                    <input type="text" class="form-control" id="zip_code" name="zip_code"
                                           value="{{ $distributorPoint->zip_code }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active">Status</label>
                                    <select class="form-control" id="is_active" name="is_active">
                                        <option value="1" {{ $distributorPoint->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $distributorPoint->status !== 'active' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ $distributorPoint->address }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="number" step="any" class="form-control" id="latitude" name="latitude"
                                           value="{{ $distributorPoint->latitude }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="number" step="any" class="form-control" id="longitude" name="longitude"
                                           value="{{ $distributorPoint->longitude }}">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- API Keys Section -->
                        <h6 class="text-primary mb-3"><i class="fas fa-key"></i> API Keys (Optional)</h6>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Leave these fields empty to keep existing API keys unchanged.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="google_maps_api_key">Google Maps API Key</label>
                                    <input type="text" class="form-control" id="google_maps_api_key" name="google_maps_api_key"
                                           placeholder="Leave empty to keep existing"
                                           value="{{ old('google_maps_api_key') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="locationiq_api_key">LocationIQ API Key</label>
                                    <input type="text" class="form-control" id="locationiq_api_key" name="locationiq_api_key"
                                           placeholder="Leave empty to keep existing"
                                           value="{{ old('locationiq_api_key') }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Distributor Point
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection