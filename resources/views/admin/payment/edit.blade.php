@extends('layouts.user_type.auth')

@section('page_title', __('Payment list'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payment Mode</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">payment</a></li>
                        <li class="breadcrumb-item active">create</li>
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
                <div class="col-sm-12">
                    <form action="{{ route('admin.payment.store') }}" method="POST" enctype="multipart/form-data" style="margin: 20px;">
                        @csrf
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label for="payMode">Payment Mode</label>
                                <input type="text" name="payMode" id="payMode" class="form-control">
                                @error('payMode')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="#" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


@endsection