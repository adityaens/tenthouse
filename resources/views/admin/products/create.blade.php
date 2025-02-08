@extends('layouts.user_type.auth')

@section('page_title', __('Add Product'))

@section('content')

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
    </div>
    <!-- /.content-header -->
    <section class="content">
        @include('components.alert')
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
                                        <label for="name">Name <span class="text-danger">*</span></label>
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
                                        <label for="category">Category <span class="text-danger">*</span></label>
                                        <select name="cat_id" id="cat_id"
                                            class="form-control custom-select @error('cat_id') is-invalid @enderror">
                                            <option value="">Select Category</option>
                                            @forelse($productCategories as $productCategory)
                                            <option {{ (old('cat_id') == $productCategory->id) ? 'selected' : '' }} value="{{ $productCategory->id }}">
                                                {{ $productCategory->name }}
                                            </option>
                                            @empty
                                            @endforelse
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
                                        <label for="price">Price <span class="text-danger">*</span></label>
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
                                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
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
                                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
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
                                        <label for="product_condition">Product Condition <span class="text-danger">*</span></label>
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
                                        @foreach ($errors->get('product_images.*') as $index => $messages)
                                        @foreach ($messages as $message)
                                        <span class="error">{{ $message }}</span>
                                        @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" class="btn btn-secondary reset-btn" onclick="window.location.href='{{ route('admin.products.index') }}'">Back</button>
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