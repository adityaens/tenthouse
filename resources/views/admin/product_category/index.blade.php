@extends('layouts.user_type.auth')

@section('page_title', __('products list'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Product Category</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">edit</li>
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
                      <th>Category Name</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  @php $sr=1; @endphp
                  @foreach($categories as $category)
                <tr align="center">
                    <td>{{ $sr }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->cat_status == 1 ? 'Active' : 'Inactive' }}</td>
                        <td><a href="{{ route('admin.products.category.edit', $category->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit </a>
                    <button type="button" class="btn btn-danger"><i class="fas fa-trash"></i> Delete </button></td>
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