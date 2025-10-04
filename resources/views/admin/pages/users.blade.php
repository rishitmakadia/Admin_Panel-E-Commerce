@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="javascript:void(0)" class="btn btn-success" id="createNewUser">
                <i class="fas fa-plus me-1"></i> Create New User
            </a>
        </div>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Photo</th>
                <th>Status</th>
                <th width="180px">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal for Create/Edit -->
    <div class="modal fade" id="ajaxUserModal" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form id="userForm" name="userForm">
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modelHeading">Create/Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control">
                            <div id="preview-container" class="mt-2">
                                <img id="preview-image" src="" alt="Preview" style="max-height: 100px; display: none;"
                                     class="img-thumbnail"/>
                            </div>
                        </div>
{{--                        <div class="mb-3">--}}
{{--                            <label for="status" class="form-label">Status</label>--}}
{{--                            <select name="status" id="status" class="form-select">--}}
{{--                                <option value="active">Active</option>--}}
{{--                                <option value="inactive">Inactive</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
                        <div class="col-12 d-flex align-items-center">
                            <label class="form-label me-5 mb-0">Status</label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="status"
                                       name="status" checked>
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
        $(function () {
            let table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'profile_photo', name: 'profile_photo', orderable: false, searchable: false},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#createNewUser').click(function () {
                $('#userForm').trigger("reset");
                $('#user_id').val('');
                $('#modelHeading').html("Create New User");
                $('#status').prop('checked', true).trigger('change');
                $('#ajaxUserModal').modal('show');
            });

            $('body').on('click', '.editUser', function () {
                const id = $(this).data('id');
                $.get("{{ route('users.index') }}/" + id + "/edit", function (data) {
                    $('#modelHeading').html("Edit User");
                    $('#user_id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    // $('#status').val(data.status);
                    const isActive = data.status === 'active';
                    $('#status').prop('checked', isActive).trigger('change');
                    $('#ajaxUserModal').modal('show');
                    if (data.profile_photo) {
                        $('#preview-image').attr('src', "{{ asset('storage') }}/" + data.profile_photo).show();
                        // $('#preview-image').attr('src', imageUrl).show();
                    } else {
                        $('#preview-image').hide();
                    }
                });
            });

            $('#status').on('change', function () {
                const isActive = $(this).prop('checked');
                $('#statusLabel').text(isActive ? 'Active' : 'Inactive')
                    .toggleClass('bg-success', isActive)
                    .toggleClass('bg-danger', !isActive);
            });

            $('#userForm').submit(function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                let id = $('#user_id').val();
                formData.set('status', $('#status').prop('checked') ? 'active' : 'inactive');
                let url = id ? `{{ url('admin/users') }}/${id}` : `{{ route('users.store') }}`;
                if (id) formData.append('_method', 'PUT');
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#ajaxUserModal').modal('hide');
                        table.draw();
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('Error saving user. Check console for details.');
                    }
                });
            });

            $('body').on('click', '.deleteUser', function () {
                const id = $(this).data("id");
                if (confirm("Are you sure to delete this user?")) {
                    $.ajax({
                        type: 'DELETE',
                        url: `{{ url('admin/users') }}/${id}`,
                        success: function () {
                            table.draw();
                        },
                        error: function () {
                            alert('Error deleting user.');
                        }
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
                    url: `{{ url('admin/users') }}/${id}`,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        status: status
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
            $('#profile_photo').on('change', function (event) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview-image').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(this.files[0]);
            });
        });
    </script>
@endpush
