<!doctype html>
<html lang="en">
<head name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $('#users-table').DataTable({
                "ajax": "{{ route('users.data') }}",
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "email" },
                    {
                        "data": "id",
                        "render": function(data, type, row) {
                            return `<button class="btn btn-warning btn-sm edit-btn" data-id="${data}">Edit</button>
                                    <button class="btn btn-secondary btn-sm soft-delete-btn" data-id="${data}">Soft Delete</button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">Delete</button>`;
                        }
                    }
                ]
            });

            //Soft Delete User
            $('users-table').on('click', '.soft-delete-btn', function() {
                if(confirm('Do you want to achive this user?')) {
                    let userId = $(this).data('id');
                    $.ajax({
                        url: '/users/${userId}/soft-delete',
                        method: 'Delete',
                        success: function() {
                            $('#users-table').DataTable().ajax.reload();
                        }
                    });
                }
            });

            //Permanently Delete User
            $('#user-table').on('click', '.delete-btn', function() {
                if(confirm('Are you sure?\nUser data will be permanently lost.')) {
                    let userId = $(this).data('id');
                    $.ajax({
                        url: '/users/${userId}/',
                        method: 'DELETE',
                        success: function() {
                            $('#user-table').DataTable().ajax.reload();
                        }
                    });
                }
            });

            $('#users-table').on('click', '.edit-btn', function() {
                var userId = $(this).data('id');
                // Fetch user data and populate the edit form
                $.get(`/users/${userId}`, function(user) {
                    $('#edit-user-id').val(user.id);
                    $('#edit-name').val(user.name);
                    $('#edit-email').val(user.email);
                    $('#editModal').modal('show');
                });
            });
            $('#save-changes').click(function() {
                var userId = $('#edit-user-id').val();
                var updatedData = {
                    name: $('#edit-name').val(),
                    email: $('#edit-email').val()
                };
                $.ajax({
                    url: `/users/${userId}`,
                    method: 'PUT',
                    data: updatedData,
                    success: function(response) {
                        $('#editModal').modal('hide');
                        $('#users-table').DataTable().ajax.reload();
                    }
                });
            });
        });
    </script>
</head>

<body>
    <table id="users-table" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-user-id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name">
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-changes">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>