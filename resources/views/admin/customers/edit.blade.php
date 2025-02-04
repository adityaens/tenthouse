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
                    <h1 class="m-0">Edit Customer</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <form action="{{ route('admin.user.update',['id'=>$user->userId]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Enter Name" value="{{old('name', $user->name ?? '')}}">
                                        @error('name')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Email</label>
                                        <input type="email" name="email" id="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="Enter Email" value="{{old('email', $user->email ?? '') }}">
                                        @error('email')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Mobile</label>
                                        <input type="text" name="mobile" id="mobile"
                                            class="form-control @error('mobile') is-invalid @enderror"
                                            placeholder="Enter Mobile" value="{{ old('mobile', $user->mobile ?? '') }}">
                                        @error('mobile')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group">Discount(%)</label>
                                        <select name="group" id="group"
                                            class="form-control custom-select @error('group') is-invalid @enderror">
                                            <option value="">Select Option</option>
                                            @foreach($groups as $id => $name)
                                            <option value="{{ $id }}" {{ old('group', $user->group_id ?? null) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('group')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address"
                                            class="form-control @error('address') is-invalid @enderror"
                                            rows="4" placeholder="Enter Address">{{ old('address', $user->address ?? '') }}</textarea>
                                        @error('address')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status"
                                            class="form-control custom-select @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status' , $user->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status' , $user->status ?? '') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">Update</button>
                                    <button type="button" class="btn btn-secondary reset-btn" onclick="window.location.href='{{ route('admin.user.index') }}'">Back</button>
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

@endsection