@extends('layouts.user_type.auth')

@section('page_title', __('Add Order'))

@section('content')
<style>
    .rem_qty_box,
    .discount_box {
        color: #497D74;
        font-size: 14px;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Order</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
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
                        <div class="row mb-6 justify-content-center">
                            <div class="col-md-5">
                                <label for="product">Customer</label>
                                <select class="form-control select2 mb-2" id="user">
                                    <option value="">Select</option>
                                    @forelse ($customers as $customer)
                                    <option value="{{ $customer->userId }}">{{ $customer->name }}</option>
                                    @empty

                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="product">Search for a product</label>
                                <select class="form-control select2" id="product">
                                    <option value="">Select</option>
                                    @forelse ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @empty

                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="product">Quantity</label>
                                <input type="number" class="form-control mb-2" id="quantity" value="1">
                                <button class="btn btn-success" id="add_cart_btn">Add to Cart</button>
                            </div>
                        </div>

                        <div class="row" id="cart_list">

                        </div>
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
    let debugMode = true;
    $(document).ready(function() {
        let cart = [];

        $('#add_cart_btn').on('click', function() {
            let userId = $('#user').find(':selected').val();
            let productId = $('#product').find(':selected').val();
            let quantity = $('#quantity').val();

            if (debugMode) {
                console.log('user:'+userId, 'product'+productId, 'qty'+quantity);
                debugger;
            }

            if (!productId || !userId || !quantity) return;

            createCart(userId, productId, quantity);
        });

        function createCart(userId, productId, quantity) {
            $.ajax({
                url: "{{ route('admin.carts.addToCart') }}",
                type: "POST",
                data: {
                    userId: userId,
                    productId: productId,
                    quantity: quantity
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    cart.push({
                        id: response.cart.id,
                        name: response.cart.product.name,
                        sku: response.cart.product.sku,
                        unitPrice: response.cart.product.price,
                        quantity: response.cart.quantity,
                        totalPrice: ''
                    });

                    if (debugMode) {
                        console.log(cart);
                        debugger;
                    }

                    updateCartUI(cart);
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.error);
                }
            });

        }

        function updateCartUI(cart) {
            let cartHtml = "";
            cart.forEach((item, index) => {
                cartHtml += `
                <tr data-id="${item.id}">
                    <td>${item.name}</td>
                    <td>${item.sku}</td>
                    <td>${item.unitPrice}</td>
                    <td><input type="number" class="form-control quantity" data-index="${index}" value="${item.quantity}" min="1"></td>
                    <td class="total-price">${item.totalPrice}</td>
                    <td><button class="btn btn-danger btn-sm remove-item" data-index="${index}">Delete</button></td>    
                </tr>
            `;
            });

            if (debugMode) {
                console.log(cartHtml);
                debugger;
            }

            $('#cart_list').html(cartHtml);
        }
    });
</script>
@endsection