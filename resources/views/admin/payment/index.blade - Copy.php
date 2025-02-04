@extends('layouts.user_type.auth')

@section('page_title', __('Payment'))
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
    @include('components.alert')
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
                    <td>
                        <!-- <a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> </a> -->
                        <form id="delete-form-{{ $payments->id }}" action="{{ route('admin.payment.destroy', ['id' => $payments->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $payments->id }})">
                                                <i class="fas fa-trash"></i>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(paymentId) {
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
                document.getElementById(`delete-form-${paymentId}`).submit();
            }
        });
    }
</script>



@endsection