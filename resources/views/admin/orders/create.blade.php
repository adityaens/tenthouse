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

    #product,
    #user {
        width: 100%;
        padding: 10px;
        font-size: 16px;
    }

    .product-list,
    .user-list {
        margin-top: 10px;
    }

    .product,
    .user {
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 5px;
        border-radius: 5px;
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
                                    <input type="hidden" id="userId">
                                    <input type="text" id="user" placeholder="Type to search...">
                                    <div id="results1" class="user-list"></div>
                                </div>
                                <div class="col-md-5">
                                    <label for="product" class="form-label fw-bold">Search for a Product</label>
                                    <input type="hidden" id="productId">
                                    <input type="text" id="product" placeholder="Type to search...">
                                    <div id="results" class="product-list"></div>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let productInput = document.getElementById("product");
        let resultsDiv = document.getElementById("results");

        if (productInput) {
            productInput.addEventListener("input", function() {
                getSearchProducts(productInput.value);
            });
        } else {
            console.error("Element #product not found");
        }
    });

    function getSearchProducts(productKeyword) {
        if (productKeyword.trim() === "") {
            document.getElementById("results").innerHTML = ""; // Clear results if input is empty
            return;
        }

        $.ajax({
            url: "{{ route('admin.products.getProductsList') }}",
            type: "POST",
            data: {
                productKeyword: productKeyword
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(response) {
                if (response.success) {
                    displayResults(response.products);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseJSON.error);
            }
        });
    }

    // Function to display search results as suggestions
    function displayResults(filteredProducts) {
        let resultsDiv = document.getElementById("results");
        resultsDiv.innerHTML = ""; // Clear previous results

        if (filteredProducts.length === 0) {
            resultsDiv.innerHTML = "<p>No products found</p>";
            return;
        }

        filteredProducts.forEach(product => {
            const div = document.createElement("div");
            const productId = document.getElementById('productId');
            div.classList.add("product");
            div.textContent = product.name+'('+product.rem_qty+')';
            div.onclick = function() {
                productId.value = product.id;
                selectProduct(product.name);
            }; // Fill input on click
            resultsDiv.appendChild(div);
        });

        resultsDiv.style.display = "block"; // Show results
    }

    // Function to handle product selection
    function selectProduct(productName) {
        let productInput = document.getElementById("product");
        productInput.value = productName; // Fill input field
        document.getElementById("results").style.display = "none"; // Hide suggestions
    }

    // Hide suggestions when clicking outside
    document.addEventListener("click", function(event) {
        if (!event.target.closest("#product") && !event.target.closest("#results")) {
            document.getElementById("results").style.display = "none";
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let userInput = document.getElementById("user");
        let resultsDiv = document.getElementById("results1");

        if (userInput) {
            userInput.addEventListener("input", function() {
                getSearchUsers(userInput.value);
            });
        } else {
            console.error("Element #user not found");
        }
    });

    // Function to perform AJAX request
    function getSearchUsers(customerKeyword) {
        if (customerKeyword.trim() === "") {
            document.getElementById("results1").innerHTML = ""; // Clear results if input is empty
            return;
        }

        // Using Fetch API instead of jQuery's $.ajax
        fetch("{{ route('admin.users.getUsersList') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    customerKeyword: customerKeyword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults1(data.customers);
                }
            })
            .catch(error => {
                console.log("Error fetching users:", error);
            });
    }

    // Function to display search results as suggestions
    function displayResults1(filteredUsers) {
        let resultsDiv = document.getElementById("results1");
        resultsDiv.innerHTML = ""; // Clear previous results

        if (filteredUsers.length === 0) {
            resultsDiv.innerHTML = "<p>No users found</p>";
            return;
        }

        filteredUsers.forEach(user => {
            const div = document.createElement("div");
            div.classList.add("user");
            div.textContent = user.name;
            div.onclick = function() {
                selectProduct1(user.name, user.userId); // Pass both name and ID
            };
            resultsDiv.appendChild(div);
        });

        resultsDiv.style.display = "block"; // Show results
    }

    // Function to handle product selection
    function selectProduct1(userName, userId) {
        let userInput = document.getElementById("user");
        userInput.value = userName; // Fill input field
        document.getElementById("userId").value = userId; // Set userId value
        document.getElementById("results1").style.display = "none"; // Hide suggestions
    }

    // Hide suggestions when clicking outside
    document.addEventListener("click", function(event) {
        if (!event.target.closest("#user") && !event.target.closest("#results1")) {
            document.getElementById("results1").style.display = "none";
        }
    });
</script>
<script>
    let debugMode = false;
    $(document).ready(function() {
        let cart = [];

        $('#add_cart_btn').on('click', function() {
            let productExists = false;
            let userId = $('#userId').val();
            let productId = $('#productId').val();
            let quantity = $('#quantity').val();

            if (debugMode) {
                console.log('user:' + userId, 'product' + productId, 'qty' + quantity);
                console.log(productId);
                debugger;
            }

            if (!productId) {
                toastr.error('Select Product.');
                return;
            }

            if(!userId) {
                toastr.error('Select User.')
                return;
            }

            if(!quantity) {
                toastr.error('Select Quantity');
                return;
            }

            $('#cart_list tr').each(function() {
                if ($(this).data('product') == productId) {
                    productExists = true;
                }
            });

            if (productExists) {
                toastr.error('Product already added.');
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

                    $('#user').prop('disabled', true);

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
            let unitPrice = parseFloat(row.find('td:nth-child(3)').text().replace('₹', '').trim()) || 0;
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
        let userId = $('#userId').val();
        if (!userId) {
            toastr.error('Please select a customer.');
            return;
        }
        formData.append('userId', userId);

        let cartItems = [];

        // Loop through each cart row to collect product data
        $('#cart_list tbody tr').each(function() {
            let productId = $(this).data('product');
            let productName = $(this).find('td:nth-child(1)').text().trim();
            let sku = $(this).find('td:nth-child(2)').text().trim();
            let unitPrice = parseFloat($(this).find('td:nth-child(3)').text().replace('₹', '').trim()) || 0;
            let quantity = parseInt($(this).find('.quantity').val()) || 1;
            let totalPrice = parseFloat($(this).find('.total-price').text().replace('₹', '').trim()) || 0;

            cartItems.push({
                productId,
                productName,
                sku,
                unitPrice,
                quantity,
                totalPrice
            });
        });

        if (cartItems.length === 0) {
            toastr.error('Cart is empty!');
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
                if (response.success) {
                    window.location.href = `/admin/orders/edit/${response.orderId}`;
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    }

    // Trigger the function when clicking "Checkout" or similar button
    $('.btn-submit').on('click', sendCartData);
</script>
@endsection