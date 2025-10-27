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
                        <td>{{ $order->created_at->diffForHumans() }}</td>
                        <td>
                            @if ( $order->status == 0)
                                <span type="badge" class="badge badge-primary">Placed</span>
                            @elseif ($order->status == 1)
                            <span type="badge" class="badge badge-success">Prossesing</span>
                            @elseif ($order->status == 2)
                            <span type="badge" class="badge badge-warning">Shiped</span>
                            @elseif ($order->status == 3)
                            <span class="badge badge-secondary">Delevired</span>
                            @elseif ($order->status == 4)
                            <span type="badge" class="badge badge-danger">Cancel</span>
                            @endif
                        </td>
                  <form action="{{ route('status.change',$order->id),  }}" method="POST">
                    @csrf
                        <td>
                           <div class="dropdown">
                               <div class="dropdown">
  <button class="bg-transparent border-0 dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
    Dropdown button
  </button>
  <div class="dropdown-menu">
   <button type="submit" name="status" value="1" class="dropdown-item">Prossesing</button>
   <button type="submit" name="status" value="2" class="dropdown-item">Shiped</button>
   <button type="submit" name="status" value="3" class="dropdown-item">Delevired</button>
   <button type="submit" name="status" value="4" class="dropdown-item">Cancel</button>
  </div>
</div>
                        </td>
                         </form>
                          <td>
                            <a target="_blank" href="{{ route('invoice.print', $order->id ) }}" class="btn btn-info">Print</a>
                            <a href="{{ route('invoice', $order->id ) }}" class="btn btn-success">Download</a>
                        </td>
                       </tr>
                       @empty
                       <tr>
                        <td colspan="8" class="text-center">
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
                <h3>Seles By Month</h3>
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
const selesData =@json($selesData);

  new Chart(ctx2, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
      datasets: [{
        label: '# of Orders',
        data: selesData,
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

@endsection
