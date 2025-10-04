@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        {{-- Page Header --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Categories for: <strong>{{ $electronic->electronics_category }} -> {{$category->category_name}}</strong></h4>
            </div>
        </div>

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

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="ajaxSubCategoryElectModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="subCategoryElectForm" name="subCategoryElectForm" enctype="multipart/form-data">
                    <input type="hidden" name="subCategory_elect_id" id="subCategory_elect_id">
                    <input type="hidden" name="category_id" id="category_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modelHeading"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="formErrors" class="alert alert-danger d-none m-3">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>
                    <div class="modal-body row g-3 p-3">
                        <div class="col-md-12">
                            <label for="subCategory_name" class="form-label">Sub-Category Name<span
                                    class="text-danger">*</span></label>
                            <input type="text" name="subCategory_name" id="subCategory_name" class="form-control"
                                   placeholder="Enter category name">
                        </div>
                        <div class="col-md-12">
                            <label for="subCategory_price" class="form-label">Price<span
                                    class="text-danger">*</span></label>
                            <input type="number" name="subCategory_price" id="subCategory_price" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="subCategory_photo" class="form-label">Category Photo</label>
                            <input type="file" name="subCategory_photo" id="subCategory_photo" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <img id="previewImage" src="" alt="Photo Preview" class="img-fluid mt-2 rounded shadow-sm"
                                 style="max-height: 150px; object-fit: contain; display: none;">
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <label class="form-label me-5 mb-0">Status</label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="subCategory_status"
                                       name="subCategory_status" checked>
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

    {{-- Photo Preview Modal --}}
    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body text-center p-0">
                    <img id="photoPreview" src="" alt="Full Size Photo" class="w-100 rounded"
                         style="max-height: 90vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script type="text/javascript">
        $(function () {
            const parentElectronicId = {{ $electronic->id }};
            const parentElectronicName = "{{ $electronic->electronics_category }}";
            const parentId = {{$category->id}};
            const parentName = "{{$category->category_name}}";

            const dataTable = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('electronics.categories.subcategories.index', ['electronic' => $electronic->id, 'category' => $category->id]) }}",                // "/electronics/3/categories"
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'parent_category', name: 'refersCategoryElect.category_name' , orderable: false, searchable: false},
                    {data: 'subCategory_name', name: 'subCategory_name'},
                    {data: 'subCategory_price', name: 'subCategory_price'},
                    {data: 'subCategory_photo', name: 'subCategory_photo', orderable: false, searchable: false},
                    {data: 'subCategory_status', name: 'subCategory_status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#createNewSubCategoryElect').click(function () {
                $('#subCategoryElectForm').trigger("reset");
                $('#subCategory_elect_id').val('');
                $('#modelHeading').html("Create Category for '" + parentName + "'");
                $('#previewImage').attr('src', '').hide();
                $('#formErrors').addClass('d-none');
                $('#errorList').empty();
                $('#subCategory_status').prop('checked', true).trigger('change');
                $('#ajaxSubCategoryElectModal').modal('show');
            });

            $('body').on('click', '.editSubCategoryElect', function () {
                const id = $(this).data('id');
                const url = "{{ url('admin/subcategories') }}/" + id + "/edit";
                $.get(url, function (res) {
                    const data = res.subcategory;
                    $('#modelHeading').html("Edit Sub-Category");
                    $('#subCategory_elect_id').val(data.id);
                    $('#subCategory_name').val(data.subCategory_name);
                    $('#subCategory_price').val(data.subCategory_price);
                    if (data.subCategory_photo) {
                        $('#previewImage').attr('src', "{{ asset('storage') }}/" + data.subCategory_photo).show();
                    } else {
                        $('#previewImage').attr('src', '').hide();
                    }
                    const isActive = data.subCategory_status === 'active';
                    $('#subCategory_status').prop('checked', isActive).trigger('change');
                    $('#ajaxSubCategoryElectModal').modal('show');
                });
            });

            $('#subCategory_status').on('change', function () {
                const isActive = $(this).prop('checked');
                $('#statusLabel').text(isActive ? 'Active' : 'Inactive')
                    .toggleClass('bg-success', isActive)
                    .toggleClass('bg-danger', !isActive);
            });

            $('#subCategoryElectForm').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                let subCategory_elect_id = $('#subCategory_elect_id').val();
                formData.set('subCategory_status', $('#subCategory_status').prop('checked') ? 'active' : 'inactive');

                let url;
                if (subCategory_elect_id) {
                    url = "{{ url('admin/subcategories') }}/" + subCategory_elect_id;
                    formData.append('_method', 'PUT');
                } else {
                    // This now creates the correct URL: /electronics/{id}/categories/{id}/subcategories
                    url = `{{ url('admin/electronics') }}/${parentElectronicId}/categories/${parentId}/subcategories`;
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        $('#ajaxSubCategoryElectModal').modal('hide');
                        dataTable.draw();
                    },
                    error: (xhr) => {
                        $('#formErrors').removeClass('d-none');
                        $('#errorList').empty();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, (key, messages) => {
                                $.each(messages, (i, msg) => $('#errorList').append('<li>' + msg + '</li>'));
                            });
                        } else {
                            $('#errorList').append('<li>An unexpected error occurred.</li>');
                        }
                    }
                });
            });

            $('body').on('click', '.deleteSubCategoryElect', function () {
                const id = $(this).data("id");
                if (confirm("Are you sure you want to delete this Category?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/subcategories') }}/" + id,
                        success: (data) => dataTable.draw(),
                        error: (xhr) => alert('Error deleting item.'),
                    });
                }
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
                $('#photoPreview').attr('src', $(this).data('src'));
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

