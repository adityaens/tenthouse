@extends('layouts.user_type.auth')

@section('page_title', __('Edit Order'))

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Order</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
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
                        <form action="{{ route('admin.orders.update', ['id' => $order->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <!-- Row for Description -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="paymentMethod">Payment Method<span class="text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method"
                                            class="form-control custom-select @error('payment_method') is-invalid @enderror">
                                            <option value="">Select</option>
                                            @forelse($paymentMethods as $paymentMethod)
                                            <option {{ ($order->payment_method_id == $paymentMethod->id) ? 'selected' : '' }} value="{{ $paymentMethod->id }}">
                                                {{ $paymentMethod->pay_mod }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('payment_method')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="booking_date_range">Booking Date Range<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control float-right @error('booking_date_range') is-invalid @enderror"
                                                id="booking_date_range"
                                                name="booking_date_range"
                                                value="{{ old('booking_date_range', ($order->booking_date_from && $order->booking_date_to ? $order->booking_date_from . ' - ' . $order->booking_date_to : '')) }}">
                                        </div>
                                        @error('booking_date_range')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Row for Price, Quantity, and Status -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total_amount">Total Amount<span class="text-danger">*</span></label>
                                        <input type="text" name="total_amount" id="total_amount"
                                            class="form-control @error('total_amount') is-invalid @enderror"
                                            placeholder="Enter total amount in Rs" value="{{ old('total_amount', $order->total_amount) }}" disabled>
                                        @error('total_amount')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="paid_amount">Paid Amount<span class="text-danger">*</span></label>
                                        <input type="text" name="paid_amount" id="paid_amount"
                                            class="form-control @error('paid_amount') is-invalid @enderror"
                                            placeholder="Enter total amount in Rs" value="{{ old('paid_amount', $order->paid_amount) }}">
                                        @error('paid_amount')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="due_amount">Due Amount<span class="text-danger">*</span></label>
                                        <input type="text" name="due_amount" id="due_amount"
                                            class="form-control @error('due_amount') is-invalid @enderror"
                                            placeholder="Enter total amount in Rs" value="{{ old('due_amount', $order->due_amount) }}">
                                        @error('due_amount')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Row for Condition and Image -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="due_date">Due Date<span class="text-danger">*</span></label>
                                        <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input @error('due_date') is-invalid @enderror" data-target="#due_date" id="due_date" name="due_date" placeholder="YYYY-MM-DD" value="{{ old('due_date', $order->due_date) }}" />
                                            <div class="input-group-append" data-target="#due_date" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        @error('due_date')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Payment Status<span class="text-danger">*</span></label>
                                        <select name="status" id="status"
                                            class="form-control custom-select @error('status') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="{{ COMPLETED }}" {{ old('status', $order->status) == COMPLETED ? 'selected' : '' }}>Completed</option>
                                            <option value="{{ PENDNG }}" {{ old('status', $order->status) == PENDNG ? 'selected' : '' }}>Pending</option>
                                            <option value="{{ CANCELLED }}" {{ old('status', $order->status) == CANCELLED ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        @error('status')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="delivered_by">Delivered By<span class="text-danger">*</span></label>
                                        <input type="text" name="delivered_by" id="delivered_by"
                                            class="form-control @error('delivered_by') is-invalid @enderror"
                                            placeholder="Enter Name" value="{{ old('delivered_by', $order->delivered_by) }}">
                                        @error('delivered_by')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $order->remarks) }}</textarea>
                                        @error('remarks')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">Update</button>
                                    {{--<button type="button" class="btn btn-secondary reset-btn" onclick="window.location.href='{{ route('admin.orders.index') }}'">Back</button>--}}
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
<link rel="stylesheet" href="{{ asset('css/tempusdominus-bootstrap-4.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/daterangepicker.css') }}">
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script>
    $(document).ready(function() {

        $('#booking_date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#due_date').datetimepicker({
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
        })
    });
</script>
@endsection