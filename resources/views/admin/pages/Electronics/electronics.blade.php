@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="javascript:void(0)" class="btn btn-success" id="createNewElectronic">
                <i class="fas fa-plus me-1"></i> Create New Electronic
            </a>
        </div>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Photo</th>
                <th>Status</th>
                <th width="220px">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="ajaxElectronicModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="electronicForm" name="electronicForm" enctype="multipart/form-data">
                    <input type="hidden" name="electronics_id" id="electronics_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modelHeading">Create/Edit Electronic</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div id="formErrors" class="alert alert-danger d-none m-3">
                        <ul class="mb-0" id="errorList"></ul>
                    </div>
                    <div class="modal-body row g-3 p-3">
                        <div class="col-md-12">
                            <label for="electronics_category" class="form-label">Electronic Category</label>
                            <input type="text" name="electronics_category" id="electronics_category" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label for="electronics_category_photo" class="form-label">Electronic Photo</label>
                            <input type="file" name="electronics_category_photo" id="electronics_category_photo" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <img id="previewImage" src="" alt="Preview" class="img-fluid mt-2 rounded" style="max-height: 250px; object-fit: contain; display: none;">
                        </div>
                        <br>
                        <div class="col-12 d-flex align-items-center">
                            <label class="form-label me-5 mb-0">Status</label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="electronics_category_status" name="status" checked>
                                <span id="statusLabel" class="badge bg-success ms-2">Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveBtn" class="btn btn-primary">Save</button>
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
                    <img id="photoPreview" src="" alt="Profile Photo" class="w-100" style="max-height: 90vh; object-fit: contain;">
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

    <script>
        $(document).ready(function () {
            let table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('electronics.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'electronics_category', name: 'electronics_category' },
                    { data: 'electronics_category_photo', name: 'electronics_category_photo', orderable: false, searchable: false },
                    { data: 'electronics_category_status', name: 'electronics_category_status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#createNewElectronic').click(function () {
                $('#electronicForm').trigger("reset");
                $('#electronics_id').val('');
                $('#modelHeading').html("Create New Electronic");
                $('#previewImage').attr('src', '').hide();
                $('#formErrors').addClass('d-none');
                $('#errorList').empty();
                $('#electronics_category_status').prop('checked', true).trigger('change');
                $('#ajaxElectronicModal').modal('show');
            });

            $('body').on('click', '.editCategory', function () {
                const id = $(this).data('id');
                $.get("{{ url('admin/electronics') }}/" + id + "/edit", function (data) {
                    $('#modelHeading').html("Edit Electronic");
                    $('#electronics_id').val(data.id);
                    $('#electronics_category').val(data.electronics_category);

                    if (data.electronics_category_photo) {
                        $('#previewImage').attr('src', "{{ asset('storage') }}/" + data.electronics_category_photo).show();
                    } else {
                        $('#previewImage').attr('src', '').hide();
                    }
                    const isActive = data.electronics_category_status === 'active';
                    $('#electronics_category_status').prop('checked', isActive).trigger('change');
                    $('#ajaxElectronicModal').modal('show');
                });
            });

            $('#electronics_category_status').on('change', function () {
                const isActive = $(this).prop('checked');
                $('#statusLabel').text(isActive ? 'Active' : 'Inactive')
                    .toggleClass('bg-success', isActive)
                    .toggleClass('bg-danger', !isActive);
            });

            $('#electronicForm').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                let electronics_id = $('#electronics_id').val();

                formData.set('electronics_category_status', $('#electronics_category_status').prop('checked') ? 'active' : 'inactive');

                let url = electronics_id ? "{{ url('admin/electronics') }}/" + electronics_id: "{{ route('electronics.store') }}";
                if (electronics_id) formData.append('_method', 'PUT');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#ajaxElectronicModal').modal('hide');
                        table.draw();
                    },
                    error: function (xhr) {
                        $('#formErrors').removeClass('d-none');
                        $('#errorList').empty();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, messages) {
                                $.each(messages, (i, msg) => $('#errorList').append('<li>' + msg + '</li>'));
                            });
                        } else {
                            $('#errorList').append('<li>An unexpected error occurred.</li>');
                        }
                    }
                });
            });

            $('body').on('click', '.deleteCategory', function () {
                const id = $(this).data("id");
                if (confirm("Are you sure you want to delete this item? This cannot be undone.")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/electronics') }}/" + id,
                        success: (data) => table.draw(),
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
                    url: `{{ url('admin/electronics') }}/${id}`,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        electronics_category_status: status
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

            $('#electronics_category_photo').on('change', function () {
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
