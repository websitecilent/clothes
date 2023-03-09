@extends('layouts.admin')
@section('title', 'Thống kê')
@section('css')
  <style>
    .table td img {
      border-radius: 0;
    }
  </style>
@endsection
@section('content')
  <div class="row">
    <div class="col-md-7 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Thống kê kho hàng</p>
          <p class="card-title">Tổng số lượng sản phẩm trong kho: {{ $t }}</p>
          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Hình</th>
                    <th>Loại sản phẩm</th>
                    <th>Số lượng</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($totalProdByCate as $index => $total)
                  <tr>
                    <td class="align-middle">{{ $index + 1 }}</td>
                    <td class="align-middle"><img src="{{ asset('uploads/cateImg/'.$total->cImg) }}" alt="cateImg"></td>
                    <td class="align-middle">{{ $total->cName }}</td>
                    <td class="align-middle">{{ $total->total }}</td>
                  </tr>         
                  @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-5 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <p class="card-title">Doanh thu</p>
          <h1>{{number_format($revenue).''.''}}<sup>đ</sup></h1>
        </div>
        <canvas id="total-sales-chart"></canvas>
      </div>
    </div>
  </div>
 
<div class="row">
  <div class="col-md-12 stretch-card mb-3">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Top 5 sản phẩm bán chạy nhất</p>
        <div class="table-responsive">
          <table id="recent-purchases-listing" class="table table-bordered text-center">
            <thead class="thead-dark">
              <tr>
                  <th>#</th>
                  <th>Hình</th>
                  <th>Tên sản phẩm</th>
                  <th>Số lượng mua</th>
                  <th>Size</th>
                  <th>Màu sắc</th>
                  <th>Doanh thu</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($topProdHighest as $index => $top)
                <tr>
                  <td class="align-middle">{{ $index + 1 }}</td>
                  <td class="align-middle"><img src="{{ asset('uploads/prodImg/'.$top->pImg) }}" alt="prodImg"></td>
                  <td class="align-middle">{{ $top->pName }}</td>
                  <td class="align-middle">{{ $top->total }}</td>
                  <td class="align-middle">{{ $top->size }}</td>
                  <td class="align-middle">{{ $top->color }}</td>
                  <td class="align-middle">{{ number_format($top->total * $top->price).''.''}}<sup>đ</sup></td>
                </tr>         
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row">
  <div class="col-md-12 stretch-card">
    <div class="card">
      <div class="card-body">
        <p class="card-title">Top 5 sản phẩm bán ít nhất</p>
        <div class="table-responsive">
          <table id="recent-purchases-listing" class="table table-bordered text-center">
            <thead class="thead-dark">
              <tr>
                  <th>#</th>
                  <th>Hình</th>
                  <th>Tên sản phẩm</th>
                  <th>Số lượng mua</th>
                  <th>Size</th>
                  <th>Màu sắc</th>
                  <th>Doanh thu</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($topProdLowest as $index => $top)
                <tr>
                  <td class="align-middle">{{ $index + 1 }}</td>
                  <td class="align-middle"><img src="{{ asset('uploads/prodImg/'.$top->pImg) }}" alt="prodImg"></td>
                  <td class="align-middle">{{ $top->pName }}</td>
                  <td class="align-middle">{{ $top->total }}</td>
                  <td class="align-middle">{{ $top->size }}</td>
                  <td class="align-middle">{{ $top->color }}</td>
                  <td class="align-middle">{{ number_format($top->total * $top->price).''.''}}<sup>đ</sup></td>
                </tr>         
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection