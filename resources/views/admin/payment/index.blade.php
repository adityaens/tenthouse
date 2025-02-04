@extends('layouts.user_type.auth')

@section('page_title', __('Products List'))
<style>
    .small-swal {
        max-width: 350px !important;
        padding: 1rem !important;
    }
    .small-swal-title {
        font-size: 16px !important; /* Adjust title font size */
    }
    .small-swal-content {
        font-size: 14px !important; /* Adjust content font size */
    }
    .small-swal-actions .swal2-confirm,
    .small-swal-actions .swal2-cancel {
        font-size: 14px !important; /* Adjust button font size */
    }
</style>

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payment Mode List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Payment Mode</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            @include('components.alert')
            <div class="d-flex justify-content-end mb-2">
                <button type="button" onclick="window.location.href='{{ route('admin.payment.create') }}'"
                    class="btn btn-primary sb-sidenav-dark border-0 text-white cust--btn">Add</button>
            </div>
            
            <div class="card mb-4">
                <!-- <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Customer User List
                        </div> -->
                <div class="card-body">

                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>                                    
                                    <th>Payment</th> 
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payment as $key => $pay_mode)
                                <tr>
                                    <td>{{ $key+1 }}</td>                                   
                                    <td>{{ $pay_mode->pay_mod ?? '' }}</td> 
                                    <td>{{ date('Y-m-d', strtotime($pay_mode->created_at)) }}</td>
                                    <td>
                                      

                                        <!-- Delete Button -->
                                        <form id="delete-form-{{ $pay_mode->id }}" action="{{ route('admin.payment.destroy', ['id' => $pay_mode->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $pay_mode->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                       
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7"><span class="">No Payment mode to show</span></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(payment_mode) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'small-swal',
                title: 'small-swal-title',
                content: 'small-swal-content',
                actions: 'small-swal-actions'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${payment_mode}`).submit();
            }
        });
    }
</script>

@endsection