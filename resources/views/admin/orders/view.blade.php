@extends('layouts.user_type.auth')

@section('page_title', __('Orders List'))
<style>
    .small-swal {
        max-width: 350px !important;
        padding: 1rem !important;
    }

    .small-swal-title {
        font-size: 16px !important;
        /* Adjust title font size */
    }

    .small-swal-content {
        font-size: 14px !important;
        /* Adjust content font size */
    }

    .small-swal-actions .swal2-confirm,
    .small-swal-actions .swal2-cancel {
        font-size: 14px !important;
        /* Adjust button font size */
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
                    <h1 class="m-0">Order No: #{{ $order->order_id }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">View Order</li>
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
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-3 w-100">
                                <!-- User Info -->
                                <div class="card-body text-center">
                                    <img src="https://dummyimage.com/80x80/888/fff" alt="User Avatar"
                                        class="rounded-circle border border-3 border-secondary shadow-sm mb-3" width="80" height="80">
                                    <h5 class="fw-bold text-dark">{{ $order->user->name }}</h5>
                                    <p class="text-muted mb-2">{{ $order->user->email }}</p>
                                </div>

                                <!-- Contact Details -->
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="fas fa-phone-alt text-primary me-2"></i>
                                        <span class="fw-medium">+91 {{ $order->user->mobile }}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span class="fw-medium">{{ $order->user->address }}</span>
                                    </li>
                                </ul>

                                <!-- User Groups -->
                                <div class="card-body">
                                    <p class="fw-semibold text-muted mb-2">Groups:</p>
                                    @forelse ($order->user->groups as $group)
                                    <span class="badge bg-info text-dark me-1">{{ $group->name }}</span>
                                    @empty
                                    <span class="text-muted">No Groups</span>
                                    @endforelse
                                </div>
                            </div>



                        </div>
                        <div class="col-md-8">
                            <h3>Products</h3>
                            <div class="table-responsive shadow-lg rounded-xl">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Name</th>
                                            <th>SKU</th>
                                            <th>Unit Price</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($order->orderProducts as $orderProduct)
                                        <tr data-id="{{ $orderProduct->id }}" data-product="{{ $orderProduct->product_id }}">
                                            <td>{{ $orderProduct->product_name }}</td>
                                            <td>{{ $orderProduct->sku }}</td>
                                            <td>{{ CURRENCY_SYMBOL }}{{ $orderProduct->unit_price }}</td>
                                            <td>{{ $orderProduct->quantity }}</td>
                                            <td class="total-price">{{ CURRENCY_SYMBOL }}{{ $orderProduct->total_price }}</td>
                                            {{--<td>
                                                <button class="btn btn-danger btn-sm remove-item" data-index="{{ $orderProduct->id }}">
                                            <i class="fas fa-trash"></i>
                                            </button>
                                            </td>--}}
                                        </tr>
                                        @empty

                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td class="sum_qty_products"><strong>Total Qty: {{ $order->quantity }}</strong></td>
                                            <td class="sum_all_products"><strong>Total Price: {{ CURRENCY_SYMBOL }}{{ $order->total_amount }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(productId) {
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
                document.getElementById(`delete-form-${productId}`).submit();
            }
        });
    }
</script>
@endsection