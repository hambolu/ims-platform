@extends('layouts.main')
@section('content')
<div class="main-content">
    <div class="card mt-4">
      <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
          <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
              <i class="fas fa-solid fa-cart-flatbed-boxes"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h4>Materials Available</h4>
              </div>
              <div class="card-body">
                {{$totals->collected}}
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
                <h4>Total Sorted</h4>
              </div>
              <div class="card-body">
                {{$totals->sorted}}
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
                <h4>Total Bailed</h4>
              </div>
              <div class="card-body">
                {{$totals->bailed}}
              </div>
            </div>
          </div>
        </div>
      </div>
      
        
        <div class="row">
            <div class="col-md-12 shadow-sm table-responsive">
                
                    <div class="row p-2">
                        <div class="col-md-8">
                            <p>Latest Sorting</p>
                        </div>
                        <div class="col align-self-end">
                            <p class="text-end">Available Stock: {{$result}}KG</p>
                        </div>
                    </div>

                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            @foreach ($bailingItems as $item)
                            <th >{{$item->item}}</th>
                            @endforeach
                            <th >Unsorted</th>
                            <th >Total</th>
                            <th >Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <tr>
                            @foreach (json_decode($sort->sort_item_weight) as $item)
                                
                            <td>
                                {{$item}}
                            </td>
                            @endforeach
                            <td>
                                0
                            </td>
                            <td>
                                {{$result}} 
                             </td>
                            <td>
                                {{date('F d, Y', strtotime($sort->created_at))}} 
                             
                             </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        
        </div>
    </div>
        
</div>
@endsection
