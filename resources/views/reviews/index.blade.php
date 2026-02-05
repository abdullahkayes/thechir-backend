@extends('layouts.admin')

@section('content')
<style>
.rating {
    display: inline-flex;
    align-items: center;
}

.rating i {
    font-size: 14px;
    margin-right: 2px;
}

.icon-sm {
    width: 14px;
    height: 14px;
}

.icon-xs {
    width: 12px;
    height: 12px;
}

/* DataTable arrow size fix */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_info {
    font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_filter input {
    font-size: 0.875rem;
}

.avatar-initial {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.badge-outline-primary {
    border: 1px solid #007bff;
    color: #007bff;
    background-color: transparent;
}

.review-text p {
    margin-bottom: 0;
    line-height: 1.4;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

@media (max-width: 768px) {
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .avatar-initial {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }

    .rating i {
        font-size: 12px;
    }

    /* Stack action buttons vertically on mobile */
    .btn-group.btn-group-sm {
        flex-direction: column;
        width: 100%;
    }

    .btn-group.btn-group-sm .btn {
        margin-bottom: 0.25rem;
        width: 100%;
        justify-content: flex-start;
    }

    .btn-group.btn-group-sm .btn:last-child {
        margin-bottom: 0;
    }
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Reviews Management</h6>
                    <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary btn-sm">
                        <i data-feather="plus" class="icon-xs mr-1"></i> Add New Review
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Customer Name</th>
                                    <th class="text-center">Rating</th>
                                    <th>Product</th>
                                    <th>Review</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                <tr>
                                    <td class="text-center">{{ $review->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm mr-2">
                                                <span class="avatar-initial rounded-circle">{{ substr($review->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $review->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i data-feather="star" class="icon-sm {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" fill="currentColor"></i>
                                            @endfor
                                            <small class="text-muted ml-1">({{ $review->rating }}/5)</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline-primary">{{ $review->product_name ?: 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="review-text" style="max-width: 250px;">
                                            <p class="mb-0 text-truncate" title="{{ $review->text }}">
                                                {{ Str::limit($review->text, 60) }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($review->product_image)
                                            <img src="{{ asset($review->product_image) }}" alt="Product"
                                                 class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                 onclick="window.open(this.src, '_blank')">
                                        @else
                                            <span class="text-muted small">No Image</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-outline-info btn-sm" title="View">
                                                <i data-feather="eye" class="icon-xs mr-1"></i>View
                                            </a>
                                            <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                                <i data-feather="edit" class="icon-xs mr-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i data-feather="trash-2" class="icon-xs mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <!-- <div class="d-flex justify-content-center mt-4">
                        {{ $reviews->links() }}
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets') }}/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="{{ asset('assets') }}/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "paging": true,
        "responsive": true,
        "language": {
            "search": "Search reviews:",
            "lengthMenu": "Show _MENU_ reviews per page",
            "zeroRecords": "No reviews found",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No reviews available",
            "infoFiltered": "(filtered from _MAX_ total reviews)"
        },
        "columnDefs": [
            { "orderable": false, "targets": [5, 7] }, // Disable sorting for Image and Actions columns
            { "className": "text-center", "targets": [0, 2, 5, 6, 7] }
        ],
        "initComplete": function() {
            // Reinitialize Feather icons after DataTable is created
            feather.replace();
        },
        "drawCallback": function() {
            // Reinitialize Feather icons after each table draw (pagination, search, etc.)
            feather.replace();
        }
    });
});
</script>
@endsection