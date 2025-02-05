@extends('layouts.user_type.auth')

@section('page_title', __('Add Order'))

@section('content')
<style>
    .rem_qty_box,
    .discount_box {
        color: #497D74;
        font-size: 14px;
    }

    .btn-submit {
        background: #497D74;
        color: #fff;
    }

    .btn-submit:hover {
        background: #497D74;
        color: #fff;
    }

    #create_order {
        display: none;
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

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-center mb-4"><i class="fas fa-shopping-cart"></i> Shopping Cart</h5>

                            <!-- Customer & Product Selection -->
                            <div class="row mb-4">
                                <div class="col-md-5">
                                    <label for="user" class="form-label fw-bold">Customer</label>
                                    <select class="form-control select2 mb-2" id="user">
                                        <option value="">Select Customer</option>
                                        @forelse ($customers as $customer)
                                        <option value="{{ $customer->userId }}">{{ $customer->name }}</option>
                                        @empty
                                        <option disabled>No customers available</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="product" class="form-label fw-bold">Search for a Product</label>
                                    <select class="form-control select2 mb-2" id="product">
                                        <option value="">Select Product</option>
                                        @forelse ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @empty
                                        <option disabled>No products available</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-2 text-center">
                                    <label for="quantity" class="form-label fw-bold">Quantity</label>
                                    <input type="number" class="form-control text-center mb-2" id="quantity" value="1" min="1">
                                    <button class="btn btn-success w-100 mt-2" id="add_cart_btn">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>

                            <!-- Cart List -->
                            <div class="row">
                                <div class="col-12">
                                    <div id="cart_list" class="table-responsive border rounded p-3 bg-light">
                                        <!-- Cart items will be dynamically added here -->
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="create_order">
                                <div class="col">
                                    <button type="button" class="btn btn-submit mt-2">Next</button>
                                </div>
                            </div>
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
    let debugMode = false;
    $(document).ready(function() {
        let cart = [];

        $('#add_cart_btn').on('click', function() {
            let productExists = false;
            let userId = $('#user').find(':selected').val();
            let productId = $('#product').find(':selected').val();
            let quantity = $('#quantity').val();

            if (debugMode) {
                console.log('user:' + userId, 'product' + productId, 'qty' + quantity);
                console.log(productId);
                debugger;
            }

            if (!productId || !userId || !quantity) return;

            $('#cart_list tr').each(function() {
                if ($(this).data('product') == productId) {
                    productExists = true;
                }
            });

            if (productExists) {
                return;
            }

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
                        productId: response.cart.product.id,
                        group: response.cart.user.group.name,
                        sku: response.cart.product.sku,
                        unitPrice: response.cart.product.price,
                        quantity: response.cart.quantity,
                        totalPrice: response.price
                    });

                    if (debugMode) {
                        console.log(cart);
                        debugger;
                    }

                    if ($('#cart_list tr').data('id') == response.cart.id) return;

                    updateCartUI(cart);
                    $('#create_order').show();
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.error);
                }
            });

        }

        function updateCartUI(cart) {
            let cartHtml = `
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Group</th>
                        <th>SKU</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    `;

            cart.forEach((item, index) => {
                cartHtml += `
            <tr data-id="${item.id}" data-product="${item.productId}">
                <td>${item.name}</td>
                <td>${item.group}</td>
                <td>${item.sku}</td>
                <td>₹${item.unitPrice}</td>
                <td>
                    <input type="number" class="form-control quantity text-center" data-index="${index}" 
                           value="${item.quantity}" min="1" style="width: 60px;">
                </td>
                <td class="total-price">₹${item.totalPrice}</td>
                <td>
                    <button class="btn btn-danger btn-sm remove-item" data-index="${index}">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </td>    
            </tr>
        `;
            });

            cartHtml += `</tbody>
            <tfoot>
            <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="sum_qty_products">Total Qty.</td>
            <td class="sum_all_products">Total Price:</td>
            <td></td>
            </tr>
            </tfoot>
            </table></div>`;

            // Debugging
            if (debugMode) {
                console.log(cartHtml);
                debugger;
            }

            $('#cart_list').html(cartHtml);
            updateCartTotals();
        }

        function updateCartTotals() {
            let totalQty = 0;
            let totalPrice = 0;

            // Loop through each row in the cart table
            $('#cart_list tbody tr').each(function() {
                let qty = parseInt($(this).find('.quantity').val()) || 0;
                let priceText = $(this).find('.total-price').text().replace('₹', '').trim();
                let price = parseFloat(priceText) || 0;

                totalQty += qty;
                totalPrice += price;
            });

            // Update total quantity
            $('.sum_qty_products').text(totalQty);

            // Update total price
            $('.sum_all_products').text(`₹${totalPrice}`);
        }

        // Call function on page load to update totals
        updateCartTotals();

        // Listen for quantity input changes
        $(document).on('input', '.quantity', function() {
            let row = $(this).closest('tr');
            let unitPrice = parseFloat(row.find('td:nth-child(4)').text().replace('₹', '').trim()) || 0;
            let qty = parseInt($(this).val()) || 0;
            let total = unitPrice * qty;

            row.find('.total-price').text(`₹${total}`);

            updateCartTotals(); // Update totals after change
        });

        // Listen for item removal
        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
            updateCartTotals(); // Update totals after removal
        });


    });

    function sendCartData() {
        let formData = new FormData();

        // Collect customer ID if needed
        let userId = $('#user').val();
        if (!userId) {
            alert('Please select a customer.');
            return;
        }
        formData.append('userId', userId);

        let cartItems = [];

        // Loop through each cart row to collect product data
        $('#cart_list tbody tr').each(function() {
            let productId = $(this).data('product');
            let productName = $(this).find('td:nth-child(1)').text().trim();
            let group = $(this).find('td:nth-child(2)').text().trim();
            let sku = $(this).find('td:nth-child(3)').text().trim();
            let unitPrice = parseFloat($(this).find('td:nth-child(4)').text().replace('₹', '').trim()) || 0;
            let quantity = parseInt($(this).find('.quantity').val()) || 1;
            let totalPrice = parseFloat($(this).find('.total-price').text().replace('₹', '').trim()) || 0;

            cartItems.push({
                productId,
                productName,
                group,
                sku,
                unitPrice,
                quantity,
                totalPrice
            });
        });

        if (cartItems.length === 0) {
            alert('Cart is empty!');
            return;
        }

        formData.append('cartItems', JSON.stringify(cartItems)); // Send as JSON string

        // Send FormData via AJAX
        $.ajax({
            url: '{{ route("admin.orders.store") }}', // Adjust the endpoint as needed
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(response) {
                if(response.success) {
                    window.location.href = `/admin/orders/edit/${response.orderId}`;
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Something went wrong. Please try again.');
            }
        });
    }

    // Trigger the function when clicking "Checkout" or similar button
    $('.btn-submit').on('click', sendCartData);
</script>
@endsection