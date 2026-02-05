@extends('layouts.admin')
@section('content')
@can('Orders_access')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3>Orders List</h3>
                <div>
              <form action="{{ route('order') }}" method="GET" >
              @csrf
              <div class="d-flex px-4">
                <div class="px-2">
                <label class="form-label">From</label>
                <input type="date" name="startDate" class="form-control">
              </div>
              <div>
                <label class="form-label">TO</label>
                <input type="date" name="endDate" class="form-control">
              </div>
              </div>
              <div class="d-flex justify-content-center pt-1">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
                 </form>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bodered">
                    <tr>
                        <th>SL</th>
                        <th>Order Id</th>
                        <th>Coustomer Id</th>
                        <th>Total</th>
                        <th>Coupon</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Invoice</th>
                    </tr>
                    @forelse ($orders as $index=> $order)
                       <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $order->order_id }}</td>
                        <td>{{ $order->coustomer_id }}</td>
                        <td>{{ $order->total }}</td>
                        <td>{{ $order->coupon }}</td>
                        <td>
                            @if($order->created_at)
                                {{ $order->created_at->diffForHumans() }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if ( $order->status == 0)
                                <span class="badge badge-primary">Placed</span>
                            @elseif ($order->status == 1)
                            <span class="badge badge-success">Processing</span>
                            @elseif ($order->status == 2)
                            <span class="badge badge-warning">Shipped</span>
                            @elseif ($order->status == 3)
                            <span class="badge badge-secondary">Delivered</span>
                            @elseif ($order->status == 4)
                            <span class="badge badge-danger">Cancelled</span>
                            @else
                            <span class="badge badge-light">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('status.change', $order->id) }}" method="POST" class="status-form">
                                @csrf
                                <div class="dropdown">
                                    <button class="bg-transparent border-0 dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        Change Status
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="submit" name="status" value="1" class="dropdown-item status-btn" data-status="1">Processing</button>
                                        <button type="submit" name="status" value="2" class="dropdown-item status-btn" data-status="2">Shipped</button>
                                        <button type="submit" name="status" value="3" class="dropdown-item status-btn" data-status="3">Delivered</button>
                                        <button type="submit" name="status" value="4" class="dropdown-item text-danger status-btn" data-status="4">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                           <td>
                            <a target="_blank" href="{{ route('invoice.print', $order->order_id ) }}" class="btn btn-info">Print</a>
                            <a href="{{ route('invoice', $order->order_id ) }}" class="btn btn-success">Download</a>
                        </td>
                       </tr>
                       @empty
                       <tr>
                        <td colspan="9" class="text-center">
                           No Data Found
                        </td>
                       </tr>
                    @endforelse 

                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2">
        <div class="card">
            <div class="card-header">
                <h3>Order By Month</h3>
            </div>
            <div class="card-body">
                   <div>
              <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-4 pt-2">
      <div class="card">
            <div class="card-header">
                <h3>Order By Day</h3>
            </div>
            <div class="card-body">
                   <div>
              <canvas id="myChart1"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2">
      <div class="card">
            <div class="card-header">
                <h3>Sales By Month</h3>
            </div>
            <div class="card-body">
                   <div>
              <canvas id="myChart2"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection
@section('script')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('myChart');
const orderData =@json($orderData);

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: '# of Orders',
        data: orderData,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<script>
  const ctx1 = document.getElementById('myChart1');
  const days =@json($days);
  const dayWiseData =@json($dayWiseData);
  new Chart(ctx1, {
    type: 'polarArea',
    data: {
      labels: days,
      datasets: [{
        label: '# of Votes',
        data: dayWiseData,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
<script>
  const ctx2 = document.getElementById('myChart2');
const salesData =@json($salesData);

  new Chart(ctx2, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: '# of Orders',
        data: salesData,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<script>
// Handle status change form submission
document.addEventListener('DOMContentLoaded', function() {
    // Handle all status buttons
    const statusBtns = document.querySelectorAll('.status-btn');
    
    statusBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get the parent form
            const form = this.closest('.status-form');
            if (!form) {
                console.error('Form not found for status button');
                return;
            }
            
            // Set the status value in the form
            let statusInput = form.querySelector('input[name="status"]');
            if (!statusInput) {
                // Create hidden input if not exists
                statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                form.appendChild(statusInput);
            }
            statusInput.value = this.dataset.status;
            
            // Close the dropdown manually
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
            }
            
            // Submit the form
            form.submit();
        });
    });
    
    // Check for success/error messages and show alerts
    @if(session('success'))
        // Show success toast/alert
        console.log('Success: {{ session('success') }}');
        // Optionally show a more visible notification
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert('Success: {{ session('success') }}');
        }
    @endif
    
    @if(session('error'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert('Error: {{ session('error') }}');
        }
    @endif
});
</script>

@endsection
