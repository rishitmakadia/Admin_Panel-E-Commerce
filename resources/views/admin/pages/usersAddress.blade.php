@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="javascript:void(0)" class="btn btn-success" id="createUserAddress">
                <i class="fas fa-plus me-1"></i> Add User Address
            </a>
        </div>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Pincode</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>AL1</th>
                <th>AL2</th>
                <th>Type</th>
                <th>Status</th>
                <th width="130px">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="modal fade" id="ajaxAddressModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modelHeading">Create/Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addressForm">
                        <input type="hidden" name="addressId" id="addressId">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">User ID *</label>
                                <input type="text" class="form-control" id="user_id" name="user_id">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pincode" class="form-label">Pincode *</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" maxlength="6">
                            </div>
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country *</label>
                                <select class="form-select" id="country" name="country">
                                    <option value="India">India</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3" id="afterPincode">
                            <div class="col-md-6">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State *</label>
                                <input type="text" class="form-control" id="state" name="state">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address_line_1" class="form-label">Address Line 1 *</label>
                            <input type="text" class="form-control" id="address_line_1" name="address_line_1">
                        </div>
                        <div class="mb-3">
                            <label for="address_line_2" class="form-label">Address Line 2</label>
                            <input type="text" class="form-control" id="address_line_2" name="address_line_2">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="default">Default</option>
                                <option value="home">Home</option>
                                <option value="work">Work</option>
                            </select>
                        </div>

                        <div class="col-12 d-flex align-items-center">
                            <label class="form-label me-5 mb-0">Status</label>
                            <div class="form-check form-switch d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="status"
                                       name="status" checked>
                                <span id="statusLabel" class="badge bg-success ms-2">Active</span>
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
                ajax: "{{ route('address.index') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'name', name: 'name'},
                    {data: 'pincode', name: 'pincode'},
                    {data: 'city', name: 'city'},
                    {data: 'state', name: 'state'},
                    {data: 'country', name: 'country'},
                    {data: 'address_line_1', name: 'address_line_1'},
                    {data: 'address_line_2', name: 'address_line_2'},
                    {data: 'type', name: 'type'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#createUserAddress').click(function () {
                $('#addressForm').trigger("reset");
                $('#addressId').val('');
                $('#modelHeading').html("Create New Address");
                $('#status').prop('checked', true).trigger('change');
                $('#ajaxAddressModal').modal('show');
            });

            $('body').on('click', '.editUser', function () {
                const id = $(this).data('id');
                $.get("{{ route('address.index') }}/" + id + "/edit", function (data) {
                    $('#modelHeading').html("Edit Address");
                    $('#addressId').val(data.id);
                    $('#user_id').val(data.user_id);
                    $('#pincode').val(data.pincode);
                    $('#country').val(data.country);
                    $('#city').val(data.city);
                    $('#state').val(data.state);
                    $('#address_line_1').val(data.address_line_1);
                    $('#address_line_2').val(data.address_line_2);
                    $('#type').val(data.type);
                    $('#status').prop('checked', data.status === 'active');
                    $('#ajaxAddressModal').modal('show');
                });
            });

            $('#addressForm').submit(function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                let id = $('#addressId').val();
                formData.set('status', $('#status').prop('checked') ? 'active' : 'inactive');

                let url = id ? `{{ url('admin/address') }}/${id}` : `{{ route('address.store') }}`;
                if (id) formData.append('_method', 'PUT');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#ajaxAddressModal').modal('hide');
                        table.draw();
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        alert('Error saving user. Check console for details.');
                    }
                });
            });


            $('#status').on('change', function () {
                const isActive = $(this).prop('checked');
                $('#statusLabel').text(isActive ? 'Active' : 'Inactive')
                    .toggleClass('bg-success', isActive)
                    .toggleClass('bg-danger', !isActive);
            });

            $('body').on('click', '.deleteUser', function () {
                const id = $(this).data("id");
                if (confirm("Are you sure to delete this user?")) {
                    $.ajax({
                        type: 'DELETE',
                        url: `{{ url('admin/address') }}/${id}`,
                        success: function () {
                            table.draw();
                        },
                        error: function () {
                            alert('Error deleting user.');
                        }
                    });
                }
            });

            $('#status').on('change', function () {
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
                    url: `{{ url('admin/address') }}/${id}`,
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

        });
    </script>
@endpush

