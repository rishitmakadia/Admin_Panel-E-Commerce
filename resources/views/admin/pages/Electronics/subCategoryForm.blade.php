@extends('layouts.app')

@section('content')
    <div class="modal fade" id="ajaxSubCategoryElectModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="subCategoryForm" name="subCategoryForm" enctype="multipart/form-data">
                    {{-- Hidden input for the subcategory ID, crucial for updates --}}
                    <input type="hidden" name="subCategory_elect_id" id="subCategory_elect_id">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modelHeading"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="formErrors" class="alert alert-danger d-none m-3">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>
                    <div class="modal-body row g-3 p-3">
                        <div class="col-md-12">
                            <label for="electronic_id" class="form-label">Parent Electronic <span
                                    class="text-danger">*</span></label>
                            <select name="electronic_id" id="electronic_id" class="form-select">
                                <option value="" selected disabled>-- Select an Electronic --</option>
                                @foreach ($electronics as $electronic)
                                    <option
                                        value="{{ $electronic->id }}">{{ $electronic->electronics_category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="category_wrapper" class="col-md-12">
                            <label for="electronics_category_id" class="form-label">Parent Category <span
                                    class="text-danger">*</span></label>
                            <select name="electronics_category_id" id="electronics_category_id" class="form-select">
                                {{-- Options will be populated via AJAX --}}
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label for="subCategory_name" class="form-label">Sub-Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="subCategory_name" id="subCategory_name" class="form-control"
                                   placeholder="Enter sub-category name">
                        </div>

                        <div class="col-md-12">
                            <label for="subCategory_price" class="form-label">Price <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="subCategory_price" id="subCategory_price" class="form-control"
                                   placeholder="Enter price">
                        </div>

                        <div class="col-md-8">
                            <label for="subCategory_photo" class="form-label">Sub-Category Photo</label>
                            <input type="file" name="subCategory_photo" id="subCategory_photo" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <img id="previewImage" src="" alt="Photo Preview" class="img-fluid mt-2 rounded shadow-sm"
                                 style="max-height: 150px; object-fit: contain; display: none;">
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <label class="form-label me-5 mb-0">Status</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="subCategory_status"
                                       name="subCategory_status"
                                       checked>
                                <span id="statusLabel" class="badge bg-success ms-2">Active</span>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="saveBtn" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-end mb-2">
            <button type="button" class="btn btn-success" id="createNewSubCategoryElect">
                <i class="fas fa-plus-circle me-2"></i> New Sub-Category
            </button>
        </div>

        {{-- Data Table --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-hover data-table w-100">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Parent</th>
                        <th>Parent Category</th>
                        <th>Sub-Category Name</th>
                        <th>Price</th>
                        <th>Photo</th>
                        <th>Status</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Photo Preview Modal --}}
    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center p-0">
                    <img id="photoPreviewModal" src="" alt="Full Size Photo" class="w-100 rounded"
                         style="max-height: 90vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Use a more recent version of jQuery if possible, but 3.6.0 is fine. --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            const dataTable = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ isset($category, $electronic) ? url('admin/electronics/' . $electronic->id . '/categories/' . $category->id . '/subcategories') : route('data.subcategories.all') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'parent', name: 'refersCategoryElect.refersElectronics.electronics_category'},
                    {data: 'parent_category', name: 'refersCategoryElect.category_name'},
                    {data: 'subCategory_name', name: 'subCategory_name'},
                    {data: 'subCategory_price', name: 'subCategory_price'},
                    {data: 'subCategory_photo', name: 'subCategory_photo', orderable: false, searchable: false},
                    {data: 'subCategory_status', name: 'subCategory_status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
            function loadCategories(electronicId, selectedCategoryId = null) {
                let categorySelect = $('#electronics_category_id');
                let categoryWrapper = $('#category_wrapper');

                if (!electronicId) {
                    categoryWrapper.addClass('d-none');
                    categorySelect.empty();
                    return;
                }

                categoryWrapper.removeClass('d-none');
                categorySelect.empty().append('<option value="" selected disabled>Loading...</option>');

                $.ajax({
                    url: `{{ url('admin/get-categories-by-electronic') }}/${electronicId}`,
                    type: 'GET',
                    success: function (data) {
                        categorySelect.empty().append('<option value="" selected disabled>-- Select a Category --</option>');
                        $.each(data, function (key, value) {
                            categorySelect.append(`<option value="${value.id}">${value.category_name}</option>`);
                        });
                        if (selectedCategoryId) {
                            categorySelect.val(selectedCategoryId);
                        }
                    },
                    error: (err) => console.error("Failed to load categories:", err)
                });
            }

            $('#electronic_id').on('change', function () {
                loadCategories($(this).val());
            });

            $('#createNewSubCategoryElect').click(function () {
                $('#subCategoryForm').trigger("reset");
                $('#subCategory_elect_id').val(''); // Clear the hidden ID
                $('#modelHeading').html("Create New Sub-Category");
                $('#previewImage').attr('src', '').hide();
                $('#formErrors').addClass('d-none');
                $('#category_wrapper').removeClass('d-none');
                $('#electronics_category_id').empty().append('<option value="" selected disabled>-- Select Parent Electronic First --</option>');
                $('#subCategory_status').prop('checked', true);
                $('#ajaxSubCategoryElectModal').modal('show');
            });

            $('body').on('click', '.editSubCategoryElect', function () {
                const id = $(this).data('id');
                const url = `{{ url('admin/subcategories') }}/${id}/edit`;

                $.get(url, function (data) {
                    $('#subCategoryForm').trigger("reset");
                    $('#formErrors').addClass('d-none');
                    $('#modelHeading').html("Edit Sub-Category");

                    $('#subCategory_elect_id').val(data.subcategory.id); // Set hidden ID
                    $('#subCategory_name').val(data.subcategory.subCategory_name);
                    $('#subCategory_price').val(data.subcategory.subCategory_price);
                    $('#subCategory_status').prop('checked', data.subcategory.subCategory_status === 'active');

                    if (data.subcategory.subCategory_photo) {
                        $('#previewImage').attr('src', `{{ asset('storage') }}/${data.subcategory.subCategory_photo}`).show();
                    } else {
                        $('#previewImage').attr('src', '').hide();
                    }

                    $('#electronic_id').val(data.electronic.id);
                    loadCategories(data.electronic.id, data.category.id);

                    $('#ajaxSubCategoryElectModal').modal('show');
                }).fail(err => alert("Error fetching data."));
            });

            $('#subCategoryForm').submit(function (e) {
                e.preventDefault();
                $('#saveBtn').text('Saving...').prop('disabled', true);

                let formData = new FormData(this);
                formData.set('subCategory_status', $('#subCategory_status').prop('checked') ? 'active' : 'inactive');

                const subCategoryId = $('#subCategory_elect_id').val();
                let url;

                if (subCategoryId) {
                    url = `{{ url('admin/subcategories') }}/${subCategoryId}`;
                    formData.append('_method', 'PUT'); // Method spoofing for update
                } else {
                    const electronicId = $('#electronic_id').val();
                    const categoryId = $('#electronics_category_id').val();
                    if (!electronicId || !categoryId) {
                        alert('Please select both a parent electronic and a parent category.');
                        $('#saveBtn').text('Save Changes').prop('disabled', false);
                        return;
                    }
                    url = `{{ url('admin/electronics') }}/${electronicId}/categories/${categoryId}/subcategories`;
                }

                $.ajax({
                    type: "POST", // Always POST, _method handles PUT/PATCH
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#ajaxSubCategoryElectModal').modal('hide');
                        dataTable.draw();
                    },
                    error: function (xhr) {
                        $('#formErrors').removeClass('d-none');
                        $('#errorList').empty();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, (key, messages) => {
                                $.each(messages, (i, msg) => $('#errorList').append('<li>' + msg + '</li>'));
                            });
                        } else {
                            $('#errorList').append('<li>An unexpected error occurred. Please try again.</li>');
                        }
                    },
                    complete: function () {
                        $('#saveBtn').text('Save Changes').prop('disabled', false);
                    }
                });
            });

            $('body').on('click', '.deleteSubCategoryElect', function () {
                const id = $(this).data("id");
                if (confirm("Are you sure you want to delete this?")) {
                    $.ajax({
                        type: "DELETE",
                        url: `{{ url('admin/subcategories') }}/${id}`,
                        success: (data) => dataTable.draw(),
                        error: (xhr) => alert('Error deleting item.'),
                    });
                }
            });

            $('#subCategory_status').on('change', function () {
                const isActive = $(this).prop('checked');
                $('#statusLabel').text(isActive ? 'Active' : 'Inactive')
                    .toggleClass('bg-success', isActive)
                    .toggleClass('bg-danger', !isActive);
            });
            $(document).on('change', '.user-status-dropdown', function () {
                const $dropdown = $(this);
                const id = $dropdown.data('id');
                const status = $dropdown.val();

                $dropdown.removeClass('text-success text-danger');
                $dropdown.addClass(status === 'active' ? 'text-success' : 'text-danger');
                $.ajax({
                    type: 'POST',
                    url: `{{ url('admin/subcategories') }}/${id}`,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        subCategory_status: status
                    },
                    success: function (response) {
                        console.log('Status updated:', response);
                    },
                    error: function () {
                        alert('Failed to update status.');
                    }
                });
            });

            $(document).on('click', '.view-photo', function () {
                $('#photoPreviewModal').attr('src', $(this).data('src'));
                $('#photoModal').modal('show');
            });

            $('#subCategory_photo').on('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => $('#previewImage').attr('src', e.target.result).show();
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
