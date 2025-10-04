@extends('layouts.main')

@section('title', 'Checkout')

@section('content')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-center mb-4">
                    <i class="fas fa-credit-card me-2"></i>Checkout
                </h2>
                <!-- Step Indicator -->
                <div class="d-flex justify-content-center align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <div class="step-circle active" id="step-cart">
                            <span>1</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-circle" id="step-address">
                            <span>2</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-circle" id="step-payment">
                            <span>3</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="d-flex" style="width: 300px; justify-content: space-between;">
                        <small class="text-muted">Review Cart</small>
                        <small class="text-muted">Address</small>
                        <small class="text-muted">Payment</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Cart Items Section -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Review Your Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-3" id="cartLoading">
                            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            <p class="mt-2">Loading your cart...</p>
                        </div>
                        <div id="cartItems"></div>
                        <div class="text-center py-5" id="emptyCart" style="display: none;">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Your cart is empty</h4>
                            <p class="text-muted">Add some items to proceed with checkout</p>
                            <a href="{{ route('user.electronics') }}" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <!-- Saved Addresses -->
                        <div id="savedAddresses"></div>

                        <!-- Add New Address Button -->
                        <button class="btn btn-outline-primary mb-3" data-bs-toggle="modal"
                                data-bs-target="#addressModal">
                            <i class="fas fa-plus me-2"></i>Add New Address
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card border-primary">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="summarySubtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span id="summaryShipping">₹50.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18%):</span>
                            <span id="summaryTax">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success">Discount:</span>
                            <span id="summaryDiscount" class="text-success">-₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="summaryTotal">₹0.00</strong>
                        </div>

                        <!-- Coupon Code -->
{{--                        <div class="mb-3">--}}
{{--                            <div class="input-group">--}}
{{--                                <input type="text" class="form-control" id="couponCode" placeholder="Coupon Code">--}}
{{--                                <button class="btn btn-outline-primary" type="button" id="applyCoupon">Apply</button>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <!-- Pay Now Button -->
                        <button class="btn btn-success w-100 py-2" id="payNowBtn" disabled>
                            <i class="fas fa-lock me-2"></i>Pay Now
                        </button>

                        <!-- Security Badge -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>Your payment is secure and encrypted
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Add Shipping Address</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addressForm">
                        <input type="hidden" name="addressId" id="addressId">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pincode" class="form-label">Pincode *</label>
                                <input type="text" class="form-control" id="pincode" maxlength="6">
                            </div>
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country *</label>
                                <select class="form-select" id="country">
                                    <option value="India">India</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3" id="afterPincode">
                            <div class="col-md-6">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city">
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addressLine1" class="form-label">Address Line 1 *</label>
                            <input type="text" class="form-control" id="addressLine1">
                        </div>
                        <div class="mb-3">
                            <label for="addressLine2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="addressLine2">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="addressType" id="defaultAddress">
                            <label class="form-check-label" for="defaultAddress">
                                Default
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveAddressBtn">Save Address</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .step-circle.active {
            background: #0d6efd;
            color: white;
        }

        .step-circle.completed {
            background: #198754;
            color: white;
        }

        .step-line {
            width: 100px;
            height: 2px;
            background: #dee2e6;
        }

        .address-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .address-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
        }

        .address-card.selected {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .cart-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #0d6efd;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
    </style>
@endsection

@push('scripts')
    <script>
        let cartData = [];
        let selectedAddress = null;
        let subtotal = 0;
        let shipping = 0;
        let tax = 0;
        let discount = 0;

        $(document).ready(function () {
            loadCartItems();
            loadSavedAddresses();
            loadCartItems();
            $('#afterPincode').hide();
        });

        // Load cart items
        function loadCartItems() {
            $('#cartLoading').show();
            $.ajax({
                url: 'http://localhost/laravel/admin-panel/public/api/user/show/cart',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {
                    api_token: '{{ session('api_token') }}'
                },
                success: function (response) {
                    $('#cartLoading').hide();
                    cartData = response.data || [];
                    renderCartItems();
                    calculateTotals();
                },
                error: function (xhr) {
                    $('#cartLoading').hide();
                    console.error('Error loading cart:', xhr);
                    showEmptyCart();
                }
            });
        }

        // Render cart items
        function renderCartItems() {
            const cartContainer = $('#cartItems');
            cartContainer.empty();

            if (cartData.length === 0) {
                showEmptyCart();
                return;
            }

            cartData.forEach(item => {
                const subImg = item.subCategory_photo || '';
                const imageHTML = subImg
                    ? `<img src="${subImg}" class="img-fluid rounded shadow-sm" alt="${item.subCategory_name}" style="height: 120px; width: 120px; object-fit: cover; cursor: pointer;"/>`
                    : `<dotlottie-wc src="https://lottie.host/eea11181-87c3-418e-926d-91e40f367396/1QlVTcFT80.lottie"
                 style="height: 120px; width: 120px; cursor: pointer;" speed="1" autoplay loop></dotlottie-wc>`;
                let totalprice = item.quantity * item.price;
                const cartItemHtml = `
            <div class="cart-item p-3 mb-3 border rounded shadow-sm bg-white" data-item-id="${item.item_id}">
                <div class="row align-items-center g-3">
                    <div class="col-md-2 text-center">
                        ${imageHTML}
                    </div>
                    <div class="col-md-3">
                        <h6 class="mb-1">${item.subCategory_name}</h6>
                        <small class="text-muted">Electronics</small>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-danger quantity-btn btn-decrement" data-item-id="${item.item_id}">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control form-control-sm text-center mx-2 item-quantity"
                                style="width: 60px;" value="${item.quantity}"
                                data-item-id="${item.item_id}" min="1">
                            <button class="btn btn-sm btn-outline-success quantity-btn btn-increment" data-item-id="${item.item_id}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <strong>₹${totalprice.toLocaleString()}</strong>
                    </div>
                    <div class="col-md-2 text-center">
                        <button class="btn btn-sm btn-outline-danger btn-remove" data-item-id="${item.item_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
                cartContainer.append(cartItemHtml);
            });
        }

        // Show empty cart
        function showEmptyCart() {
            $('#cartItems').hide();
            $('#emptyCart').show();
            $('#payNowBtn').prop('disabled', true);
        }

        // Calculate totals
        function calculateTotals() {
            subtotal = 0;
            cartData.forEach(item => {
                subtotal += item.price * item.quantity;
            });

            tax = subtotal * 0.18; // 18% GST
            const total = subtotal + shipping + tax - discount;

            $('#summarySubtotal').text(`₹${subtotal.toFixed(2)}`);
            $('#summaryShipping').text(`₹${shipping.toFixed(2)}`);
            $('#summaryTax').text(`₹${tax.toFixed(2)}`);
            $('#summaryDiscount').text(`-₹${discount.toFixed(2)}`);
            $('#summaryTotal').text(`₹${total.toFixed(2)}`);

            // Enable pay button if cart has items and address is selected
            if (cartData.length > 0 && selectedAddress) {
                $('#payNowBtn').prop('disabled', false);
            }
        }

        // Quantity controls
        $(document).on('click', '.btn-increment', function () {
            const itemId = $(this).data('item-id');
            const input = $(`.item-quantity[data-item-id="${itemId}"]`);
            const currentQty = parseInt(input.val());
            const newQty = currentQty + 1;
            input.val(newQty);
            updateQuantity(itemId, newQty);
        });

        $(document).on('click', '.btn-decrement', function () {
            const itemId = $(this).data('item-id');
            const input = $(`.item-quantity[data-item-id="${itemId}"]`);
            const currentQty = parseInt(input.val());
            if (currentQty > 1) {
                const newQty = currentQty - 1;
                input.val(newQty);
                updateQuantity(itemId, newQty);
            }
        });

        $(document).on('change', '.item-quantity', function () {
            const itemId = $(this).data('item-id');
            const newQty = parseInt($(this).val());
            if (newQty >= 1) {
                updateQuantity(itemId, newQty);
            } else {
                $(this).val(1);
            }
        });

        $(document).on('click', '.btn-remove', function () {
            const itemId = $(this).data('item-id');
            if (confirm('Remove this item from cart?')) {
                updateQuantity(itemId, 0);
            }
        });

        // Update quantity
        function updateQuantity(itemId, quantity) {
            $.ajax({
                url: 'http://localhost/laravel/admin-panel/public/api/user/add/cart',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {
                    api_token: '{{ session('api_token') }}',
                    itemID: itemId,
                    item_type: 'Electronics',
                    quantity: quantity,
                },
                success: function (response) {
                    const itemIndex = cartData.findIndex(item => item.item_id == itemId);
                    if (itemIndex !== -1) {
                        if (quantity === 0) {
                            cartData.splice(itemIndex, 1);
                        } else {
                            cartData[itemIndex].quantity = quantity;
                        }
                    }
                    renderCartItems();
                    calculateTotals();
                    cardCount(); // Update navbar cart count
                },
                error: function (xhr) {
                    console.error('Error updating quantity:', xhr);
                    alert('Error updating item quantity');
                }
            });
        }

        // Render saved addresses
        function loadSavedAddresses() {
            const container = $('#savedAddresses');
            container.empty();
            $.ajax({
                url: 'http://localhost/laravel/admin-panel/public/api/user/show/address',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {},
                success: function (response) {
                    console.log(response);
                    const addresses = response.data || [];
                    if (addresses.length === 0) {
                        container.append(`
            <div class="alert alert-info text-center w-100">
                <i class="fa fa-info-circle"></i> No saved addresses. Please add a new address.
            </div>
        `);
                        return;
                    }

                    let rowHtml = `<div class="row">`;

                    addresses.forEach((address, index) => {
                        rowHtml += `
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 border-0 address-card" data-address-id="${address.id}">
                    <div class="card-body">
                        <!-- Radio + Address -->
                        <div class="form-check mb-2">
                            <input class="form-check-input address-radio"
                                   type="radio" name="selectedAddress" value="${address.id}">
                            <label class="form-check-label fw-bold">
                                <i class="fa fa-home text-primary me-1"></i> Address #${address.id}
                            </label>
                        </div>

                        <p class="text-muted mb-2">
                            ${address.address_line_1 ?? ''} <br>
                            ${address.address_line_2 ? address.address_line_2 + '<br>' : ''}
                            ${address.city ?? ''}, ${address.state ?? ''} - ${address.pincode}
                        </p>

                        <!-- Small edit/delete below address -->
                        <div class="d-flex justify-content-start gap-3 mt-2">
    <button type="button" class="btn btn-sm btn-outline-primary edit-address" data-id="${address.id}">
        <i class="fa fa-pen"></i> Edit
    </button>

    <button type="button" class="btn btn-sm btn-outline-danger delete-address ms-2" data-id="${address.id}">
        <i class="fa fa-trash"></i> Delete
    </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

                        // Close row and open new row after every 3 addresses
                        if ((index + 1) % 3 === 0) {
                            rowHtml += `</div><div class="row">`;
                        }
                    });

                    rowHtml += `</div>`;
                    container.append(rowHtml);
                },
                error: function (xhr) {
                    console.log("Error: ", xhr.responseText);
                }
            })
        }

        $(document).on('click', ".edit-address" , function(){
            const id = $(this).data("id");
            console.log("Edit address", id);
            $.ajax({
                url: 'http://localhost/laravel/admin-panel/public/api/user/show/address',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {
                    address_id: id,
                },
                success: function (response) {
                    $('#addressId').val(response.data.id);
                    $('#pincode').val(response.data.pincode);
                    $('#city').val(response.data.city);
                    $('#state').val(response.data.state);
                    $('#country').val(response.data.country);
                    $('#addressLine1').val(response.data.address_line_1);
                    $('#addressLine2').val(response.data.address_line_2);
                    $('input[name="addressType"]:checked').val(response.data.type);
                    $('#addressModal').modal('show');
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            })
        });

        $(".delete-address").on("click", function () {
            const id = $(this).data("id");
            console.log("Delete address", id);
            // TODO: confirm & call delete API
        });

        // Address selection
        $(document).on('change', '.address-radio', function () {
            selectedAddress = $(this).val();
            $('.address-card').removeClass('selected');
            $(this).closest('.address-card').addClass('selected');
            updateSteps();
            calculateTotals();
        });

        // Save new address
        $('#saveAddressBtn').click(function () {
            const add= $('#addressId').val();
            $.ajax({
                url: 'http://localhost/laravel/admin-panel/public/api/user/address',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {
                    api_token: '{{ session('api_token') }}',
                    address_id: add !== "" ? add : null,
                    pincode: $('#pincode').val(),
                    city: $('#city').val(),
                    state: $('#state').val(),
                    country: $('#country').val(),
                    address_line_1: $('#addressLine1').val(),
                    address_line_2: $('#addressLine2').val(),
                    type: $('input[name="addressType"]:checked').val(),
                },
                success: function (response) {
                    $('#addressId').val(response.address_id)
                    $('#addressModal').modal('hide');
                    $('#addressForm')[0].reset();
                    loadSavedAddresses();
                    updateSteps();
                    calculateTotals();
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            })
        });

        // Apply coupon (placeholder)
        // $('#applyCoupon').click(function () {
        //     const couponCode = $('#couponCode').val().trim();
        //     if (!couponCode) {
        //         alert('Please enter a coupon code');
        //         return;
        //     }
        //
        //     // Mock coupon validation
        //     if (couponCode === 'SAVE10') {
        //         discount = subtotal * 0.1; // 10% discount
        //         calculateTotals();
        //         alert('Coupon applied successfully!');
        //     } else {
        //         alert('Invalid coupon code');
        //     }
        // });

        // Update step indicators
        function updateSteps() {
            if (cartData.length > 0) {
                $('#step-cart').addClass('completed').removeClass('active');
                $('#step-address').addClass('active');

                if (selectedAddress) {
                    $('#step-address').addClass('completed').removeClass('active');
                    $('#step-payment').addClass('active');
                }
            }
        }

        // Pay Now button
        $('#payNowBtn').click(function () {
            if (!selectedAddress) {
                alert('Please select a shipping address');
                return;
            }
            if (cartData.length === 0) {
                alert('Your cart is empty');
                return;
            }
            // Prepare order data
            const orderData = {
                address_id: selectedAddress,
                subtotal: subtotal,
                shipping: shipping,
                tax: tax,
                discount: discount,
                total: subtotal + shipping + tax - discount,
                // coupon_code: $('#couponCode').val()
            };

            // For now, show confirmation - replace with your payment gateway integration
            if (confirm('Proceed with payment of ₹' + (subtotal + shipping + tax - discount).toFixed(2) + '?')) {
                // Call your existing purchase API
                $.ajax({
                    url: 'http://localhost/laravel/admin-panel/public/api/user/checkout',
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer {{ session('api_token') }}'
                    },
                    data: {
                        address_id:orderData.address_id,
                        subtotal:orderData.subtotal,
                        shipping:orderData.shipping,
                        tax:orderData.tax,
                        discount:orderData.discount,
                        totalAmount:orderData.total,
                        api_token: '{{ session('api_token') }}',
                    }, // amount in INR
                    success: function (order) {
                        alert(order.id);
                        let options = {
                            "key": "{{ env('RAZORPAY_KEY') }}",
                            // "amount": order.amount, // No need to pass this, only id is enough
                            "currency": "INR",
                            "name": "Project AP",
                            "description": "Test Payment",
                            "order_id": order.id,
                            "handler": function (response) {
                                $.ajax({
                                    url: 'http://localhost/laravel/admin-panel/public/api/user/verify-checkout',
                                    method: 'POST',
                                    headers: {
                                        'Authorization': 'Bearer {{ session('api_token') }}'
                                    },
                                    data: {
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        razorpay_order_id: response.razorpay_order_id || options.order_id,
                                        razorpay_signature: response.razorpay_signature
                                    },
                                    success: function (data) {
                                        loadCartItems();
                                        alert("Payment Successful!");
                                    },
                                    error: function (xhr) {
                                        console.log(xhr.responseText); // 🔍 log actual error
                                        alert("Payment verification failed.");
                                        loadCartItems();
                                    }
                                });
                            },
                            "theme": {
                                "color": "#3399cc"
                            }
                        };
                        let rzp = new Razorpay(options);
                        rzp.open();
                    }
                });
            }
        });

        // Pincode validation
        $('#pincode').on('input', function () {
            const pincode = $(this).val();
            if (pincode.length === 6) {
                console.log('Validating pincode:', pincode);
                $('#afterPincode').hide();
                $('#city').val('');
                $('#state').val('');
                $.ajax({
                    url: `https://api.postalpincode.in/pincode/${pincode}`,
                    type: 'GET',
                    success: function (data) {
                        if (data[0].Status === 'Success') {
                            const section = data[0].PostOffice[0];
                            console.log(section);
                            $('#city').val(section.Block);
                            $('#state').val(section.State);
                            $('#afterPincode').show();
                        }
                        if (data[0].Status === 'Error') {
                            alert(data[0].Message);
                            $('#pincode').val('');
                        }
                    },
                    error: function (xhr) {
                        console.log('pincode xhr: ', xhr);
                    }
                })
            }
        });

        // Clear the form
        $('#addressModal').on('hidden.bs.modal', function () {
            $('#addressForm')[0].reset();   // clear all inputs
            $('#addressId').val('');        // clear hidden field
            $('input[name="addressType"]').prop('checked', false); // clear radio buttons
        });

    </script>
@endpush
