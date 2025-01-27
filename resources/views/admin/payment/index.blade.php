@extends('layouts.user_type.auth')

@section('page_title', __('Payment'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Product Payment</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">home</a></li>
                        <li class="breadcrumb-item active">payment</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body table-responsive p-0" style="height: 300px;">
                <table class="table table-head-fixed text-nowrap">
                  <thead>
                    <tr align="center">
                      <th>Sr.No.</th>
                      <th>Payment Mode</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                   @php $sr=1; @endphp
                  @foreach($payment as $payments)
                <tr align="center">
                    <td>{{ $sr }}</td>
                    <td>{{ $payments->pay_mod }}</td>
                        <td><a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> Edit </a>
                        <form action="#" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                 </td>
                </tr>
                @php $sr++ @endphp
                  @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


@endsection