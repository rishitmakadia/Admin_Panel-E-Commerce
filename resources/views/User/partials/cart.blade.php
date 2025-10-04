<div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar" aria-labelledby="cartSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="cartSidebarLabel">
            <i class="fas fa-shopping-cart me-2"></i>Your Cart
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-group" id="cartItemsList">
            <li class="list-group-item text-muted">Loading cart items...</li>
        </ul>

        <div class="mt-3 d-flex justify-content-between fw-bold">
            <span>Total:</span>
            <span id="cartTotal">0</span>
        </div>

        <button id="checkoutBtn" class="btn btn-warning w-100 mt-3">
            Checkout
        </button>
    </div>
</div>

<script>
    $(document).ready(function () {
        cardCount();
        // Load cart when sidebar opens
        $('#cartSidebar').on('show.bs.offcanvas', function () {
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
                    let cartList = $('#cartItemsList');
                    cartList.empty();
                    let total = 0;

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(item => {
                            total += item.price * item.quantity;
                            cartList.append(`
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${item.subCategory_name}</strong> <br>
                                    <div class="input-group input-group-sm" style="width: 120px;">
                                        <button class="btn btn-outline-danger btn-decrement" type="button">−</button>
                                        <input type="text" class="form-control text-center item-qty" value="${item.quantity}" data-id="${item.item_id}" readonly>
                                        <button class="btn btn-outline-success btn-increment" type="button">+</button>
                                    </div>
                                </div>
                                <span class="px-2 py-1 border border-primary rounded">Price: ₹${item.price}</span>
                            </li>
                        `);
                        });
                    } else {
                        cartList.append('<li class="list-group-item text-muted">Your cart is empty.</li>');
                    }

                    $('#cartTotal').text(`${total.toFixed(2)}`);
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        });

        $('#checkoutBtn').click(function () {
            window.location.href = 'http://localhost/laravel/admin-panel/public/checkout'
            {{--$.ajax({--}}
            {{--    url: 'http://localhost/laravel/admin-panel/public/api/user/purchase',--}}
            {{--    type: 'POST',--}}
            {{--    data: {--}}
            {{--        api_token: '{{ session('api_token') }}'--}}
            {{--    },--}}
            {{--    success: function (response) {--}}
            {{--        cardCount();--}}
            {{--        alert(response.message);--}}
            {{--        $('#cartSidebar').offcanvas('hide');--}}
            {{--    },--}}
            {{--    error: function (xhr) {--}}
            {{--        console.log(xhr);--}}
            {{--    }--}}
            {{--});--}}
        });

        $(document).on('click', '.btn-increment', function () {
            let input = $(this).siblings('.item-qty');
            let currentVal = parseInt(input.val());
            let newVal = currentVal + 1;
            input.val(newVal);
            let itemID = input.data('id');
            updateQuantity(itemID, newVal);
        });

        $(document).on('click', '.btn-decrement', function () {
            let input = $(this).siblings('.item-qty');
            let currentVal = parseInt(input.val());
            console.log(currentVal);
            if (currentVal > 0) {
                let newVal = currentVal - 1;
                input.val(newVal);
                let itemID = input.data('id');
                updateQuantity(itemID, newVal);
            }
        });

        function updateQuantity(itemID, quantity) {
            $.ajax({
                url: 'http://localhost/laravel/admin-panel/public/api/user/add/cart',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {
                    api_token: '{{ session('api_token') }}',
                    itemID: itemID,
                    item_type: 'Electronics',
                    quantity: quantity,
                },
                success: function (response) {
                    console.log('Quantity updated:', response);
                    $('#cartSidebar').trigger('show.bs.offcanvas');
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }
    });
</script>
