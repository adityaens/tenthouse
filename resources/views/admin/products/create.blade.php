@extends('layouts.user_type.auth')

@section('page_title', __('Add Product'))

@section('content')

<<<<<<< HEAD
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Add Product</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
=======
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
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" style="margin: 20px;">
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
>>>>>>> a066a46af0a21abd7dd0f2eeb2fdc9405382c60e
        </div>
        <!-- /.content-header -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">

                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Row for Name and Category -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" 
                                            class="form-control @error('name') is-invalid @enderror" 
                                            placeholder="Enter Name" value="{{ old('name') }}">
                                        @error('name')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select name="cat_id" id="cat_id" 
                                                class="form-control custom-select @error('cat_id') is-invalid @enderror">
                                            <option value="">Select Category</option>
                                            <option value="1">Cat 1</option>
                                            <option value="2">Cat 2</option>
                                        </select>
                                        @error('cat_id')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Row for Description -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" 
                                                class="form-control @error('description') is-invalid @enderror" 
                                                rows="4" placeholder="Enter Product Description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Row for Price, Quantity, and Status -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <input type="text" name="price" id="price" 
                                            class="form-control @error('price') is-invalid @enderror" 
                                            placeholder="Enter price in Rs" value="{{ old('price') }}">
                                        @error('price')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" 
                                            class="form-control @error('quantity') is-invalid @enderror" 
                                            placeholder="Enter Quantity" value="{{ old('quantity') }}">
                                        @error('quantity')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" 
                                                class="form-control custom-select @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        @error('status')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Row for Condition and Image -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_condition">Product Condition</label>
                                        <input type="text" name="product_condition" id="product_condition" 
                                            class="form-control @error('product_condition') is-invalid @enderror" 
                                            placeholder="Enter Product Condition" value="{{ old('product_condition') }}">
                                        @error('product_condition')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="product_images">Image</label>
                                        <input type="file" name="product_images[]" id="product_images" 
                                            class="form-control @error('product_images') is-invalid @enderror" 
                                            multiple>
                                        <div id="image-preview" style="display: flex; flex-wrap: wrap; margin-top: 10px;"></div>
                                        @error('product_images')
                                            <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

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
@section('script')
<script src="{{ asset('js/custom.js') }}"></script>
<script>
    $('#product_images').on('change', function() {
        previewImages(this, 'image-preview');
    });
</script>
@endsection