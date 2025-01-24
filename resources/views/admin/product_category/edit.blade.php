@extends('layouts.user_type.auth')

@section('page_title', __('Edit Product Category'))

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Product Category</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin.products.category.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name">Category Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="cat_status">Status</label>
                                    <select class="form-control" id="cat_status" name="cat_status" required>
                                        <option value="1" {{ $category->cat_status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $category->cat_status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">Update</button>
                                <a href="{{ route('admin.products.category.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
