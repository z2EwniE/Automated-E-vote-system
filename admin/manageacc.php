<?php
require_once '../config/init.php';

// Check if user is logged in and has Super Admin role
$user = new User();
$userData = $user->getUserData($_SESSION['uid'] ?? 0);
if (!isset($_SESSION['uid']) || $userData['role'] !== 'super_admin') {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="qoshima">
    <title>Manage Accounts - E-Vote System</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="css/light.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <?php include_once 'includes/sidebar.php'; ?>

        <div class="main">
            <?php include_once 'includes/navbar.php'; ?>

            <main class="content">
                <div class="container-fluid p-0">
                    <div class="mb-3">
                        <h1 class="h3 d-inline align-middle">Manage Accounts</h1>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">User Accounts</h5>
                                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                        <i class="align-middle" data-feather="user-plus"></i> Add User
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $stmt = $db->query("SELECT user_id as id, username, email, role, status FROM users ORDER BY username");
                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                                echo "<td>" . ($row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-warning">Inactive</span>') . "</td>";
                                                echo "<td>
                                                    <button class='btn btn-sm btn-info edit-user' data-id='" . $row['id'] . "'><i data-feather='edit'></i></button>
                                                    <button class='btn btn-sm btn-danger delete-user' data-id='" . $row['id'] . "'><i data-feather='trash'></i></button>
                                                </td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm">
                                <input type="hidden" name="register_token" id="register_token" value="<?= htmlentities(CSRF::generate('register_form')) ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" name="role" required>
                                        <option value="admin">Admin</option>
                                        <option value="super_admin">Super Admin</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editUserForm">
                                <input type="hidden" name="user_id" id="edit_user_id">
                                
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" id="edit_username" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="edit_email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" name="role" id="edit_role" required>
                                        <option value="admin">Admin</option>
                                        <option value="super_admin">Super Admin</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status" id="edit_status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="updateUserBtn">Update User</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include_once 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="../assets/js/sha512.min.js"></script>

    <script>
        const notyf = new Notyf({
            duration: 3000,
            position: { x: 'right', y: 'top' }
        });

        // Handle Add User
        $('#saveUserBtn').click(function() {
            const form = $('#addUserForm');
            const password = form.find('[name="password"]').val();
            const confirm_password = form.find('[name="confirm_password"]').val();

            if (password !== confirm_password) {
                notyf.error('Passwords do not match');
                return;
            }

            const hashedPassword = CryptoJS.SHA512(password).toString();
            const hashedConfirmPassword = CryptoJS.SHA512(confirm_password).toString();

            $.ajax({
                url: '../ajax/manage_users.php',
                type: 'POST',
                data: {
                    action: 'add',
                    username: form.find('[name="username"]').val(),
                    email: form.find('[name="email"]').val(),
                    password: hashedPassword,
                    confirm_password: hashedConfirmPassword,
                    role: form.find('[name="role"]').val(),
                    token: $('#register_token').val()
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        notyf.success(data.message);
                        $('#addUserModal').modal('hide');
                        location.reload();
                    } else {
                        notyf.error(data.message);
                    }
                }
            });
        });

        // Handle Edit User
        $('.edit-user').click(function() {
            const userId = $(this).data('id');
            $.ajax({
                url: 'ajax/manage_users.php',
                type: 'POST',
                data: {
                    action: 'get',
                    user_id: userId
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            $('#edit_user_id').val(data.user.id);
                            $('#edit_username').val(data.user.username);
                            $('#edit_email').val(data.user.email);
                            $('#edit_role').val(data.user.role);
                            $('#edit_status').val(data.user.status);
                            $('#editUserModal').modal('show');
                        } else {
                            notyf.error(data.message);
                        }
                    } catch (e) {
                        notyf.error('Error processing response');
                    }
                },
                error: function() {
                    notyf.error('Failed to fetch user data');
                }
            });
        });

        // Handle Update User
        $('#updateUserBtn').click(function() {
            const form = $('#editUserForm');
            $.ajax({
                url: 'ajax/manage_users.php',
                type: 'POST',
                data: {
                    action: 'update',
                    user_id: $('#edit_user_id').val(),
                    username: $('#edit_username').val(),
                    email: $('#edit_email').val(),
                    role: $('#edit_role').val(),
                    status: $('#edit_status').val()
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            notyf.success(data.message);
                            $('#editUserModal').modal('hide');
                            location.reload();
                        } else {
                            notyf.error(data.message);
                        }
                    } catch (e) {
                        notyf.error('Error processing response');
                    }
                },
                error: function() {
                    notyf.error('Failed to update user');
                }
            });
        });

        // Handle Delete User
        $('.delete-user').click(function() {
            if (!confirm('Are you sure you want to delete this user?')) {
                return;
            }

            const userId = $(this).data('id');
            $.ajax({
                url: 'ajax/manage_users.php',
                type: 'POST',
                data: {
                    action: 'delete',
                    user_id: userId
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            notyf.success(data.message);
                            location.reload();
                        } else {
                            notyf.error(data.message);
                        }
                    } catch (e) {
                        notyf.error('Error processing response');
                    }
                },
                error: function() {
                    notyf.error('Failed to delete user');
                }
            });
        });
    </script>
</body>
</html> 