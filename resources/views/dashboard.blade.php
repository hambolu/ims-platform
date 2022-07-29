@extends('layouts.main')
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Dashboard</h1>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-solid fa-cart-flatbed-boxes"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Items</h4>
                  </div>
                  <div class="card-body">
                    {{$items}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-solid fa-money-bill-transfer"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Sales</h4>
                  </div>
                  <div class="card-body">
                    {{number_format($sales, 2)}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                  <i class="fas fa-solid fa-users"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Users</h4>
                  </div>
                  <div class="card-body">
                    {{$users}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-solid fa-garage"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Total Collection Center</h4>
                  </div>
                  <div class="card-body">
                    {{$tcollect}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-solid fa-forklift"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Factory</h4>
                  </div>
                  <div class="card-body">
                    {{$factory}}
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class=" fas fa-solid fa-user-tie-hair"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Staffs</h4>
                  </div>
                  <div class="card-body">
                    {{$staffs}}
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          
          <div class="row">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
              <div class="card">
                <div class="card-header">
                  <h4>Latest Collections</h4>
                  
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-striped mb-0">
                      <thead>
                        <tr>
                          <th>Center Name</th>
                          <th>Weight(Kg)</th>
                          <th>Amount</th>
                          <th>Staff</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($collections as $item)
                        <tr>
                            <td>{{$item->location->name}}</td>
                            <td>{{$item->item_weight}}</td>
                            <td>{{number_format($item->amount, 2)}}</td>
                            <td>{{$item->user->first_name }} {{$item->user->last_name }}</td>
                            <td>{{date('F d, Y', strtotime($item->created_at))}}</td>
                            
                        </tr>
                        @empty
                            <tr colspan="20" class="text-center">
                              <td colspan="20">No Record Found</td>
                            </tr>
                        @endforelse
                        
                        
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

            
    <!-- Main Container end -->

    
@endsection