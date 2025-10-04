@extends('layouts.app')

@section('content')
    <div class="modal fade" id="ajaxCategoryElectModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="categoryElectForm" name="categoryElectForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modelHeading"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div id="formErrors" class="alert alert-danger d-none mb-3">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>
                    <input type="hidden" name="category_id" id="category_id">
                    <div class="modal-body row g-3 p-3">
                        <div class="col-md-12">
                            <label for="category_elect_id" class="form-label">Parent Electronic <span
                                    class="text-danger">*</span></label>
                            <select name="category_elect_id" id="category_elect_id" class="form-select">
                                <option selected disabled>-- Select an Electronic --</option>
                                {{-- Loop through electronics passed from your controller --}}
                                @foreach ($electronics as $electronic)
                                    <option value="{{ $electronic->id }}"
                                            data-id="{{$electronic->id}}">{{ $electronic->electronics_category }}</option>
                                @endforeach
                            </select>
                        </div>

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
                                       name="category_status"
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            const dataTable = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('data.categories.all') }}",
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
                // $('#modelHeading').html("Create Category for '" + parentElectronicName + "'");
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
                    const category = res.category;
                    const electronic = res.electronic;

                    $('#modelHeading').html("Edit Category");
                    $('#category_id').val(category.id);
                    $('#category_elect_id').val(electronic.id); // Set selected electronics item
                    $('#category_name').val(category.category_name);

                    if (category.category_photo) {
                        $('#previewImage')
                            .attr('src', "{{ asset('storage') }}/" + category.category_photo)
                            .show();
                    } else {
                        $('#previewImage').attr('src', '').hide();
                    }

                    const isActive = category.category_status === 'active';
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
                let parentElectronicId = $('#category_elect_id').val();
                const id = $('#category_id').val();
                if (!parentElectronicId) {
                    alert('Please select a Parent Electronic.');
                    return;
                }
                console.log(id);
                console.log(parentElectronicId);
                formData.set('category_status', $('#category_status').prop('checked') ? 'active' : 'inactive');
                let url;
                if (parentElectronicId && id) {
                    url = "{{ url('admin/categories') }}/" + id;
                    formData.append('_method', 'PUT');
                    alert('id');
                } else {
                    url = `{{ url('admin/electronics') }}/${parentElectronicId}/categories`
                    alert('w/o id');
                }
                {{--let url = `{{ url('admin/electronics') }}/${parentElectronicId}/categories`;--}}

                $('#saveBtn').text('Saving...').prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#ajaxCategoryElectModal').modal('hide');
                        dataTable.draw();
                        $('#categoryElectForm').trigger("reset");
                        alert('Category created successfully!');
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
                            $('#errorList').append('<li>An unexpected error occurred.</li>');
                        }
                    },
                    complete: function () {
                        $('#saveBtn').text('Save Changes').prop('disabled', false);
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
                alert('adfsf');
                $('#photoPreview').attr('src', $(this).data('src'));
                $('#photoModal').modal('show');
            });
            $('#category_photo').on('change', function () {
                const file = this.files[0];
                alert('gsdf');
                if (file) {
                    const reader = new FileReader();
                    alert('hkhoh');
                    reader.onload = (e) => $('#previewImage').attr('src', e.target.result).show();
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
