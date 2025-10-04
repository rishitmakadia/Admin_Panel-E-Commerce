@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        {{-- Page Header --}}
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Categories for: <strong>{{ $electronic->electronics_category }}</strong></h4>
            </div>
            <div>

            </div>
        </div>

        <div class="d-flex justify-content-end mb-2">
            <button type="button" class="btn btn-success" id="createNewCategoryElect">
                <i class="fas fa-plus-circle me-2"></i> Create New Category
            </button>
        </div>

        {{-- Data Table --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-hover data-table w-100">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Parent Electronic</th>
                        <th>Category Name</th>
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
    <div class="modal fade" id="ajaxCategoryElectModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="categoryElectForm" name="categoryElectForm" enctype="multipart/form-data">
                    <input type="hidden" name="category_elect_id" id="category_elect_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modelHeading"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="formErrors" class="alert alert-danger d-none m-3">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>
                    <div class="modal-body row g-3 p-3">
                        <div class="col-md-12">
                            <label for="category_name" class="form-label">Category Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="category_name" id="category_name" class="form-control"
                                   placeholder="Enter category name">
                        </div>
                        <div class="col-md-12">
                            <label for="category_photo" class="form-label">Category Photo</label>
                            <input type="file" name="category_photo" id="category_photo" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <img id="previewImage" src="" alt="Photo Preview" class="img-fluid mt-2 rounded shadow-sm"
                                 style="max-height: 150px; object-fit: contain; display: none;">
                        </div>
                        <div class="col-12 d-flex align-items-center">
                            <label class="form-label me-5 mb-0">Status</label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="category_status"
                                       name="category_status" checked>
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

            const dataTable = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('electronics.categories.index', ['electronic' => $electronic->id]) }}",
                // "/electronics/3/categories"
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'parent_electronic', name: 'refersElectronics.electronics_category'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'category_photo', name: 'category_photo', orderable: false, searchable: false},
                    {data: 'category_status', name: 'category_status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#createNewCategoryElect').click(function () {
                $('#categoryElectForm').trigger("reset");
                $('#category_elect_id').val('');
                $('#modelHeading').html("Create Category for '" + parentElectronicName + "'");
                $('#previewImage').attr('src', '').hide();
                $('#formErrors').addClass('d-none');
                $('#errorList').empty();
                $('#category_status').prop('checked', true).trigger('change');
                $('#ajaxCategoryElectModal').modal('show');
            });

            $('body').on('click', '.editCategoryElect', function () {
                const id = $(this).data('id');
                const url = "{{ url('admin/categories') }}/" + id + "/edit";
                $.get(url, function (res) {
                    const data=res.category;
                    $('#modelHeading').html("Edit Category");
                    $('#category_elect_id').val(data.id);
                    $('#category_name').val(data.category_name);

                    if (data.category_photo) {
                        $('#previewImage').attr('src', "{{ asset('storage') }}/" + data.category_photo).show();
                    } else {
                        $('#previewImage').attr('src', '').hide();
                    }
                    const isActive = data.category_status === 'active';
                    $('#category_status').prop('checked', isActive).trigger('change');
                    $('#ajaxCategoryElectModal').modal('show');
                });
            });

            $('#category_status').on('change', function () {
                const isActive = $(this).prop('checked');
                $('#statusLabel').text(isActive ? 'Active' : 'Inactive')
                    .toggleClass('bg-success', isActive)
                    .toggleClass('bg-danger', !isActive);
            });

            $('#categoryElectForm').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                let category_elect_id = $('#category_elect_id').val();
                formData.set('category_status', $('#category_status').prop('checked') ? 'active' : 'inactive');

                let url;
                if (category_elect_id) {
                    url = "{{ url('admin/categories') }}/" + category_elect_id;
                    formData.append('_method', 'PUT');
                } else {
                    url = "{{ url('admin/electronics') }}/" + parentElectronicId + "/categories";
                }

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        $('#ajaxCategoryElectModal').modal('hide');
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

            $('body').on('click', '.deleteCategoryElect', function () {
                const id = $(this).data("id");
                if (confirm("Are you sure you want to delete this Category?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/categories') }}/" + id,
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
                    url: `{{ url('admin/categories') }}/${id}`,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        category_status: status
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

            $('#category_photo').on('change', function () {
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
