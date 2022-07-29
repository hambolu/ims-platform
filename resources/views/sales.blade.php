@extends('layouts.main')
@section('content')
    <div class="main-content">
        <div class="card mt-4 p-4">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="card-header">
                        <h4> Create Sales</h4>
                    </div>
                    <form action="/addsales" method="post" class="mb-4 ">
                        @csrf
                        <div class="row d-flex">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Weight</label>
                                    <input type="text" name="item_weight" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Amount</label>
                                    <input type="text" name="amount" class="form-control">
                                </div>
                            </div>
                           <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Factory</label>
                                <select name="factory_id" id="" class="form-control">
                                    @forelse ($factory as $items)
                                    <option value="{{$items->id}}">{{$items->name}}</option>
                                    @empty
                                    <option value="">No Record Found </option>
                                    @endforelse
                                </select>
                            </div>
                           </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <input type="submit" value="Create" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-12 shadow-sm table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Factory</th>
                                <th>Weight</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $item)
                                <tr>
                                    <td>{{ $item->factory->name }}</td>
                                    <td>{{ $item->item_weight }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{$item->created_at->format('D, d M Y ')}}</td>
                                    <td>
                                        <form action="" method="post">
                                            <a href="{{ $item->id }}" class="btn btn-primary"><i class="fa-light fa-eye"></i></a>
                                            <a href="/{{ $item->id }}" class="btn btn-info"><i
                                                    class="fa-light fa-pen-to-square"></i></a>
                                            @csrf
                                            <button type="submit" class="btn btn-danger"><i
                                                    class="fa-light fa-trash-can"></i></button>
                                        </form>
                                    </td>
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
    @endsection
