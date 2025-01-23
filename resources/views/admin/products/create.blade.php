@extends('layouts.user_type.auth')

@section('page_title', __('create product'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Fillup the products details below.</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Products</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">General</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="inputName">Product Name</label>
        <input type="text" name="inputName" id="inputName" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="inputDescription">Product Description</label>
        <textarea name="inputDescription" id="inputDescription" class="form-control" rows="4" required></textarea>
    </div>
    <div class="form-group">
        <label for="inputfile">Image</label>
        <input type="file" name="inputfile" id="inputfile" class="form-control">
    </div>
    <div class="form-group">
        <label for="price">Price</label>
        <input type="text" name="price" id="price" class="form-control" placeholder="Price Rs." required>
    </div>
    <div class="form-group">
        <label for="inputStatus">Product Status</label>
        <select name="inputStatus" id="inputStatus" class="form-control custom-select" required>
            <option value="">Select Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
    <div class="row">
        <div class="col-12 mb-4">
            <input type="submit" value="Submit" class="btn btn-success">
        </div>
    </div>
  </form>
 </div>
 </div>
 </div>
</section>
</div><!-- /.container-fluid -->
</div>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->


@endsection