<?php
require_once '../config/db.php';
require_once '../src/Auth.php';
require_once '../src/User.php';
require_once '../src/functions.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);
$userObj = new User($db);

$auth->requireRole(['admin']);

$message = '';

// Handle Create User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $role = clean_input($_POST['role']);
    $full_name = clean_input($_POST['full_name']);
    $email = clean_input($_POST['email']);

    if ($userObj->create($username, $password, $role, $full_name, $email)) {
        $message = '<div class="alert alert-success">User created successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to create user. Username may already exist.</div>';
    }
}

// Handle Delete User
if (isset($_GET['delete_id'])) {
    $id = clean_input($_GET['delete_id']);
    if ($userObj->delete($id)) {
        redirect('users.php');
    }
}

$users = $userObj->readAll();

include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">User Management</h2>
        <?php echo $message; ?>
        
        <div class="card mb-4">
            <div class="card-header">Add New User</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="doctor">Doctor</option>
                                <option value="nurse">Nurse</option>
                                <option value="receptionist">Receptionist</option>
                                <option value="patient">Patient</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="create_user" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Existing Users</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo $row['role']; ?></span></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <a href="users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
