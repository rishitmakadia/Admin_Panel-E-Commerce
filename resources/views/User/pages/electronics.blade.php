@extends('layouts.main')
@section('title', "ELECTRONICS")

@section('content')
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
    <div class="container my-4">
        <div class="mb-4">
            <!-- Main Category Filter Bar -->
            <div id="filter-bar" class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <button class="btn btn-primary filter-btn active" data-filter-type="all">All Products</button>
                <div id="filter-bar-loading" class="text-muted">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading filters...</span>
                    </div>
                </div>
            </div>

            <!-- Subcategory Filter Bar -->
            <div id="subcategory-filter-bar" class="d-flex flex-wrap align-items-center gap-2" style="display: none;">
                <span class="text-muted me-2">Filter by subcategory:</span>
                <button class="btn btn-sm btn-secondary subcategory-filter-btn active" data-filter-type="all-subs">All
                    Subcategories
                </button>
                <div id="subcategory-filters-container"></div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div id="main-content" class="row g-3">
            <div class="col-12 text-center text-muted">Loading categories...</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            const apiBase = "http://localhost/laravel/admin-panel/public/api";
            const token = "{{ session('api_token') }}";
            let allElectronicsData = [];
            let currentCategoryData = [];
            let currentCategory = '';
            let allSubcategoriesData = [];

            loadElectronicsData();

            function loadElectronicsData() {
                $.ajax({
                    url: `${apiBase}/electronic`,
                    method: 'POST',
                    headers: {'Authorization': `Bearer ${token}`},
                    success: function (res) {
                        if (res.data && res.data.length > 0) {
                            allElectronicsData = res.data;
                            populateFilterBar();
                            displayAllSubcategories(); // Show all subcategories instead of categories
                        } else {
                            $('#main-content').html(`
                                <div class="col-12 text-center text-danger">No electronics found</div>
                            `);
                            $('#filter-bar-loading').hide();
                        }
                    },
                    error: function () {
                        $('#main-content').html(`
                            <div class="col-12 text-center text-danger">Error loading categories</div>
                        `);
                        $('#filter-bar-loading').hide();
                    }
                });
            }

            function populateFilterBar() {
                $('#filter-bar-loading').hide();

                allElectronicsData.forEach(category => {
                    const categoryName = category.electronics_category || 'Unnamed';
                    $('#filter-bar').append(`
                        <button class="btn btn-outline-primary filter-btn"
                                data-filter-type="category"
                                data-category="${categoryName}">
                            ${categoryName}
                        </button>
                    `);
                });
            }

            // New function to display all subcategories from all electronics categories
            function displayAllSubcategories() {
                hideSubcategoryFilters();
                const $content = $('#main-content').empty();
                $content.html(`<div class="col-12 text-center text-muted">Loading all subcategories...</div>`);

                let completedRequests = 0;
                let allSubcategories = [];

                allElectronicsData.forEach(category => {
                    const categoryName = category.electronics_category || 'Unnamed';
                    $.ajax({
                        url: `${apiBase}/electronic/category`,
                        method: 'POST',
                        headers: {'Authorization': `Bearer ${token}`},
                        data: {category: categoryName},
                        success: function (res) {
                            if (res.data && res.data.length > 0) {
                                res.data.forEach(sub => {
                                    sub.parentCategory = categoryName;
                                    allSubcategories.push(sub);
                                });
                            }
                            completedRequests++;
                            if (completedRequests === allElectronicsData.length) {
                                displaySubcategories(allSubcategories);
                                allSubcategoriesData = allSubcategories; // Store for later use
                            }
                        },
                        error: function () {
                            completedRequests++;
                            if (completedRequests === allElectronicsData.length) {
                                displaySubcategories(allSubcategories);
                                allSubcategoriesData = allSubcategories;
                            }
                        }
                    });
                });
            }

            function displaySubcategories(subcategories) {
                const $content = $('#main-content').empty();

                if (subcategories.length === 0) {
                    $content.html(`<div class="col-12 text-center text-muted">No subcategories found</div>`);
                    return;
                }

                subcategories.forEach(sub => {
                    const subLabel = sub.category_name || 'Unnamed';
                    const subImg = sub.category_photo || '';
                    const parentCategory = sub.parentCategory || '';

                    const imageHTML = subImg
                        ? `<img src="${subImg}" class="card-img-top img-thumbnail" alt="${subLabel}" style="height: 200px; object-fit: cover; cursor: pointer;"/>`
                        : `<dotlottie-wc src="https://lottie.host/eea11181-87c3-418e-926d-91e40f367396/1QlVTcFT80.lottie" style="height: 200px; cursor: pointer;" speed="1" autoplay loop></dotlottie-wc>`;

                    $content.append(`
                        <div class="col-6 col-md-3">
                            <div class="card shadow-sm subCategory-card" style="cursor:pointer" data-subcategory="${subLabel}">
                                ${imageHTML}
                                <div class="card-body text-center">
                                    <h6 class="card-title">${subLabel}</h6>
                                    ${parentCategory ? `<small class="text-muted d-block">${parentCategory}</small>` : ''}
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            function displayAllSubcategoryItems(categoryName) {
                currentCategory = categoryName;

                $.ajax({
                    url: `${apiBase}/electronic/category`,
                    method: 'POST',
                    headers: {'Authorization': `Bearer ${token}`},
                    data: {category: categoryName},
                    success: function (res) {
                        if (!res.data || res.data.length === 0) {
                            $('#main-content').html(`
                                <div class="col-12 text-center text-danger">No subcategories found for ${categoryName}</div>
                            `);
                            hideSubcategoryFilters();
                            return;
                        }

                        currentCategoryData = res.data;
                        populateSubcategoryFilters(res.data);
                        loadAllSubcategoryItems(res.data);
                    },
                    error: function () {
                        $('#main-content').html(`
                            <div class="col-12 text-center text-danger">Error loading subcategories for ${categoryName}</div>
                        `);
                        hideSubcategoryFilters();
                    }
                });
            }

            function populateSubcategoryFilters(subcategories) {
                const $container = $('#subcategory-filters-container').empty();

                subcategories.forEach(sub => {
                    const subLabel = sub.category_name || 'Unnamed';
                    $container.append(`
                        <button class="btn btn-sm btn-outline-secondary subcategory-filter-btn"
                                data-filter-type="subcategory"
                                data-subcategory="${subLabel}">
                            ${subLabel}
                        </button>
                    `);
                });

                showSubcategoryFilters();
            }

            function loadAllSubcategoryItems(subcategories) {
                const $content = $('#main-content').empty();
                $content.html(`<div class="col-12 text-center text-muted">Loading items...</div>`);

                let completedRequests = 0;
                let allItems = [];

                if (subcategories.length === 0) {
                    $content.html(`<div class="col-12 text-center text-danger">No subcategories found</div>`);
                    return;
                }

                subcategories.forEach(sub => {
                    const subLabel = sub.category_name || 'Unnamed';

                    $.ajax({
                        url: `${apiBase}/electronic/category/subcategory`,
                        method: 'POST',
                        headers: {'Authorization': `Bearer ${token}`},
                        data: {subCategory: subLabel}, // Fixed: using the correct parameter name
                        success: function (res) {
                            if (res.data && res.data.length > 0) {
                                res.data.forEach(item => {
                                    item.parentSubcategory = subLabel;
                                    allItems.push(item);
                                });
                            }

                            completedRequests++;
                            if (completedRequests === subcategories.length) {
                                displayItems(allItems);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error loading items for', subLabel, ':', error);
                            completedRequests++;
                            if (completedRequests === subcategories.length) {
                                displayItems(allItems);
                            }
                        }
                    });
                });
            }

            function displaySpecificSubcategoryItems(subcategoryName) {
                $.ajax({
                    url: `${apiBase}/electronic/category/subcategory`,
                    method: 'POST',
                    headers: {'Authorization': `Bearer ${token}`},
                    data: {subCategory: subcategoryName},
                    success: function (res) {
                        if (!res.data || res.data.length === 0) {
                            $('#main-content').html(`
                                <div class="col-12 text-center text-danger">No items found for ${subcategoryName}</div>
                            `);
                            return;
                        }
                        const itemsWithParent = res.data.map(item => ({
                            ...item,
                            parentSubcategory: subcategoryName
                        }));

                        displayItems(itemsWithParent);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading items for', subcategoryName, ':', error);
                        $('#main-content').html(`
                            <div class="col-12 text-center text-danger">Error loading items for ${subcategoryName}</div>
                        `);
                    }
                });
            }

            function displayItems(items) {
                const $content = $('#main-content').empty();

                if (items.length === 0) {
                    $content.html(`<div class="col-12 text-center text-muted">No items found</div>`);
                    return;
                }

                items.forEach(item => {
                    const itemID = item.id;
                    const itemLabel = item.subCategory_name || 'Unnamed';
                    const itemImg = item.subCategory_photo || '';
                    const price = item.subCategory_price || 'XXX';
                    const parentSub = item.parentSubcategory || '';

                    const imageHTML = itemImg
                        ? `<img src="${itemImg}" class="card-img-top img-thumbnail" alt="${itemLabel}" style="height: 200px; object-fit: cover; cursor: pointer;"/>`
                        : `<dotlottie-wc src="https://lottie.host/eea11181-87c3-418e-926d-91e40f367396/1QlVTcFT80.lottie" style="height: 200px; cursor: pointer;" speed="1" autoplay loop></dotlottie-wc>`;

                    $content.append(`
                        <div class="col-6 col-md-3">
                            <div class="card shadow-sm">
                                ${imageHTML}
                                <div class="card-body text-center">
                                    <h6 class="card-title">${itemLabel}</h6>
                                    ${parentSub ? `<small class="text-muted d-block mb-2">${parentSub}</small>` : ''}
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <strong class="text-success">${price}</strong>
                                        <button type="button" class="btn btn-outline-success btn-sm add-to-cart-btn" data-id="${itemID}">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            function showSubcategoryFilters() {
                $('#subcategory-filter-bar').show();
            }

            function hideSubcategoryFilters() {
                $('#subcategory-filter-bar').hide();
                $('#subcategory-filters-container').empty();
            }

            // Main filter bar click handler
            $(document).on('click', '.filter-btn', function () {
                // Remove active class from all main filter buttons
                $('.filter-btn').removeClass('active').removeClass('btn-primary').addClass('btn-outline-primary');

                // Add active class to clicked button
                $(this).addClass('active').removeClass('btn-outline-primary').addClass('btn-primary');

                const filterType = $(this).data('filter-type');
                const categoryName = $(this).data('category');

                if (filterType === 'all') {
                    displayAllSubcategories(); // Show all subcategories instead of main categories
                } else if (filterType === 'category') {
                    displayAllSubcategoryItems(categoryName);
                }
            });

            // Subcategory filter bar click handler
            $(document).on('click', '.subcategory-filter-btn', function () {
                // Remove active class from all subcategory filter buttons
                $('.subcategory-filter-btn').removeClass('active').removeClass('btn-secondary').addClass('btn-outline-secondary');

                // Add active class to clicked button
                $(this).addClass('active').removeClass('btn-outline-secondary').addClass('btn-secondary');

                const filterType = $(this).data('filter-type');
                const subcategoryName = $(this).data('subcategory');

                if (filterType === 'all-subs') {
                    loadAllSubcategoryItems(currentCategoryData);
                } else if (filterType === 'subcategory') {
                    displaySpecificSubcategoryItems(subcategoryName);
                }
            });

            // Subcategory card click handler (when clicking on subcategory cards)
            $(document).on('click', '.subCategory-card', function () {
                const subCategoryName = $(this).data('subcategory');
                displaySpecificSubcategoryItems(subCategoryName);
            });

            // Add to cart handler
            $(document).on('click', '.add-to-cart-btn', function () {
                const itemID = $(this).data('id');
                const $button = $(this);
                const item_type = 'Electronics';
                // Disable button temporarily
                $button.prop('disabled', true).text('Adding...');

                $.ajax({
                    url: 'http://localhost/laravel/admin-panel/public/api/user/add/cart',
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer {{ session('api_token') }}'
                    },
                    data: {
                        api_token: '{{session('api_token')}}',
                        itemID: itemID,
                        item_type: item_type,
                    },
                    success: function (response) {
                        if (response.data !== undefined) {
                            console.log(response);
                            $button.prop('disabled', false).text('Added').removeClass('btn-outline-success').addClass('btn-success');
                            if (typeof cardCount === 'function') {
                                cardCount();
                            }
                        } else {
                            alert('Error in adding to the cart')
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr);
                    }
                });
                console.log('Adding item to cart:', itemId);
            });
        });
    </script>

    <style>
        .filter-btn {
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
        }

        .subcategory-filter-btn {
            transition: all 0.3s ease;
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .filter-btn:hover, .subcategory-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .filter-btn.active {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .subcategory-filter-btn.active {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        }

        .card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-img-top, dotlottie-wc {
            border-radius: 12px 12px 0 0;
        }

        .add-to-cart-btn {
            transition: all 0.3s ease;
            border-radius: 20px;
        }

        #filter-bar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        #subcategory-filter-bar {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
        }

        .text-success {
            font-weight: 600;
        }
    </style>
@endpush
