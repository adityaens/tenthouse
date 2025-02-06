@extends('layouts.user_type.auth')

@section('page_title', __('Orders List'))
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
                    <h1 class="m-0">Orders List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
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
                <button type="button" onclick="window.location.href='{{ route('admin.orders.create') }}'"
                    class="btn btn-primary sb-sidenav-dark border-0 text-white cust--btn">Add</button>
            </div>
            {{--<ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Customer List </li>
            </ol> --}}
            <div class="card mb-4">
                <!-- <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Customer User List
                        </div> -->
                <div class="card-body">

                    {{--<div class="filter-form-wrapper mb-5">

                        <form class="form filter-form" method="GET">
                            <div class="row">
                                <!-- Search by Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ __('Customer') }}</label>
                                    <select name="customer" id="customer" class="form-control select2"> 
                                        <option value="">{{ __('Select Customer') }}</option>
                                    @forelse ($customers as $customer)
                                        <option {{ request()->get('customer') == $customer->userId ? 'selected' : '' }} value="{{ $customer->userId }}">{{ $customer->name }}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                </div>

                                <!-- Filter by Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="product" class="form-label">{{ __('Product') }}</label>
                                    
                                    <select name="product" id="product" class="form-control select2">
                                        <option value="">{{ __('Select Product') }}</option>
                                        @forelse ($products as $product)
                                        <option {{ request()->get('product') == $product->id ? 'selected' : '' }} value="{{ $product->id }}">{{ $product->name }}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Filter by Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="" {{ request()->get('status') == '' ? 'selected' : '' }}>{{ __('Select Status') }}</option>
                                        <option {{ request()->get('status') == ACTIVE ? 'selected' : '' }} value="{{ ACTIVE }}">{{ __('Active') }}</option>
                                        <option {{ request()->get('status') == INACTIVE ? 'selected' : '' }} value="{{ INACTIVE }}">{{ __('Inactive') }}</option>
                                    </select>
                                </div>

                                <!-- Filter by Created On -->
                                <div class="col-md-6 mb-3">
                                    <label for="created_on" class="form-label">{{ __('Created On') }}</label>
                                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input @error('created_on') is-invalid @enderror" data-target="#created_on" id="created_on" name="created_on" placeholder="YYYY-MM-DD" value="{{ request()->get('created_on') }}" />
                                            <div class="input-group-append" data-target="#created_on" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2">{{ __('Search') }}</button>
                                    <button type="button" class="btn btn-secondary reset-btn" onclick="window.location.href='{{ route('admin.orders.index') }}'">{{ __('Reset') }}</button>
                                </div>
                            </div>
                        </form>

                        <hr />
                    </div>--}}
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Customer</th>
                                    <th>Group</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Due Amount</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($orders as $key => $order)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $order->user->name ?? '' }}</td>
                                    <td>
                                        @forelse ($order->user->groups as $group)
                                            {{ $group->name }} <br>
                                        @empty
                                            
                                        @endforelse
                                    </td>
                                    <td>{{ $order->quantity ?? '' }}</td>
                                    <td>{{ $order->total_amount ?? '' }}</td>
                                    <td>{{ $order->paid_amount ?? '' }}</td>
                                    <td>{{ $order->due_amount ?? '' }}</td>
                                    <td>
                                        {{--
                                            @if($order->status == 0)

                                        @elseif ($order->status == 1)

                                        @elseif ($order->status == 2)

                                        
                                            class="p-2 badge {{ $order->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $order->status ? 'Active' : 'Inactive' }}
                                            --}}
                                        <span
                                            class="p-2 badge {{ $order->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $order->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ date('Y-m-d', strtotime($order->created_at)) }}</td>
                                    <td>
                                        <!-- View Button -->
                                        <a href="{{ route('admin.orders.view', ['id' => $order->id]) }}" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.orders.edit', ['id' => $order->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <form id="delete-form-{{ $order->id }}" action="{{ route('admin.orders.destroy', ['id' => $order->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $order->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @php $i++; @endphp
                                @empty
                                <tr>
                                    <td colspan="10"><span class="">No Orders to show</span></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($orders->count() > 0)
                        <div class="pagination_wrapper">
                            {{ $orders->onEachSide(PER_PAGE)->withQueryString()->links() }}
                        </div>
                        @endif
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
<link rel="stylesheet" href="{{ asset('css/tempusdominus-bootstrap-4.min.css') }}">
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/tempusdominus-bootstrap-4.min.js') }}"></script>
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

    $(document).ready(function() {
        $('#created_on').datetimepicker({
            format: 'YYYY-MM-DD',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            }
        });
    });
</script>
@endsection